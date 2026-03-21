@extends('themes.default.layouts.app')

@section('content')
<div class="container py-5">

    <div class="mb-4">
        <h2 class="fw-bold">القوائم المالية</h2>
    </div>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="بحث">
        </div>

        <div class="col-md-3">
            <input type="number" name="year" value="{{ request('year') }}" class="form-control" placeholder="السنة">
        </div>

        <div class="col-md-3">
            <select name="quarter" class="form-control">
                <option value="">كل الفترات</option>
                <option value="Q1">الربع الأول</option>
                <option value="Q2">الربع الثاني</option>
                <option value="Q3">الربع الثالث</option>
                <option value="Q4">الربع الرابع</option>
                <option value="annual">سنوي</option>
            </select>
        </div>

        <div class="col-md-2 d-grid">
            <button class="btn btn-primary">تصفية</button>
        </div>
    </form>

    @if($items->count())
        <div class="row g-4">
            @foreach($items as $item)
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column">

                            <h5>{{ $item->title }}</h5>

                            <div class="mb-2">
                                @if($item->year)
                                    <span class="badge bg-light text-dark border">السنة: {{ $item->year }}</span>
                                @endif
                                @if($item->quarter)
                                    <span class="badge bg-light text-dark border">{{ $item->quarter }}</span>
                                @endif
                            </div>

                            <div class="mt-auto d-flex justify-content-between">
                                <small>{{ optional($item->published_at)->format('Y-m-d') }}</small>

                                @if($item->fileMedia && $item->fileMedia->file)
                                    <a href="{{ asset('storage/' . $item->fileMedia->file) }}" target="_blank" class="btn btn-success">
                                        تحميل
                                    </a>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-light border">لا توجد نتائج</div>
    @endif

</div>
@endsection
