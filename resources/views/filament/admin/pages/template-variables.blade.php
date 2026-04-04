<x-filament-panels::page>
    <div
        x-data="{
            selectedGroup: 'الكل',
            matches(group) {
                return this.selectedGroup === 'الكل' || this.selectedGroup === group;
            }
        }"
        class="space-y-6"
    >
        <div class="rounded-xl border bg-white p-4 shadow-sm">
            <h2 class="text-lg font-bold">دليل متغيرات القوالب</h2>
            <p class="mt-2 text-sm text-gray-600">
                هذه الصفحة مرجع ثابت للمتغيرات التي يمكنك نسخها واستخدامها داخل القوالب و HTML والصفحات الديناميكية.
            </p>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">فلترة حسب القسم</label>
                    <select
                        x-model="selectedGroup"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                        <option value="الكل">الكل</option>
                        @foreach(collect($this->variables)->pluck('group')->unique()->values() as $group)
                            <option value="{{ $group }}">{{ $group }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <div class="rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-600">
                        القسم الحالي:
                        <span class="font-bold text-gray-900" x-text="selectedGroup"></span>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <code class="rounded bg-gray-100 px-2 py-1 text-xs" dir="ltr">@{{ site.association_name }}</code>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">القسم</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">الاسم</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">المتغير</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">الاستخدام</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($this->variables as $item)
                            <tr class="align-top" x-show="matches(@js($item['group']))" x-cloak>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                    {{ $item['group'] }}
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $item['label'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <code class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-800" dir="ltr">
                                            {{ $item['code'] }}
                                        </code>
                                        <button
                                            type="button"
                                            class="rounded border px-2 py-1 text-xs"
                                            x-data="{}"
                                            x-on:click="navigator.clipboard.writeText(@js($item['code']))"
                                        >
                                            نسخ
                                        </button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $item['description'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
