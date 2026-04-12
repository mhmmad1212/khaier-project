@extends('themes.default.layouts.app')

@section('content')
@php
    $volunteerOpportunities = collect($volunteerOpportunities ?? ($items ?? []));
    $buttonColor = $siteSettings->button_color ?? $siteSettings->primary_color ?? '#127962';
    $secondaryColor = $siteSettings->secondary_color ?? '#10b981';

    $typeLabels = [
        'social' => 'اجتماعي',
        'relief' => 'إغاثي',
        'medical' => 'طبي',
        'digital' => 'رقمي',
        'other' => 'أخرى',
    ];
@endphp

<div style="max-width:1200px; margin:40px auto; padding:0 16px; direction:rtl; text-align:right;">
    <div style="background:linear-gradient(135deg, {{ $buttonColor }} 0%, {{ $secondaryColor }} 100%); border-radius:28px; padding:40px 26px; color:#fff; margin-bottom:28px; box-shadow:0 20px 40px rgba(15,23,42,.14);">
        <h1 style="margin:0 0 10px; font-size:34px; font-weight:900;">
            {{ $page->title ?? 'فرص التطوع' }}
        </h1>

        <div style="font-size:16px; line-height:2; opacity:.95; max-width:760px;">
            {{ $page->excerpt ?? 'استعرض فرص التطوع المتاحة واطلع على التفاصيل ثم انتقل مباشرة إلى منصة تطوع.' }}
        </div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:24px;">
        @forelse($volunteerOpportunities as $volunteerOpportunity)
            @php
                $detailsUrl = !empty($page?->slug)
                    ? url('/page/' . $page->slug . '/volunteer/' . $volunteerOpportunity->slug)
                    : route('volunteer-opportunities.show', ['slug' => $volunteerOpportunity->slug]);

                $imageUrl = $volunteerOpportunity->imageMedia->url
                    ?? (
                        !empty($volunteerOpportunity->image)
                            ? (
                                \Illuminate\Support\Str::startsWith($volunteerOpportunity->image, ['http://', 'https://'])
                                    ? $volunteerOpportunity->image
                                    : \App\Support\Media\MediaUrl::forDiskPath('public', ltrim($volunteerOpportunity->image, '/'))
                            )
                            : null
                    );
            @endphp

            <div style="background:#fff; border:1px solid #e5e7eb; border-radius:24px; overflow:hidden; box-shadow:0 14px 28px rgba(15,23,42,.06); display:flex; flex-direction:column;">
                <div style="height:220px; background:#f8fafc; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $volunteerOpportunity->title }}" style="width:100%; height:100%; object-fit:cover; display:block;">
                    @else
                        <div style="font-size:54px;">🤝</div>
                    @endif
                </div>

                <div style="padding:24px; display:flex; flex-direction:column; gap:16px; flex:1;">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                        <h3 style="margin:0; font-size:24px; font-weight:900; color:#111827; line-height:1.6;">
                            {{ $volunteerOpportunity->title }}
                        </h3>

                        <span style="display:inline-flex; align-items:center; justify-content:center; padding:8px 12px; border-radius:999px; background:#f0fdf4; color:#166534; font-size:13px; font-weight:800; white-space:nowrap;">
                            {{ $typeLabels[$volunteerOpportunity->opportunity_type] ?? 'أخرى' }}
                        </span>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:16px; padding:14px;">
                            <div style="font-size:13px; color:#6b7280; font-weight:700; margin-bottom:6px;">بداية التطوع</div>
                            <div style="font-size:15px; color:#111827; font-weight:800;">
                                {{ optional($volunteerOpportunity->start_date)->format('Y-m-d') }}
                            </div>
                        </div>

                        <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:16px; padding:14px;">
                            <div style="font-size:13px; color:#6b7280; font-weight:700; margin-bottom:6px;">نهاية الفرصة</div>
                            <div style="font-size:15px; color:#111827; font-weight:800;">
                                {{ optional($volunteerOpportunity->end_date)->format('Y-m-d') }}
                            </div>
                        </div>
                    </div>

                    <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:16px; padding:14px;">
                        <div style="font-size:13px; color:#6b7280; font-weight:700; margin-bottom:6px;">عدد الساعات</div>
                        <div style="font-size:16px; color:#111827; font-weight:800;">
                            {{ $volunteerOpportunity->hours_count }} ساعة
                        </div>
                    </div>

                    <div style="color:#4b5563; line-height:2; font-size:15px;">
                        {!! \Illuminate\Support\Str::limit(strip_tags($volunteerOpportunity->description), 220) !!}
                    </div>

                    <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:auto;">
                        <a href="{{ $detailsUrl }}" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:12px 16px; border-radius:12px; border:1px solid {{ $buttonColor }}; color:{{ $buttonColor }}; background:#fff; text-decoration:none; font-weight:800;">
                            <i class="fas fa-circle-info"></i>
                            تفاصيل الفرصة
                        </a>

                        <a href="{{ $volunteerOpportunity->platform_url }}" target="_blank" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:12px 16px; border-radius:12px; border:1px solid {{ $buttonColor }}; color:#fff; background:{{ $buttonColor }}; text-decoration:none; font-weight:800;">
                            <i class="fas fa-arrow-up-right-from-square"></i>
                            التقديم في منصة تطوع
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1; background:#fff; border:1px dashed #cbd5e1; border-radius:24px; padding:50px 20px; text-align:center; color:#6b7280;">
                لا توجد فرص تطوع مضافة حاليًا.
            </div>
        @endforelse
    </div>
</div>
@endsection