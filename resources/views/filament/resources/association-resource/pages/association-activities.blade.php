<x-filament-panels::page>
    <div class="space-y-4">
        <div class="rounded-xl border bg-white p-4">
            <div class="text-lg font-bold">{{ $this->record->name }}</div>
            <div class="text-sm text-gray-500">سجل الحركات الخاص بالجمعية</div>
        </div>

        <div class="rounded-xl border bg-white overflow-hidden">
            <table class="w-full text-right text-sm">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="p-4">الرمز</th>
                        <th class="p-4">النوع</th>
                        <th class="p-4">العنوان</th>
                        <th class="p-4">التفاصيل</th>
                        <th class="p-4">بواسطة</th>
                        <th class="p-4">التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->activities as $activity)
                        <tr class="border-b align-top">
                            <td class="p-4 whitespace-nowrap">{{ $activity->action_code }}</td>
                            <td class="p-4 whitespace-nowrap">{{ $activity->action_type }}</td>
                            <td class="p-4 font-medium">{{ $activity->title }}</td>
                            <td class="p-4 text-gray-600 whitespace-pre-line">{{ $activity->details }}</td>
                            <td class="p-4 whitespace-nowrap">{{ $activity->performedBy->name ?? '-' }}</td>
                            <td class="p-4 whitespace-nowrap">{{ optional($activity->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-gray-500">لا توجد حركات مسجلة لهذه الجمعية</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
