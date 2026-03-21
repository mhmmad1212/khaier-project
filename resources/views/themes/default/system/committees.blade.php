@extends('themes.default.layouts.app')

@section('title', 'اللجان - ' . $association->name)

@push('styles')
<style>
    .committee-wrap{
        background:linear-gradient(180deg,#f4f8f7 0%, #ffffff 260px);
        padding:50px 0 80px;
    }

    .committee-hero{
        background:linear-gradient(135deg,#127962,#0d5948);
        border-radius:30px;
        padding:38px;
        color:#fff;
        margin-bottom:30px;
        box-shadow:0 24px 50px rgba(15,23,42,.13);
    }

    .committee-title{
        font-size:2.2rem;
        font-weight:800;
        margin:0 0 10px;
    }

    .committee-subtitle{
        color:rgba(255,255,255,.88);
        line-height:2;
        margin:0;
    }

    .committee-card{
        background:#fff;
        border-radius:24px;
        border:1px solid rgba(18,121,98,.08);
        box-shadow:0 18px 40px rgba(15,23,42,.08);
        padding:26px;
        height:100%;
        transition:.25s ease;
    }

    .committee-card:hover{
        transform:translateY(-6px);
        box-shadow:0 24px 52px rgba(15,23,42,.12);
    }

    .committee-icon{
        width:68px;
        height:68px;
        border-radius:20px;
        background:rgba(18,121,98,.10);
        color:#127962;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.5rem;
        margin-bottom:16px;
    }

    .committee-name{
        font-size:1.2rem;
        font-weight:800;
        color:#1f2937;
        margin-bottom:10px;
        line-height:1.6;
    }

    .committee-desc{
        color:#6b7280;
        line-height:1.95;
        margin-bottom:16px;
        min-height:72px;
    }

    .committee-meta{
        display:grid;
        gap:10px;
        margin-top:16px;
    }

    .committee-meta-item{
        display:flex;
        align-items:flex-start;
        gap:10px;
        background:#fafcfc;
        border:1px solid #edf3f1;
        border-radius:16px;
        padding:12px 14px;
        color:#4b5563;
        font-size:.94rem;
        line-height:1.8;
    }

    .committee-meta-item i{
        color:#127962;
        margin-top:2px;
        flex:0 0 auto;
    }

    .committee-meta-item strong{
        display:block;
        color:#1f2937;
        margin-bottom:2px;
    }

    .committee-file{
        margin-top:18px;
        display:inline-flex;
        align-items:center;
        gap:8px;
        color:#127962;
        font-weight:800;
    }

    .committee-empty{
        background:#fff;
        border:1px solid rgba(18,121,98,.08);
        border-radius:26px;
        box-shadow:0 18px 40px rgba(15,23,42,.08);
        padding:40px 30px;
        text-align:center;
        color:#6b7280;
    }
</style>
@endpush

@section('content')
<section class="committee-wrap">
    <div class="container">
        <div class="committee-hero">
            <h1 class="committee-title">اللجان</h1>
            <p class="committee-subtitle">
                تعرف على لجان الجمعية وأدوارها التنظيمية، مع عرض واضح للبيانات الأساسية لكل لجنة.
            </p>
        </div>

        @if($committees->count())
            <div class="row g-4">
                @foreach($committees as $committee)
                    <div class="col-lg-4 col-md-6">
                        <article class="committee-card">
                            <div class="committee-icon">
                                <i class="bi bi-diagram-3"></i>
                            </div>

                            <h2 class="committee-name">{{ $committee->name }}</h2>

                            @if(!empty($committee->description))
                                <p class="committee-desc">{{ $committee->description }}</p>
                            @else
                                <p class="committee-desc">لجنة تنظيمية ضمن الهيكل الإداري للجمعية.</p>
                            @endif

                            <div class="committee-meta">
                                @if(!empty($committee->chairman))
                                    <div class="committee-meta-item">
                                        <i class="bi bi-person-badge"></i>
                                        <div>
                                            <strong>رئيس اللجنة</strong>
                                            <span>{{ $committee->chairman }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($committee->members_count))
                                    <div class="committee-meta-item">
                                        <i class="bi bi-people"></i>
                                        <div>
                                            <strong>عدد الأعضاء</strong>
                                            <span>{{ $committee->members_count }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if(!empty($committee->attachment))
                                <a class="committee-file" href="{{ $committee->attachment }}" target="_blank">
                                    <i class="bi bi-paperclip"></i>
                                    <span>عرض المرفق</span>
                                </a>
                            @endif
                        </article>
                    </div>
                @endforeach
            </div>
        @else
            <div class="committee-empty">
                لا توجد لجان مضافة حاليًا.
            </div>
        @endif
    </div>
</section>
@endsection
