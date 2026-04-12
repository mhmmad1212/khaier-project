@extends('themes.default.layouts.app')

@section('content')
<div style="background:#f8fafc; min-height:100vh; padding:40px 0;">
    <div class="container" style="max-width:1000px; margin:0 auto; padding:0 16px;">

        <div style="
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:24px;
            overflow:hidden;
            box-shadow:0 12px 30px rgba(15,23,42,.06);
        ">
            @if(!empty($page->featuredMedia?->file))
                <div>
                    <img
                        src="{{ \App\Support\Media\MediaUrl::forDiskPath('public', ltrim($page->featuredMedia->url, '/')) }}"
                        alt="{{ $page->title }}"
                        style="width:100%; max-height:380px; object-fit:cover; display:block;"
                    >
                </div>
            @endif

            <div style="padding:28px;">
                <div style="font-size:13px; color:#6b7280; margin-bottom:8px;">
                    صفحة داخلية
                </div>

                <h1 style="
                    margin:0 0 18px;
                    font-size:34px;
                    font-weight:800;
                    color:#111827;
                    line-height:1.5;
                ">
                    {{ $page->title }}
                </h1>

                @if(!empty($page->content))
                    <div style="
                        color:#374151;
                        font-size:16px;
                        line-height:2;
                    ">
                        {!! $page->content !!}
                    </div>
                @elseif(!empty($renderedRawHtml))
                    <div>
                        {!! $renderedRawHtml !!}
                    </div>
                @elseif(!empty($page->raw_html))
                    <div>
                        {!! $page->raw_html !!}
                    </div>
                @else
                    <div style="color:#9ca3af; font-size:15px;">
                        لا يوجد محتوى متاح لهذه الصفحة حالياً.
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
