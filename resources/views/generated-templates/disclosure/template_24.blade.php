@extends('themes.default.layouts.app')

@section('title', ($page->title ?? 'الإفصاح') . ' - ' . ($association->name ?? 'الجمعية'))

@section('content')
<div class="disclosure-container">

    <div class="page-header">
        <h1>{{ $page->title ?? 'الإفصاح' }}</h1>
        <p>جميع مستندات الإفصاح والشفافية الخاصة بالجمعية</p>
    </div>

    <div class="disclosure-table">

        <div class="table-head">
            <div>اسم الإفصاح</div>
            <div>الوصف</div>
            <div>الملف</div>
        </div>

        @forelse(($disclosures ?? $items ?? collect()) as $item)
            <div class="table-row">
                <div class="name" data-label="اسم الإفصاح">
                    {{ $item->name ?? $item->title ?? 'بدون اسم' }}
                </div>

                <div data-label="الوصف" class="desc">
                    {{ $item->description ?? '-' }}
                </div>

                <div data-label="الملف">
                    @if(!empty($item->attachment))
                        <a href="{{ \App\Support\Media\MediaUrl::forDiskPath('public', $item->attachment) }}" target="_blank" class="btn">
                            عرض
                        </a>
                    @elseif(!empty($item->file))
                        <a href="{{ \App\Support\Media\MediaUrl::forDiskPath('public', $item->file) }}" target="_blank" class="btn">
                            عرض
                        </a>
                    @else
                        <span class="no-file">-</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                لا توجد بيانات إفصاح حالياً
            </div>
        @endforelse

    </div>

</div>

<style>
.disclosure-container{
    max-width:1100px;
    margin:auto;
    padding:40px 20px;
    font-family:'Noto Kufi Arabic',sans-serif;
}

.page-header{
    background:#fff;
    border-radius:14px;
    padding:22px;
    margin-bottom:20px;
    border:1px solid #e5e7eb;
    box-shadow:0 8px 24px rgba(15,23,42,.04);
}

.page-header h1{
    margin:0;
    font-size:24px;
    font-weight:800;
    color:#111827;
}

.page-header p{
    margin:8px 0 0;
    color:#6b7280;
    font-size:14px;
}

.disclosure-table{
    background:#fff;
    border-radius:14px;
    border:1px solid #e5e7eb;
    overflow:hidden;
    box-shadow:0 8px 24px rgba(15,23,42,.04);
}

.table-head{
    display:grid;
    grid-template-columns:1fr 2fr 120px;
    background:#f8fafc;
    padding:16px 18px;
    font-weight:700;
    font-size:13px;
    color:#374151;
    border-bottom:1px solid #e5e7eb;
}

.table-row{
    display:grid;
    grid-template-columns:1fr 2fr 120px;
    padding:16px 18px;
    border-top:1px solid #f1f5f9;
    font-size:13px;
    align-items:center;
    color:#374151;
}

.table-row:hover{
    background:#fafafa;
}

.name{
    font-weight:700;
    color:#111827;
}

.desc{
    color:#6b7280;
    line-height:1.9;
}

.btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background:#127962;
    color:#fff;
    padding:8px 14px;
    border-radius:10px;
    text-decoration:none;
    font-size:12px;
    font-weight:700;
    transition:.2s ease;
}

.btn:hover{
    background:#0f5f4e;
}

.no-file{
    color:#9ca3af;
    font-weight:600;
}

.empty-state{
    text-align:center;
    padding:34px 20px;
    color:#6b7280;
    font-size:14px;
}

@media (max-width:768px){
    .table-head{
        display:none;
    }

    .table-row{
        grid-template-columns:1fr;
        gap:10px;
        padding:16px;
    }

    .table-row > div{
        display:flex;
        justify-content:space-between;
        gap:12px;
        align-items:flex-start;
    }

    .table-row > div::before{
        content:attr(data-label);
        color:#6b7280;
        font-weight:700;
        font-size:12px;
        min-width:90px;
    }
}
</style>
@endsection