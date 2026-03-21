@extends('themes.default.layouts.app')

@section('title', $page->title . ' - ' . $association->name)

@php
    $featuredMedia = null;

    if (!empty($page->featured_media_id)) {
        $featuredMedia = \App\Models\MediaItem::query()->find($page->featured_media_id);
    }
@endphp

@push('styles')
<style>
    .page-wrap{
        background:linear-gradient(180deg,#f4f8f7 0%, #ffffff 280px);
        padding:50px 0 80px;
    }

    .page-hero{
        background:linear-gradient(135deg,#127962,#0d5948);
        border-radius:30px;
        padding:40px;
        color:#fff;
        margin-bottom:30px;
        box-shadow:0 24px 50px rgba(15,23,42,.13);
    }

    .page-title{
        font-size:2.2rem;
        font-weight:800;
        margin:0 0 10px;
        line-height:1.5;
    }

    .page-subtitle{
        color:rgba(255,255,255,.88);
        line-height:2;
        margin:0;
    }

    .page-card{
        background:#fff;
        border-radius:26px;
        border:1px solid rgba(18,121,98,.08);
        box-shadow:0 18px 40px rgba(15,23,42,.08);
        overflow:hidden;
    }

    .page-featured-image{
        width:100%;
        max-height:420px;
        object-fit:cover;
        display:block;
        border-bottom:1px solid #eef2f7;
    }

    .page-body{
        padding:34px;
    }

    .page-content{
        color:#374151;
        line-height:2.1;
        font-size:1.04rem;
    }

    .page-content img{
        max-width:100%;
        height:auto;
        border-radius:14px;
        margin:18px 0;
    }

    .page-content table{
        width:100%;
        border-collapse:collapse;
        margin:20px 0;
    }

    .page-content table th,
    .page-content table td{
        border:1px solid #e5e7eb;
        padding:10px 12px;
    }

    .page-content blockquote{
        border-right:4px solid #127962;
        background:#f8fafc;
        padding:16px 18px;
        border-radius:12px;
        margin:20px 0;
    }

    @media (max-width: 768px){
        .page-hero{
            padding:26px;
            border-radius:22px;
        }

        .page-title{
            font-size:1.7rem;
        }

        .page-body{
            padding:22px;
        }
    }
</style>
@endpush

@section('content')
<section class="page-wrap">
    <div class="container">
        <div class="page-hero">
            <h1 class="page-title">{{ $page->title }}</h1>
            <p class="page-subtitle">
                صفحة تعريفية ضمن الموقع الرسمي للجمعية.
            </p>
        </div>

        <article class="page-card">
            @if($featuredMedia && !empty($featuredMedia->file) && $featuredMedia->is_image)
                <img
                    class="page-featured-image"
                    src="{{ asset('storage/' . $featuredMedia->file) }}"
                    alt="{{ $featuredMedia->alt_text ?: $page->title }}"
                >
            @endif

            <div class="page-body">
                <div class="page-content">
                    {!! $page->content !!}
                </div>
            </div>
        </article>
    </div>
</section>
@endsection
