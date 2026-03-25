@php
    $file = $getRecord()->file;
    $isImage = (bool) $getRecord()->is_image;
@endphp

@if($file)
    @if($isImage)
        <img loading="lazy" decoding="async"
            src="{{ asset('storage/' . $file) }}"
            alt="media-preview"
            style="width:64px;height:64px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb;"
        >
    @else
        <div style="width:64px;height:64px;border-radius:10px;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;background:#f8fafc;font-weight:700;color:#dc2626;">
            PDF
        </div>
    @endif
@endif
