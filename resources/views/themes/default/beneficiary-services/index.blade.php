@extends('themes.default.layouts.app')

@section('content')
@php
    $beneficiaryServices = collect($beneficiaryServices ?? ($items ?? []));
    $buttonColor = $siteSettings->button_color
        ?? $siteSettings->primary_color
        ?? '#127962';
@endphp

<div style="max-width:1200px; margin:40px auto; padding:0 16px; direction:rtl; text-align:right;">
    <div style="margin-bottom:28px;">
        <h1 style="margin:0 0 10px; font-size:32px; font-weight:800; color:#111827;">
            {{ $page->title ?? 'خدمات المستفيدين' }}
        </h1>

        @if(!empty($page->excerpt))
            <div style="color:#6b7280; font-size:16px; line-height:1.9;">
                {{ $page->excerpt }}
            </div>
        @endif
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:24px;">
        @forelse($beneficiaryServices as $service)
            <div style="background:#fff; border:1px solid #e5e7eb; border-radius:22px; padding:24px; box-shadow:0 12px 28px rgba(15,23,42,.06); display:flex; flex-direction:column; gap:18px;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="width:58px; height:58px; border-radius:16px; background:rgba(18,121,98,.08); color:{{ $buttonColor }}; display:flex; align-items:center; justify-content:center;">
                        @if(!empty($service->icon))
                            <x-filament::icon :icon="$service->icon" class="w-8 h-8" />
                        @else
                            <span style="font-size:28px;">🧾</span>
                        @endif
                    </div>
                    <div>
                        <h3 style="margin:0; font-size:22px; font-weight:800; color:#111827;">
                            {{ $service->name }}
                        </h3>
                    </div>
                </div>

                @if(!empty($service->conditions))
                    <div style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:16px; padding:16px; color:#374151; line-height:2;">
                        {!! $service->conditions !!}
                    </div>
                @endif

                <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:auto;">
                    @if(!empty($service->guide_url))
                        <a href="{{ $service->guide_url }}" target="_blank" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:12px 16px; border-radius:12px; background:#fff; color:{{ $buttonColor }}; border:1px solid {{ $buttonColor }}; text-decoration:none; font-weight:700;">
                            <i class="fas fa-circle-play"></i>
                            شرح التقديم
                        </a>
                    @endif

                    @if(!empty($service->application_url))
                        <a href="{{ $service->application_url }}" target="_blank" style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:12px 16px; border-radius:12px; background:{{ $buttonColor }}; color:#fff; border:1px solid {{ $buttonColor }}; text-decoration:none; font-weight:700;">
                            <i class="fas fa-paper-plane"></i>
                            تقديم الطلب
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1; text-align:center; background:#fff; border:1px dashed #cbd5e1; color:#6b7280; border-radius:20px; padding:48px 20px;">
                لا توجد خدمات مستفيدين مضافة حاليًا.
            </div>
        @endforelse
    </div>
</div>
@endsection
