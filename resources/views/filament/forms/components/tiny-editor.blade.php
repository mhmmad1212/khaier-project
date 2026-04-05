@php
    $mediaItems = \App\Models\MediaItem::query()
        ->where('is_active', true)
        ->orderByDesc('id')
        ->limit(300)
        ->get()
        ->map(fn ($item) => [
            'id' => $item->id,
            'title' => $item->title ?: basename((string) $item->file),
            'file' => $item->file,
            'url' => asset('storage/' . ltrim((string) $item->file, '/')),
            'alt_text' => $item->alt_text,
            'is_image' => (bool) ($item->is_image ?? false),
            'mime_type' => $item->mime_type ?? null,
            'extension' => strtolower(pathinfo((string) $item->file, PATHINFO_EXTENSION)),
        ])
        ->values()
        ->toJson();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        data-tiny-editor-wrapper
        x-data
        x-init="
            window.tinyEditorMediaItems = {{ $mediaItems }};
            setTimeout(() => window.initTinyEditor($el), 100)
        "
        wire:key="tiny-{{ $getStatePath() }}"
    >
        <textarea
            class="tiny-source-textarea"
            wire:model.defer="{{ $getStatePath() }}"
            rows="16"
            style="width:100%;min-height:420px;"
        >{{ $getState() }}</textarea>
    </div>
</x-dynamic-component>
