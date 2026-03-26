<x-filament-panels::page>
    <div class="flex flex-col gap-4 md:flex-row md:items-center mb-4">
        <div>
            <h2 class="text-lg font-bold">النسخ الاحتياطية</h2>
            <p class="text-sm text-gray-500">يمكنك تشغيل النسخ الاحتياطي الآن، وسيعمل في الخلفية.</p>
        </div>

        <div class="flex gap-2 mr-auto">
            <x-filament::button wire:click="runBackup" color="success">
                إنشاء نسخة الآن
            </x-filament::button>

            <x-filament::button wire:click="loadBackups" color="gray">
                تحديث القائمة
            </x-filament::button>
        </div>
    </div>

    <div class="rounded-xl border bg-white overflow-hidden">
        <table class="w-full text-right text-sm">
            <thead class="bg-gray-50">
                <tr class="border-b">
                    <th class="p-4">الجمعية</th>
                    <th class="p-4">التاريخ والوقت</th>
                    <th class="p-4">الحجم</th>
                    <th class="p-4">المسار</th>
                    <th class="p-4">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->backups as $backup)
                    <tr class="border-b align-top">
                        <td class="p-4 font-semibold whitespace-nowrap">
                            {{ $backup['association_name'] }}
                        </td>

                        <td class="p-4">
                            <div class="inline-flex flex-col gap-2">
                                <span class="inline-flex w-fit rounded-lg bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                    {{ $backup['date_display'] }}
                                </span>

                                @if (!empty($backup['time_display']))
                                    <span class="inline-flex w-fit rounded-lg bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700">
                                        {{ $backup['time_display'] }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        <td class="p-4 whitespace-nowrap">
                            {{ $backup['size'] }}
                        </td>

                        <td class="p-4 text-xs text-gray-500 max-w-xs break-words leading-5">
                            {{ $backup['path'] }}
                        </td>

                        <td class="p-4 whitespace-nowrap">
                            <div class="flex flex-wrap gap-2">
                                <x-filament::button
                                    size="sm"
                                    wire:click="download('{{ $backup['path'] }}')">
                                    تحميل
                                </x-filament::button>

                                <div x-data>
                                    <x-filament::button
                                        size="sm"
                                        color="danger"
                                        x-on:click.prevent="if (confirm('تحذير: سيتم حذف هذه النسخة الاحتياطية نهائيًا من السيرفر. هل أنت متأكد؟')) { $wire.delete('{{ $backup['path'] }}') }">
                                        حذف
                                    </x-filament::button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">
                            لا توجد نسخ احتياطية بعد
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
