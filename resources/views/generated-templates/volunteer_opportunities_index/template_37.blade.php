@extends('themes.default.layouts.app')

@section('content')
@php
    $volunteerOpportunities = collect($volunteerOpportunities ?? ($items ?? []));

    $buttonColor = $siteSettings->button_color
        ?? $siteSettings->primary_color
        ?? '#127962';

    $secondaryColor = $siteSettings->secondary_color
        ?? '#10b981';

    $associationName = $siteSettings->association_name
        ?? $siteSettings->site_name
        ?? 'الجمعية';

    $typeLabels = [
        'social' => 'اجتماعي',
        'relief' => 'إغاثي',
        'medical' => 'طبي',
        'digital' => 'رقمي',
        'other' => 'أخرى',
    ];
@endphp

<style>
    .vol-wrap{
        max-width:1200px;
        margin:40px auto;
        padding:0 16px;
        direction:rtl;
        text-align:right;
        font-family:'Cairo', Tahoma, Arial, sans-serif;
        box-sizing:border-box;
    }

    .vol-wrap *{
        box-sizing:border-box;
    }

    .vol-hero{
        position:relative;
        overflow:hidden;
        border-radius:30px;
        padding:46px 30px;
        margin-bottom:28px;
        color:#fff;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.15), transparent 28%),
            radial-gradient(circle at bottom left, rgba(255,255,255,.08), transparent 24%),
            linear-gradient(135deg, {{ $buttonColor }} 0%, {{ $secondaryColor }} 100%);
        box-shadow:0 20px 42px rgba(15,23,42,.16);
    }

    .vol-hero::before,
    .vol-hero::after{
        content:'';
        position:absolute;
        border-radius:999px;
        background:rgba(255,255,255,.08);
        pointer-events:none;
    }

    .vol-hero::before{
        width:220px;
        height:220px;
        top:-70px;
        left:-60px;
    }

    .vol-hero::after{
        width:170px;
        height:170px;
        bottom:-45px;
        right:-35px;
    }

    .vol-hero-content{
        position:relative;
        z-index:2;
    }

    .vol-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        background:rgba(255,255,255,.14);
        border:1px solid rgba(255,255,255,.20);
        padding:10px 16px;
        border-radius:999px;
        font-size:14px;
        font-weight:700;
        margin-bottom:18px;
        backdrop-filter:blur(8px);
    }

    .vol-title{
        margin:0 0 10px;
        font-size:36px;
        font-weight:900;
        line-height:1.5;
    }

    .vol-subtitle{
        margin:0;
        max-width:780px;
        font-size:16px;
        line-height:2;
        opacity:.96;
    }

    .vol-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));
        gap:24px;
    }

    .vol-card{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:26px;
        overflow:hidden;
        box-shadow:0 14px 30px rgba(15,23,42,.06);
        transition:transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        display:flex;
        flex-direction:column;
        height:100%;
    }

    .vol-card:hover{
        transform:translateY(-5px);
        box-shadow:0 20px 38px rgba(15,23,42,.10);
        border-color:rgba(18,121,98,.24);
    }

    .vol-image{
        height:220px;
        background:linear-gradient(180deg,#f8fafc 0%, #eef2f7 100%);
        display:flex;
        align-items:center;
        justify-content:center;
        overflow:hidden;
    }

    .vol-image img{
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
    }

    .vol-image-placeholder{
        font-size:58px;
    }

    .vol-body{
        padding:24px;
        display:flex;
        flex-direction:column;
        gap:16px;
        flex:1;
    }

    .vol-top{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
    }

    .vol-card-title{
        margin:0;
        font-size:24px;
        font-weight:900;
        color:#111827;
        line-height:1.6;
    }

    .vol-type{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:8px 12px;
        border-radius:999px;
        background:#f0fdf4;
        color:#166534;
        font-size:13px;
        font-weight:800;
        white-space:nowrap;
    }

    .vol-meta-grid{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:12px;
    }

    .vol-meta-box{
        background:#f8fafc;
        border:1px solid #e5e7eb;
        border-radius:16px;
        padding:14px;
    }

    .vol-meta-label{
        display:block;
        margin-bottom:6px;
        color:#6b7280;
        font-size:13px;
        font-weight:700;
    }

    .vol-meta-value{
        color:#111827;
        font-size:15px;
        font-weight:800;
        line-height:1.8;
    }

    .vol-hours-box{
        background:#f8fafc;
        border:1px solid #e5e7eb;
        border-radius:16px;
        padding:14px;
    }

    .vol-desc{
        color:#4b5563;
        font-size:15px;
        line-height:2;
    }

    .vol-actions{
        display:flex;
        flex-wrap:wrap;
        gap:10px;
        margin-top:auto;
    }

    .vol-btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        text-decoration:none;
        padding:12px 16px;
        border-radius:12px;
        font-size:14px;
        font-weight:800;
        transition:.25s ease;
        border:1px solid transparent;
    }

    .vol-btn-outline{
        background:#fff;
        color:{{ $buttonColor }};
        border-color:{{ $buttonColor }};
    }

    .vol-btn-outline:hover{
        background:#f0fdf4;
    }

    .vol-btn-primary{
        background:{{ $buttonColor }};
        color:#fff;
        border-color:{{ $buttonColor }};
    }

    .vol-btn-primary:hover{
        filter:brightness(.96);
        color:#fff;
    }

    .vol-empty{
        grid-column:1 / -1;
        background:#fff;
        border:1px dashed #cbd5e1;
        border-radius:24px;
        padding:54px 20px;
        text-align:center;
        color:#6b7280;
        box-shadow:0 10px 24px rgba(15,23,42,.04);
    }

    .vol-empty-icon{
        width:82px;
        height:82px;
        margin:0 auto 18px;
        border-radius:24px;
        background:#f8fafc;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:32px;
        color:#94a3b8;
    }

    .vol-empty-title{
        margin:0 0 8px;
        font-size:24px;
        font-weight:900;
        color:#111827;
    }

    .vol-empty-text{
        margin:0;
        font-size:15px;
        line-height:1.9;
    }

    @media (max-width:768px){
        .vol-hero{
            padding:30px 20px;
            border-radius:24px;
        }

        .vol-title{
            font-size:28px;
        }

        .vol-subtitle{
            font-size:15px;
        }

        .vol-body{
            padding:18px;
        }

        .vol-top{
            flex-direction:column;
        }

        .vol-actions{
            flex-direction:column;
        }

        .vol-btn{
            width:100%;
        }
    }
