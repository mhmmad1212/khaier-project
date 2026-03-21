@extends('themes.default.layouts.app')

@section('content')
<div class="container py-5">

    <div class="mb-4">
        <h2 class="fw-bold">اللوائح</h2>
        <p class="text-muted mb-0">استعراض وتحميل اللوائح والأنظمة المعتمدة.</p>
    </div>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-5">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="بحث بالاسم">
        </div>

        <div class="col-md-5">
            <input type="number" name="year" value="{{ request('year') }}" class="form-control" placeholder="السنة">
        </div>

        <div class="col-md-2 d-grid">
            <button class="btn btn-primary">تصفية</button>
        </div>
    </form>

    @if($items->count())
        <div class="row g-4">
            @foreach($items as $item)
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold mb-2">{{ $item->title }}</h5>

                            <div class="mb-2">
                                <span class="badge bg-light text-dark border">
                                    السنة:
                                    {{ $item->year ?: '—' }}
                                </span>
                            </div>

                            @if($item->description)
                                <p class="text-muted mb-3">{{ $item->description }}</p>
                            @endif

                            <div class="mt-auto d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <small class="text-muted">
                                    {{ optional($item->published_at)->format('Y-m-d') ?: '—' }}
                                </small>

                                @if($item->fileMedia && $item->fileMedia->file)
                                    <a href="{{ asset('storage/' . $item->fileMedia->file) }}" target="_blank" class="btn btn-primary">
                                        عرض / تحميل الملف
                                    </a>
                                @else
                                    <span class="badge bg-secondary">لا يوجد ملف</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-light border">لا توجد نتائج.</div>
    @endif

</div>
@endsection
