@extends('themes.default.layouts.app')

@section('content')
<div class="container py-5">

    <div class="mb-5 text-center">
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 mb-3">المركز الإعلامي</span>
        <h1 class="fw-bold mb-3">الأخبار</h1>
        <p class="text-muted mx-auto" style="max-width: 720px;">
            تابع آخر أخبار الجمعية والمبادرات والأنشطة والبرامج والتحديثات الرسمية.
        </p>
    </div>

    @if($news->count())
        <div class="row g-4">
            @foreach($news as $item)
                @php
                    $imageUrl = null;

                    if (!empty($item->featured_image)) {
                        $imageUrl = \App\Support\Media\MediaUrl::forDiskPath('public', $item->featured_image);
                    } elseif (!empty($item->image)) {
                        $imageUrl = \App\Support\Media\MediaUrl::forDiskPath('public', $item->image);
                    } elseif (!empty($item->photo)) {
                        $imageUrl = \App\Support\Media\MediaUrl::forDiskPath('public', $item->photo);
                    } elseif (!empty($item->cover_image)) {
                        $imageUrl = \App\Support\Media\MediaUrl::forDiskPath('public', $item->cover_image);
                    } elseif (method_exists($item, 'featuredMedia') && $item->featuredMedia && !empty($item->featuredMedia->url)) {
                        $imageUrl = \App\Support\Media\MediaUrl::forDiskPath('public', $item->featuredMedia->url);
                    }
                @endphp

                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 border-0 shadow-sm overflow-hidden news-card">
                        <div class="news-card-image-wrap">
                            @if($imageUrl)
                                <img loading="lazy" decoding="async" src="{{ $imageUrl }}" alt="{{ $item->title }}" class="news-card-image">
                            @else
                                <div class="news-card-placeholder d-flex align-items-center justify-content-center">
                                    <i class="bi bi-newspaper fs-1 text-success"></i>
                                </div>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column p-4">
                            <div class="mb-2">
                                <span class="news-date">
                                    <i class="bi bi-calendar3 ms-1"></i>
                                    {{ optional($item->published_at ?? $item->created_at)->format('Y-m-d') }}
                                </span>
                            </div>

                            <h5 class="fw-bold mb-3 news-title">
                                {{ $item->title }}
                            </h5>

                            @php
                                $excerpt = $item->excerpt
                                    ?? $item->summary
                                    ?? \Illuminate\Support\Str::limit(strip_tags($item->content ?? $item->description ?? ''), 140);
                            @endphp

                            @if($excerpt)
                                <p class="text-muted mb-4 news-excerpt">
                                    {{ $excerpt }}
                                </p>
                            @endif

                            <div class="mt-auto">
                                <a href="{{ url('/news/' . ($item->slug ?: $item->id)) }}" class="btn btn-success w-100">
                                    قراءة المزيد
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-light border text-center py-5">
            <div class="mb-3">
                <i class="bi bi-newspaper fs-1 text-muted"></i>
            </div>
            <h5 class="fw-bold">لا توجد أخبار حالياً</h5>
            <p class="text-muted mb-0">سيتم نشر الأخبار والتحديثات هنا قريبًا.</p>
        </div>
    @endif

</div>

<style>
    .news-card{
        border-radius: 20px;
        transition: all .25s ease;
    }

    .news-card:hover{
        transform: translateY(-6px);
        box-shadow: 0 18px 35px rgba(0,0,0,.10) !important;
    }

    .news-card-image-wrap{
        position: relative;
        width: 100%;
        height: 240px;
        overflow: hidden;
        background: #f3f4f6;
    }

    .news-card-image{
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .35s ease;
    }

    .news-card:hover .news-card-image{
        transform: scale(1.05);
    }

    .news-card-placeholder{
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f8fafc, #eef2f7);
    }

    .news-title{
        line-height: 1.7;
        font-size: 1.08rem;
        min-height: 62px;
    }

    .news-excerpt{
        line-height: 1.9;
        font-size: .96rem;
    }

    .news-date{
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: .85rem;
        font-weight: 600;
    }

    @media (max-width: 767.98px){
        .news-card-image-wrap{
            height: 220px;
        }

        .news-title{
            min-height: auto;
        }
    }
</style>
@endsection
