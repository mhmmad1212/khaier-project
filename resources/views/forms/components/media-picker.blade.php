@php
    $statePath = $getStatePath();
    $media = null;

    if ($getState()) {
        $media = \App\Models\MediaItem::query()->find($getState());
    }
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{ selected: @entangle($statePath) }"
        x-init="
            window.addEventListener('media-library-selected', (event) => {
                if (event.detail?.statePath !== '{{ $statePath }}') return;
                selected = event.detail.item.id;
            });
        "
    >
        <input type="hidden" wire:model.defer="{{ $statePath }}">

        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <button
                type="button"
                x-on:click="window.dispatchEvent(new CustomEvent('open-media-library',{ detail:{ state:'{{ $statePath }}' } }))"
                style="background:#127962;color:#fff;border:none;padding:10px 16px;border-radius:10px;cursor:pointer;font-weight:600;"
            >
                اختيار/رفع وسائط
            </button>

            <button
                type="button"
                x-show="selected"
                x-on:click="selected = null"
                style="background:#eef2f7;color:#111827;border:none;padding:10px 16px;border-radius:10px;cursor:pointer;font-weight:600;"
            >
                إزالة الاختيار
            </button>
        </div>

        @if($media)
            <div style="margin-top:14px;padding:14px;border:1px solid #e5e7eb;border-radius:14px;background:#fafafa;">
                @if($media->is_image)
                    <img
                        src="{{ asset('storage/' . $media->file) }}"
                        style="width:130px;height:130px;object-fit:cover;border-radius:12px;border:1px solid #ddd;"
                        alt="{{ $media->alt_text ?: $media->title }}"
                    >
                @else
                    <div style="width:130px;height:130px;display:flex;align-items:center;justify-content:center;border-radius:12px;border:1px solid #ddd;background:#fff;color:#dc2626;font-weight:800;font-size:28px;">
                        PDF
                    </div>
                @endif

                <div style="margin-top:10px;font-weight:700;">
                    {{ $media->title ?: basename($media->file) }}
                </div>

                <div style="margin-top:4px;font-size:13px;color:#6b7280;">
                    #{{ $media->id }} — {{ strtoupper($media->extension ?: 'FILE') }}
                </div>
            </div>
        @elseif($getState())
            <div style="margin-top:14px;padding:12px;border:1px dashed #d1d5db;border-radius:12px;color:#6b7280;">
                الملف المحدد غير متوفر.
            </div>
        @endif
    </div>
</x-dynamic-component>
