<x-filament-panels::page>
    <div style="display:flex;flex-direction:column;gap:24px;">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;">

            <div style="position:relative;border-radius:18px;padding:20px;color:#fff;background:linear-gradient(135deg,#1f2937,#111827);box-shadow:0 10px 25px rgba(0,0,0,.10);overflow:hidden;">
                <svg style="position:absolute;left:-12px;bottom:-12px;width:110px;height:110px;opacity:.10;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
                </svg>
                <div style="position:relative;">
                    <div style="font-size:14px;opacity:.9;">إجمالي الجمعيات</div>
                    <div style="margin-top:10px;font-size:32px;font-weight:800;">{{ $this->stats['total_associations'] ?? 0 }}</div>
                </div>
            </div>

            <div style="position:relative;border-radius:18px;padding:20px;color:#fff;background:linear-gradient(135deg,#10b981,#059669);box-shadow:0 10px 25px rgba(16,185,129,.18);overflow:hidden;">
                <svg style="position:absolute;left:-12px;bottom:-12px;width:110px;height:110px;opacity:.10;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 16.2l-3.5-3.5L4 14.2 9 19l12-12-1.5-1.5z"/>
                </svg>
                <div style="position:relative;">
                    <div style="font-size:14px;opacity:.95;">الجمعيات النشطة</div>
                    <div style="margin-top:10px;font-size:32px;font-weight:800;">{{ $this->stats['active_associations'] ?? 0 }}</div>
                </div>
            </div>

            <div style="position:relative;border-radius:18px;padding:20px;color:#fff;background:linear-gradient(135deg,#f43f5e,#e11d48);box-shadow:0 10px 25px rgba(244,63,94,.18);overflow:hidden;">
                <svg style="position:absolute;left:-12px;bottom:-12px;width:110px;height:110px;opacity:.10;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9m-6 13a2.5 2.5 0 002.45-2h-4.9A2.5 2.5 0 0012 21z"/>
                </svg>
                <div style="position:relative;">
                    <div style="font-size:14px;opacity:.95;">الجمعيات الموقوفة</div>
                    <div style="margin-top:10px;font-size:32px;font-weight:800;">{{ $this->stats['inactive_associations'] ?? 0 }}</div>
                </div>
            </div>

            <div style="position:relative;border-radius:18px;padding:20px;color:#fff;background:linear-gradient(135deg,#b91c1c,#7f1d1d);box-shadow:0 10px 25px rgba(185,28,28,.18);overflow:hidden;">
                <svg style="position:absolute;left:-12px;bottom:-12px;width:110px;height:110px;opacity:.10;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm1 13h-2v2h2v-2zm0-8h-2v6h2V7z"/>
                </svg>
                <div style="position:relative;">
                    <div style="font-size:14px;opacity:.95;">الجمعيات المنتهية</div>
                    <div style="margin-top:10px;font-size:32px;font-weight:800;">{{ $this->stats['expired_associations'] ?? 0 }}</div>
                </div>
            </div>

            <div style="position:relative;border-radius:18px;padding:20px;color:#fff;background:linear-gradient(135deg,#f59e0b,#d97706);box-shadow:0 10px 25px rgba(245,158,11,.18);overflow:hidden;">
                <svg style="position:absolute;left:-12px;bottom:-12px;width:110px;height:110px;opacity:.10;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8v5l3 3 1.4-1.4-2.4-2.6V8H12zm0-6a10 10 0 100 20 10 10 0 000-20z"/>
                </svg>
                <div style="position:relative;">
                    <div style="font-size:14px;opacity:.95;">تنتهي خلال 60 يوم</div>
                    <div style="margin-top:10px;font-size:32px;font-weight:800;">{{ $this->stats['expiring_60_days'] ?? 0 }}</div>
                </div>
            </div>

            <div style="position:relative;border-radius:18px;padding:20px;color:#fff;background:linear-gradient(135deg,#f97316,#ea580c);box-shadow:0 10px 25px rgba(249,115,22,.18);overflow:hidden;">
                <svg style="position:absolute;left:-12px;bottom:-12px;width:110px;height:110px;opacity:.10;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8v5l3 3 1.4-1.4-2.4-2.6V8H12zm0-6a10 10 0 100 20 10 10 0 000-20z"/>
                </svg>
                <div style="position:relative;">
                    <div style="font-size:14px;opacity:.95;">تنتهي خلال 30 يوم</div>
                    <div style="margin-top:10px;font-size:32px;font-weight:800;">{{ $this->stats['expiring_30_days'] ?? 0 }}</div>
                </div>
            </div>

            <div style="position:relative;border-radius:18px;padding:20px;color:#fff;background:linear-gradient(135deg,#0ea5e9,#0284c7);box-shadow:0 10px 25px rgba(14,165,233,.18);overflow:hidden;">
                <svg style="position:absolute;left:-12px;bottom:-12px;width:110px;height:110px;opacity:.10;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.98 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
                <div style="position:relative;">
                    <div style="font-size:14px;opacity:.95;">إجمالي المستخدمين</div>
                    <div style="margin-top:10px;font-size:32px;font-weight:800;">{{ $this->stats['total_users'] ?? 0 }}</div>
                </div>
            </div>

            <div style="position:relative;border-radius:18px;padding:20px;color:#fff;background:linear-gradient(135deg,#6366f1,#4f46e5);box-shadow:0 10px 25px rgba(99,102,241,.18);overflow:hidden;">
                <svg style="position:absolute;left:-12px;bottom:-12px;width:110px;height:110px;opacity:.10;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 17.9V18h-2v1.9A8.001 8.001 0 014.1 13H6v-2H4.1A8.001 8.001 0 0111 4.1V6h2V4.1A8.001 8.001 0 0119.9 11H18v2h1.9A8.001 8.001 0 0113 19.9z"/>
                </svg>
                <div style="position:relative;">
                    <div style="font-size:14px;opacity:.95;">الدومينات المخصصة</div>
                    <div style="margin-top:10px;font-size:32px;font-weight:800;">{{ $this->stats['custom_domains'] ?? 0 }}</div>
                </div>
            </div>

            <div style="position:relative;border-radius:18px;padding:20px;color:#fff;background:linear-gradient(135deg,#8b5cf6,#7c3aed);box-shadow:0 10px 25px rgba(139,92,246,.18);overflow:hidden;">
                <svg style="position:absolute;left:-12px;bottom:-12px;width:110px;height:110px;opacity:.10;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3.9 12a5 5 0 010-7.1l1-1a5 5 0 017.1 7.1l-.7.7-1.4-1.4.7-.7a3 3 0 10-4.2-4.2l-1 1a3 3 0 004.2 4.2l.7-.7 1.4 1.4-.7.7A5 5 0 013.9 12zm16.2 0a5 5 0 01-7.1 7.1l-1-1a5 5 0 117.1-7.1l.7.7-1.4 1.4-.7-.7a3 3 0 10-4.2 4.2l1 1a3 3 0 104.2-4.2l-.7-.7 1.4-1.4.7.7z"/>
                </svg>
                <div style="position:relative;">
                    <div style="font-size:14px;opacity:.95;">Subdomains</div>
                    <div style="margin-top:10px;font-size:32px;font-weight:800;">{{ $this->stats['subdomains'] ?? 0 }}</div>
                </div>
            </div>

            <div style="position:relative;border-radius:18px;padding:20px;color:#fff;background:linear-gradient(135deg,#14b8a6,#0f766e);box-shadow:0 10px 25px rgba(20,184,166,.18);overflow:hidden;">
                <svg style="position:absolute;left:-12px;bottom:-12px;width:110px;height:110px;opacity:.10;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9 14H7v-3h3v3zm0-4H7V7h3v6zm7 4h-5v-3h5v3zm0-4h-5V7h5v6z"/>
                </svg>
                <div style="position:relative;">
                    <div style="font-size:14px;opacity:.95;">إجمالي القوالب</div>
                    <div style="margin-top:10px;font-size:32px;font-weight:800;">{{ $this->stats['total_templates'] ?? 0 }}</div>
                </div>
            </div>

        </div>

        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:20px;box-shadow:0 4px 14px rgba(0,0,0,.05);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <h2 style="margin:0;font-size:20px;font-weight:800;color:#111827;">أقرب 10 جمعيات انتهاءً</h2>
            </div>

            <div style="overflow-x:auto;">
                <table style="width:100%;min-width:900px;border-collapse:collapse;font-size:14px;">
                    <thead style="background:#f9fafb;">
                        <tr>
                            <th style="padding:12px;text-align:right;border-bottom:1px solid #e5e7eb;">اسم الجمعية</th>
                            <th style="padding:12px;text-align:right;border-bottom:1px solid #e5e7eb;">الدومين</th>
                            <th style="padding:12px;text-align:right;border-bottom:1px solid #e5e7eb;">نوع الربط</th>
                            <th style="padding:12px;text-align:right;border-bottom:1px solid #e5e7eb;">حالة الاشتراك</th>
                            <th style="padding:12px;text-align:right;border-bottom:1px solid #e5e7eb;">نشط</th>
                            <th style="padding:12px;text-align:right;border-bottom:1px solid #e5e7eb;">تاريخ الانتهاء</th>
                            <th style="padding:12px;text-align:right;border-bottom:1px solid #e5e7eb;">الأيام المتبقية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->expiringAssociations as $association)
                            <tr>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#111827;">{{ $association['name'] }}</td>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;color:#374151;">{{ $association['domain'] ?: '-' }}</td>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;color:#374151;">{{ $association['domain_type'] ?: '-' }}</td>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;color:#374151;">{{ $association['subscription_status'] ?: '-' }}</td>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">
                                    @if($association['is_active'])
                                        <span style="display:inline-block;background:#ecfdf5;color:#047857;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;">نعم</span>
                                    @else
                                        <span style="display:inline-block;background:#fff1f2;color:#be123c;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;">لا</span>
                                    @endif
                                </td>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;color:#374151;">{{ $association['subscription_end_date'] ?: '-' }}</td>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">
                                    @php $days = $association['days_left']; @endphp
                                    @if(is_null($days))
                                        <span style="color:#6b7280;">-</span>
                                    @elseif($days < 0)
                                        <span style="font-weight:700;color:#b91c1c;">منتهية منذ {{ abs($days) }} يوم</span>
                                    @elseif($days === 0)
                                        <span style="font-weight:700;color:#c2410c;">اليوم</span>
                                    @else
                                        <span style="font-weight:700;color:#b45309;">{{ $days }} يوم</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding:24px;text-align:center;color:#6b7280;">لا توجد بيانات حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
