@extends('themes.default.layouts.app')

@section('title', ($page->title ?? 'الخدمات') . ' - ' . ($association->name ?? 'الجمعية'))

@section('content')
<div class="services-container">

    <div class="page-header">
        <h1>{{ $page->title ?? 'الخدمات' }}</h1>
        <p>استعرض الخدمات التي تقدمها الجمعية للمستفيدين والزوار</p>
    </div>

    <div class="services-grid">

        @forelse(($services ?? $items ?? collect()) as $service)
            <div class="service-card">
                <div class="service-icon">
                    @if(!empty($service->icon) && str_contains($service->icon, 'heroicon'))
                        <x-filament::icon :icon="$service->icon" class="w-7 h-7" />
                    @else
                        🛠️
                    @endif
                </div>

                <div class="service-body">
                    <h3 class="service-title">
                        {{ $service->name ?? 'بدون اسم' }}
                    </h3>

                    @if(!empty($service->description))
                        <p class="service-desc">
                            {{ trim(strip_tags($service->description)) }}
                        </p>
                    @endif

                    <div class="service-actions">
                        @if(!empty($service->url))
                            <a href="{{ $service->url }}" class="btn" target="_blank">عرض الخدمة</a>
                        @else
                            <span class="no-link">لا يوجد رابط</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                لا توجد خدمات حالياً
            </div>
        @endforelse

    </div>

</div>

<style>
.services-container{
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

.services-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));
    gap:20px;
}

.service-card{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:16px;
    padding:22px;
    box-shadow:0 8px 24px rgba(15,23,42,.04);
    transition:.2s ease;
}

.service-card:hover{
    transform:translateY(-4px);
    box-shadow:0 14px 30px rgba(15,23,42,.08);
}

.service-icon{
    width:54px;
    height:54px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#ecfdf5;
    color:#127962;
    font-size:24px;
    margin-bottom:14px;
}

.service-title{
    margin:0 0 10px;
    font-size:18px;
    font-weight:800;
    color:#111827;
    line-height:1.8;
}

.service-desc{
    margin:0 0 18px;
    font-size:13px;
    color:#6b7280;
    line-height:2;
    min-height:52px;
}

.service-actions{
    margin-top:auto;
}

.btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background:#127962;
    color:#fff;
    padding:10px 16px;
    border-radius:10px;
    text-decoration:none;
    font-size:13px;
    font-weight:700;
    transition:.2s ease;
}

.btn:hover{
    background:#0f5f4e;
}

.no-link{
    color:#9ca3af;
    font-size:13px;
    font-weight:600;
}

.empty-state{
    grid-column:1 / -1;
    text-align:center;
    padding:34px 20px;
    color:#6b7280;
    font-size:14px;
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:14px;
}

@media (max-width:640px){
    .services-container{
        padding:24px 14px;
    }

    .page-header{
        padding:18px;
    }

    .page-header h1{
        font-size:22px;
    }

    .service-card{
        padding:18px;
    }

    .service-title{
        font-size:16px;
    }

    .service-desc{
        min-height:auto;
    }
}
</style>
@endsection