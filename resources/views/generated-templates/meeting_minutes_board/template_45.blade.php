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
    .minutes-page{
        max-width: 1180px;
        margin: 40px auto;
        padding: 0 16px;
        direction: rtl;
        text-align: right;
        font-family: 'Cairo', Tahoma, Arial, sans-serif;
    }

    .minutes-hero{
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        padding: 38px 32px;
        margin-bottom: 28px;
        background:
            radial-gradient(circle at top left, rgba(255,255,255,.16), transparent 28%),
            linear-gradient(135deg, {{ $buttonColor }} 0%, {{ $secondaryColor }} 100%);
        color: #fff;
        box-shadow: 0 22px 48px rgba(15, 23, 42, 0.16);
    }

    .minutes-hero::before{
        content:'';
        position:absolute;
        inset:0;
        background: linear-gradient(to left, rgba(255,255,255,.04), transparent 50%);
        pointer-events:none;
    }

    .minutes-hero-inner{
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .minutes-hero-text h1{
        margin: 0 0 10px;
        font-size: 34px;
        font-weight: 900;
        line-height: 1.4;
    }

    .minutes-hero-text p{
        margin: 0;
        font-size: 15px;
        line-height: 2;
        max-width: 760px;
        opacity: .96;
    }

    .minutes-hero-badge{
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        border-radius: 999px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
        font-size: 14px;
        font-weight: 800;
        backdrop-filter: blur(8px);
        white-space: nowrap;
    }

    .minutes-list{
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .minute-card{
        background: #fff;
        border: 1px solid #e7edf3;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        transition: .25s ease;
    }

    .minute-card:hover{
        transform: translateY(-4px);
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.10);
        border-color: rgba(15, 76, 129, .20);
    }

    .minute-card-inner{
        display: grid;
        grid-template-columns: 140px 1fr 190px;
        min-height: 100%;
    }

    .minute-date{
        background: linear-gradient(180deg, #f8fbff 0%, #f1f6fb 100%);
        border-left: 1px solid #edf2f7;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 24px 16px;
    }

    .minute-date-day{
        font-size: 40px;
        line-height: 1;
        font-weight: 900;
        color: {{ $buttonColor }};
        margin-bottom: 8px;
    }

    .minute-date-full{
        font-size: 13px;
        color: #64748b;
        font-weight: 700;
        text-align: center;
        line-height: 1.8;
    }

    .minute-main{
        padding: 24px 26px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 16px;
    }

    .minute-top{
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .minute-title{
        margin: 0;
        font-size: 24px;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.7;
    }

    .minute-tags{
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .minute-tag{
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 800;
        white-space: nowrap;
    }

    .minute-tag-board{
        background: #f8fafc;
        color: #334155;
        border: 1px solid #e2e8f0;
    }

    .minute-meta{
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        color: #64748b;
        font-size: 14px;
        font-weight: 700;
    }

    .minute-meta-item{
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .minute-side{
        padding: 22px;
        border-right: 1px solid #edf2f7;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .minute-action{
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 18px;
        border-radius: 14px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 800;
        transition: .25s ease;
        border: 1px solid transparent;
    }

    .minute-action-primary{
        background: {{ $buttonColor }};
        color: #fff;
        border-color: {{ $buttonColor }};
        box-shadow: 0 10px 24px rgba(15, 76, 129, .18);
    }

    .minute-action-primary:hover{
        background: {{ $secondaryColor }};
        border-color: {{ $secondaryColor }};
        color: #fff;
        transform: translateY(-1px);
    }

    .minute-action-disabled{
        background: #f8fafc;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: default;
    }

    .minutes-empty{
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 24px;
        padding: 56px 20px;
        text-align: center;
        color: #64748b;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.03);
    }

    .minutes-empty i{
        font-size: 34px;
        margin-bottom: 14px;
        color: #94a3b8;
        display: block;
    }

    @media (max-width: 900px){
        .minute-card-inner{
            grid-template-columns: 1fr;
        }

        .minute-date{
            border-left: none;
            border-bottom: 1px solid #edf2f7;
            flex-direction: row;
            gap: 14px;
        }

        .minute-date-day{
            margin-bottom: 0;
            font-size: 32px;
        }

        .minute-side{
            border-right: none;
            border-top: 1px solid #edf2f7;
            padding: 18px 22px 22px;
        }
    }

    @media (max-width: 768px){
        .minutes-hero{
            padding: 28px 22px;
            border-radius: 22px;
        }

        .minutes-hero-text h1{
            font-size: 28px;
        }

        .minute-main{
            padding: 20px 18px;
        }

        .minute-title{
            font-size: 20px;
        }

        .minute-action{
            width: 100%;
        }
    }
</style>

<div class="minutes-page">
    <div class="minutes-hero">
        <div class="minutes-hero-inner">
            <div class="minutes-hero-text">
                <h1>{{ $page->title ?? $categoryLabel }}</h1>
                <p>
                    {{ $page->excerpt ?? 'استعراض محاضر اجتماعات مجلس الإدارة والاطلاع على المرفقات الرسمية الخاصة بكل اجتماع ضمن واجهة مؤسسية واضحة ومنظمة.' }}
                </p>
            </div>

            <div class="minutes-hero-badge">
                <i class="fas fa-landmark"></i>
                مجلس الإدارة
            </div>
        </div>
    </div>

    <div class="minutes-list">
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

                $meetingDate = $meetingMinute->meeting_date
                    ? \Carbon\Carbon::parse($meetingMinute->meeting_date)
                    : null;
            @endphp

            <div class="minute-card">
                <div class="minute-card-inner">
                    <div class="minute-date">
                        <div class="minute-date-day">
                            {{ $meetingDate ? $meetingDate->format('d') : '--' }}
                        </div>
                        <div class="minute-date-full">
                            {{ $meetingDate ? $meetingDate->translatedFormat('F Y') : 'غير محدد' }}
                        </div>
                    </div>

                    <div class="minute-main">
                        <div class="minute-top">
                            <h3 class="minute-title">{{ $meetingMinute->title }}</h3>

                            <div class="minute-tags">
                                <span class="minute-tag" style="background:{{ $meetingTypeColor }}; color:#fff;">
                                    {{ $meetingTypeLabel }}
                                </span>
                            </div>
                        </div>

                        <div class="minute-meta">
                            <span class="minute-meta-item">
                                <i class="fas fa-calendar-days"></i>
                                {{ $meetingDate ? $meetingDate->format('Y-m-d') : 'غير محدد' }}
                            </span>

                            <span class="minute-meta-item">
                                <i class="fas fa-building-columns"></i>
                                {{ $categoryLabel }}
                            </span>
                        </div>
                    </div>

                    <div class="minute-side">
                        @if($fileUrl)
                            <a href="{{ $fileUrl }}" target="_blank" class="minute-action minute-action-primary">
                                <i class="fas fa-file-arrow-down"></i>
                                عرض المرفق
                            </a>
                        @else
                            <span class="minute-action minute-action-disabled">
                                <i class="fas fa-ban"></i>
                                لا يوجد مرفق
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="minutes-empty">
                <i class="fas fa-folder-open"></i>
                لا توجد محاضر اجتماعات مجلس إدارة حالياً.
            </div>
        @endforelse
    </div>
</div>
@endsection