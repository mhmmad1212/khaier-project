@extends('themes.default.layouts.app')

@section('content')
<div class="container" style="padding:40px 20px;">
    <h1 style="margin-bottom:20px;">{{ $project->title }}</h1>

    @if(!empty($project->cover_image))
        <img loading="lazy" decoding="async" src="{{ asset('storage/' . ltrim($project->cover_image, '/')) }}" alt="{{ $project->title }}" style="width:100%;max-height:420px;object-fit:cover;border-radius:18px;margin-bottom:24px;">
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px;">
        @if($project->start_date)
            <div><strong>تاريخ البداية:</strong> {{ \Illuminate\Support\Carbon::parse($project->start_date)->format('Y-m-d') }}</div>
        @endif

        @if($project->end_date)
            <div><strong>تاريخ النهاية:</strong> {{ \Illuminate\Support\Carbon::parse($project->end_date)->format('Y-m-d') }}</div>
        @endif

        @if($project->project_amount)
            <div><strong>مبلغ المشروع:</strong> {{ number_format($project->project_amount, 2) }} ر.س</div>
        @endif

        @if($project->donation_amount)
            <div><strong>مبلغ التبرع:</strong> {{ number_format($project->donation_amount, 2) }} ر.س</div>
        @endif
    </div>

    @if(!empty($project->description))
        <div style="margin-bottom:30px;">
            {!! $project->description !!}
        </div>
    @endif

    @if(!empty($project->donation_url))
        <div style="margin-bottom:30px;">
            <a href="{{ $project->donation_url }}" target="_blank" style="display:inline-block;padding:12px 20px;border-radius:10px;background:#127962;color:#fff;text-decoration:none;">
                تبرع الآن
            </a>
        </div>
    @endif

    @if($project->galleryImages->count())
        <h3 style="margin-bottom:16px;">صور المشروع</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:30px;">
            @foreach($project->galleryImages as $image)
                @if($image->mediaItem && !empty($image->mediaItem->file))
                    <img loading="lazy" decoding="async" src="{{ asset('storage/' . ltrim($image->mediaItem->file, '/')) }}" alt="{{ $project->title }}" style="width:100%;height:220px;object-fit:cover;border-radius:14px;">
                @endif
            @endforeach
        </div>
    @endif

    @if(!empty($project->report_file))
        <div>
            <a href="{{ asset('storage/' . ltrim($project->report_file, '/')) }}" target="_blank" style="display:inline-block;padding:10px 16px;border-radius:10px;border:1px solid #127962;color:#127962;text-decoration:none;">
                تحميل تقرير المشروع
            </a>
        </div>
    @endif
</div>
@endsection
