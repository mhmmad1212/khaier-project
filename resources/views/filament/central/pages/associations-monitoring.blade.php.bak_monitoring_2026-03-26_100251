<x-filament-panels::page>
    <div style="display:grid;gap:16px;">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:16px;">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;">
                <div>
                    <label style="display:block;margin-bottom:6px;font-weight:700;">بحث</label>
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        placeholder="اسم الجمعية أو الدومين"
                        style="width:100%;border:1px solid #d1d5db;border-radius:12px;padding:10px 12px;"
                    >
                </div>

                <div>
                    <label style="display:block;margin-bottom:6px;font-weight:700;">حالة الموقع</label>
                    <select wire:model.live="statusFilter" style="width:100%;border:1px solid #d1d5db;border-radius:12px;padding:10px 12px;">
                        <option value="">الكل</option>
                        <option value="active">نشطة</option>
                        <option value="closed">مغلقة</option>
                        <option value="suspended">موقوفة</option>
                    </select>
                </div>

                <div>
                    <label style="display:block;margin-bottom:6px;font-weight:700;">حالة الاشتراك</label>
                    <select wire:model.live="subscriptionFilter" style="width:100%;border:1px solid #d1d5db;border-radius:12px;padding:10px 12px;">
                        <option value="">الكل</option>
                        <option value="active">نشط</option>
                        <option value="expired">منتهي</option>
                        <option value="suspended">معلق</option>
                    </select>
                </div>

                <div>
                    <label style="display:block;margin-bottom:6px;font-weight:700;">ينتهي خلال</label>
                    <select wire:model.live="daysFilter" style="width:100%;border:1px solid #d1d5db;border-radius:12px;padding:10px 12px;">
                        <option value="">الكل</option>
                        <option value="30">30 يوم</option>
                        <option value="60">60 يوم</option>
                        <option value="90">90 يوم</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:18px;overflow:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:1100px;">
                <thead style="background:#f9fafb;">
                    <tr>
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;text-align:right;">الجمعية</th>
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;text-align:right;">الدومين</th>
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;text-align:right;">حالة الموقع</th>
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;text-align:right;">الاشتراك</th>
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;text-align:right;">تاريخ الانتهاء</th>
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;text-align:right;">المتبقي</th>
                        <th style="padding:12px;border-bottom:1px solid #e5e7eb;text-align:right;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->associations as $association)
                        @php
                            $daysLeft = null;
                            if (!empty($association->subscription_end_date)) {
                                $daysLeft = now()->startOfDay()->diffInDays(\Illuminate\Support\Carbon::parse($association->subscription_end_date)->startOfDay(), false);
                            }

                            $siteStatusLabel = match($association->site_status) {
                                'active' => 'نشطة',
                                'closed' => 'مغلقة',
                                'suspended' => 'موقوفة',
                                default => '-',
                            };

                            $subscriptionLabel = match($association->subscription_status) {
                                'active' => 'نشط',
                                'expired' => 'منتهي',
                                'suspended' => 'معلق',
                                default => '-',
                            };
                        @endphp

                        <tr>
                            <td style="padding:12px;border-bottom:1px solid #f3f4f6;">
                                <div style="font-weight:700;">{{ $association->name }}</div>
                                <div style="color:#6b7280;font-size:12px;">{{ $association->slug }}</div>
                            </td>

                            <td style="padding:12px;border-bottom:1px solid #f3f4f6;">{{ $association->domain ?: '-' }}</td>

                            <td style="padding:12px;border-bottom:1px solid #f3f4f6;">{{ $siteStatusLabel }}</td>

                            <td style="padding:12px;border-bottom:1px solid #f3f4f6;">{{ $subscriptionLabel }}</td>

                            <td style="padding:12px;border-bottom:1px solid #f3f4f6;">
                                {{ $association->subscription_end_date ? \Illuminate\Support\Carbon::parse($association->subscription_end_date)->format('Y-m-d') : '-' }}
                            </td>

                            <td style="padding:12px;border-bottom:1px solid #f3f4f6;">
                                @if(is_null($daysLeft))
                                    -
                                @elseif($daysLeft < 0)
                                    منتهي منذ {{ abs($daysLeft) }} يوم
                                @elseif($daysLeft === 0)
                                    ينتهي اليوم
                                @else
                                    باقي {{ $daysLeft }} يوم
                                @endif
                            </td>

                            <td style="padding:12px;border-bottom:1px solid #f3f4f6;">
                                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                    <a
                                        href="{{ \App\Filament\Resources\AssociationResource::getUrl('edit', ['record' => $association]) }}"
                                        style="padding:8px 12px;border-radius:10px;background:#f3f4f6;color:#111827;text-decoration:none;"
                                    >
                                        تعديل
                                    </a>

                                    @if($association->site_status === 'active')
                                        <button
                                            wire:click="stopAssociation({{ $association->id }})"
                                            style="padding:8px 12px;border-radius:10px;background:#fee2e2;color:#991b1b;border:none;cursor:pointer;"
                                        >
                                            إيقاف
                                        </button>
                                    @else
                                        <button
                                            wire:click="activateAssociation({{ $association->id }})"
                                            style="padding:8px 12px;border-radius:10px;background:#dcfce7;color:#166534;border:none;cursor:pointer;"
                                        >
                                            تفعيل
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:24px;text-align:center;color:#6b7280;">لا توجد جمعيات مطابقة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
