@extends('themes.default.layouts.app')

@section('content')
<div class="container" style="padding:40px 20px;">
    <h1 style="font-size:32px;font-weight:800;margin-bottom:20px;">
        {{ $page->title ?? 'الإفصاح' }}
    </h1>

    @if(isset($items) && $items->count())
        <div style="display:grid;gap:16px;">
            @foreach($items as $item)
                <div style="border:1px solid #e5e7eb;border-radius:14px;padding:16px;background:#fff;">
                    <div style="font-size:18px;font-weight:700;">
                        {{ $item->title ?? $item->name ?? ('عنصر #' . $item->id) }}
                    </div>

                    @if(!empty($item->description))
                        <div style="margin-top:8px;color:#6b7280;">{!! $item->description !!}</div>
                    @elseif(!empty($item->content))
                        <div style="margin-top:8px;color:#6b7280;">{!! $item->content !!}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div style="padding:20px;border:1px solid #e5e7eb;border-radius:12px;color:#6b7280;">
            لا توجد عناصر إفصاح متاحة حالياً.
        </div>
    @endif
</div>
@endsection
