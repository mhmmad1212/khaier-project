@extends('themes.default.layouts.app')

@section('content')
<div class="container" style="padding:40px 20px;">
    <h1 style="margin-bottom:24px;">{{ $page->title ?? 'البرامج والمشاريع' }}</h1>

    @if(!empty($page->excerpt))
        <p style="margin-bottom:24px;color:#666;">{{ $page->excerpt }}</p>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:24px;">
        @forelse($projects as $project)
            <div style="border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;background:#fff;">
                @if(!empty($project->cover_image))
                    <img loading="lazy" decoding="async" src="{{ asset('storage/' . ltrim($project->cover_image, '/')) }}" alt="{{ $project->title }}" style="width:100%;height:220px;object-fit:cover;">
                @endif
                <div style="padding:18px;">
                    <h3 style="margin:0 0 10px;">{{ $project->title }}</h3>

                    @if(!empty($project->description))
                        <div style="color:#666;margin-bottom:14px;">
                            {!! \Illuminate\Support\Str::limit(strip_tags($project->description), 140) !!}
                        </div>
                    @endif

                    <a href="{{ url('/projects/' . $project->id) }}" style="display:inline-block;padding:10px 16px;border-radius:10px;background:#127962;color:#fff;text-decoration:none;">
                        عرض المشروع
                    </a>
                </div>
            </div>
        @empty
            <div style="color:#666;">لا توجد مشاريع أو برامج مضافة حاليًا.</div>
        @endforelse
    </div>
</div>
@endsection
