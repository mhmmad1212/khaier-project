@extends('themes.default.layouts.app')

@section('title', 'مجلس الإدارة - ' . $association->name)

@push('styles')
<style>
    .board-wrap{
        background:linear-gradient(180deg,#f4f8f7 0%, #ffffff 260px);
        padding:50px 0 80px;
    }

    .board-hero{
        background:linear-gradient(135deg,#127962,#0d5948);
        border-radius:30px;
        padding:38px;
        color:#fff;
        margin-bottom:30px;
        box-shadow:0 24px 50px rgba(15,23,42,.13);
    }

    .board-title{
        font-size:2.2rem;
        font-weight:800;
        margin:0 0 10px;
    }

    .board-subtitle{
        color:rgba(255,255,255,.88);
        line-height:2;
        margin:0;
    }

    .board-card{
        background:#fff;
        border-radius:24px;
        border:1px solid rgba(18,121,98,.08);
        box-shadow:0 18px 40px rgba(15,23,42,.08);
        padding:26px;
        text-align:center;
        height:100%;
        transition:.25s ease;
    }

    .board-card:hover{
        transform:translateY(-6px);
        box-shadow:0 24px 52px rgba(15,23,42,.12);
    }

    .board-photo{
        width:110px;
        height:110px;
        border-radius:50%;
        margin:0 auto 16px;
        overflow:hidden;
        background:#eef2f7;
        border:4px solid #f8fafc;
        box-shadow:0 8px 24px rgba(15,23,42,.10);
    }

    .board-photo img{
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
    }

    .board-name{
        font-size:1.15rem;
        font-weight:800;
        margin-bottom:6px;
        color:#1f2937;
    }

    .board-position{
        color:#127962;
        font-weight:700;
        margin-bottom:12px;
    }

    .board-bio{
        color:#6b7280;
        line-height:1.9;
        font-size:.95rem;
    }

    .board-meta{
        margin-top:14px;
        display:grid;
        gap:8px;
        font-size:.92rem;
        color:#4b5563;
    }
</style>
@endpush

@section('content')
<section class="board-wrap">
    <div class="container">
        <div class="board-hero">
            <h1 class="board-title">مجلس الإدارة</h1>
            <p class="board-subtitle">
                تعرف على أعضاء مجلس الإدارة والهيكل القيادي في الجمعية.
            </p>
        </div>

        <div class="row g-4">
            @foreach($members as $member)
                @php
                    $photoUrl = null;

                    if (!empty($member->photo_media_id)) {
                        $media = \App\Models\MediaItem::query()->find($member->photo_media_id);
                        if ($media && !empty($media->file) && $media->is_image) {
                            $photoUrl = \App\Support\Media\MediaUrl::forDiskPath('public', $media->file);
                        }
                    }

                    if (!$photoUrl && !empty($member->photo)) {
                        $photoUrl = $member->photo;
                    }
                @endphp

                <div class="col-lg-4 col-md-6">
                    <article class="board-card">
                        <div class="board-photo">
                            @if($photoUrl)
                                <img loading="lazy" decoding="async" src="{{ $photoUrl }}" alt="{{ $member->name }}">
                            @endif
                        </div>

                        <h2 class="board-name">{{ $member->name }}</h2>
                        <div class="board-position">{{ $member->position }}</div>

                        @if(!empty($member->bio))
                            <div class="board-bio">{{ $member->bio }}</div>
                        @endif

                        <div class="board-meta">
                            @if(!empty($member->email))
                                <div>{{ $member->email }}</div>
                            @endif
                            @if(!empty($member->phone))
                                <div>{{ $member->phone }}</div>
                            @endif
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
