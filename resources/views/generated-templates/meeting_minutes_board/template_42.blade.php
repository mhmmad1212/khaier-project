@extends('themes.default.layouts.app')

@section('content')
@php
    $meetingMinutes = collect($meetingMinutes ?? ($items ?? []));
    $buttonColor = $siteSettings->button_color ?? $siteSettings->primary_color ?? '#0f4c81';
    $secondaryColor = $siteSettings->secondary_color ?? '#127962';

    $categoryLabel = $categoryLabel ?? ($page->title ?? 'محاضر اجتماعات مجلس الإدارة');

    $meetingTypeLabels = [
        'regular' => 'اجتماع دوري',
        'emergency' => 'اجتماع طارئ',
    ];

    $meetingTypeColors = [
        'regular' => '#1d4ed8',
        'emergency' => '#dc2626',
    ];
@endphp

<style>
    .board-wrap{
        max-width:1200px;
        margin:40px auto;
        padding:0 16px;
        direction:rtl;
        text-align:right;
        font-family:'Cairo', Tahoma, Arial, sans-serif;
    }

    .board-hero{
        position:relative;
        overflow:hidden;
        border-radius:30px;
        padding:46px 30px;
        margin-bottom:30px;
        color:#fff;
        background:
            linear-gradient(135deg, #0b2447 0%, {{ $buttonColor }} 55%, {{ $secondaryColor }} 100%);
        box-shadow:0 22px 42px rgba(15,23,42,.18);
    }

    .board-hero:before,
    .board-hero:after{
        content:'';
        position:absolute;
        border-radius:999px;
        background:rgba(255,255,255,.08);
    }

    .board-hero:before{
        width:220px;
        height:220px;
        top:-70px;
        left:-50px;
    }

    .board-hero:after{
        width:180px;
        height:180px;
        bottom:-60px;
        right:-40px;
    }

    .board-hero-content{
        position:relative;
        z-index:2;
    }

    .board-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:10px 16px;
        border-radius:999px;
        background:rgba(255,255,255,.14);
        border:1px solid rgba(255,255,255,.22);
        margin-bottom:18px;
        font-size:14px;
        font-weight:800;
    }

    .board-title{
        margin:0 0 10px;
        font-size:36px;
        font-weight:900;
        line-height:1.5;
    }

    .board-subtitle{
        margin:0;
        max-width:760px;
        font-size:16px;
        line-height:2;
        opacity:.95;
    }

    .board-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(330px, 1fr));
        gap:24px;
    }

    .board-card{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:26px;
        overflow:hidden;
        box-shadow:0 14px 32px rgba(15,23,42,.06);
        transition:.25s ease;
        display:flex;
        flex-direction:column;
    }

    .board-card:hover{
        transform:translateY(-4px);
        box-shadow:0 22px 40px rgba(15,23,42,.10);
    }

    .board-card-top{
        padding:22px 22px 16px;
        border-bottom:1px solid #eef2f7;
        background:linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .board-card-title{
        margin:0 0 12px;
        font-size:24px;
        font-weight:900;
        color:#111827;
        line-height:1.7;
    }

    .board-badges{
        display:flex;
        flex-wrap:wrap;
        gap:8px;
    }

    .board-chip{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:8px 12px;
        border-radius:999px;
        font-size:13px;
        font-weight:800;
        white-space:nowrap;
    }

    .board-chip-date{
        background:#eff6ff;
        color:#1d4ed8;
    }

    .board-body{
        padding:22px;
        display:flex;
        flex-direction:column;
        gap:16px;
        flex:1;
    }

    .board-summary{
        color:#4b5563;
        font-size:15px;
        line-height:2;
        min-height:88px;
    }

    .board-note{
        background:#f8fafc;
        border:1px solid #e5e7eb;
        border-radius:16px;
        padding:14px 16px;
        color:#334155;
        font-size:14px;
        line-height:1.9;
    }

    .board-actions{
        margin-top:auto;
        display:flex;
        gap:10px;
        flex-wrap:wrap;
    }

    .board-btn{
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

    .board-btn-primary{
        background:{{ $buttonColor }};
        color:#fff;
        border-color:{{ $buttonColor }};
    }

    .board-btn-primary:hover{
        filter:brightness(.96);
        color:#fff;
    }

    .board-empty{
        grid-column:1/-1;
        background:#fff;
        border:1px dashed #cbd5e1;
        border-radius:24px;
        padding:54px 20px;
        text-align:center;
        color:#64748b;
    }

    @media (max-width:768px){
        .board-hero{
            padding:30px 20px;
            border-radius:24px;
        }

        .board-title{
            font-size:28px;
        }

        .board-body,
        .board-card-top{
            padding:18px;
        }

        .board-actions{
            flex-direction:column;
        }

        .board-btn{
            width:100%;
        }
    }
</style>

<div class="board-wrap">
    <div class="board-hero">
        <div class="board-hero-content">
            <div class="board-badge">
                <i class="fas fa-landmark"></i>
                مجلس الإدارة
            </div>

            <h1 class="board-title">
                {{ $page->title ?? $categoryLabel }}
            </h1>

            <p class="board-subtitle">
                {{ $page->excerpt ?? 'استعراض محاضر اجتماعات مجلس الإدارة مع الوصول المباشر إلى الملفات والمرفقات الرسمية الخاصة بكل اجتماع.' }}
            </p>
        </div>
    </div>

    <div class="board-grid">
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

            <div class="board-card">
                <div class="board-card-top">
                    <h3 class="board-card-title">{{ $meetingMinute->title }}</h3>

                    <div class="board-badges">
                        <span class="board-chip board-chip-date">
                            <i class="fas fa-calendar-days"></i>
                            {{ optional($meetingMinute->meeting_date)->format('Y-m-d') ?: 'غير محدد' }}
                        </span>

                        <span class="board-chip" style="background:{{ $meetingTypeColor }}; color:#fff;">
                            {{ $meetingTypeLabel }}
                        </span>
                    </div>
                </div>

                <div class="board-body">
                    <div class="board-summary">
                        {!! nl2br(e(\Illuminate\Support\Str::limit($meetingMinute->description ?: 'لا يوجد وصف مختصر لهذا المحضر.', 240))) !!}
                    </div>

                    <div class="board-note">
                        <strong>الجهة:</strong> {{ $categoryLabel }}
                    </div>

                    <div class="board-actions">
                        @if($fileUrl)
                            <a href="{{ $fileUrl }}" target="_blank" class="board-btn board-btn-primary">
                                <i class="fas fa-file-arrow-down"></i>
                                عرض المرفق
                            </a>
                        @else
                            <span class="board-btn" style="background:#f8fafc; color:#64748b; border-color:#e5e7eb; cursor:default;">
                                <i class="fas fa-ban"></i>
                                لا يوجد مرفق
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="board-empty">
                لا توجد محاضر اجتماعات مجلس إدارة حالياً.
            </div>
        @endforelse
    </div>
</div>
@endsection