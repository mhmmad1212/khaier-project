@php
    $items = \App\Models\MediaItem::query()
        ->where('is_active', true)
        ->where('is_image', true)
        ->orderByDesc('id')
        ->limit(12)
        ->get();

    $statePath = $getStatePath();
@endphp

@if($items->count())
    <div
        x-data="{
            selected: $wire.entangle('{{ $statePath }}')
        }"
        style="margin-top:8px;"
    >
        <div style="font-weight:700;margin-bottom:10px;">آخر الوسائط المضافة</div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:12px;">
            @foreach($items as $item)
                <button
                    type="button"
                    x-on:click="selected = {{ $item->id }}"
                    x-bind:style="selected == {{ $item->id }}
                        ? 'border:2px solid #127962; box-shadow:0 0 0 3px rgba(18,121,98,.12); background:#eef7f4;'
                        : 'border:1px solid #e5e7eb; background:#fff;'"
                    style="border-radius:12px;padding:8px;text-align:right;cursor:pointer;"
                >
                    <img loading="lazy" decoding="async"
                        src="{{ asset('storage/' . $item->file) }}"
                        alt="{{ $item->title }}"
                        style="width:100%;height:90px;object-fit:cover;border-radius:8px;border:1px solid #eee;display:block;margin-bottom:8px;"
                    >
                    <div style="font-size:12px;font-weight:700;line-height:1.5;">#{{ $item->id }}</div>
                    <div style="font-size:11px;color:#6b7280;line-height:1.5;word-break:break-word;">
                        {{ $item->title ?: basename($item->file) }}
                    </div>
                </button>
            @endforeach
        </div>

        <div style="margin-top:10px;font-size:12px;color:#6b7280;">
            اضغط على أي صورة لاختيارها مباشرة كصورة رمزية للخبر.
        </div>
    </div>
@endif
