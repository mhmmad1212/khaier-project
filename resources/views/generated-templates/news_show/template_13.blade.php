@extends('themes.default.layouts.app')

@section('content')
@php
    $buttonColor = $siteSettings->button_color
        ?? $siteSettings->primary_color
        ?? '#2ea36b';

    $newsItem = $item ?? $news ?? null;

    $title = $newsItem->title ?? 'بدون عنوان';
    $content = $newsItem->content ?? $newsItem->description ?? null;
    $publishedAt = $newsItem->published_at ?? $newsItem->created_at ?? null;

    $image = $newsItem->image
        ?? ($newsItem->featuredMedia->file ?? null)
        ?? ($newsItem->featured_media?->file ?? null)
        ?? null;

    $currentUrl = url()->current();
    $encodedUrl = urlencode($currentUrl);
    $encodedTitle = urlencode($title);
@endphp

<div style="direction: rtl; text-align: right; max-width: 1100px; margin: 40px auto; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; box-sizing: border-box;">

    <style>
        .news-show-header {
            margin-bottom: 28px;
            border-bottom: 2px solid {{ $buttonColor }};
            padding-bottom: 16px;
        }

        .news-show-title {
            font-size: 34px;
            font-weight: 800;
            color: #111827;
            margin: 0 0 10px;
            line-height: 1.7;
        }

        .news-show-meta {
            color: #6b7280;
            font-size: 14px;
        }

        .news-show-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .news-show-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .news-show-image-wrap {
            background: #f3f4f6;
            max-height: 460px;
            overflow: hidden;
        }

        .news-show-image {
            width: 100%;
            max-height: 460px;
            object-fit: cover;
            display: block;
        }

        .news-show-content {
            padding: 28px 24px;
            font-size: 16px;
            line-height: 2.1;
            color: #374151;
        }

        .news-show-content p {
            margin-top: 0;
            margin-bottom: 18px;
        }

        .news-share-box {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #eef2f7;
        }

        .news-share-title {
            font-size: 16px;
            font-weight: 800;
            color: #111827;
            margin: 0 0 14px;
        }

        .news-share-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .news-share-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 16px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: opacity .2s ease, transform .2s ease;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .news-share-btn:hover {
            opacity: .92;
            transform: translateY(-2px);
        }

        .news-share-btn-primary {
            background: {{ $buttonColor }};
            color: #fff;
            border-color: {{ $buttonColor }};
        }

        .news-share-btn-light {
            background: #fff;
            color: {{ $buttonColor }};
            border-color: {{ $buttonColor }};
        }

        @media (max-width: 768px) {
            .news-show-title {
                font-size: 26px;
            }

            .news-show-content {
                padding: 22px 18px;
                font-size: 15px;
            }

            .news-share-actions {
                flex-direction: column;
            }

            .news-share-btn {
                width: 100%;
            }
        }
    </style>

    <div class="news-show-header">
        <h1 class="news-show-title">{{ $title }}</h1>

        @if(!empty($publishedAt))
            <div class="news-show-meta">
                تاريخ النشر: {{ \Illuminate\Support\Carbon::parse($publishedAt)->format('Y-m-d') }}
            </div>
        @endif
    </div>

    <div class="news-show-layout">
        <div class="news-show-card">
            @if(!empty($image))
                <div class="news-show-image-wrap">
                    <img
                        src="{{ asset('storage/' . ltrim($image, '/')) }}"
                        alt="{{ $title }}"
                        class="news-show-image"
                    >
                </div>
            @endif

            <div class="news-show-content">
                {!! $content ?: 'لا يوجد محتوى لهذا الخبر حاليًا.' !!}

                <div class="news-share-box">
                    <div class="news-share-title">مشاركة الخبر</div>

                    <div class="news-share-actions">
                        <a href="https://wa.me/?text={{ urlencode($title . ' - ' . $currentUrl) }}"
                           target="_blank"
                           class="news-share-btn news-share-btn-primary">
                            مشاركة واتساب
                        </a>

                        <a href="https://twitter.com/intent/tweet?text={{ $encodedTitle }}&url={{ $encodedUrl }}"
                           target="_blank"
                           class="news-share-btn news-share-btn-light">
                            مشاركة في إكس
                        </a>

                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}"
                           target="_blank"
                           class="news-share-btn news-share-btn-light">
                            مشاركة في فيسبوك
                        </a>

                        <button type="button"
                                onclick="navigator.clipboard.writeText('{{ $currentUrl }}'); this.innerText='تم نسخ الرابط';"
                                class="news-share-btn news-share-btn-light">
                            نسخ الرابط
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection