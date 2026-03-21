@extends('themes.default.layouts.app')

@section('title', $association->name)

@push('styles')
<style>
    .hero-home{
        position:relative;
        margin-top:-114px;
        padding-top:170px;
        padding-bottom:110px;
        background:
            linear-gradient(90deg, rgba(7,32,28,.72) 0%, rgba(7,32,28,.38) 45%, rgba(7,32,28,.18) 100%),
            linear-gradient(135deg, #0d5948 0%, #127962 55%, #189177 100%);
        overflow:hidden;
    }

    .hero-home::before{
        content:'';
        position:absolute;
        inset:0;
        background:
            radial-gradient(circle at top left, rgba(255,255,255,.14), transparent 28%),
            radial-gradient(circle at bottom right, rgba(212,175,55,.10), transparent 24%);
        pointer-events:none;
    }

    .hero-slider-shell{
        position:relative;
        z-index:2;
    }

    .hero-main-card{
        position:relative;
        min-height:620px;
        border-radius:30px;
        overflow:hidden;
        box-shadow:0 25px 55px rgba(0,0,0,.18);
        border:1px solid rgba(255,255,255,.12);
        background:rgba(255,255,255,.08);
        backdrop-filter:blur(8px);
    }

    .hero-slide-image{
        width:100%;
        height:620px;
        object-fit:cover;
        display:block;
    }

    .hero-overlay{
        position:absolute;
        inset:0;
        display:flex;
        align-items:center;
        background:linear-gradient(90deg, rgba(6,24,22,.78) 0%, rgba(6,24,22,.46) 42%, rgba(6,24,22,.08) 100%);
    }

    .hero-content{
        max-width:640px;
        color:#fff;
        padding:56px;
    }

    .hero-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:10px 18px;
        border-radius:999px;
        background:rgba(255,255,255,.12);
        border:1px solid rgba(255,255,255,.18);
        margin-bottom:20px;
        font-weight:700;
        font-size:.95rem;
    }

    .hero-title{
        font-size:3.3rem;
        font-weight:800;
        line-height:1.3;
        margin-bottom:18px;
    }

    .hero-text{
        font-size:1.08rem;
        color:rgba(255,255,255,.9);
        line-height:2;
        margin-bottom:26px;
        max-width:580px;
    }

    .hero-actions{
        display:flex;
        gap:14px;
        flex-wrap:wrap;
    }

    .hero-btn-primary,
    .hero-btn-outline{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:13px 24px;
        border-radius:999px;
        font-weight:700;
        font-size:.98rem;
        transition:all .25s ease;
    }

    .hero-btn-primary{
        background:#fff;
        color:#0d5948;
        box-shadow:0 10px 25px rgba(0,0,0,.10);
    }

    .hero-btn-primary:hover{
        transform:translateY(-2px);
        color:#0d5948;
    }

    .hero-btn-outline{
        border:1px solid rgba(255,255,255,.48);
        color:#fff;
        background:rgba(255,255,255,.08);
    }

    .hero-btn-outline:hover{
        background:rgba(255,255,255,.14);
        color:#fff;
    }

    .hero-side-box{
        position:absolute;
        left:26px;
        bottom:26px;
        width:320px;
        background:rgba(255,255,255,.14);
        border:1px solid rgba(255,255,255,.16);
        border-radius:22px;
        padding:20px;
        color:#fff;
        backdrop-filter:blur(12px);
        z-index:3;
    }

    .hero-side-box h5{
        font-weight:800;
        margin-bottom:10px;
    }

    .hero-side-box p{
        margin:0;
        color:rgba(255,255,255,.88);
        line-height:1.9;
        font-size:.95rem;
    }

    .swiper-pagination{
        bottom:18px !important;
    }

    .swiper-pagination-bullet{
        width:12px;
        height:12px;
        background:#fff;
        opacity:.5;
    }

    .swiper-pagination-bullet-active{
        background:#d4af37;
        opacity:1;
    }

    .stats-strip{
        margin-top:-58px;
        position:relative;
        z-index:6;
    }

    .stats-card{
        background:#fff;
        border-radius:24px;
        box-shadow:0 20px 45px rgba(15,23,42,.10);
        padding:26px 24px;
        height:100%;
        border:1px solid rgba(18,121,98,.08);
        position:relative;
        overflow:hidden;
    }

    .stats-card::before{
        content:'';
        position:absolute;
        inset:0 auto 0 0;
        width:6px;
        background:linear-gradient(180deg, #127962, #d4af37);
    }

    .stats-icon{
        width:56px;
        height:56px;
        border-radius:18px;
        background:rgba(18,121,98,.09);
        color:var(--primary);
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.35rem;
        margin-bottom:16px;
    }

    .stats-value{
        font-size:2.2rem;
        font-weight:800;
        color:var(--primary-dark);
        line-height:1.15;
    }

    .stats-label{
        color:var(--text-muted);
        margin-top:6px;
        font-size:.98rem;
    }

    .home-section{
        padding:86px 0;
    }

    .section-kicker{
        color:var(--primary);
        font-weight:800;
        margin-bottom:10px;
        display:block;
        letter-spacing:.2px;
    }

    .section-title-home{
        font-size:2.3rem;
        font-weight:800;
        margin-bottom:14px;
        color:var(--dark);
        line-height:1.4;
    }

    .section-subtitle-home{
        color:var(--text-muted);
        font-size:1.02rem;
        line-height:2;
        max-width:760px;
    }

    .about-card,
    .about-side-card,
    .news-card-home,
    .partner-card-home{
        background:#fff;
        border-radius:24px;
        box-shadow:0 16px 40px rgba(15,23,42,.08);
        border:1px solid rgba(18,121,98,.08);
    }

    .about-card,
    .about-side-card{
        padding:38px;
        height:100%;
    }

    .about-side-card{
        background:linear-gradient(135deg,#127962,#0d5948);
        color:#fff;
        box-shadow:0 18px 40px rgba(15,23,42,.14);
    }

    .about-side-card p{
        color:rgba(255,255,255,.86);
    }

    .about-feature{
        background:#f6fbf9;
        border-radius:18px;
        padding:18px;
        height:100%;
        border:1px solid rgba(18,121,98,.06);
    }

    .about-feature strong{
        display:block;
        margin-bottom:8px;
        color:var(--dark);
    }

    .about-side-list{
        list-style:none;
        padding:0;
        margin:24px 0 0;
        display:grid;
        gap:12px;
    }

    .about-side-list li{
        background:rgba(255,255,255,.10);
        border:1px solid rgba(255,255,255,.14);
        border-radius:16px;
        padding:15px 16px;
        font-weight:700;
    }

    .news-card-home{
        overflow:hidden;
        height:100%;
        transition:all .25s ease;
    }

    .news-card-home:hover{
        transform:translateY(-6px);
        box-shadow:0 22px 44px rgba(15,23,42,.12);
    }

    .news-card-home img{
        width:100%;
        height:235px;
        object-fit:cover;
        display:block;
    }

    .news-card-body{
        padding:24px;
    }

    .news-card-meta{
        color:var(--text-muted);
        font-size:.92rem;
        margin-bottom:10px;
    }

    .news-card-title{
        font-size:1.22rem;
        font-weight:800;
        line-height:1.75;
        margin-bottom:10px;
        color:var(--dark);
    }

    .news-card-body p{
        color:var(--text-muted);
        line-height:1.9;
    }

    .read-more-link{
        color:var(--primary);
        font-weight:800;
        display:inline-flex;
        align-items:center;
        gap:6px;
    }

    .read-more-link:hover{
        color:var(--primary-dark);
    }

    .partner-card-home{
        padding:30px 24px;
        text-align:center;
        height:100%;
        transition:all .25s ease;
    }

    .partner-card-home:hover{
        transform:translateY(-5px);
        box-shadow:0 22px 44px rgba(15,23,42,.12);
    }

    .partner-icon{
        width:70px;
        height:70px;
        border-radius:20px;
        background:rgba(18,121,98,.10);
        color:var(--primary);
        display:flex;
        align-items:center;
        justify-content:center;
        margin:0 auto 18px;
        font-size:1.5rem;
    }

    .section-header-flex{
        display:flex;
        justify-content:space-between;
        align-items:end;
        gap:20px;
        margin-bottom:26px;
        flex-wrap:wrap;
    }

    @media (max-width: 991.98px){
        .hero-home{
            margin-top:0;
            padding-top:32px;
            padding-bottom:60px;
        }

        .hero-main-card{
            min-height:auto;
        }

        .hero-slide-image{
            height:500px;
        }

        .hero-overlay{
            align-items:end;
            background:linear-gradient(0deg, rgba(6,24,22,.84) 0%, rgba(6,24,22,.22) 70%);
        }

        .hero-content{
            padding:30px;
            max-width:none;
        }

        .hero-title{
            font-size:2.2rem;
        }

        .hero-side-box{
            position:static;
            width:auto;
            margin:18px;
        }

        .stats-strip{
            margin-top:24px;
        }

        .home-section{
            padding:70px 0;
        }
    }

    @media (max-width: 575.98px){
        .hero-slide-image{
            height:430px;
        }

        .hero-title{
            font-size:1.85rem;
        }

        .hero-text{
            font-size:.98rem;
        }

        .about-card,
        .about-side-card{
            padding:24px;
        }

        .section-title-home{
            font-size:1.8rem;
        }
    }
</style>
@endpush

@section('hero')
<section class="hero-home">
    <div class="container hero-slider-shell">
        <div class="hero-main-card">
            <div class="swiper heroSwiper">
                <div class="swiper-wrapper">
                    @if($sliders->count())
                        @foreach($sliders as $slide)
                            <div class="swiper-slide position-relative">
                                @if(!empty($slide->image))
                                    <img class="hero-slide-image" src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title ?? $association->name }}">
                                @else
                                    <div class="hero-slide-image"></div>
                                @endif

                                <div class="hero-overlay">
                                    <div class="hero-content">
                                        <div class="hero-badge">
                                            <i class="bi bi-stars"></i>
                                            <span>منصة جمعية أهلية حديثة</span>
                                        </div>

                                        <h1 class="hero-title">{{ $slide->title ?? $association->name }}</h1>

                                        <div class="hero-text">
                                            {{ $slide->description ?? 'نقدم محتوى مؤسسيًا وإعلاميًا يبرز أثر الجمعية وبرامجها ومبادراتها وهيكلها الإداري بصورة احترافية حديثة.' }}
                                        </div>

                                        <div class="hero-actions">
                                            <a href="{{ $slide->button_link ?? '/news' }}" class="hero-btn-primary">
                                                <i class="bi bi-arrow-left-circle"></i>
                                                {{ $slide->button_text ?? 'اكتشف المزيد' }}
                                            </a>

                                            <a href="/board-members" class="hero-btn-outline">
                                                <i class="bi bi-people"></i>
                                                مجلس الإدارة
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="swiper-slide position-relative">
                            <div class="hero-slide-image"></div>
                            <div class="hero-overlay">
                                <div class="hero-content">
                                    <div class="hero-badge">
                                        <i class="bi bi-stars"></i>
                                        <span>منصة جمعية أهلية حديثة</span>
                                    </div>

                                    <h1 class="hero-title">{{ $association->name }}</h1>

                                    <div class="hero-text">
                                        واجهة رقمية مؤسسية تعرض أعمال الجمعية وبرامجها وأخبارها ومحتواها الإداري والحوكمي بشكل منظم ومؤثر.
                                    </div>

                                    <div class="hero-actions">
                                        <a href="/news" class="hero-btn-primary"><i class="bi bi-arrow-left-circle"></i> المركز الإعلامي</a>
                                        <a href="/board-members" class="hero-btn-outline"><i class="bi bi-people"></i> مجلس الإدارة</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="swiper-pagination"></div>
            </div>

            <div class="hero-side-box d-none d-lg-block">
                <h5>محتوى مؤسسي منظم</h5>
                <p>صفحات تعريفية، مركز إعلامي، أخبار، شراكات، ومحتوى إداري وهيكلي يمكن تطويره لاحقًا بشكل كامل.</p>
            </div>
        </div>
    </div>
</section>

@if($statistics->count())
<section class="stats-strip">
    <div class="container">
        <div class="row g-4">
            @foreach($statistics->take(4) as $item)
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <div class="stats-icon"><i class="bi bi-bar-chart-line"></i></div>
                        <div class="stats-value">{{ $item->value }}</div>
                        <div class="stats-label">{{ $item->title }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@section('content')
<section class="home-section">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-7">
                <div class="about-card">
                    <span class="section-kicker">عن الجمعية</span>
                    <h2 class="section-title-home">تجربة رقمية مؤسسية تليق بالجمعيات الحديثة</h2>
                    <p class="section-subtitle-home mb-4">
                        هذه الواجهة صُممت لتكون قاعدة قوية لموقع الجمعية، بحيث تجمع بين المظهر الرسمي الحديث، وسهولة عرض الأخبار والبرامج والصفحات والمحتوى الإداري والحوكمي.
                    </p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="about-feature">
                                <strong>هوية بصرية احترافية</strong>
                                <span class="text-muted">تصميم واضح وحديث بمستوى أقرب للمواقع المؤسسية الكبيرة.</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="about-feature">
                                <strong>إدارة محتوى مرنة</strong>
                                <span class="text-muted">أخبار، صفحات، هيكل إداري، حوكمة، وشركاء ضمن نظام واحد.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="about-side-card">
                    <h3 class="fw-bold mb-3">ماذا يوفر الموقع؟</h3>
                    <p class="mb-0">
                        واجهة مؤسسية قابلة للتوسع، تستوعب مختلف أقسام الجمعية وتعرضها بأسلوب عصري ومنظم وسهل التصفح.
                    </p>

                    <ul class="about-side-list">
                        <li>المركز الإعلامي والأخبار والفعاليات</li>
                        <li>مجلس الإدارة والجمعية العمومية</li>
                        <li>الشركاء والبرامج والمشاريع والصفحات المؤسسية</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

@if($news->count())
<section class="home-section pt-0">
    <div class="container">
        <div class="section-header-flex">
            <div>
                <span class="section-kicker">المركز الإعلامي</span>
                <h2 class="section-title-home mb-2">آخر الأخبار</h2>
                <p class="section-subtitle-home mb-0">تابع آخر مستجدات الجمعية وأنشطتها وبرامجها ومبادراتها.</p>
            </div>

            <a href="/news" class="btn btn-head"><i class="bi bi-arrow-left"></i> عرض جميع الأخبار</a>
        </div>

        <div class="row g-4">
            @foreach($news->take(3) as $item)
                <div class="col-lg-4">
                    <article class="news-card-home">
                        @if(!empty($item->image))
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}">
                        @else
                            <div style="height:235px;background:linear-gradient(135deg,#d8efe8,#edf7f4);"></div>
                        @endif

                        <div class="news-card-body">
                            <div class="news-card-meta">
                                <i class="bi bi-calendar-event"></i>
                                {{ $item->published_at }}
                            </div>

                            <h3 class="news-card-title">{{ $item->title }}</h3>

                            @if(!empty($item->excerpt))
                                <p>{{ $item->excerpt }}</p>
                            @endif

                            <a class="read-more-link" href="/news/{{ $item->slug }}">قراءة الخبر <span>←</span></a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($partners->count())
