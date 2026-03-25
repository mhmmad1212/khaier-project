@extends('themes.default.layouts.app')

@section('content')
@php
    $buttonColor = $siteSettings->button_color
        ?? $siteSettings->primary_color
        ?? '#2ea36b';

    $projectsList = collect($projects ?? ($items ?? []));
@endphp

<div style="direction: rtl; text-align: right; max-width: 1240px; margin: 40px auto; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; box-sizing: border-box;">

    <style>
        .projects-header {
            border-bottom: 2px solid {{ $buttonColor }};
            padding-bottom: 15px;
            margin-bottom: 35px;
        }

        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .project-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .project-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.12);
        }

        .project-image-wrap {
            position: relative;
            height: 220px;
            overflow: hidden;
            background: #f3f4f6;
        }

        .project-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .35s ease;
        }

        .project-card:hover .project-image {
            transform: scale(1.05);
        }

        .project-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 15px;
            font-weight: 700;
        }

        .project-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            background: rgba(255, 255, 255, 0.92);
            color: #111827;
            font-size: 13px;
            font-weight: 800;
            padding: 8px 12px;
            border-radius: 999px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        .project-body {
            padding: 20px 18px 18px;
        }

        .project-title {
            font-size: 20px;
            font-weight: 800;
            color: #1f2937;
            margin: 0 0 12px;
            line-height: 1.7;
            min-height: 68px;
        }

        .project-meta {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 18px;
        }

        .project-meta-item {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.8;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 1px dashed #edf2f7;
            padding-bottom: 8px;
        }

        .project-meta-label {
            font-weight: 700;
            color: #111827;
            white-space: nowrap;
        }

        .project-meta-value {
            color: #374151;
            text-align: left;
        }

        .project-actions {
            margin-top: 10px;
        }

        .project-btn {
            display: inline-block;
            width: 100%;
            text-align: center;
            padding: 12px 16px;
            border-radius: 12px;
            background: {{ $buttonColor }};
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 800;
            border: 1px solid {{ $buttonColor }};
            transition: opacity .2s ease;
            box-sizing: border-box;
        }

        .project-btn:hover {
            opacity: .9;
        }

        .projects-empty {
            text-align: center;
            padding: 70px 20px;
            background: #f9fafb;
            border: 2px dashed #cbd5e0;
            border-radius: 16px;
            color: #6b7280;
            font-size: 18px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .project-image-wrap {
                height: 200px;
            }

            .project-title {
                font-size: 18px;
                min-height: auto;
            }

            .project-meta-item {
                flex-direction: row;
                align-items: center;
            }
        }
    </style>

    <div class="projects-header">
        <h1 style="color: #1a4a38; font-size: 30px; font-weight: 800; margin: 0 0 8px;">
            {{ $page->title ?? 'المشاريع' }}
        </h1>

        @if(!empty($page->excerpt))
            <p style="margin: 0; color: #6b7280; font-size: 15px; line-height: 1.8;">
                {{ $page->excerpt }}
            </p>
        @endif
    </div>

    @if($projectsList->count())
        <div class="projects-grid">
            @foreach($projectsList as $project)
                @php
                    $title = $project->title ?? 'بدون عنوان';
                    $image = $project->cover_image
                        ?? ($project->coverMedia->file ?? null)
                        ?? null;

                    $projectAmount = $project->project_amount ?? null;
                    $startDate = $project->start_date ?? null;
                    $endDate = $project->end_date ?? null;
                @endphp

                <div class="project-card">
                    <div class="project-image-wrap">
                        @if(!empty($image))
                            <img loading="lazy" decoding="async"
                                src="{{ asset('storage/' . ltrim($image, '/')) }}"
                                alt="{{ $title }}"
                                class="project-image"
                            >
                        @else
                            <div class="project-image-placeholder">
                                لا توجد صورة للمشروع
                            </div>
                        @endif

                        @if(!empty($projectAmount))
                            <div class="project-badge">
                                {{ number_format($projectAmount, 2) }} ر.س
                            </div>
                        @endif
                    </div>

                    <div class="project-body">
                        <h2 class="project-title">{{ $title }}</h2>

                        <div class="project-meta">
                            @if(!empty($projectAmount))
                                <div class="project-meta-item">
                                    <span class="project-meta-label">مبلغ المشروع</span>
                                    <span class="project-meta-value">{{ number_format($projectAmount, 2) }} ر.س</span>
                                </div>
                            @endif

                            @if(!empty($startDate))
                                <div class="project-meta-item">
                                    <span class="project-meta-label">تاريخ البداية</span>
                                    <span class="project-meta-value">
                                        {{ \Illuminate\Support\Carbon::parse($startDate)->format('Y-m-d') }}
                                    </span>
                                </div>
                            @endif

                            @if(!empty($endDate))
                                <div class="project-meta-item">
                                    <span class="project-meta-label">تاريخ النهاية</span>
                                    <span class="project-meta-value">
                                        {{ \Illuminate\Support\Carbon::parse($endDate)->format('Y-m-d') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="project-actions">
                            <a href="{{ url('/projects/' . $project->id) }}" class="project-btn">
                                تفاصيل المشروع
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="projects-empty">
            لا توجد مشاريع مضافة حاليًا.
        </div>
    @endif
</div>
@endsection