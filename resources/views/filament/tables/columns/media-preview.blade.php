@php
    /** @var \App\Models\MediaItem $record */
    $record = $getRecord();
    $file = ltrim((string) ($record->file ?? ''), '/');
    $url = $file ? asset('storage/' . $file) : null;
    $ext = strtoupper((string) ($record->extension ?: pathinfo($file, PATHINFO_EXTENSION) ?: 'FILE'));
    $isImage = (bool) ($record->is_image ?? false);
@endphp

<div style="width:72px;height:72px;display:flex;align-items:center;justify-content:center;">
    @if($isImage && $url)
        <img
            src="{{ $url }}"
            alt="{{ $record->alt_text ?: $record->title }}"
            style="width:72px;height:72px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb;background:#fff;"
        >
    @else
        <div style="width:72px;height:72px;border-radius:10px;border:1px solid #e5e7eb;background:#f8fafc;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:12px;color:#dc2626;">
            {{ $ext }}
        </div>
    @endif
</div>
