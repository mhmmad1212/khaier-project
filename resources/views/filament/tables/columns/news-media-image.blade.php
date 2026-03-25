@php
    $media = $getRecord()->imageMedia;
@endphp

@if($media && $media->file)
    @if($media->is_image)
        <img loading="lazy" decoding="async"
            src="{{ asset('storage/' . $media->file) }}"
            alt="news-media-image"
            style="width:60px;height:60px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb;"
        >
    @else
        <div style="width:60px;height:60px;border-radius:10px;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;background:#f8fafc;font-weight:700;color:#dc2626;">
            PDF
        </div>
    @endif
@endif
