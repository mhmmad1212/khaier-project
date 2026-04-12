@extends('themes.default.layouts.app')

@section('content')
@php
    $volunteerOpportunity = $volunteerOpportunity ?? ($item ?? null);

    $buttonColor = $siteSettings->button_color
        ?? $siteSettings->primary_color
        ?? '#127962';

    $secondaryColor = $siteSettings->secondary_color
        ?? '#10b981';

    $typeLabels = [
        'social' => 'اجتماعي',
        'relief' => 'إغاثي',
        'medical' => 'طبي',
        'digital' => 'رقمي',
        'other' => 'أخرى',
    ];

    $imageUrl =
        $volunteerOpportunity?->image_url
        ?? ($volunteerOpportunity?->imageMedia?->url ?? null)
        ?? (
            !empty($volunteerOpportunity?->image)
                ? (
                    \Illuminate\Support\Str::startsWith($volunteerOpportunity->image, ['http://', 'https://'])
                        ? $volunteerOpportunity->image
                        : \App\Support\Media\MediaUrl::forDiskPath('public', ltrim($volunteerOpportunity->image, '/'))
                )
                : null
        );

    $backUrl = !empty($page?->slug)
        ? url('/page/' . $page->slug)
        : url()->previous();
@endphp

<style>
    .vol-show-wrap{
        max-width:1100px;
        margin:40px auto;
        padding:0 16px;
        direction:rtl;
        text-align:right;
        font-family:'Cairo', Tahoma, Arial, sans-serif;
        box-sizing:border-box;
    }

    .vol-show-wrap *{
        box-sizing:border-box;
    }

    .vol-show-hero{
        position:relative;
        overflow:hidden;
        border-radius:30px;
        padding:40px 28px;
        margin-bottom:24px;
        color:#fff;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.16), transparent 28%),
            radial-gradient(circle at bottom left, rgba(255,255,255,.08), transparent 24%),
            linear-gradient(135deg, {{ $buttonColor }} 0%, {{ $secondaryColor }} 100%);
        box-shadow:0 20px 40px rgba(15,23,42,.15);
    }

    .vol-show-hero::before,
    .vol-show-hero::after{
        content:'';
        position:absolute;
        border-radius:999px;
        background:rgba(255,255,255,.08);
        pointer-events:none;
    }

    .vol-show-hero::before{
        width:220px;
        height:220px;
        top:-80px;
        left:-60px;
    }

    .vol-show-hero::after{
        width:170px;
        height:170px;
        bottom:-50px;
        right:-40px;
    }

    .vol-show-hero-content{
        position:relative;
        z-index:2;
        display:flex;
        flex-wrap:wrap;
        align-items:center;
        justify-content:space-between;
        gap:16px;
    }

    .vol-show-type{
        display:inline-flex;
        padding:8px 12px;
        border-radius:999px;
        background:rgba(255,255,255,.16);
        font-size:13px;
        font-weight:800;
        margin-bottom:12px;
    }

    .vol-show-title{
        margin:0 0 10px;
        font-size:36px;
        font-weight:900;
        line-height:1.6;
    }

    .vol-show-subtitle{
        margin:0;
        font-size:16px;
        line-height:2;
        opacity:.96;
    }

    .vol-show-apply{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        padding:14px 18px;
        border-radius:14px;
        background:#fff;
        color:{{ $buttonColor }};
        text-decoration:none;
        font-weight:800;
        min-width:220px;
    }

    .vol-show-image-card{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:26px;
        overflow:hidden;
        margin-bottom:24px;
        box-shadow:0 14px 28px rgba(15,23,42,.06);
    }

    .vol-show-image-card img{
        width:100%;
        max-height:440px;
        object-fit:cover;
        display:block;
    }

    .vol-show-main{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:26px;
        padding:26px;
        box-shadow:0 14px 28px rgba(15,23,42,.06);
    }

    .vol-show-meta-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
        gap:16px;
        margin-bottom:24px;
    }

    .vol-show-meta-box{
        background:#f8fafc;
        border:1px solid #e5e7eb;
        border-radius:16px;
        padding:16px;
    }

    .vol-show-label{
        display:block;
        margin-bottom:6px;
        font-size:13px;
        color:#6b7280;
        font-weight:700;
    }

    .vol-show-value{
        font-size:16px;
        color:#111827;
        font-weight:800;
        line-height:1.8;
    }

    .vol-show-desc-box{
        background:#fff;
        border:1px solid #edf2f7;
        border-radius:20px;
        padding:22px;
        margin-bottom:20px;
    }

    .vol-show-section-title{
        margin:0 0 12px;
        font-size:22px;
        font-weight:900;
        color:#111827;
    }

    .vol-show-desc{
        color:#374151;
        font-size:16px;
        line-height:2.05;
    }

    .vol-show-desc p:last-child{
        margin-bottom:0;
    }

    .vol-show-actions{
        display:flex;
        flex-wrap:wrap;
        gap:10px;
    }

    .vol-show-btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        padding:13px 18px;
        border-radius:14px;
        text-decoration:none;
        font-weight:800;
        font-size:14px;
        transition:.25s ease;
        border:1px solid transparent;
    }

    .vol-show-btn-primary{
        background:{{ $buttonColor }};
        color:#fff;
        border-color:{{ $buttonColor }};
    }

    .vol-show-btn-primary:hover{
        filter:brightness(.96);
        color:#fff;
    }

    .vol-show-btn-outline{
        background:#fff;
        color:{{ $buttonColor }};
        border-color:{{ $buttonColor }};
    }

    .vol-show-btn-outline:hover{
        background:#f0fdf4;
    }

    .vol-show-empty{
        background:#fff;
        border:1px dashed #cbd5e1;
        border-radius:24px;
        padding:50px 20px;
        text-align:center;
        color:#6b7280;
    }

    @media (max-width:768px){
        .vol-show-hero{
            padding:30px 20px;
            border-radius:24px;
        }

        .vol-show-title{
            font-size:28px;
        }

        .vol-show-main{
            padding:18px;
        }

        .vol-show-apply,
        .vol-show-btn{
            width:100%;
        }

        .vol-show-actions{
            flex-direction:column;
        }
    }
