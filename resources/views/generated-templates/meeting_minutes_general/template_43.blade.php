@extends('themes.default.layouts.app')

@section('content')
@php
    $meetingMinutes = collect($meetingMinutes ?? ($items ?? []));
    $buttonColor = $siteSettings->button_color ?? $siteSettings->primary_color ?? '#127962';
    $secondaryColor = $siteSettings->secondary_color ?? '#d4a017';

    $categoryLabel = $categoryLabel ?? ($page->title ?? 'محاضر اجتماعات الجمعية العمومية');

    $meetingTypeLabels = [
        'regular' => 'اجتماع عادي',
        'emergency' => 'اجتماع طارئ',
    ];

    $meetingTypeColors = [
        'regular' => '#127962',
        'emergency' => '#b45309',
    ];
@endphp

<style>
    .ga-wrap{
        max-width:1200px;
        margin:40px auto;
        padding:0 16px;
        direction:rtl;
        text-align:right;
        font-family:'Cairo', Tahoma, Arial, sans-serif;
    }

    .ga-hero{
        position:relative;
        overflow:hidden;
        border-radius:30px;
        padding:46px 30px;
        margin-bottom:30px;
        color:#fff;
        background:
            linear-gradient(135deg, {{ $buttonColor }} 0%, {{ $secondaryColor }} 100%);
        box-shadow:0 20px 42px rgba(15,23,42,.16);
    }

    .ga-hero::before,
    .ga-hero::after{
        content:'';
        position:absolute;
        border-radius:999px;
        background:rgba(255,255,255,.10);
    }

    .ga-hero::before{
        width:220px;
        height:220px;
        top:-75px;
        left:-55px;
    }

    .ga-hero::after{
        width:170px;
        height:170px;
        bottom:-50px;
        right:-40px;
    }

    .ga-hero-content{
        position:relative;
        z-index:2;
    }

    .ga-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:10px 16px;
        border-radius:999px;
        background:rgba(255,255,255,.15);
        border:1px solid rgba(255,255,255,.20);
        margin-bottom:18px;
        font-size:14px;
        font-weight:800;
    }

    .ga-title{
        margin:0 0 10px;
        font-size:36px;
        font-weight:900;
        line-height:1.5;
    }

    .ga-subtitle{
        margin:0;
        max-width:760px;
        font-size:16px;
        line-height:2;
        opacity:.96;
    }

    .ga-list{
        display:grid;
        gap:20px;
    }

    .ga-card{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:24px;
        padding:22px;
        box-shadow:0 12px 28px rgba(15,23,42,.05);
    }

    .ga-card-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:16px;
        margin-bottom:16px;
    }

    .ga-card-title{
        margin:0;
        font-size:24px;
        font-weight:900;
        color:#111827;
        line-height:1.7;
    }

    .ga-side{
        display:flex;
        flex-direction:column;
        gap:8px;
        align-items:flex-end;
    }

    .ga-chip{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:8px 12px;
        border-radius:999px;
        font-size:13px;
        font-weight:800;
        white-space:nowrap;
    }

    .ga-date{
        background:#fef3c7;
        color:#92400e;
    }

    .ga-body{
        display:grid;
        grid-template-columns:1fr 240px;
        gap:18px;
    }

    .ga-desc{
        color:#4b5563;
        font-size:15px;
        line-height:2.05;
    }

    .ga-box{
        background:#f8fafc;
        border:1px solid #e5e7eb;
        border-radius:18px;
        padding:16px;
        display:flex;
        flex-direction:column;
        gap:12px;
    }

    .ga-label{
        font-size:13px;
        color:#6b7280;
        font-weight:700;
    }

    .ga-value{
        font-size:15px;
        color:#111827;
        font-weight:800;
        line-height:1.8;
    }

    .ga-actions{
        margin-top:18px;
        display:flex;
        flex-wrap:wrap;
        gap:10px;
    }

    .ga-btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        padding:12px 16px;
        border-radius:12px;
        text-decoration:none;
        font-size:14px;
        font-weight:800;
        border:1px solid transparent;
        transition:.25s ease;
    }

    .ga-btn-primary{
        background:{{ $buttonColor }};
        color:#fff;
        border-color:{{ $buttonColor }};
    }

    .ga-btn-primary:hover{
        filter:brightness(.96);
        color:#fff;
    }

    .ga-empty{
        background:#fff;
        border:1px dashed #cbd5e1;
        border-radius:24px;
        padding:54px 20px;
        text-align:center;
        color:#64748b;
    }

    @media (max-width:850px){
        .ga-body{
            grid-template-columns:1fr;
        }
    }

    @media (max-width:768px){
        .ga-hero{
            padding:30px 20px;
            border-radius:24px;
        }

        .ga-title{
            font-size:28px;
        }

        .ga-card{
            padding:18px;
        }

        .ga-card-head{
            flex-direction:column;
        }

        .ga-side{
            align-items:flex-start;
        }

        .ga-actions{
            flex-direction:column;
        }

        .ga-btn{
            width:100%;
        }
    }
