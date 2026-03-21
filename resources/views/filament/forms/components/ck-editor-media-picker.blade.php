@php
    $mediaItems = \App\Models\MediaItem::query()
        ->where('is_active', true)
        ->where('is_image', true)
        ->orderByDesc('id')
        ->limit(100)
        ->get()
        ->map(fn ($item) => [
            'id' => $item->id,
            'title' => $item->title ?: basename((string) $item->file),
            'file' => $item->file,
            'url' => asset('storage/' . $item->file),
            'alt_text' => $item->alt_text,
        ])
        ->values()
        ->toJson();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        data-ckeditor-wrapper
        x-data
        x-init="
            window.ckEditorMediaItems = {{ $mediaItems }};
            setTimeout(() => window.initCkEditorMediaPicker($el), 100)
        "
        wire:key="ckeditor-{{ $getStatePath() }}"
    >
        <textarea
            class="ck-source-textarea"
            wire:model.defer="{{ $getStatePath() }}"
            rows="16"
            style="display:none;width:100%;min-height:420px;"
        >{{ $getState() }}</textarea>
    </div>
</x-dynamic-component>