<section class="home-section pt-0">
    <div class="container">
        <div class="mb-4">
            <span class="section-kicker">شركاء النجاح</span>
            <h2 class="section-title-home mb-2">الجهات الشريكة والداعمة</h2>
            <p class="section-subtitle-home mb-0">شراكات تسهم في تعزيز الأثر المجتمعي وتوسيع نطاق المبادرات والبرامج.</p>
        </div>

        <div class="row g-4">
            @foreach($partners as $item)
                <div class="col-lg-3 col-md-6">
                    <div class="partner-card-home">
                        <div class="partner-icon"><i class="bi bi-building"></i></div>
                        <h5 class="fw-bold mb-2">{{ $item->name }}</h5>
                        @if(!empty($item->url))
                            <a class="read-more-link" href="{{ $item->url }}">زيارة الرابط <span>←</span></a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
    new Swiper('.heroSwiper', {
        loop: true,
        autoplay: {
            delay: 5500,
            disableOnInteraction: false
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        }
    });
</script>
@endpush


<!-- SLIDER -->
<section class="container my-4">
    <div class="row">
        @foreach($sliders as $slider)
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">

                    @php
                        $sliderMedia = !empty($slider->image_media_id)
                            ? \App\Models\MediaItem::query()->find($slider->image_media_id)
                            : null;
                    @endphp
                    @if($sliderMedia && !empty($sliderMedia->file))
                        <img src="{{ asset('storage/' . $sliderMedia->file) }}" class="card-img-top">
                    @endif

                    <div class="card-body text-center">
                        <h5>{{ $slider->title }}</h5>
                        <p>{{ $slider->description }}</p>

                        @if($slider->button_url)
                            <a href="{{ $slider->button_url }}" class="btn btn-head">
                                {{ $slider->button_text ?? 'عرض المزيد' }}
                            </a>
                        @endif
                    </div>

                </div>
            </div>
        @endforeach
    </div>
</section>
