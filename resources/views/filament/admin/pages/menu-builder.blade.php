<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-2xl border bg-white p-6">
            {{ $this->form }}
            <div class="mt-4">
                <x-filament::button wire:click="addItem">
                    إضافة العنصر
                </x-filament::button>
            </div>
        </div>

        <div
            x-data="menuBuilderTree(@js(json_decode($this->menuTreeJson, true) ?: []), @js($this->getId()))"
            class="rounded-2xl border bg-white p-6"
        >
            <div class="mb-2 text-lg font-bold">ترتيب عناصر القائمة</div>
            <div class="mb-4 text-sm text-gray-500">
                اسحب العنصر لأعلى أو أسفل لتغيير ترتيبه، واسحبه داخل المنطقة المتقطعة أسفل أي عنصر ليصبح عنصرًا فرعيًا.
            </div>

            <div class="mb-4 flex gap-2">
                <x-filament::button color="success" x-on:click="save()">
                    حفظ ترتيب القائمة
                </x-filament::button>
                <x-filament::button color="gray" x-on:click="reload()">
                    إعادة تحميل
                </x-filament::button>
            </div>

            <div class="rounded-2xl border bg-slate-50 p-4">
                <div id="menu-tree-root"></div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        function menuBuilderTree(initialTree, livewireId) {
            return {
                tree: initialTree,

                reload() {
                    this.tree = JSON.parse(JSON.stringify(initialTree));
                    this.render();
                },

                save() {
                    this.tree = this.readTree(document.getElementById('menu-tree-root'));
                    window.Livewire.find(livewireId).call('saveHierarchy', JSON.stringify(this.tree));
                },

                render() {
                    const root = document.getElementById('menu-tree-root');
                    if (!root) return;

                    root.innerHTML = '';

                    const createSortableList = (items = [], depth = 0) => {
                        const list = document.createElement('div');
                        list.className = 'menu-sortable-list space-y-3';
                        list.dataset.depth = depth;

                        items.forEach((item) => {
                            const wrapper = document.createElement('div');
                            wrapper.className = 'menu-node-wrapper';
                            wrapper.dataset.id = item.id;

                            const card = document.createElement('div');
                            card.className = 'rounded-2xl border bg-white p-4 shadow-sm';

                            const iconHtml = item.icon ? `<i class="${item.icon}"></i>` : '';
                            const childrenCount = Array.isArray(item.children) ? item.children.length : 0;

                            card.innerHTML = `
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <div class="font-bold flex items-center gap-2">
                                            <span class="cursor-move text-gray-400">☰</span>
                                            ${iconHtml}
                                            <span>${item.title}</span>
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1">${item.type} — ${item.url ?? '#'}</div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            ${childrenCount ? `عدد العناصر الفرعية: ${childrenCount}` : 'عنصر رئيسي أو بدون عناصر فرعية'}
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button" class="rounded-xl border px-3 py-1 text-sm text-red-600" data-delete-id="${item.id}">
                                            حذف
                                        </button>
                                    </div>
                                </div>
                            `;

                            const childZone = document.createElement('div');
                            childZone.className = 'child-drop-zone mt-3 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50/70 p-3';
                            childZone.innerHTML = `
                                <div class="mb-2 text-xs text-gray-400">
                                    اسحب عنصرًا إلى هنا لجعله عنصرًا فرعيًا
                                </div>
                            `;

                            const childList = createSortableList(item.children || [], depth + 1);
                            childZone.appendChild(childList);

                            wrapper.appendChild(card);
                            wrapper.appendChild(childZone);
                            list.appendChild(wrapper);
                        });

                        new Sortable(list, {
                            group: 'nested-menu',
                            animation: 150,
                            fallbackOnBody: true,
                            swapThreshold: 0.65,
                            emptyInsertThreshold: 10,
                            handle: '.cursor-move',
                            onEnd: () => {
                                this.tree = this.readTree(document.getElementById('menu-tree-root'));
                                this.bindDeleteButtons();
                            },
                        });

                        return list;
                    };

                    const topList = createSortableList(this.tree, 0);
                    root.appendChild(topList);
                    this.bindDeleteButtons();
                },

                bindDeleteButtons() {
                    document.querySelectorAll('[data-delete-id]').forEach((btn) => {
                        btn.onclick = () => {
                            const id = parseInt(btn.dataset.deleteId);
                            window.Livewire.find(livewireId).call('deleteItem', id);
                        };
                    });
                },

                readTree(root) {
                    const parseList = (listEl) => {
                        const result = [];
                        const wrappers = [...listEl.children].filter(el => el.classList.contains('menu-node-wrapper'));

                        wrappers.forEach((wrapper) => {
                            const id = parseInt(wrapper.dataset.id);
                            const childList = wrapper.querySelector(':scope > .child-drop-zone > .menu-sortable-list');

                            result.push({
                                id,
                                children: childList ? parseList(childList) : []
                            });
                        });

                        return result;
                    };

                    const topList = root.querySelector(':scope > .menu-sortable-list');
                    return topList ? parseList(topList) : [];
                },

                init() {
                    this.render();
                }
            }
        }
    </script>
@endpush
