<x-filament-panels::page>
    <div class="mb-6">
        <x-filament::button wire:click="refreshStats" icon="heroicon-o-arrow-path">
            تحديث الإحصائيات
        </x-filament::button>
    </div>

    @php
        $cards = [
            ['title' => 'إجمالي الزيارات', 'value' => $stats['all'] ?? 0, 'icon' => 'chart-bar-square', 'bg' => 'linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)'],
            ['title' => 'زيارات اليوم', 'value' => $stats['today'] ?? 0, 'icon' => 'calendar-days', 'bg' => 'linear-gradient(135deg, #059669 0%, #10b981 100%)'],
            ['title' => 'زيارات الرئيسية', 'value' => $stats['home'] ?? 0, 'icon' => 'home', 'bg' => 'linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%)'],
            ['title' => 'زيارات الصفحات', 'value' => $stats['pages'] ?? 0, 'icon' => 'document-text', 'bg' => 'linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%)'],
            ['title' => 'زيارات الأخبار', 'value' => $stats['news'] ?? 0, 'icon' => 'newspaper', 'bg' => 'linear-gradient(135deg, #d97706 0%, #f59e0b 100%)'],
            ['title' => 'زيارات المشاريع', 'value' => $stats['projects'] ?? 0, 'icon' => 'briefcase', 'bg' => 'linear-gradient(135deg, #e11d48 0%, #f43f5e 100%)'],
        ];
    @endphp

    <div class="grid grid-cols-1 gap-5 mb-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach($cards as $card)
            <div
                class="relative overflow-hidden rounded-2xl p-5 shadow-lg"
                style="background: {{ $card['bg'] }}; color: white;"
            >
                <div style="position:absolute; inset-inline-start:-20px; top:-20px; width:90px; height:90px; border-radius:9999px; background:rgba(255,255,255,.08);"></div>
                <div style="position:absolute; inset-inline-end:-20px; bottom:-20px; width:90px; height:90px; border-radius:9999px; background:rgba(255,255,255,.08);"></div>

                <div class="relative flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-medium" style="color: rgba(255,255,255,.82);">
                            {{ $card['title'] }}
                        </div>

                        <div class="mt-4 text-center text-4xl font-extrabold leading-none tracking-tight" style="color: white;">
                            {{ number_format($card['value']) }}
                        </div>

                        <div class="mt-4 h-2 overflow-hidden rounded-full" style="background: rgba(255,255,255,.18);">
                            <div class="h-full rounded-full" style="width: 68%; background: rgba(255,255,255,.45);"></div>
                        </div>
                    </div>

                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl"
                        style="background: rgba(255,255,255,.16);"
                    >
                        <x-dynamic-component :component="'heroicon-o-' . $card['icon']" class="h-7 w-7 text-white" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-gray-700">
            <div class="font-bold text-gray-900 dark:text-white">أكثر الروابط زيارة</div>
            <div class="text-xs text-gray-400">آخر تحديث حسب البيانات</div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">الرابط</th>
                        <th class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">النوع</th>
                        <th class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">عدد الزيارات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topUrls as $row)
                        <tr class="border-t border-gray-100 dark:border-gray-700">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $row['url'] }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $type = match($row['type'] ?? null) {
                                        'home' => 'الرئيسية',
                                        'page' => 'صفحة',
                                        'news' => 'خبر',
                                        'project' => 'مشروع',
                                        default => $row['type'] ?? '—',
                                    };
                                @endphp
                                <span class="rounded px-2 py-1 text-xs text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-gray-200">
                                    {{ $type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">
                                {{ number_format($row['visits']) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                لا توجد زيارات
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