</style>

<div class="vol-wrap">
    <div class="vol-hero">
        <div class="vol-hero-content">
            <div class="vol-badge">
                <i class="fas fa-hand-holding-heart"></i>
                فرص تطوعية
            </div>

            <h1 class="vol-title">
                {{ $page->title ?? 'قائمة فرص التطوع' }}
            </h1>

            <p class="vol-subtitle">
                {{ $page->excerpt ?? ('استعرض الفرص التطوعية المتاحة في ' . $associationName . '، وتعرّف على تفاصيل كل فرصة ثم انتقل مباشرة إلى منصة تطوع.') }}
            </p>
        </div>
    </div>

    <div class="vol-grid">
        @forelse($volunteerOpportunities as $volunteer_opportunity)
            @php
                $detailsUrl = !empty($page?->slug)
                    ? url('/page/' . $page->slug . '/volunteer/' . $volunteer_opportunity->slug)
                    : route('volunteer-opportunities.show', ['slug' => $volunteer_opportunity->slug]);

                $imageUrl =
                    $volunteer_opportunity->image_url
                    ?? ($volunteer_opportunity->imageMedia->url ?? null)
                    ?? (
                        !empty($volunteer_opportunity->image)
                            ? (
                                \Illuminate\Support\Str::startsWith($volunteer_opportunity->image, ['http://', 'https://'])
                                    ? $volunteer_opportunity->image
                                    : \App\Support\Media\MediaUrl::forDiskPath('public', ltrim($volunteer_opportunity->image, '/'))
                            )
                            : null
                    );
            @endphp

            <div class="vol-card">
                <div class="vol-image">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $volunteer_opportunity->title }}">
                    @else
                        <div class="vol-image-placeholder">🤝</div>
                    @endif
                </div>

                <div class="vol-body">
                    <div class="vol-top">
                        <h3 class="vol-card-title">{{ $volunteer_opportunity->title }}</h3>

                        <span class="vol-type">
                            {{ $typeLabels[$volunteer_opportunity->opportunity_type] ?? 'أخرى' }}
                        </span>
                    </div>

                    <div class="vol-meta-grid">
                        <div class="vol-meta-box">
                            <span class="vol-meta-label">بداية التطوع</span>
                            <div class="vol-meta-value">
                                {{ optional($volunteer_opportunity->start_date)->format('Y-m-d') }}
                            </div>
                        </div>

                        <div class="vol-meta-box">
                            <span class="vol-meta-label">نهاية الفرصة</span>
                            <div class="vol-meta-value">
                                {{ optional($volunteer_opportunity->end_date)->format('Y-m-d') }}
                            </div>
                        </div>
                    </div>

                    <div class="vol-hours-box">
                        <span class="vol-meta-label">عدد ساعات الفرصة</span>
                        <div class="vol-meta-value">
                            {{ $volunteer_opportunity->hours_count }} ساعة
                        </div>
                    </div>

                    <div class="vol-desc">
                        {!! \Illuminate\Support\Str::limit(strip_tags($volunteer_opportunity->description), 220) !!}
                    </div>

                    <div class="vol-actions">
                        <a href="{{ $detailsUrl }}" class="vol-btn vol-btn-outline">
                            <i class="fas fa-circle-info"></i>
                            تفاصيل الفرصة
                        </a>

                        <a href="{{ $volunteer_opportunity->platform_url }}" target="_blank" class="vol-btn vol-btn-primary">
                            <i class="fas fa-arrow-up-right-from-square"></i>
                            التقديم في منصة تطوع
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="vol-empty">
                <div class="vol-empty-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h3 class="vol-empty-title">لا توجد فرص تطوعية حالياً</h3>
                <p class="vol-empty-text">
                    لم تتم إضافة أي فرص تطوعية حتى الآن.
                </p>
            </div>
        @endforelse
    </div>
</div>
@endsection