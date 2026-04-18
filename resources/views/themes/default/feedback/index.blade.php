@extends('themes.default.layouts.app')

@section('content')
<div class="container" style="padding:40px 20px;">
    <h1 style="font-size:32px;font-weight:800;margin-bottom:20px;">{{ $page->title ?? 'التغذية الراجعة' }}</h1>

    @if(isset($items) && $items->count())
        <div style="display:grid;gap:16px;">
            @foreach($items as $item)
                <div style="border:1px solid #e5e7eb;border-radius:14px;padding:16px;background:#fff;">
                    <div style="font-size:18px;font-weight:700;">{{ $item->title }}</div>

                    @if(!empty($item->description))
                        <div style="margin-top:8px;color:#6b7280;">{{ $item->description }}</div>
                    @endif

                    @if(!empty($item->fileMedia))
                        @php
                            $fileUrl = null;
                            if (!empty($item->fileMedia->url)) {
                                $fileUrl = $item->fileMedia->url;
                            } elseif (!empty($item->fileMedia->file)) {
                                $fileUrl = \App\Support\Media\MediaUrl::forDiskPath($item->fileMedia->disk ?? 'public', $item->fileMedia->file);
                            }
                        @endphp

                        @if($fileUrl)
                            <div style="margin-top:12px;">
                                <a href="{{ $fileUrl }}" target="_blank" style="background:#127962;color:#fff;padding:10px 16px;border-radius:10px;text-decoration:none;font-weight:600;">
                                    عرض المرفق
                                </a>
                            </div>
                        @endif
                    @elseif(!empty($item->file))
                        <div style="margin-top:12px;">
                            <a href="{{ str_starts_with($item->file, 'http') ? $item->file : asset('storage/' . ltrim($item->file, '/')) }}" target="_blank" style="background:#127962;color:#fff;padding:10px 16px;border-radius:10px;text-decoration:none;font-weight:600;">
                                عرض المرفق
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div style="padding:20px;border:1px solid #e5e7eb;border-radius:12px;color:#6b7280;">
            لا توجد عناصر تغذية راجعة مضافة حالياً.
        </div>
    @endif
</div>
@endsection