</style>

<div class="vol-show-wrap">
    @if($volunteerOpportunity)
        <div class="vol-show-hero">
            <div class="vol-show-hero-content">
                <div>
                    <span class="vol-show-type">
                        {{ $typeLabels[$volunteerOpportunity->opportunity_type] ?? 'أخرى' }}
                    </span>

                    <h1 class="vol-show-title">
                        {{ $volunteerOpportunity->title }}
                    </h1>

                    <p class="vol-show-subtitle">
                        تفاصيل فرصة التطوع وطريقة التقديم عبر منصة تطوع.
                    </p>
                </div>

                <a href="{{ $volunteerOpportunity->platform_url }}" target="_blank" class="vol-show-apply">
                    <i class="fas fa-arrow-up-right-from-square"></i>
                    التقديم في منصة تطوع
                </a>
            </div>
        </div>

        @if($imageUrl)
            <div class="vol-show-image-card">
                <img src="{{ $imageUrl }}" alt="{{ $volunteerOpportunity->title }}">
            </div>
        @endif

        <div class="vol-show-main">
            <div class="vol-show-meta-grid">
                <div class="vol-show-meta-box">
                    <span class="vol-show-label">نوع الفرصة</span>
                    <div class="vol-show-value">
                        {{ $typeLabels[$volunteerOpportunity->opportunity_type] ?? 'أخرى' }}
                    </div>
                </div>

                <div class="vol-show-meta-box">
                    <span class="vol-show-label">تاريخ بداية التطوع</span>
                    <div class="vol-show-value">
                        {{ optional($volunteerOpportunity->start_date)->format('Y-m-d') }}
                    </div>
                </div>

                <div class="vol-show-meta-box">
                    <span class="vol-show-label">تاريخ نهاية الفرصة</span>
                    <div class="vol-show-value">
                        {{ optional($volunteerOpportunity->end_date)->format('Y-m-d') }}
                    </div>
                </div>

                <div class="vol-show-meta-box">
                    <span class="vol-show-label">عدد ساعات الفرصة</span>
                    <div class="vol-show-value">
                        {{ $volunteerOpportunity->hours_count }} ساعة
                    </div>
                </div>
            </div>

            <div class="vol-show-desc-box">
                <h3 class="vol-show-section-title">وصف الفرصة</h3>

                <div class="vol-show-desc">
                    {!! $volunteerOpportunity->description !!}
                </div>
            </div>

            <div class="vol-show-actions">
                <a href="{{ $volunteerOpportunity->platform_url }}" target="_blank" class="vol-show-btn vol-show-btn-primary">
                    <i class="fas fa-arrow-up-right-from-square"></i>
                    التقديم في منصة تطوع
                </a>

                <a href="{{ $backUrl }}" class="vol-show-btn vol-show-btn-outline">
                    <i class="fas fa-arrow-right"></i>
                    الرجوع
                </a>
            </div>
        </div>
    @else
        <div class="vol-show-empty">
            لا توجد بيانات لهذه الفرصة.
        </div>
    @endif
</div>
@endsection