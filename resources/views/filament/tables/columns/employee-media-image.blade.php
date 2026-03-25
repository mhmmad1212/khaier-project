@php
    $media = $getRecord()->photoMedia;
@endphp

@if($media && $media->file && $media->is_image)
    <img loading="lazy" decoding="async"
        src="{{ asset('storage/' . $media->file) }}"
        alt="employee-media-image"
        style="width:60px;height:60px;object-fit:cover;border-radius:999px;border:1px solid #e5e7eb;"
    >
@elseif(!empty($getRecord()->photo))
    <img loading="lazy" decoding="async"
        src="{{ $getRecord()->photo }}"
        alt="employee-legacy-image"
        style="width:60px;height:60px;object-fit:cover;border-radius:999px;border:1px solid #e5e7eb;"
    >
@endif
