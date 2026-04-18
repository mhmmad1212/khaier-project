@extends('themes.default.layouts.app')

@section('content')
@php
    $connection = \Illuminate\Support\Facades\DB::connection('tenant');

    $settings = $connection->table('site_settings')->orderByDesc('id')->first();

    $buttonColor = $settings->button_color
        ?? $settings->primary_color
        ?? '#2ea36b';

    if (!function_exists('resolveMediaUrlForPath')) {
        function resolveMediaUrlForPath($path, $disk = 'public')
        {
            if (empty($path)) {
                return null;
            }

            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }

            return \App\Support\Media\MediaUrl::forDiskPath($disk, $path);
        }
    }

    if (!function_exists('resolveMediaUrlFromMediaId')) {
        function resolveMediaUrlFromMediaId($mediaId, $fallbackPath = null, $fallbackDisk = 'public')
        {
            if (!empty($mediaId)) {
                try {
                    $media = \App\Models\MediaItem::query()->find($mediaId);

                    if ($media) {
                        if (!empty($media->url)) {
                            return $media->url;
                        }

                        if (!empty($media->file)) {
                            return \App\Support\Media\MediaUrl::forDiskPath($media->disk ?: $fallbackDisk, $media->file);
                        }
                    }
                } catch (\Throwable $e) {
                    //
                }
            }

            return resolveMediaUrlForPath($fallbackPath, $fallbackDisk);
        }
    }

    $projectsList = collect();

    try {
        $projectsList = $connection->table('program_projects')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'asc')
            ->orderByDesc('id')
            ->get()
            ->map(function ($project) {
                $project->final_cover_url = resolveMediaUrlFromMediaId(
                    $project->cover_image_media_id ?? null,
                    $project->cover_image ?? null,
                    'public'
                );

                return $project;
            });
    } catch (\Throwable $e) {
        $projectsList = collect();
    }
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
            display: flex;
            flex-direction: column;
            height: 100%;
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

        .project-body {
            padding: 20px 18px 18px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .project-title {
            font-size: 20px;
            font-weight: 800;
            color: #1f2937;
            margin: 0 0 12px;
            line-height: 1.7;
            min-height: 68px;
        }

        .project-description {
            font-size: 14px;
            color: #6b7280;
            line-height: 2;
            margin-bottom: 18px;
            flex-grow: 1;
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
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: auto;
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

        .project-btn-secondary {
            background: #ffffff;
            color: {{ $buttonColor }};
            border: 1px solid {{ $buttonColor }};
        }

        .project-btn-secondary:hover {
            background: #f8fafc;
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
                    $title = $project->title ?? 'مشروع خيري';
                    $description = $project->description ?? 'مشروع خيري يهدف لخدمة المجتمع ودعم الفئات المستحقة.';
                    $image = $project->final_cover_url ?? null;
                    $startDate = $project->start_date ?? null;
                    $endDate = $project->end_date ?? null;
                    $donationUrl = $project->donation_url ?? null;
                    $detailsUrl = url('/projects/' . ($project->id ?? '#'));
                @endphp

                <div class="project-card">
                    <div class="project-image-wrap">
                        @if(!empty($image))
                            <img
                                src="{{ $image }}"
                                alt="{{ $title }}"
                                class="project-image"
                            >
                        @else
                            <div class="project-image-placeholder">
                                لا توجد صورة للمشروع
                            </div>
                        @endif
                    </div>

                    <div class="project-body">
                        <h2 class="project-title">{{ $title }}</h2>

                        <div class="project-description">
                            {{ \Illuminate\Support\Str::limit(strip_tags($description), 140) }}
                        </div>

                        <div class="project-meta">
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
                            <a href="{{ $detailsUrl }}" class="project-btn">
                                تفاصيل المشروع
                            </a>

                            @if(!empty($donationUrl))
                                <a href="{{ $donationUrl }}" class="project-btn project-btn-secondary">
                                    تبرع الآن
                                </a>
                            @endif
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