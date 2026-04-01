<x-filament-panels::page>
    @php
        $statusLabel = match($submission->status) {
            'new' => 'جديد',
            'under_review' => 'قيد المراجعة',
            'awaiting_completion' => 'بانتظار استكمال',
            'replied' => 'تم الرد',
            'completed' => 'مكتمل',
            'rejected' => 'مرفوض',
            default => $submission->status,
        };

        $statusBg = match($submission->status) {
            'new' => 'background:#dbeafe;color:#1d4ed8;',
            'under_review' => 'background:#fef3c7;color:#b45309;',
            'awaiting_completion' => 'background:#fde68a;color:#92400e;',
            'replied' => 'background:#dcfce7;color:#166534;',
            'completed' => 'background:#dcfce7;color:#166534;',
            'rejected' => 'background:#fee2e2;color:#991b1b;',
            default => 'background:#e5e7eb;color:#374151;',
        };
    @endphp

    <div class="space-y-6">
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl p-5 shadow-sm" style="background:linear-gradient(135deg,#0f766e 0%,#14b8a6 100%);color:#fff;">
                <div style="font-size:13px;opacity:.85;">رقم الطلب</div>
                <div style="margin-top:10px;font-size:26px;font-weight:800;line-height:1.2;">{{ $submission->reference_number }}</div>
            </div>

            <div class="rounded-2xl p-5 shadow-sm" style="background:linear-gradient(135deg,#2563eb 0%,#60a5fa 100%);color:#fff;">
                <div style="font-size:13px;opacity:.85;">رقم الجوال</div>
                <div style="margin-top:10px;font-size:26px;font-weight:800;line-height:1.2;">{{ $submission->phone }}</div>
            </div>

            <div class="rounded-2xl p-5 shadow-sm" style="background:linear-gradient(135deg,#7c3aed 0%,#a78bfa 100%);color:#fff;">
                <div style="font-size:13px;opacity:.85;">اسم النموذج</div>
                <div style="margin-top:10px;font-size:24px;font-weight:800;line-height:1.2;">{{ $record->name }}</div>
            </div>

            <div class="rounded-2xl p-5 shadow-sm" style="background:linear-gradient(135deg,#ea580c 0%,#fb923c 100%);color:#fff;">
                <div style="font-size:13px;opacity:.85;">حالة الطلب</div>
                <div style="margin-top:10px;">
                    <span style="display:inline-block;padding:10px 14px;border-radius:999px;background:rgba(255,255,255,.18);font-size:16px;font-weight:800;">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="xl:col-span-2 space-y-6">
                <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 dark:border-gray-700">
                        <div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">تفاصيل الطلب</div>
                            <div class="mt-1 text-sm text-gray-500">البيانات التي أرسلها العميل عند تقديم الطلب</div>
                        </div>
                        <div style="{{ $statusBg }}padding:8px 14px;border-radius:999px;font-size:13px;font-weight:700;">
                            {{ $statusLabel }}
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            @foreach(($submission->data ?? []) as $key => $value)
                                <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4 dark:border-gray-700 dark:bg-gray-800">
                                    <div class="mb-2 text-xs font-semibold tracking-wide text-gray-500">
                                        {{ $key }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white" style="line-height:1.9;">
                                        @if(is_string($value) && str_starts_with($value, 'form-submissions/'))
                                            <a href="{{ asset('storage/' . $value) }}" target="_blank" class="inline-flex items-center gap-2 text-primary-600 underline">
                                                عرض المرفق
                                            </a>
                                        @else
                                            {{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="border-b border-gray-100 px-6 py-5 dark:border-gray-700">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">سجل الردود والملاحظات</div>
                        <div class="mt-1 text-sm text-gray-500">كل ما تم على هذا الطلب من ملاحظات أو ردود</div>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($submission->messages as $message)
                                @php
                                    $isCustomer = $message->type === 'customer_reply';
                                    $isInternal = $message->type === 'internal_note';
                                    $title = match($message->type) {
                                        'staff_reply' => 'رد للعميل',
                                        'customer_reply' => 'رد العميل',
                                        'internal_note' => 'ملاحظة داخلية',
                                        default => $message->type,
                                    };

                                    $boxStyle = $isCustomer
                                        ? 'background:#eff6ff;border:1px solid #bfdbfe;'
                                        : ($isInternal
                                            ? 'background:#f9fafb;border:1px solid #e5e7eb;'
                                            : 'background:#ecfdf5;border:1px solid #a7f3d0;');

                                    $badgeStyle = $isCustomer
                                        ? 'background:#dbeafe;color:#1d4ed8;'
                                        : ($isInternal
                                            ? 'background:#e5e7eb;color:#374151;'
                                            : 'background:#d1fae5;color:#065f46;');
                                @endphp

                                <div class="rounded-2xl p-4 dark:bg-gray-800/50" style="{{ $boxStyle }}">
                                    <div class="mb-3 flex flex-wrap items-center gap-2">
                                        <span style="{{ $badgeStyle }}padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;">
                                            {{ $title }}
                                        </span>

                                        @if($message->is_visible_to_customer)
                                            <span style="background:#ede9fe;color:#6d28d9;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;">
                                                ظاهر للعميل
                                            </span>
                                        @endif

                                        <span class="text-xs text-gray-500">
                                            {{ $message->created_at?->format('Y-m-d h:i A') }}
                                        </span>
                                    </div>

                                    <div class="text-sm text-gray-900 dark:text-white" style="line-height:2;">
                                        {{ $message->message }}
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-gray-300 px-6 py-10 text-center text-gray-500 dark:border-gray-700">
                                    لا توجد رسائل حتى الآن
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="border-b border-gray-100 px-6 py-5 dark:border-gray-700">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">إدارة الطلب</div>
                        <div class="mt-1 text-sm text-gray-500">تغيير حالة الطلب وخيارات التفاعل</div>
                    </div>

                    <div class="space-y-5 p-6">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">الحالة</label>
                            <select wire:model="status" class="w-full rounded-2xl border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                <option value="new">جديد</option>
                                <option value="under_review">قيد المراجعة</option>
                                <option value="awaiting_completion">بانتظار استكمال</option>
                                <option value="replied">تم الرد</option>
                                <option value="completed">مكتمل</option>
                                <option value="rejected">مرفوض</option>
                            </select>
                        </div>

                        <label class="flex items-center gap-3 rounded-2xl border border-gray-200 px-4 py-4 dark:border-gray-700">
                            <input type="checkbox" wire:model="allow_customer_reply">
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">السماح للعميل بالرد</div>
                                <div class="text-xs text-gray-500">عند التفعيل يمكنه إضافة رد من صفحة الاستعلام</div>
                            </div>
                        </label>

                        <button
                            wire:click="saveMeta"
                            type="button"
                            style="width:100%; background:linear-gradient(135deg,#ea580c 0%,#fb923c 100%); color:#ffffff; border:none; border-radius:16px; padding:14px 18px; font-weight:800; cursor:pointer; display:block;"
                        >
                            حفظ التعديلات
                        </button>
                    </div>
                </div>

                <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="border-b border-gray-100 px-6 py-5 dark:border-gray-700">
                        <div class="text-lg font-bold text-gray-900 dark:text-white">إضافة رد أو ملاحظة</div>
                        <div class="mt-1 text-sm text-gray-500">يمكنك إرسال رد رسمي للعميل أو كتابة ملاحظة داخلية</div>
                    </div>

                    <div class="space-y-5 p-6">
                        <div class="rounded-2xl bg-amber-50 px-4 py-4 text-sm text-amber-800 dark:bg-amber-500/10 dark:text-amber-200">
                            <strong>تنبيه:</strong>
                            زر <strong>حفظ التعديلات</strong> يحفظ الحالة وخيار الرد فقط، أما نص الرسالة هنا فيُحفظ من زر <strong>إضافة الرسالة</strong>.
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">نوع الرسالة</label>
                            <select wire:model="message_type" class="w-full rounded-2xl border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                <option value="staff_reply">رد للعميل</option>
                                <option value="internal_note">ملاحظة داخلية</option>
                            </select>
                        </div>

                        <div class="rounded-2xl bg-gray-50 px-4 py-3 text-xs text-gray-500 dark:bg-gray-800">
                            الرد للعميل يظهر له في صفحة الاستعلام، أما الملاحظة الداخلية فهي خاصة بفريق العمل فقط.
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">نص الرسالة</label>
                            <textarea
                                wire:model="message"
                                rows="7"
                                class="w-full rounded-2xl border-gray-300 dark:border-gray-600 dark:bg-gray-800"
                                placeholder="اكتب الرد أو الملاحظة هنا"
                            ></textarea>
                        </div>

                        <button
                            wire:click="addMessage"
                            type="button"
                            style="width:100%; background:linear-gradient(135deg,#059669 0%,#10b981 100%); color:#ffffff; border:none; border-radius:16px; padding:14px 18px; font-weight:800; cursor:pointer; display:block;"
                        >
                            إضافة الرسالة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
