<x-filament-panels::page>
    <div class="space-y-6">
        <div style="
            background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
            color: #fff;
            border-radius: 20px;
            padding: 22px 24px;
            box-shadow: 0 10px 30px rgba(15, 118, 110, .15);
        ">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:14px;opacity:.9;margin-bottom:6px;">مرحباً بك</div>
                    <div style="font-size:28px;font-weight:800;line-height:1.4;">
                        {{ $this->associationInfo->name ?? ($this->siteSettings->association_name ?? 'لوحة التحكم') }}
                    </div>
                    <div style="font-size:14px;opacity:.92;margin-top:8px;">
                        تابع مؤشرات الموقع وبيانات الجمعية من مكان واحد.
                    </div>
                </div>

                @if(!empty($this->associationExpiryDate))
                    <div style="
                        background: rgba(255,255,255,.14);
                        border: 1px solid rgba(255,255,255,.18);
                        border-radius: 16px;
                        padding: 14px 16px;
                        min-width: 220px;
                    ">
                        <div style="font-size:12px;opacity:.9;">تاريخ انتهاء الاشتراك</div>
                        <div style="font-size:22px;font-weight:800;margin-top:4px;">{{ $this->associationExpiryDate }}</div>
                    </div>
                @endif
            </div>
        </div>

        <div style="
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
            gap:16px;
        ">
            @foreach($this->stats as $item)
                <div style="
                    background:#fff;
                    border:1px solid #e5e7eb;
                    border-radius:18px;
                    padding:18px;
                    box-shadow:0 8px 24px rgba(15,23,42,.05);
                    transition:.2s ease;
                ">
                    <div style="font-size:28px;margin-bottom:10px;">{{ $item['icon'] ?? '📌' }}</div>
                    <div style="font-size:14px;color:#6b7280;margin-bottom:6px;">{{ $item['label'] ?? '' }}</div>
                    <div style="font-size:30px;font-weight:800;color:#111827;line-height:1;">{{ $item['value'] ?? 0 }}</div>
                </div>
            @endforeach
        </div>

        <div style="
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:20px;
            box-shadow:0 8px 24px rgba(15,23,42,.05);
            overflow:hidden;
        ">
            <div style="padding:18px 20px;border-bottom:1px solid #eef2f7;">
                <div style="font-size:18px;font-weight:800;color:#111827;">بيانات الجمعية</div>
                <div style="font-size:13px;color:#6b7280;margin-top:4px;">معلومات مرتبطة من السجل المركزي وإعدادات الموقع</div>
            </div>

            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th style="padding:14px 16px;text-align:right;font-size:13px;color:#374151;border-bottom:1px solid #eef2f7;">اسم الجمعية</th>
                            <th style="padding:14px 16px;text-align:right;font-size:13px;color:#374151;border-bottom:1px solid #eef2f7;">الإيميل المسجل</th>
                            <th style="padding:14px 16px;text-align:right;font-size:13px;color:#374151;border-bottom:1px solid #eef2f7;">رقم الجوال</th>
                            <th style="padding:14px 16px;text-align:right;font-size:13px;color:#374151;border-bottom:1px solid #eef2f7;">تاريخ انتهاء الاشتراك</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:16px;border-bottom:1px solid #f1f5f9;color:#111827;font-weight:700;">
                                {{ $this->associationInfo->name ?? ($this->siteSettings->association_name ?? '—') }}
                            </td>
                            <td style="padding:16px;border-bottom:1px solid #f1f5f9;color:#374151;">
                                {{ $this->associationInfo->official_email ?? ($this->siteSettings->email ?? '—') }}
                            </td>
                            <td style="padding:16px;border-bottom:1px solid #f1f5f9;color:#374151;">
                                {{ $this->associationInfo->official_phone ?? ($this->siteSettings->phone ?? '—') }}
                            </td>
                            <td style="padding:16px;border-bottom:1px solid #f1f5f9;color:#374151;">
                                {{ $this->associationExpiryDate ?? '—' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
