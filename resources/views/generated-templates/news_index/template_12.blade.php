@extends('themes.default.layouts.app')

@section('content')
@php
    $buttonColor = $siteSettings->button_color
        ?? $siteSettings->primary_color
        ?? '#2ea36b';

    $newsList = collect($items ?? ($news ?? []));
@endphp

<div style="direction: rtl; text-align: right; max-width: 1240px; margin: 40px auto; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; box-sizing: border-box;">

    <style>
        .news-header {
            border-bottom: 2px solid {{ $buttonColor }};
            padding-bottom: 15px;
            margin-bottom: 35px;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
            gap: 24px;
        }

        .news-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .news-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.12);
        }

        .news-image-wrap {
            position: relative;
            height: 220px;
            overflow: hidden;
            background: #f3f4f6;
        }

        .news-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .35s ease;
        }

        .news-card:hover .news-image {
            transform: scale(1.05);
        }

        .news-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 15px;
            font-weight: 700;
        }

        .news-date-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            background: rgba(255,255,255,.95);
            color: #111827;
            font-size: 12px;
            font-weight: 800;
            padding: 8px 12px;
            border-radius: 999px;
            box-shadow: 0 4px 14px rgba(0,0,0,.08);
        }

        .news-body {
            padding: 20px 18px 18px;
        }

        .news-title {
            font-size: 20px;
            font-weight: 800;
            color: #1f2937;
            margin: 0 0 12px;
            line-height: 1.8;
            min-height: 72px;
        }

        .news-meta {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .news-excerpt {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.9;
            margin-bottom: 18px;
            min-height: 78px;
        }

        .news-actions {
            margin-top: 10px;
        }

        .news-btn {
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

        .news-btn:hover {
            opacity: .9;
        }

        .news-empty {
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
            .news-image-wrap {
                height: 200px;
            }

            .news-title {
                font-size: 18px;
                min-height: auto;
            }

            .news-excerpt {
                min-height: auto;
            }
        }
    </style>

    <div class="news-header">
        <h1 style="color: #1a4a38; font-size: 30px; font-weight: 800; margin: 0 0 8px;">
            {{ $page->title ?? 'الأخبار' }}
        </h1>

        @if(!empty($page->excerpt))
            <p style="margin: 0; color: #6b7280; font-size: 15px; line-height: 1.8;">
                {{ $page->excerpt }}
            </p>
        @endif
    </div>

    @if($newsList->count())
        <div class="news-grid">
            @foreach($newsList as $item)
                @php
                    $title = $item->title ?? 'بدون عنوان';
                    $image = $item->image
                        ?? ($item->featuredMedia->url ?? null)
                        ?? ($item->featured_media?->file ?? null)
                        ?? null;

                    $publishedAt = $item->published_at ?? $item->created_at ?? null;

                    $excerpt = $item->excerpt
                        ?? $item->summary
                        ?? $item->short_description
                        ?? $item->description
                        ?? strip_tags($item->content ?? '');

                    $slug = $item->slug ?? $item->id;
                @endphp

                <div class="news-card">
                    <div class="news-image-wrap">
                        @if(!empty($image))
                            <img
                                src="{{ \App\Support\Media\MediaUrl::forDiskPath('public', ltrim($image, '/')) }}"
                                alt="{{ $title }}"
                                class="news-image"
                            >
                        @else
                            <div class="news-image-placeholder">
                                لا توجد صورة للخبر
                            </div>
                        @endif

                        @if(!empty($publishedAt))
                            <div class="news-date-badge">
                                {{ \Illuminate\Support\Carbon::parse($publishedAt)->format('Y-m-d') }}
                            </div>
                        @endif
                    </div>

                    <div class="news-body">
                        <h2 class="news-title">{{ $title }}</h2>

                        @if(!empty($publishedAt))
                            <div class="news-meta">
                                تاريخ النشر: {{ \Illuminate\Support\Carbon::parse($publishedAt)->format('Y-m-d') }}
                            </div>
                        @endif

                        <div class="news-excerpt">
                            {{ \Illuminate\Support\Str::limit(strip_tags($excerpt), 140) }}
                        </div>

                        <div class="news-actions">
                            <a href="{{ url('/news/' . $slug) }}" class="news-btn">
                                قراءة المزيد
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="news-empty">
            لا توجد أخبار مضافة حاليًا.
        </div>
    @endif
</div>
@endsection