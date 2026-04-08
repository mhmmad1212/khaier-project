@extends('themes.default.layouts.app')

@section('content')
@php
    $imageUrl = null;

    if (!empty($news->featured_image)) {
        $imageUrl = \App\Support\Media\MediaUrl::forDiskPath('public', $news->featured_image);
    } elseif (!empty($news->image)) {
        $imageUrl = \App\Support\Media\MediaUrl::forDiskPath('public', $news->image);
    } elseif (!empty($news->photo)) {
        $imageUrl = \App\Support\Media\MediaUrl::forDiskPath('public', $news->photo);
    } elseif (!empty($news->cover_image)) {
        $imageUrl = \App\Support\Media\MediaUrl::forDiskPath('public', $news->cover_image);
    } elseif (method_exists($news, 'featuredMedia') && $news->featuredMedia && !empty($news->featuredMedia->url)) {
        $imageUrl = \App\Support\Media\MediaUrl::forDiskPath('public', $news->featuredMedia->url);
    }

    $publishedAt = $news->published_at ?? $news->created_at;
@endphp

<div class="container py-5">

    <div class="row g-4">
        <div class="col-lg-8">
            <article class="news-single-card bg-white shadow-sm border-0">

                @if($imageUrl)
                    <div class="news-single-image-wrap">
                        <img loading="lazy" decoding="async" src="{{ $imageUrl }}" alt="{{ $news->title }}" class="news-single-image">
                    </div>
                @endif

                <div class="p-4 p-lg-5">
                    <div class="mb-3">
                        <span class="news-single-date">
                            <i class="bi bi-calendar3 ms-1"></i>
                            {{ optional($publishedAt)->format('Y-m-d') }}
                        </span>
                    </div>

                    <h1 class="news-single-title fw-bold mb-4">
                        {{ $news->title }}
                    </h1>

                    @if(!empty($news->excerpt) || !empty($news->summary))
                        <div class="news-single-summary mb-4">
                            {{ $news->excerpt ?? $news->summary }}
                        </div>
                    @endif

                    <div class="news-single-content">
                        {!! $news->content ?? $news->description ?? '' !!}
                    </div>
                </div>
            </article>
        </div>

        <div class="col-lg-4">
            <div class="news-sidebar-card bg-white shadow-sm border-0 p-4 sticky-top" style="top: 110px;">
                <h5 class="fw-bold mb-3">روابط سريعة</h5>

                <div class="d-grid gap-2">
                    <a href="{{ url('/news') }}" class="btn btn-outline-success">
                        <i class="bi bi-arrow-right-circle ms-1"></i>
                        العودة للأخبار
                    </a>

                    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer ms-1"></i>
                        طباعة الخبر
                    </button>
                </div>

                <hr class="my-4">

                <h6 class="fw-bold mb-3">مشاركة الخبر</h6>

                <div class="d-flex flex-wrap gap-2">
                    <a href="https://wa.me/?text={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-success btn-sm">
                        واتساب
                    </a>

                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($news->title) }}" target="_blank" class="btn btn-dark btn-sm">
                        X
                    </a>

                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="btn btn-primary btn-sm">
                        فيسبوك
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .news-single-card,
    .news-sidebar-card{
        border-radius: 22px;
    }

    .news-single-image-wrap{
        width: 100%;
        height: 420px;
        overflow: hidden;
        border-top-left-radius: 22px;
        border-top-right-radius: 22px;
        background: #f3f4f6;
    }

    .news-single-image{
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .news-single-title{
        line-height: 1.7;
        font-size: 2rem;
        color: #111827;
    }

    .news-single-summary{
        background: #f8fafc;
        border-right: 4px solid #198754;
        padding: 16px 18px;
        border-radius: 14px;
        color: #4b5563;
        line-height: 1.9;
        font-size: 1rem;
    }

    .news-single-date{
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: .9rem;
        font-weight: 600;
    }

    .news-single-content{
        line-height: 2;
        color: #374151;
        font-size: 1.02rem;
    }

    .news-single-content p{
        margin-bottom: 1.2rem;
    }

    .news-single-content img{
        max-width: 100%;
        height: auto;
        border-radius: 16px;
        margin: 18px 0;
    }

    .news-single-content h2,
    .news-single-content h3,
    .news-single-content h4{
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-weight: 700;
        color: #111827;
    }

    @media (max-width: 991.98px){
        .news-single-image-wrap{
            height: 280px;
        }

        .news-single-title{
            font-size: 1.55rem;
        }
    }

    @media (max-width: 767.98px){
        .news-sidebar-card{
            position: static !important;
        }

        .news-single-image-wrap{
            height: 220px;
        }

        .news-single-title{
            font-size: 1.35rem;
        }

        .news-single-content{
            font-size: .98rem;
        }
    }
</style>
@endsection
