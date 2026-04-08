@extends('themes.default.layouts.app')

@section('content')
<div style="background:#f7faf9; min-height:100vh; padding:40px 0;">
    <div class="container" style="max-width:1100px; margin:0 auto; padding:0 16px;">

        <div style="
            background: linear-gradient(135deg, {{ $siteSettings->primary_color ?? '#127962' }} 0%, {{ $siteSettings->secondary_color ?? '#0f5c4d' }} 100%);
            border-radius:24px;
            padding:36px 28px;
            color:#fff;
            margin-bottom:28px;
            box-shadow:0 10px 30px rgba(0,0,0,.08);
        ">
            <div style="font-size:14px; opacity:.9; margin-bottom:8px;">الصفحات النظامية</div>
            <h1 style="margin:0; font-size:32px; font-weight:800;">
                {{ $page->title ?? 'خطط الجمعية' }}
            </h1>

            @if(!empty($page->content))
                <div style="margin-top:14px; font-size:15px; line-height:1.9; opacity:.95;">
                    {!! $page->content !!}
                </div>
            @else
                <p style="margin:14px 0 0; font-size:15px; line-height:1.9; opacity:.95;">
                    استعراض خطط الجمعية المعتمدة ومحتواها بشكل منظم وواضح.
                </p>
            @endif
        </div>

        @if(isset($items) && $items->count())
            <div style="
                display:grid;
                grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));
                gap:20px;
            ">
                @foreach($items as $item)
                    <div style="
                        background:#fff;
                        border:1px solid #e5e7eb;
                        border-radius:20px;
                        overflow:hidden;
                        box-shadow:0 8px 24px rgba(0,0,0,.05);
                        display:flex;
                        flex-direction:column;
                    ">
                        <div style="
                            padding:18px 20px;
                            border-bottom:1px solid #f1f5f9;
                            background:#fcfdfd;
                        ">
                            <div style="
                                display:inline-flex;
                                align-items:center;
                                gap:8px;
                                font-size:12px;
                                color:{{ $siteSettings->primary_color ?? '#127962' }};
                                background:rgba(18,121,98,.08);
                                border-radius:999px;
                                padding:6px 12px;
                                margin-bottom:12px;
                                font-weight:700;
                            ">
                                خطة الجمعية
                            </div>

                            <h2 style="
                                margin:0;
                                font-size:20px;
                                font-weight:800;
                                color:#111827;
                                line-height:1.7;
                            ">
                                {{ $item->title ?? $item->name ?? ('خطة #' . $item->id) }}
                            </h2>
                        </div>

                        <div style="padding:20px; flex:1;">
                            @if(!empty($item->description))
                                <div style="
                                    color:#6b7280;
                                    font-size:14px;
                                    line-height:2;
                                ">
                                    {!! $item->description !!}
                                </div>
                            @elseif(!empty($item->content))
                                <div style="
                                    color:#6b7280;
                                    font-size:14px;
                                    line-height:2;
                                ">
                                    {!! \Illuminate\Support\Str::limit(strip_tags($item->content), 280) !!}
                                </div>
                            @else
                                <div style="
                                    color:#9ca3af;
                                    font-size:14px;
                                    line-height:1.9;
                                ">
                                    لا يوجد وصف متاح لهذه الخطة حالياً.
                                </div>
                            @endif
                        </div>

                        <div style="
                            padding:18px 20px;
                            border-top:1px solid #f1f5f9;
                            display:flex;
                            align-items:center;
                            justify-content:space-between;
                            gap:12px;
                        ">
                            <div style="font-size:12px; color:#9ca3af;">
                                رقم العنصر: {{ $item->id }}
                            </div>

                            @if(!empty($item->file) || !empty($item->file_url))
                                <a href="{{ $item->file_url ?? \App\Support\Media\MediaUrl::forDiskPath('public', ltrim($item->file, '/')) }}"
                                   target="_blank"
                                   style="
                                        display:inline-flex;
                                        align-items:center;
                                        justify-content:center;
                                        background:{{ $siteSettings->button_color ?? ($siteSettings->primary_color ?? '#127962') }};
                                        color:#fff;
                                        text-decoration:none;
                                        font-size:14px;
                                        font-weight:700;
                                        border-radius:12px;
                                        padding:10px 16px;
                                   ">
                                    عرض الخطة
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="
                background:#fff;
                border:1px solid #e5e7eb;
                border-radius:20px;
                padding:28px;
                text-align:center;
                color:#6b7280;
                box-shadow:0 8px 24px rgba(0,0,0,.04);
            ">
                لا توجد خطط جمعية متاحة حالياً.
            </div>
        @endif
    </div>
</div>
@endsection