</style>

<div class="ga-wrap">
    <div class="ga-hero">
        <div class="ga-hero-content">
            <div class="ga-badge">
                <i class="fas fa-users"></i>
                الجمعية العمومية
            </div>

            <h1 class="ga-title">
                {{ $page->title ?? $categoryLabel }}
            </h1>

            <p class="ga-subtitle">
                {{ $page->excerpt ?? 'استعراض محاضر اجتماعات الجمعية العمومية وقراراتها والمرفقات المرتبطة بكل اجتماع بشكل واضح ومنظم.' }}
            </p>
        </div>
    </div>

    <div class="ga-list">
        @forelse($meetingMinutes as $meetingMinute)
            @php
                $fileUrl = $meetingMinute->fileMedia->url
                    ?? (
                        !empty($meetingMinute->file)
                            ? (
                                \Illuminate\Support\Str::startsWith($meetingMinute->file, ['http://', 'https://'])
                                    ? $meetingMinute->file
                                    : \App\Support\Media\MediaUrl::forDiskPath('public', ltrim($meetingMinute->file, '/'))
                            )
                            : null
                    );

                $meetingTypeLabel = $meetingTypeLabels[$meetingMinute->meeting_type] ?? 'اجتماع';
                $meetingTypeColor = $meetingTypeColors[$meetingMinute->meeting_type] ?? '#475569';
            @endphp

            <div class="ga-card">
                <div class="ga-card-head">
                    <h3 class="ga-card-title">{{ $meetingMinute->title }}</h3>

                    <div class="ga-side">
                        <span class="ga-chip ga-date">
                            <i class="fas fa-calendar-days"></i>
                            {{ optional($meetingMinute->meeting_date)->format('Y-m-d') ?: 'غير محدد' }}
                        </span>

                        <span class="ga-chip" style="background:{{ $meetingTypeColor }}; color:#fff;">
                            {{ $meetingTypeLabel }}
                        </span>
                    </div>
                </div>

                <div class="ga-body">
                    <div class="ga-desc">
                        {!! nl2br(e($meetingMinute->description ?: 'لا يوجد وصف مختصر لهذا المحضر.')) !!}
                    </div>

                    <div class="ga-box">
                        <div>
                            <div class="ga-label">جهة الاجتماع</div>
                            <div class="ga-value">{{ $categoryLabel }}</div>
                        </div>

                        <div>
                            <div class="ga-label">حالة المرفق</div>
                            <div class="ga-value">{{ $fileUrl ? 'متوفر' : 'غير متوفر' }}</div>
                        </div>
                    </div>
                </div>

                <div class="ga-actions">
                    @if($fileUrl)
                        <a href="{{ $fileUrl }}" target="_blank" class="ga-btn ga-btn-primary">
                            <i class="fas fa-file-arrow-down"></i>
                            عرض المرفق
                        </a>
                    @else
                        <span class="ga-btn" style="background:#f8fafc; color:#64748b; border-color:#e5e7eb; cursor:default;">
                            <i class="fas fa-ban"></i>
                            لا يوجد مرفق
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="ga-empty">
                لا توجد محاضر اجتماعات جمعية عمومية حالياً.
            </div>
        @endforelse
    </div>
</div>
@endsection