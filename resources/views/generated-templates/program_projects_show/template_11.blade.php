@extends('themes.default.layouts.app')

@section('content')
@php
    $buttonColor = $siteSettings->button_color
        ?? $siteSettings->primary_color
        ?? '#2ea36b';

    $title = $project->title ?? 'بدون عنوان';
    $description = $project->description ?? null;
    $coverImage = $project->cover_image
        ?? ($project->coverMedia->file ?? null)
        ?? null;

    $projectAmount = $project->project_amount ?? null;
    $donationAmount = $project->donation_amount ?? null;
    $startDate = $project->start_date ?? null;
    $endDate = $project->end_date ?? null;
    $donationUrl = $project->donation_url ?? null;
    $reportFile = $project->report_file
        ?? ($project->reportMedia->file ?? null)
        ?? null;

    $galleryItems = collect($project->galleryImages ?? []);
@endphp

<div style="direction: rtl; text-align: right; max-width: 1240px; margin: 40px auto; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; box-sizing: border-box;">

    <style>
        .project-show-header {
            background: linear-gradient(135deg, rgba(46, 163, 107, 0.10), rgba(255,255,255,0.95));
            border: 1px solid #e5e7eb;
            border-right: 5px solid {{ $buttonColor }};
            border-radius: 20px;
            padding: 24px 24px;
            margin-bottom: 28px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .project-show-title {
            font-size: 34px;
            font-weight: 800;
            color: #111827;
            margin: 0 0 10px;
            line-height: 1.6;
        }

        .project-show-subtitle {
            font-size: 15px;
            color: #6b7280;
            line-height: 1.9;
            margin: 0;
        }

        .project-show-layout {
            display: grid;
            grid-template-columns: 1.35fr .95fr;
            gap: 28px;
            align-items: start;
            margin-bottom: 32px;
        }

        .project-show-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .project-show-card-body {
            padding: 24px;
        }

        .project-show-section-title {
            font-size: 22px;
            font-weight: 800;
            color: #1f2937;
            margin: 0 0 18px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(0,0,0,0.05);
        }

        .project-show-description {
            font-size: 15px;
            color: #374151;
            line-height: 2;
        }

        .project-show-image-wrap {
            position: relative;
            height: 100%;
            min-height: 350px;
            background: #f3f4f6;
            overflow: hidden;
        }

        .project-show-image {
            width: 100%;
            height: 100%;
            min-height: 350px;
            object-fit: cover;
            display: block;
        }

        .project-show-image-placeholder {
            width: 100%;
            min-height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 16px;
            font-weight: 700;
        }

        .project-show-meta {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .project-show-meta-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 14px 16px;
            background: #f9fafb;
            border: 1px solid #eef2f7;
            border-radius: 14px;
            font-size: 14px;
            color: #374151;
        }

        .project-show-meta-label {
            font-weight: 800;
            color: #111827;
        }

        .project-show-meta-value {
            text-align: left;
        }

        .project-show-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 22px;
        }

        .project-show-btn {
            display: inline-block;
            padding: 13px 18px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 800;
            transition: opacity .2s ease;
        }

        .project-show-btn:hover {
            opacity: .9;
        }

        .project-show-btn-primary {
            background: {{ $buttonColor }};
            color: #fff;
            border: 1px solid {{ $buttonColor }};
        }

        .project-show-btn-secondary {
            background: #fff;
            color: {{ $buttonColor }};
            border: 1px solid {{ $buttonColor }};
        }

        .project-gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .project-gallery-item {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background: #fff;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .project-gallery-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.10);
        }

        .project-gallery-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }

        .project-empty-gallery {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
            background: #f9fafb;
            border: 2px dashed #cbd5e0;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 600;
        }

        @media (max-width: 992px) {
            .project-show-layout {
                grid-template-columns: 1fr;
            }

            .project-show-title {
                font-size: 28px;
            }
        }

        @media (max-width: 768px) {
            .project-show-card-body {
                padding: 18px;
            }

            .project-show-title {
                font-size: 24px;
            }

            .project-show-meta-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .project-show-meta-value {
                text-align: right;
            }

            .project-show-image-wrap,
            .project-show-image,
            .project-show-image-placeholder {
                min-height: 260px;
            }
        }
    </style>

    <div class="project-show-header">
        <h1 class="project-show-title">{{ $title }}</h1>

        @if(!empty($description))
            <p class="project-show-subtitle">
                {{ \Illuminate\Support\Str::limit(strip_tags($description), 220) }}
            </p>
        @endif
    </div>

    <div class="project-show-layout">
        <div class="project-show-card">
            <div class="project-show-card-body">
                <h2 class="project-show-section-title">نبذة عن المشروع</h2>

                <div class="project-show-description">
                    {!! $description ?: 'لا يوجد وصف مضاف لهذا المشروع حاليًا.' !!}
                </div>
            </div>
        </div>

        <div class="project-show-card">
            <div class="project-show-image-wrap">
                @if(!empty($coverImage))
                    <img loading="lazy" decoding="async"
                        src="{{ asset('storage/' . ltrim($coverImage, '/')) }}"
                        alt="{{ $title }}"
                        class="project-show-image"
                    >
                @else
                    <div class="project-show-image-placeholder">
                        لا توجد صورة بارزة للمشروع
                    </div>
                @endif
            </div>

            <div class="project-show-card-body">
                <h2 class="project-show-section-title">بيانات المشروع</h2>

                <div class="project-show-meta">
                    @if(!empty($projectAmount))
                        <div class="project-show-meta-item">
                            <span class="project-show-meta-label">مبلغ المشروع</span>
                            <span class="project-show-meta-value">{{ number_format($projectAmount, 2) }} ر.س</span>
                        </div>
                    @endif

                    @if(!empty($donationAmount))
                        <div class="project-show-meta-item">
                            <span class="project-show-meta-label">مبلغ التبرع</span>
                            <span class="project-show-meta-value">{{ number_format($donationAmount, 2) }} ر.س</span>
                        </div>
                    @endif

                    @if(!empty($startDate))
                        <div class="project-show-meta-item">
                            <span class="project-show-meta-label">تاريخ البداية</span>
                            <span class="project-show-meta-value">{{ \Illuminate\Support\Carbon::parse($startDate)->format('Y-m-d') }}</span>
                        </div>
                    @endif

                    @if(!empty($endDate))
                        <div class="project-show-meta-item">
                            <span class="project-show-meta-label">تاريخ النهاية</span>
                            <span class="project-show-meta-value">{{ \Illuminate\Support\Carbon::parse($endDate)->format('Y-m-d') }}</span>
                        </div>
                    @endif
                </div>

                <div class="project-show-actions">
                    @if(!empty($donationUrl))
                        <a href="{{ $donationUrl }}" target="_blank" class="project-show-btn project-show-btn-primary">
                            التبرع للمشروع
                        </a>
                    @endif

                    @if(!empty($reportFile))
                        <a href="{{ asset('storage/' . ltrim($reportFile, '/')) }}" target="_blank" class="project-show-btn project-show-btn-secondary">
                            تحميل تقرير المشروع
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="project-show-card">
        <div class="project-show-card-body">
            <h2 class="project-show-section-title">معرض صور المشروع</h2>

            @if($galleryItems->count())
                <div class="project-gallery-grid">
                    @foreach($galleryItems as $galleryItem)
                        @php
                            $galleryFile = $galleryItem->mediaItem->file ?? null;
                        @endphp

                        @if(!empty($galleryFile))
                            <div class="project-gallery-item">
                                <img loading="lazy" decoding="async"
                                    src="{{ asset('storage/' . ltrim($galleryFile, '/')) }}"
                                    alt="{{ $title }}"
                                    class="project-gallery-image"
                                >
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="project-empty-gallery">
                    لا توجد صور مضافة لهذا المشروع حاليًا.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection