<x-filament-panels::page>
    @php
        $renderTree = function ($items) use (&$renderTree) {
            $html = '<ul class="menu-tree space-y-2">';

            foreach ($items as $item) {
                $html .= '<li class="menu-item bg-gray-100 p-3 rounded cursor-move" data-id="' . e($item['id']) . '">';
                $html .= '<div class="flex justify-between items-center">';
                $html .= '<span>' . e($item['title']) . '</span>';
                $html .= '</div>';

                if (! empty($item['children'])) {
                    $html .= '<div class="children-wrap mt-2 ps-6">';
                    $html .= $renderTree($item['children']);
                    $html .= '</div>';
                } else {
                    $html .= '<div class="children-wrap mt-2 ps-6"><ul class="menu-tree empty-drop-zone"></ul></div>';
                }

                $html .= '</li>';
            }

            $html .= '</ul>';

            return $html;
        };
    @endphp

    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold mb-6">بناء القوائم (مثل ووردبريس)</h2>

        <button
            type="button"
            class="mb-4 px-4 py-2 bg-primary-600 text-white rounded"
            onclick="saveMenuTree()"
        >
            حفظ القوائم
        </button>

        <div id="menuTreeRoot">
            {!! $renderTree($this->getTree()) !!}
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script>
            function initNestedSortable() {
                document.querySelectorAll('.menu-tree').forEach((el) => {
                    if (el.dataset.sortableInit === '1') return;
                    el.dataset.sortableInit = '1';

                    new Sortable(el, {
                        group: 'nested',
                        animation: 150,
                        fallbackOnBody: true,
                        swapThreshold: 0.65,
                    });
                });
            }

            function buildTree(ul) {
                let tree = [];

                ul.querySelectorAll(':scope > li.menu-item').forEach((li) => {
                    const childUl = li.querySelector(':scope > .children-wrap > ul.menu-tree');

                    tree.push({
                        id: parseInt(li.dataset.id),
                        children: childUl ? buildTree(childUl) : []
                    });
                });

                return tree;
            }

            function saveMenuTree() {
                const rootUl = document.querySelector('#menuTreeRoot > ul.menu-tree');
                const tree = buildTree(rootUl);

                const wireId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                if (!wireId) return;

                Livewire.find(wireId).call('saveTree', tree);
            }

            document.addEventListener('DOMContentLoaded', initNestedSortable);
            document.addEventListener('livewire:navigated', initNestedSortable);
            document.addEventListener('livewire:updated', initNestedSortable);
        </script>
    @endpush

    <style>
        .menu-item {
            border: 1px solid #e5e7eb;
        }

        .menu-tree {
            min-height: 12px;
        }

        .empty-drop-zone {
            min-height: 18px;
        }
    </style>
</x-filament-panels::page>
