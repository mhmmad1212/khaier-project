@extends('themes.default.layouts.app')

@section('title', 'التراخيص')

@section('content')

@php
$licenses = $licenses ?? collect();
@endphp

<section class="licenses-page">

    <div class="container">

        <!-- العنوان -->
        <div class="page-header">
            <h1>التراخيص الرسمية</h1>
            <p>جميع التراخيص المعتمدة للجمعية من الجهات الرسمية</p>
        </div>

        <!-- القائمة -->
        <div class="licenses-grid">

            @forelse($licenses as $license)
                <div class="license-card">

                    <div class="license-icon">
                        📄
                    </div>

                    <div class="license-content">
                        <h3>{{ $license->title }}</h3>

                        @if($license->description)
                            <p>{{ $license->description }}</p>
                        @endif

                        <div class="license-meta">
                            @if($license->issue_date)
                                <span>📅 {{ $license->issue_date }}</span>
                            @endif

                            @if($license->issuer)
                                <span>🏢 {{ $license->issuer }}</span>
                            @endif
                        </div>

                        @if($license->file)
                            <a href="{{ asset('storage/'.$license->file) }}" target="_blank" class="license-btn">
                                عرض الترخيص
                            </a>
                        @endif
                    </div>

                </div>
            @empty

                <div class="empty-state">
                    <h3>لا يوجد تراخيص حالياً</h3>
                    <p>سيتم إضافة التراخيص قريباً</p>
                </div>

            @endforelse

        </div>

    </div>

</section>

<style>

.licenses-page{
    padding:60px 0;
    background:#f8fafc;
}

.page-header{
    text-align:center;
    margin-bottom:40px;
}

.page-header h1{
    font-size:2rem;
    font-weight:800;
    color:#111827;
}

.page-header p{
    color:#6b7280;
    margin-top:10px;
}

/* grid */
.licenses-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
    gap:20px;
}

/* card */
.license-card{
    background:#fff;
    border-radius:18px;
    padding:22px;
    box-shadow:0 10px 25px rgba(0,0,0,.05);
    border:1px solid #e5e7eb;
    transition:.3s;
}

.license-card:hover{
    transform:translateY(-5px);
}

/* icon */
.license-icon{
    font-size:32px;
    margin-bottom:10px;
}

/* content */
.license-content h3{
    font-size:1.1rem;
    font-weight:700;
    margin-bottom:8px;
    color:#111827;
}

.license-content p{
    font-size:.9rem;
    color:#6b7280;
    margin-bottom:12px;
}

/* meta */
.license-meta{
    display:flex;
    flex-direction:column;
    gap:4px;
    font-size:.8rem;
    color:#9ca3af;
    margin-bottom:14px;
}

/* button */
.license-btn{
    display:inline-block;
    background:#127962;
    color:#fff;
    padding:8px 14px;
    border-radius:10px;
    text-decoration:none;
    font-size:.85rem;
    font-weight:600;
}

.license-btn:hover{
    background:#0d5948;
}

/* empty */
.empty-state{
    text-align:center;
    grid-column:1/-1;
    padding:40px;
}

</style>

@endsection