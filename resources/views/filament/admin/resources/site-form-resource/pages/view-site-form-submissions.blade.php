<x-filament-panels::page>
    @php
        $submissions = $this->submissions;

        $statusMap = [
            'new' => ['label' => 'جديد', 'classes' => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-300'],
            'under_review' => ['label' => 'قيد المراجعة', 'classes' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300'],
            'awaiting_completion' => ['label' => 'بانتظار الاستكمال', 'classes' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-300'],
            'replied' => ['label' => 'تم الرد', 'classes' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300'],
            'completed' => ['label' => 'مكتمل', 'classes' => 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-300'],
            'rejected' => ['label' => 'مرفوض', 'classes' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-300'],
        ];

        $allCount = $record->submissions()->count();
        $newCount = $record->submissions()->where('status', 'new')->count();
        $openCount = $record->submissions()->whereIn('status', ['new', 'under_review', 'awaiting_completion', 'replied'])->count();
        $completedCount = $record->submissions()->where('status', 'completed')->count();
    @endphp

    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="text-sm text-gray-500 dark:text-gray-400">إجمالي الطلبات</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($allCount) }}</div>
            </div>

            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 shadow-sm dark:border-blue-800 dark:bg-blue-500/10">
                <div class="text-sm text-blue-700 dark:text-blue-300">الطلبات الجديدة</div>
                <div class="mt-2 text-3xl font-bold text-blue-900 dark:text-blue-200">{{ number_format($newCount) }}</div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm dark:border-amber-800 dark:bg-amber-500/10">
                <div class="text-sm text-amber-700 dark:text-amber-300">الطلبات المفتوحة</div>
                <div class="mt-2 text-3xl font-bold text-amber-900 dark:text-amber-200">{{ number_format($openCount) }}</div>
            </div>

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm dark:border-emerald-800 dark:bg-emerald-500/10">
                <div class="text-sm text-emerald-700 dark:text-emerald-300">الطلبات المكتملة</div>
                <div class="mt-2 text-3xl font-bold text-emerald-900 dark:text-emerald-200">{{ number_format($completedCount) }}</div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="grid gap-4 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">بحث</label>
                    <x-filament::input.wrapper>
                        <x-filament::input
                            wire:model.live.debounce.300ms="search"
                            placeholder="ابحث برقم الطلب أو رقم الجوال"
                        />
                    </x-filament::input.wrapper>
                </div>

                <div class="lg:col-span-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-200">الحالة</label>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="statusFilter">
                            <option value="">كل الحالات</option>
                            <option value="new">جديد</option>
                            <option value="under_review">قيد المراجعة</option>
                            <option value="awaiting_completion">بانتظار الاستكمال</option>
                            <option value="replied">تم الرد</option>
                            <option value="completed">مكتمل</option>
                            <option value="rejected">مرفوض</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>

                <div class="lg:col-span-3 flex items-end">
                    <x-filament::button
                        wire:click="exportCsv"
                        icon="heroicon-o-arrow-down-tray"
                        class="w-full"
                    >
                        تصدير CSV
                    </x-filament::button>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-4 py-3.5 text-right text-sm font-semibold text-gray-700 dark:text-gray-200">رقم الطلب</th>
                            <th class="px-4 py-3.5 text-right text-sm font-semibold text-gray-700 dark:text-gray-200">رقم الجوال</th>
                            <th class="px-4 py-3.5 text-right text-sm font-semibold text-gray-700 dark:text-gray-200">تاريخ الإرسال</th>
                            <th class="px-4 py-3.5 text-right text-sm font-semibold text-gray-700 dark:text-gray-200">الحالة</th>
                            <th class="px-4 py-3.5 text-right text-sm font-semibold text-gray-700 dark:text-gray-200">رد العميل</th>
                            <th class="px-4 py-3.5 text-right text-sm font-semibold text-gray-700 dark:text-gray-200">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($submissions as $submission)
                            @php
                                $status = $statusMap[$submission->status] ?? ['label' => $submission->status, 'classes' => 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-500/10 dark:text-gray-300'];
                            @endphp
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-white/5">
                                <td class="whitespace-nowrap px-4 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $submission->reference_number }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-800 dark:text-gray-200">
                                    {{ $submission->phone }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ optional($submission->submitted_at)->format('Y-m-d h:i A') ?: optional($submission->created_at)->format('Y-m-d h:i A') }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $status['classes'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm">
                                    @if($submission->allow_customer_reply)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                                            مفعل
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-700 ring-1 ring-inset ring-gray-600/20 dark:bg-gray-500/10 dark:text-gray-300">
                                            غير مفعل
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm">
                                    <x-filament::button
                                        tag="a"
                                        :href="\App\Filament\Admin\Resources\SiteFormResource::getUrl('submission', ['record' => $record, 'submission' => $submission])"
                                        icon="heroicon-o-eye"
                                        size="sm"
                                    >
                                        عرض الطلب
                                    </x-filament::button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                    لا توجد طلبات مطابقة
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $submissions->links() }}
        </div>
    </div>
</x-filament-panels::page>
