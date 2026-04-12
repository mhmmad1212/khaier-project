@extends('themes.default.layouts.app')

@section('content')
@php
    $meetingMinutes = collect($meetingMinutes ?? ($items ?? []));
    $buttonColor = $siteSettings->button_color ?? $siteSettings->primary_color ?? '#127962';
    $secondaryColor = $siteSettings->secondary_color ?? '#10b981';

    $categoryLabel = $categoryLabel ?? ($page->title ?? 'محاضر الاجتماعات');

    $meetingTypeLabels = [
        'regular' => 'عادي / دوري',
        'emergency' => 'طارئ',
    ];

    $meetingTypeColors = [
        'regular' => '#64748b',
        'emergency' => '#dc2626',
    ];
@endphp

<style>
    .mm-wrap{
        max-width:1200px;
        margin:40px auto;
        padding:0 16px;
        direction:rtl;
        text-align:right;
        font-family:'Cairo', Tahoma, Arial, sans-serif;
        box-sizing:border-box;
    }

    .mm-wrap *{ box-sizing:border-box; }

    .mm-hero{
        position:relative;
        overflow:hidden;
        border-radius:30px;
        padding:44px 28px;
        margin-bottom:28px;
        color:#fff;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.16), transparent 28%),
            radial-gradient(circle at bottom left, rgba(255,255,255,.08), transparent 24%),
            linear-gradient(135deg, {{ $buttonColor }} 0%, {{ $secondaryColor }} 100%);
        box-shadow:0 20px 42px rgba(15,23,42,.16);
    }

    .mm-hero::before,
    .mm-hero::after{
        content:'';
        position:absolute;
        border-radius:999px;
        background:rgba(255,255,255,.08);
        pointer-events:none;
    }

    .mm-hero::before{
        width:220px;
        height:220px;
        top:-70px;
        left:-60px;
    }

    .mm-hero::after{
        width:170px;
        height:170px;
        bottom:-50px;
        right:-40px;
    }

    .mm-hero-content{ position:relative; z-index:2; }

    .mm-badge{
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

    .mm-title{
        margin:0 0 10px;
        font-size:36px;
        font-weight:900;
        line-height:1.5;
    }

    .mm-subtitle{
        margin:0;
        max-width:780px;
        font-size:16px;
        line-height:2;
        opacity:.96;
    }

    .mm-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));
        gap:24px;
    }

    .mm-card{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:26px;
        padding:24px;
        box-shadow:0 14px 30px rgba(15,23,42,.06);
        display:flex;
        flex-direction:column;
        gap:16px;
    }

    .mm-card-top{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
    }

    .mm-card-title{
        margin:0;
        font-size:24px;
        font-weight:900;
        color:#111827;
        line-height:1.6;
    }

    .mm-type-badge{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:8px 12px;
        border-radius:999px;
        color:#fff;
        font-size:13px;
        font-weight:800;
        white-space:nowrap;
    }

    .mm-meta{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:12px;
    }

    .mm-meta-box{
        background:#f8fafc;
        border:1px solid #e5e7eb;
        border-radius:16px;
        padding:14px;
    }

    .mm-label{
        display:block;
        margin-bottom:6px;
        color:#6b7280;
        font-size:13px;
        font-weight:700;
    }

    .mm-value{
        color:#111827;
        font-size:15px;
        font-weight:800;
        line-height:1.8;
    }

    .mm-desc{
        color:#4b5563;
        font-size:15px;
        line-height:2;
    }

    .mm-actions{
        display:flex;
        flex-wrap:wrap;
        gap:10px;
        margin-top:auto;
    }

    .mm-btn{
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

    .mm-btn-primary{
        background:{{ $buttonColor }};
        color:#fff;
        border-color:{{ $buttonColor }};
    }

    .mm-btn-primary:hover{
        filter:brightness(.96);
        color:#fff;
    }

    .mm-empty{
        grid-column:1/-1;
        background:#fff;
        border:1px dashed #cbd5e1;
        border-radius:24px;
        padding:54px 20px;
        text-align:center;
        color:#6b7280;
        box-shadow:0 10px 24px rgba(15,23,42,.04);
    }

    .mm-empty-icon{
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

    .mm-empty-title{
        margin:0 0 8px;
        font-size:24px;
        font-weight:900;
        color:#111827;
    }

    .mm-empty-text{
        margin:0;
        font-size:15px;
        line-height:1.9;
    }

    @media (max-width:768px){
        .mm-hero{
            padding:30px 20px;
            border-radius:24px;
        }

        .mm-title{
            font-size:28px;
        }

        .mm-subtitle{
            font-size:15px;
        }

        .mm-card{
            padding:18px;
        }

        .mm-card-top{
            flex-direction:column;
        }

        .mm-actions{
            flex-direction:column;
        }

        .mm-btn{
            width:100%;
        }
    }
</style>

<div class="mm-wrap">
    <div class="mm-hero">
        <div class="mm-hero-content">
            <div class="mm-badge">
                <i class="fas fa-file-lines"></i>
                محاضر الاجتماعات
            </div>

            <h1 class="mm-title">
                {{ $page->title ?? $categoryLabel }}
            </h1>

            <p class="mm-subtitle">
                {{ $page->excerpt ?? ('استعراض جميع المحاضر الخاصة بـ ' . $categoryLabel . ' مع إمكانية فتح المرفقات الخاصة بكل محضر.') }}
            </p>
        </div>
    </div>

    <div class="mm-grid">
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

                $meetingTypeLabel = $meetingTypeLabels[$meetingMinute->meeting_type] ?? ($meetingMinute->meeting_type ?: 'غير محدد');
                $meetingTypeColor = $meetingTypeColors[$meetingMinute->meeting_type] ?? '#64748b';
            @endphp

            <div class="mm-card">
                <div class="mm-card-top">
                    <h3 class="mm-card-title">{{ $meetingMinute->title }}</h3>

                    <span class="mm-type-badge" style="background:{{ $meetingTypeColor }};">
                        {{ $meetingTypeLabel }}
                    </span>
                </div>

                <div class="mm-meta">
                    <div class="mm-meta-box">
                        <span class="mm-label">تاريخ الاجتماع</span>
                        <div class="mm-value">
                            {{ optional($meetingMinute->meeting_date)->format('Y-m-d') ?: 'غير محدد' }}
                        </div>
                    </div>

                    <div class="mm-meta-box">
                        <span class="mm-label">جهة الاجتماع</span>
                        <div class="mm-value">{{ $categoryLabel }}</div>
                    </div>
                </div>

                @if(!empty($meetingMinute->description))
                    <div class="mm-desc">
                        {!! nl2br(e(\Illuminate\Support\Str::limit($meetingMinute->description, 260))) !!}
                    </div>
                @endif

                <div class="mm-actions">
                    @if($fileUrl)
                        <a href="{{ $fileUrl }}" target="_blank" class="mm-btn mm-btn-primary">
                            <i class="fas fa-file-arrow-down"></i>
                            عرض المرفق
                        </a>
                    @else
                        <span class="mm-btn" style="background:#f8fafc; color:#64748b; border-color:#e5e7eb; cursor:default;">
                            <i class="fas fa-ban"></i>
                            لا يوجد مرفق
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="mm-empty">
                <div class="mm-empty-icon">
                    <i class="fas fa-file-lines"></i>
                </div>
                <h3 class="mm-empty-title">لا توجد محاضر حالياً</h3>
                <p class="mm-empty-text">
                    لم تتم إضافة أي محاضر لهذا القسم حتى الآن.
                </p>
            </div>
        @endforelse
    </div>
</div>
@endsection
