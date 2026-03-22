@php
    $statePath = $getStatePath();
    $current = $getState();

    $icons = [
        'heroicon-o-home' => 'الرئيسية',
        'heroicon-o-building-office' => 'مبنى',
        'heroicon-o-users' => 'مستخدمون',
        'heroicon-o-user-group' => 'مجموعة',
        'heroicon-o-user' => 'مستخدم',
        'heroicon-o-briefcase' => 'حقيبة',
        'heroicon-o-chart-bar' => 'إحصائيات',
        'heroicon-o-presentation-chart-bar' => 'عرض',
        'heroicon-o-document-text' => 'مستند',
        'heroicon-o-newspaper' => 'أخبار',
        'heroicon-o-photo' => 'صورة',
        'heroicon-o-camera' => 'كاميرا',
        'heroicon-o-envelope' => 'بريد',
        'heroicon-o-phone' => 'هاتف',
        'heroicon-o-map-pin' => 'موقع',
        'heroicon-o-globe-alt' => 'عالم',
        'heroicon-o-link' => 'رابط',
        'heroicon-o-hand-raised' => 'شراكة',
        'heroicon-o-heart' => 'قلب',
        'heroicon-o-star' => 'نجمة',
        'heroicon-o-trophy' => 'كأس',
        'heroicon-o-academic-cap' => 'تعليم',
        'heroicon-o-banknotes' => 'أموال',
        'heroicon-o-currency-dollar' => 'عملة',
        'heroicon-o-calendar-days' => 'تقويم',
        'heroicon-o-clock' => 'وقت',
        'heroicon-o-cog-6-tooth' => 'إعدادات',
        'heroicon-o-bars-3-bottom-right' => 'قائمة',
        'heroicon-o-rectangle-stack' => 'طبقات',
        'heroicon-o-folder' => 'مجلد',
        'heroicon-o-folder-open' => 'مجلد مفتوح',
        'heroicon-o-tag' => 'وسم',
        'heroicon-o-megaphone' => 'إعلان',
        'heroicon-o-bell' => 'تنبيه',
        'heroicon-o-check-circle' => 'نجاح',
        'heroicon-o-x-circle' => 'إلغاء',
        'heroicon-o-information-circle' => 'معلومة',
        'heroicon-o-exclamation-circle' => 'تحذير',
        'heroicon-o-shield-check' => 'حماية',
        'heroicon-o-scale' => 'ميزان',
        'heroicon-o-clipboard-document-list' => 'قائمة',
        'heroicon-o-document-chart-bar' => 'تقارير',
        'heroicon-o-building-library' => 'مكتبة',
        'heroicon-o-squares-2x2' => 'مربعات',
    ];
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{ open: false, search: '' }" style="display:flex;flex-direction:column;gap:12px;">
        <input type="hidden" wire:model="{{ $statePath }}">

        <button
            type="button"
            x-on:click="open = true"
            style="width:100%;display:flex;align-items:center;justify-content:space-between;border:1px solid #d1d5db;background:#fff;padding:12px 16px;border-radius:12px;text-align:right;box-shadow:0 1px 2px rgba(0,0,0,.05);"
        >
            <span style="display:flex;align-items:center;gap:12px;">
                @if($current)
                    <x-filament::icon :icon="$current" class="h-5 w-5" />
                    <span style="font-size:14px;font-weight:600;color:#1f2937;">{{ $icons[$current] ?? $current }}</span>
                @else
                    <span style="font-size:14px;color:#6b7280;">اختيار الأيقونة</span>
                @endif
            </span>

            <span style="font-size:12px;color:#9ca3af;">اضغط للاختيار</span>
        </button>

        @if($current)
            <div style="text-align:left;">
                <button
                    type="button"
                    wire:click="$set('{{ $statePath }}', null)"
                    style="border:1px solid #fecaca;background:#fef2f2;color:#dc2626;padding:6px 12px;border-radius:8px;font-size:12px;font-weight:600;"
                >
                    إزالة الأيقونة
                </button>
            </div>
        @endif

        <div
            x-show="open"
            x-transition.opacity
            x-cloak
            style="position:fixed;inset:0;z-index:100;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5);padding:16px;"
        >
            <div
                x-show="open"
                x-transition
                x-on:click.away="open = false"
                style="width:100%;max-width:900px;background:#fff;border-radius:18px;box-shadow:0 25px 50px rgba(0,0,0,.25);overflow:hidden;"
            >
                <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e5e7eb;padding:16px 20px;">
                    <div>
                        <h3 style="margin:0;font-size:16px;font-weight:700;color:#111827;">اختيار الأيقونة</h3>
                        <p style="margin:4px 0 0;font-size:12px;color:#6b7280;">
                            @if($current)
                                المحدد الآن: {{ $icons[$current] ?? $current }}
                            @else
                                اختر الأيقونة المناسبة
                            @endif
                        </p>
                    </div>

                    <button
                        type="button"
                        x-on:click="open = false"
                        style="padding:8px 12px;border-radius:8px;font-size:13px;color:#6b7280;background:#f9fafb;border:1px solid #e5e7eb;"
                    >
                        إغلاق
                    </button>
                </div>

                <div style="padding:20px;">
                    <div style="margin-bottom:16px;">
                        <input
                            type="text"
                            x-model="search"
                            placeholder="ابحث عن أيقونة..."
                            style="width:100%;border:1px solid #d1d5db;border-radius:12px;padding:10px 12px;font-size:14px;"
                        >
                    </div>

                    <div
                        style="
                            display:grid;
                            grid-template-columns:repeat(5, minmax(0, 1fr));
                            gap:10px;
                            max-height:60vh;
                            overflow-y:auto;
                        "
                    >
                        @foreach($icons as $iconKey => $iconLabel)
                            <button
                                type="button"
                                wire:click="$set('{{ $statePath }}', '{{ $iconKey }}')"
                                x-on:click="open = false"
                                x-show="'{{ mb_strtolower($iconKey . ' ' . $iconLabel) }}'.includes(search.toLowerCase())"
                                title="{{ $iconLabel }}"
                                style="
                                    height:56px;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    border-radius:10px;
                                    border:1px solid {{ $current === $iconKey ? '#2563eb' : '#d1d5db' }};
                                    background: {{ $current === $iconKey ? '#eff6ff' : '#f9fafb' }};
                                    color: {{ $current === $iconKey ? '#1d4ed8' : '#374151' }};
                                    cursor:pointer;
                                "
                            >
                                <x-filament::icon :icon="$iconKey" class="h-5 w-5" />
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
