@php
    $record = $getRecord();
    $media = $record->imageMedia ?? null;
    $url = $media?->url;

    if (! $url && filled($record->image ?? null)) {
        $url = \Illuminate\Support\Str::startsWith($record->image, ['http://', 'https://'])
            ? $record->image
            : \Illuminate\Support\Facades\Storage::disk('public')->url($record->image);
    }
@endphp

@if($url)
    <img loading="lazy" decoding="async"
        src="{{ $url }}"
        alt="{{ $record->title ?? 'news-image' }}"
        style="width:60px;height:60px;object-fit:cover;border-radius:12px;border:1px solid #e5e7eb;"
    >
@endif
