@extends('themes.default.layouts.app')

@section('content')
<div style="direction: rtl; text-align: right; max-width: 1200px; margin: 40px auto; padding: 20px;">

    <div style="margin-bottom: 40px; border-bottom: 2px solid #e5e7eb; padding-bottom: 15px;">
        <h1 style="font-size: 30px; font-weight: 800; color: #1f2937;">
            مجلس الإدارة
        </h1>
    </div>

    @php
        $buttonColor = $siteSettings->button_color ?? '#16a34a';
    @endphp

    @if(isset($items) && $items->count())
        <div style="
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
        ">

            @foreach($items as $member)
                <div style="
                    background: #fff;
                    border: 1px solid #e5e7eb;
                    border-radius: 16px;
                    padding: 25px 20px;
                    text-align: center;
                    transition: all 0.3s ease;
                "
                onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 10px 25px rgba(0,0,0,0.08)'"
                onmouseout="this.style.transform='none';this.style.boxShadow='none'"
                >

                    {{-- الصورة --}}
                    <div style="margin-bottom: 15px;">
                        @if(!empty($member->imageMedia) && !empty($member->imageMedia->file))
                            <img src="{{ asset('storage/' . $member->imageMedia->file) }}"
                                 style="
                                    width: 110px;
                                    height: 110px;
                                    object-fit: cover;
                                    border-radius: 50%;
                                    border: 4px solid #f3f4f6;
                                 ">
                        @else
                            <div style="
                                width: 110px;
                                height: 110px;
                                border-radius: 50%;
                                background: #e5e7eb;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 30px;
                                color: #9ca3af;
                                margin: auto;
                            ">
                                👤
                            </div>
                        @endif
                    </div>

                    {{-- الاسم --}}
                    <div style="
                        font-size: 18px;
                        font-weight: 700;
                        color: #111827;
                        margin-bottom: 6px;
                    ">
                        {{ $member->name ?? 'بدون اسم' }}
                    </div>

                    {{-- المنصب --}}
                    @if(!empty($member->position))
                        <div style="
                            font-size: 14px;
                            color: #6b7280;
                            margin-bottom: 12px;
                        ">
                            {{ $member->position }}
                        </div>
                    @endif

                    {{-- الوصف --}}
                    @if(!empty($member->bio))
                        <div style="
                            font-size: 13px;
                            color: #6b7280;
                            line-height: 1.6;
                            margin-bottom: 15px;
                        ">
                            {{ $member->bio }}
                        </div>
                    @endif

                    {{-- وسائل التواصل --}}
                    <div style="display: flex; justify-content: center; gap: 10px;">

                        @if(!empty($member->email))
                            <a href="mailto:{{ $member->email }}"
                               style="
                                background: #f3f4f6;
                                padding: 8px 14px;
                                border-radius: 8px;
                                font-size: 13px;
                                text-decoration: none;
                                color: #374151;
                               ">
                                ✉️ إيميل
                            </a>
                        @endif

                        @if(!empty($member->phone))
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $member->phone) }}"
                               target="_blank"
                               style="
                                background: {{ $buttonColor }};
                                padding: 8px 14px;
                                border-radius: 8px;
                                font-size: 13px;
                                text-decoration: none;
                                color: #fff;
                               ">
                                واتساب
                            </a>
                        @endif

                    </div>

                </div>
            @endforeach

        </div>
    @else
        <div style="
            text-align: center;
            padding: 60px;
            border: 2px dashed #e5e7eb;
            border-radius: 12px;
            color: #9ca3af;
        ">
            لا يوجد أعضاء مجلس إدارة
        </div>
    @endif

</div>
@endsection