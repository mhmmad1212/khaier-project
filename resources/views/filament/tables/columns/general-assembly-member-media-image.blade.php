@php
    $record = $getRecord();
    $media = $record->photoMedia ?? null;
    $url = $media?->url;

    if (! $url && filled($record->photo ?? null)) {
        $url = \Illuminate\Support\Str::startsWith($record->photo, ['http://', 'https://'])
            ? $record->photo
            : \Illuminate\Support\Facades\Storage::disk('public')->url($record->photo);
    }
@endphp

@if($url)
    <img loading="lazy" decoding="async"
        src="{{ $url }}"
        alt="general-assembly-member-media-image"
        style="width:60px;height:60px;object-fit:cover;border-radius:999px;border:1px solid #e5e7eb;"
    >
@endif
