@extends('themes.default.layouts.app')

@section('content')
@php
    $executiveDirector = $executiveDirector ?? ($item ?? null);
    $buttonColor = $siteSettings->button_color
        ?? $siteSettings->primary_color
        ?? '#127962';
@endphp

<div style="max-width:1100px; margin:40px auto; padding:0 16px; direction:rtl; text-align:right;">
    <div style="background:linear-gradient(135deg,#065f46 0%,#10b981 100%); border-radius:24px; padding:36px 24px; color:#fff; margin-bottom:28px; box-shadow:0 14px 28px rgba(16,185,129,.20);">
        <h1 style="margin:0 0 10px; font-size:32px; font-weight:800;">
            {{ $page->title ?? 'المدير التنفيذي' }}
        </h1>

        <div style="font-size:16px; opacity:.92; line-height:1.9;">
            {{ $page->excerpt ?? 'تعرف على المدير التنفيذي ووسائل التواصل والنبذة التعريفية.' }}
        </div>
    </div>

    @if($executiveDirector)
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:24px; padding:28px; box-shadow:0 12px 28px rgba(15,23,42,.06);">
            <div style="display:flex; flex-wrap:wrap; gap:14px; align-items:flex-start; justify-content:space-between; margin-bottom:20px;">
                <div>
                    <h2 style="margin:0 0 8px; font-size:28px; font-weight:800; color:#111827;">
                        {{ $executiveDirector->name ?: 'بدون اسم' }}
                    </h2>

                    <div style="display:flex; flex-wrap:wrap; gap:10px;">
                        @if(!empty($executiveDirector->phone))
                            <span style="display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border-radius:999px; background:#f0fdf4; color:#166534; font-size:14px; font-weight:700;">
                                <i class="fas fa-phone"></i>
                                {{ $executiveDirector->phone }}
                            </span>
                        @endif

                        @if(!empty($executiveDirector->email))
                            <span style="display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-size:14px; font-weight:700;">
                                <i class="fas fa-envelope"></i>
                                {{ $executiveDirector->email }}
                            </span>
                        @endif
                    </div>
                </div>

                @if(!empty($executiveDirector->email))
                    <a href="mailto:{{ $executiveDirector->email }}" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:12px 18px; border-radius:12px; background:{{ $buttonColor }}; color:#fff; text-decoration:none; font-weight:700;">
                        <i class="fas fa-paper-plane"></i>
                        مراسلة المدير التنفيذي
                    </a>
                @endif
            </div>

            @if(!empty($executiveDirector->bio))
                <div style="border-top:1px solid #e5e7eb; padding-top:20px; color:#374151; line-height:2;">
                    {!! $executiveDirector->bio !!}
                </div>
            @else
                <div style="color:#6b7280;">
                    لا توجد نبذة مضافة حاليًا.
                </div>
            @endif
        </div>
    @else
        <div style="background:#fff; border:1px dashed #cbd5e1; border-radius:24px; padding:44px 20px; color:#6b7280; text-align:center;">
            لا توجد بيانات للمدير التنفيذي مضافة حاليًا.
        </div>
    @endif
</div>
@endsection