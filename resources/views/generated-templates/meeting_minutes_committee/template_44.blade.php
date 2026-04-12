@extends('themes.default.layouts.app')

@section('content')
@php
    $meetingMinutes = collect($meetingMinutes ?? ($items ?? []));
    $buttonColor = $siteSettings->button_color ?? $siteSettings->primary_color ?? '#7c3aed';
    $secondaryColor = $siteSettings->secondary_color ?? '#0ea5a4';

    $categoryLabel = $categoryLabel ?? ($page->title ?? 'محاضر اجتماعات اللجان');

    $meetingTypeLabels = [
        'regular' => 'اجتماع دوري',
        'emergency' => 'اجتماع طارئ',
    ];

    $meetingTypeColors = [
        'regular' => '#7c3aed',
        'emergency' => '#dc2626',
    ];
@endphp

<style>
    .com-wrap{
        max-width:1200px;
        margin:40px auto;
        padding:0 16px;
        direction:rtl;
        text-align:right;
        font-family:'Cairo', Tahoma, Arial, sans-serif;
    }

    .com-hero{
        position:relative;
        overflow:hidden;
        border-radius:30px;
        padding:44px 28px;
        margin-bottom:28px;
        color:#fff;
        background:
            linear-gradient(135deg, {{ $buttonColor }} 0%, {{ $secondaryColor }} 100%);
        box-shadow:0 20px 42px rgba(15,23,42,.16);
    }

    .com-hero:before,
    .com-hero:after{
        content:'';
        position:absolute;
        border-radius:999px;
        background:rgba(255,255,255,.09);
    }

    .com-hero:before{
        width:210px;
        height:210px;
        top:-70px;
        left:-55px;
    }

    .com-hero:after{
        width:170px;
        height:170px;
        bottom:-45px;
        right:-35px;
    }

    .com-hero-content{
        position:relative;
        z-index:2;
    }

    .com-badge{
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

    .com-title{
        margin:0 0 10px;
        font-size:36px;
        font-weight:900;
        line-height:1.5;
    }

    .com-subtitle{
        margin:0;
        max-width:760px;
        font-size:16px;
        line-height:2;
        opacity:.96;
    }

    .com-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));
        gap:22px;
    }

    .com-card{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:24px;
        overflow:hidden;
        box-shadow:0 12px 28px rgba(15,23,42,.06);
        display:flex;
        flex-direction:column;
    }

    .com-topbar{
        padding:18px 20px;
        background:linear-gradient(180deg, #faf5ff 0%, #f8fafc 100%);
        border-bottom:1px solid #eef2f7;
    }

    .com-top-flex{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
    }

    .com-card-title{
        margin:0;
        font-size:22px;
        font-weight:900;
        color:#111827;
        line-height:1.6;
    }

    .com-type{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:8px 12px;
        border-radius:999px;
        font-size:13px;
        font-weight:800;
        white-space:nowrap;
        color:#fff;
    }

    .com-body{
        padding:20px;
        display:flex;
        flex-direction:column;
        gap:14px;
        flex:1;
    }

    .com-date{
        display:flex;
        align-items:center;
        gap:8px;
        color:#6b7280;
        font-size:14px;
        font-weight:700;
    }

    .com-desc{
        color:#4b5563;
        font-size:15px;
        line-height:2;
        min-height:86px;
    }

    .com-info{
        background:#f8fafc;
        border:1px solid #e5e7eb;
        border-radius:16px;
        padding:14px;
        color:#334155;
        font-size:14px;
        line-height:1.9;
    }

    .com-actions{
        margin-top:auto;
        display:flex;
        flex-wrap:wrap;
        gap:10px;
    }

    .com-btn{
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

    .com-btn-primary{
        background:{{ $buttonColor }};
        color:#fff;
        border-color:{{ $buttonColor }};
    }

    .com-btn-primary:hover{
        filter:brightness(.96);
        color:#fff;
    }

    .com-empty{
        grid-column:1/-1;
        background:#fff;
        border:1px dashed #cbd5e1;
        border-radius:24px;
        padding:54px 20px;
        text-align:center;
        color:#64748b;
    }

    @media (max-width:768px){
        .com-hero{
            padding:30px 20px;
            border-radius:24px;
        }

        .com-title{
            font-size:28px;
        }

        .com-top-flex{
            flex-direction:column;
            align-items:flex-start;
        }

        .com-actions{
            flex-direction:column;
        }

        .com-btn{
            width:100%;
        }
    }
</style>

<div class="com-wrap">
    <div class="com-hero">
        <div class="com-hero-content">
            <div class="com-badge">
                <i class="fas fa-people-group"></i>
                محاضر اللجان
            </div>

            <h1 class="com-title">
                {{ $page->title ?? $categoryLabel }}
            </h1>

            <p class="com-subtitle">
                {{ $page->excerpt ?? 'عرض محاضر اجتماعات اللجان بأسلوب منظم وعصري مع الوصول المباشر إلى ملفات الاجتماعات والمرفقات ذات الصلة.' }}
            </p>
        </div>
    </div>

    <div class="com-grid">
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

            <div class="com-card">
                <div class="com-topbar">
                    <div class="com-top-flex">
                        <h3 class="com-card-title">{{ $meetingMinute->title }}</h3>

                        <span class="com-type" style="background:{{ $meetingTypeColor }};">
                            {{ $meetingTypeLabel }}
                        </span>
                    </div>
                </div>

                <div class="com-body">
                    <div class="com-date">
                        <i class="fas fa-calendar-days"></i>
                        {{ optional($meetingMinute->meeting_date)->format('Y-m-d') ?: 'غير محدد' }}
                    </div>

                    <div class="com-desc">
                        {!! nl2br(e(\Illuminate\Support\Str::limit($meetingMinute->description ?: 'لا يوجد وصف مختصر لهذا المحضر.', 230))) !!}
                    </div>

                    <div class="com-info">
                        <strong>جهة الاجتماع:</strong> {{ $categoryLabel }}
                    </div>

                    <div class="com-actions">
                        @if($fileUrl)
                            <a href="{{ $fileUrl }}" target="_blank" class="com-btn com-btn-primary">
                                <i class="fas fa-file-arrow-down"></i>
                                فتح المرفق
                            </a>
                        @else
                            <span class="com-btn" style="background:#f8fafc; color:#64748b; border-color:#e5e7eb; cursor:default;">
                                <i class="fas fa-ban"></i>
                                لا يوجد مرفق
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="com-empty">
                لا توجد محاضر اجتماعات لجان حالياً.
            </div>
        @endforelse
    </div>
</div>
@endsection