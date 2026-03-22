@extends('themes.default.layouts.app')

@section('content')
<div class="container" style="padding:40px 20px;">
    <h1 style="margin-bottom:24px;">{{ $page->title ?? 'الخدمات' }}</h1>

    @if(!empty($page->excerpt))
        <p style="margin-bottom:24px;color:#666;">{{ $page->excerpt }}</p>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:24px;">
        @forelse($services as $service)
            <div style="border:1px solid #e5e7eb;border-radius:16px;padding:22px;background:#fff;">
                @if(!empty($service->icon))
                    <div style="margin-bottom:14px;color:#127962;">
                        <x-filament::icon :icon="$service->icon" class="h-10 w-10" />
                    </div>
                @endif

                <h3 style="margin:0 0 10px;">{{ $service->name }}</h3>

                @if(!empty($service->description))
                    <div style="color:#666;margin-bottom:14px;">
                        {{ $service->description }}
                    </div>
                @endif

                @if(!empty($service->url))
                    <a href="{{ $service->url }}" target="_blank" style="display:inline-block;padding:10px 16px;border-radius:10px;background:#127962;color:#fff;text-decoration:none;">
                        عرض الخدمة
                    </a>
                @endif
            </div>
        @empty
            <div style="color:#666;">لا توجد خدمات مضافة حاليًا.</div>
        @endforelse
    </div>
</div>
@endsection
