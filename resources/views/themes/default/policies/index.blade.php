@extends('themes.default.layouts.app')

@section('content')
<div class="container" style="padding:40px 20px;">
    <h1 style="font-size:32px;font-weight:800;margin-bottom:20px;">السياسات</h1>

    <div style="margin-bottom:20px;padding:12px 16px;background:#f3f4f6;border-radius:12px;">
        items_count = {{ isset($items) ? $items->count() : 'items_not_passed' }}
    </div>

    @if(isset($items) && $items->count())
        <div style="display:grid;gap:16px;">
            @foreach($items as $item)
                <div style="border:1px solid #e5e7eb;border-radius:14px;padding:16px;background:#fff;">
                    <div style="font-size:18px;font-weight:700;">{{ $item->title }}</div>

                    @if(!empty($item->description))
                        <div style="margin-top:8px;color:#6b7280;">{{ $item->description }}</div>
                    @endif

                    @if(!empty($item->fileMedia) && !empty($item->fileMedia->file))
                        <div style="margin-top:12px;">
                            <a href="{{ asset('storage/' . $item->fileMedia->file) }}" target="_blank" style="background:#127962;color:#fff;padding:10px 16px;border-radius:10px;text-decoration:none;font-weight:600;">
                                عرض الملف
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div style="padding:20px;border:1px solid #e5e7eb;border-radius:12px;color:#6b7280;">
            لا توجد سياسات أو أن المتغير items لم يُمرر للقالب.
        </div>
    @endif
</div>
@endsection
