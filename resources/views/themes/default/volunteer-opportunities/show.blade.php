@extends('themes.default.layouts.app')

@section('content')
@php
    $volunteerOpportunity = $volunteerOpportunity ?? ($item ?? null);
    $buttonColor = $siteSettings->button_color ?? $siteSettings->primary_color ?? '#127962';
    $secondaryColor = $siteSettings->secondary_color ?? '#10b981';

    $typeLabels = [
        'social' => 'اجتماعي',
        'relief' => 'إغاثي',
        'medical' => 'طبي',
        'digital' => 'رقمي',
        'other' => 'أخرى',
    ];

    $backUrl = !empty($page?->slug) ? url('/page/' . $page->slug) : url()->previous();

    $imageUrl = $volunteerOpportunity?->imageMedia?->url
        ?? (
            !empty($volunteerOpportunity?->image)
                ? (
                    \Illuminate\Support\Str::startsWith($volunteerOpportunity->image, ['http://', 'https://'])
                        ? $volunteerOpportunity->image
                        : \App\Support\Media\MediaUrl::forDiskPath('public', ltrim($volunteerOpportunity->image, '/'))
                )
                : null
        );
@endphp

<div style="max-width:1100px; margin:40px auto; padding:0 16px; direction:rtl; text-align:right;">
    @if($volunteerOpportunity)
        <div style="background:linear-gradient(135deg, {{ $buttonColor }} 0%, {{ $secondaryColor }} 100%); border-radius:28px; padding:38px 26px; color:#fff; margin-bottom:24px; box-shadow:0 18px 36px rgba(15,23,42,.14);">
            <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center; justify-content:space-between;">
                <div>
                    <span style="display:inline-flex; padding:8px 12px; border-radius:999px; background:rgba(255,255,255,.16); font-size:13px; font-weight:800; margin-bottom:12px;">
                        {{ $typeLabels[$volunteerOpportunity->opportunity_type] ?? 'أخرى' }}
                    </span>

                    <h1 style="margin:0 0 10px; font-size:34px; font-weight:900; line-height:1.6;">
                        {{ $volunteerOpportunity->title }}
                    </h1>

                    <div style="font-size:16px; opacity:.95; line-height:2;">
                        تفاصيل فرصة التطوع وطريقة التقديم عبر منصة تطوع.
                    </div>
                </div>

                <a href="{{ $volunteerOpportunity->platform_url }}" target="_blank" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:14px 18px; border-radius:14px; background:#fff; color:{{ $buttonColor }}; text-decoration:none; font-weight:800;">
                    <i class="fas fa-arrow-up-right-from-square"></i>
                    التقديم في منصة تطوع
                </a>
            </div>
        </div>

        @if($imageUrl)
            <div style="background:#fff; border:1px solid #e5e7eb; border-radius:24px; overflow:hidden; margin-bottom:24px; box-shadow:0 14px 28px rgba(15,23,42,.06);">
                <img src="{{ $imageUrl }}" alt="{{ $volunteerOpportunity->title }}" style="width:100%; max-height:420px; object-fit:cover; display:block;">
            </div>
        @endif

        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:24px; padding:26px; box-shadow:0 14px 28px rgba(15,23,42,.06);">
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:24px;">
                <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:16px; padding:16px;">
                    <div style="font-size:13px; color:#6b7280; font-weight:700; margin-bottom:6px;">نوع الفرصة</div>
                    <div style="font-size:16px; color:#111827; font-weight:800;">
                        {{ $typeLabels[$volunteerOpportunity->opportunity_type] ?? 'أخرى' }}
                    </div>
                </div>

                <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:16px; padding:16px;">
                    <div style="font-size:13px; color:#6b7280; font-weight:700; margin-bottom:6px;">بداية التطوع</div>
                    <div style="font-size:16px; color:#111827; font-weight:800;">
                        {{ optional($volunteerOpportunity->start_date)->format('Y-m-d') }}
                    </div>
                </div>

                <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:16px; padding:16px;">
                    <div style="font-size:13px; color:#6b7280; font-weight:700; margin-bottom:6px;">نهاية الفرصة</div>
                    <div style="font-size:16px; color:#111827; font-weight:800;">
                        {{ optional($volunteerOpportunity->end_date)->format('Y-m-d') }}
                    </div>
                </div>

                <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:16px; padding:16px;">
                    <div style="font-size:13px; color:#6b7280; font-weight:700; margin-bottom:6px;">عدد الساعات</div>
                    <div style="font-size:16px; color:#111827; font-weight:800;">
                        {{ $volunteerOpportunity->hours_count }} ساعة
                    </div>
                </div>
            </div>

            <div style="background:#fff; border:1px solid #edf2f7; border-radius:20px; padding:22px; margin-bottom:20px;">
                <h3 style="margin:0 0 12px; font-size:22px; font-weight:900; color:#111827;">وصف الفرصة</h3>

                <div style="color:#374151; font-size:16px; line-height:2.05;">
                    {!! $volunteerOpportunity->description !!}
                </div>
            </div>

            <div style="display:flex; flex-wrap:wrap; gap:10px;">
                <a href="{{ $volunteerOpportunity->platform_url }}" target="_blank" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:13px 18px; border-radius:14px; background:{{ $buttonColor }}; color:#fff; text-decoration:none; font-weight:800;">
                    <i class="fas fa-arrow-up-right-from-square"></i>
                    التقديم في منصة تطوع
                </a>

                <a href="{{ $backUrl }}" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:13px 18px; border-radius:14px; background:#fff; border:1px solid {{ $buttonColor }}; color:{{ $buttonColor }}; text-decoration:none; font-weight:800;">
                    <i class="fas fa-arrow-right"></i>
                    الرجوع
                </a>
            </div>
        </div>
    @else
        <div style="background:#fff; border:1px dashed #cbd5e1; border-radius:24px; padding:50px 20px; text-align:center; color:#6b7280;">
            لا توجد بيانات لهذه الفرصة.
        </div>
    @endif
</div>
@endsection
