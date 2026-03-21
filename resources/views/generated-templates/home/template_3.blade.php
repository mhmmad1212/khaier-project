@php
    // 1. حماية المتغيرات الأساسية لمنع أخطاء Undefined variable
    $safeSettings = $siteSettings ?? null;
    $safeAssoc = $association ?? null;

    // 2. تجهيز اسم الجمعية
    $associationName = $safeSettings->association_name ?? $safeAssoc->name ?? $safeSettings->site_name ?? 'جمعية خير التجريبية';

    // 3. تجهيز الشعار بأمان
    $logo = null;
    if (!empty($safeSettings->logo)) {
        $logo = asset('storage/' . ltrim($safeSettings->logo, '/'));
    } elseif (!empty($safeSettings->logo_media_id)) {
        // إذا كان يقرأ من جدول الميديا
        $mediaLogo = \App\Models\MediaItem::find($safeSettings->logo_media_id);
        if ($mediaLogo && !empty($mediaLogo->file)) {
            $logo = asset('storage/' . ltrim($mediaLogo->file, '/'));
        }
    }

    // 4. تجهيز نصوص التعريف
    $aboutText = $safeSettings->about_text ?? 'تأسست الجمعية لتقديم خدمات رائدة تساهم في تنمية المجتمع ودعم الفئات المستهدفة بأعلى معايير الجودة والشفافية.';
    $visionText = $safeSettings->vision ?? 'الريادة والتميز في العمل الخيري المؤسسي، وتحقيق الاستدامة في جميع برامجنا ومشاريعنا التنموية.';
    $missionText = $safeSettings->mission ?? 'تمكين المستفيدين وتقديم الدعم اللازم لهم عبر برامج نوعية مبتكرة وشراكات فاعلة مع كافة قطاعات المجتمع.';
    $siteDesc = $safeSettings->site_description ?? 'نسعى لتقديم العمل الخيري بأسلوب مؤسسي تقني حديث، لضمان وصول الدعم لمستحقيه.';
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $associationName }} - الرئيسية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --brand-deep: #0b4b3c;
            --brand-light: #2e8b57;
            --warm-gold: #c9a45e;
            --bg-off-white: #fdfdfd;
            --bg-very-light: #f4f8f6;
            --text-main: #333;
            --text-muted: #666;
            --white: #ffffff;
            --radius-md: 15px;
            --radius-lg: 30px;
            --shadow-soft: 0 10px 30px rgba(0,0,0,0.03);
            --shadow-medium: 0 15px 40px rgba(0,0,0,0.06);
            --transition-smooth: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Tajawal', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-off-white);
            color: var(--text-main);
            line-height: 1.7;
            overflow-x: hidden;
        }

        .container { max-width: 1300px; margin: 0 auto; padding: 0 20px; }
        a { text-decoration: none; color: inherit; }
        ul { list-style: none; }

        /* --- Header --- */
        header {
            position: absolute; top: 0; left: 0; right: 0;
            z-index: 1000; height: 100px; display: flex; align-items: center;
            transition: var(--transition-smooth);
        }

        header.scrolled {
            position: fixed; background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px); box-shadow: 0 5px 20px rgba(0,0,0,0.05); height: 80px;
        }

        .navbar { display: flex; justify-content: space-between; align-items: center; width: 100%; }
        .logo img { height: 65px; transition: var(--transition-smooth); }
        .logo h2 { color: var(--white); font-size: 1.5rem; transition: var(--transition-smooth); }
        
        header.scrolled .logo img { height: 50px; }
        header.scrolled .logo h2 { color: var(--brand-deep); }

        .nav-menu { display: flex; align-items: center; gap: 30px; }
        .nav-menu a {
            font-weight: 700; color: var(--white);
            transition: var(--transition-smooth); position: relative; font-size: 1.05rem;
        }

        header.scrolled .nav-menu a { color: var(--brand-deep); }
        
        .nav-menu a::after {
            content: ''; position: absolute; width: 0; height: 2px;
            bottom: -5px; right: 0; background-color: var(--warm-gold);
            transition: var(--transition-smooth);
        }
        .nav-menu a:hover::after { width: 100%; }

        .donate-btn {
            background-color: var(--warm-gold); color: var(--white) !important;
            padding: 12px 30px; border-radius: 50px; font-weight: bold;
            box-shadow: 0 5px 15px rgba(201, 164, 94, 0.3); transition: var(--transition-smooth);
        }
        .donate-btn:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(201, 164, 94, 0.5); }
        .donate-btn::after { display: none; }

        .menu-toggle { display: none; font-size: 28px; cursor: pointer; color: var(--white); }
        header.scrolled .menu-toggle { color: var(--brand-deep); }

        /* --- Hero Slider --- */
        .hero { position: relative; height: 100vh; overflow: hidden; }
        .slide {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0; transition: opacity 0.8s ease-in-out;
            background-size: cover; background-position: center;
            display: flex; align-items: center; justify-content: center; text-align: center; color: var(--white);
        }
        .slide::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(11, 75, 60, 0.7), rgba(0,0,0,0.3));
        }
        .slide.active { opacity: 1; z-index: 1; }
        
        .hero-content { position: relative; z-index: 10; max-width: 900px; padding: 20px; transform: translateY(30px); opacity: 0; transition: all 0.8s ease 0.3s; }
        .slide.active .hero-content { transform: translateY(0); opacity: 1; }
        .hero h3 { font-size: 1.5rem; color: var(--warm-gold); margin-bottom: 15px; }
        .hero h1 { font-size: 4rem; font-weight: 800; margin-bottom: 25px; line-height: 1.2; }
        .hero p { font-size: 1.3rem; margin-bottom: 40px; opacity: 0.95; }

        .slider-dots { position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%); display: flex; gap: 12px; z-index: 10; }
        .dot { width: 12px; height: 12px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.4); cursor: pointer; transition: var(--transition-smooth); }
        .dot.active { background-color: var(--warm-gold); transform: scale(1.3); }

        /* --- Section Titles --- */
        .section-title { text-align: center; color: var(--brand-deep); font-size: 2.8rem; margin-bottom: 60px; position: relative; }
        .section-title::after {
            content: ''; position: absolute; width: 80px; height: 4px;
            background-color: var(--warm-gold); bottom: -15px; right: 50%; transform: translateX(50%); border-radius: 2px;
        }

        /* --- About Cards --- */
        .about { padding: 120px 0; background-color: var(--bg-very-light); position: relative; }
        .about-cards {
            display: flex; gap: 30px; justify-content: center; flex-wrap: wrap;
            margin-top: -80px; position: relative; z-index: 20;
        }
        .about-card {
            background-color: var(--white); border-radius: var(--radius-md); padding: 40px 30px;
            flex: 1 1 300px; max-width: 380px; box-shadow: var(--shadow-medium);
            transition: var(--transition-smooth); text-align: center;
        }
        .about-card:hover { transform: translateY(-10px); box-shadow: 0 20px 50px rgba(0,0,0,0.08); }
        .about-card i { font-size: 3rem; color: var(--brand-light); margin-bottom: 20px; }
        .about-card h3 { color: var(--brand-deep); font-size: 1.5rem; margin-bottom: 15px; }
        .about-card p { font-size: 1rem; color: var(--text-muted); }

        /* --- Circular Stats --- */
        .stats { padding: 80px 0; background-color: var(--white); text-align: center; }
        .stats-grid { display: flex; justify-content: space-around; flex-wrap: wrap; gap: 40px; }
        .stat-item { flex: 0 0 200px; text-align: center; }
        .stat-circle {
            width: 160px; height: 160px; border-radius: 50%; border: 8px solid #eee; margin: 0 auto 20px;
            display: flex; align-items: center; justify-content: center; position: relative; transition: var(--transition-smooth);
        }
        .stat-item:hover .stat-circle { border-color: var(--brand-light); transform: scale(1.05); }
        .stat-circle i {
            font-size: 2.5rem; color: var(--warm-gold); position: absolute; top: -15px; right: -15px;
            background: var(--white); padding: 10px; border-radius: 50%; box-shadow: var(--shadow-soft);
        }
        .stat-circle h3 { font-size: 2.8rem; font-weight: 800; color: var(--brand-deep); direction: ltr; }
        .stat-item p { font-size: 1.1rem; font-weight: 600; color: var(--text-main); }

        /* --- Projects & News Grid --- */
        .grid-section { padding: 100px 0; background-color: var(--bg-very-light); }
        .mixed-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        
        .projects-col, .news-col { display: flex; flex-direction: column; gap: 30px; }
        .col-title { color: var(--brand-deep); font-size: 1.8rem; margin-bottom: 20px; border-right: 4px solid var(--warm-gold); padding-right: 15px; }

        .card { background: var(--white); border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-soft); transition: var(--transition-smooth); }
        .card:hover { transform: translateY(-5px); box-shadow: var(--shadow-medium); }
        
        /* News Cards */
        .news-card { display: flex; align-items: center; }
        .news-card img { width: 180px; height: 160px; object-fit: cover; }
        .news-content { padding: 20px; flex: 1; }
        .news-title { font-size: 1.2rem; color: var(--brand-deep); margin-bottom: 10px; font-weight: 700; }
        .news-text { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 15px; }
        .news-date { font-size: 0.8rem; color: var(--warm-gold); }

        /* Project Cards */
        .project-card img { width: 100%; height: 220px; object-fit: cover; }
        .project-content { padding: 25px; text-align: center; }
        
        .btn-outline {
            display: inline-block; padding: 8px 20px; border: 2px solid var(--brand-light);
            color: var(--brand-light); border-radius: 30px; font-weight: bold; transition: var(--transition-smooth); font-size: 0.9rem;
        }
        .btn-outline:hover { background: var(--brand-light); color: var(--white); }

        /* --- Partners --- */
        .partners { padding: 80px 0; background-color: var(--white); text-align: center; }
        .partners-grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 40px; align-items: center; }
        .partner-img { max-width: 140px; max-height: 80px; filter: grayscale(100%); opacity: 0.6; transition: var(--transition-smooth); }
        .partner-img:hover { filter: grayscale(0%); opacity: 1; transform: scale(1.05); }

        /* --- Testimonials Bubble --- */
        .testimonials { padding: 100px 0; background: linear-gradient(135deg, var(--brand-deep), var(--brand-light)); color: var(--white); text-align: center; }
        .testimonial-bubble {
            background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: var(--radius-lg);
            padding: 50px; box-shadow: 0 15px 50px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto; position: relative;
        }
        .testimonial-bubble::after {
            content: ''; position: absolute; bottom: -25px; left: 50%; transform: translateX(-50%);
            border-left: 25px solid transparent; border-right: 25px solid transparent; border-top: 25px solid rgba(255, 255, 255, 0.1);
        }
        .testimonial-icon { font-size: 3rem; color: var(--warm-gold); margin-bottom: 20px; }
        .testimonial-quote { font-size: 1.3rem; font-style: italic; margin-bottom: 20px; line-height: 1.6; }

        /* --- Footer --- */
        footer { background-color: #1a1a1a; color: #ccc; padding: 70px 0 30px; border-radius: var(--radius-lg) var(--radius-lg) 0 0; }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-bottom: 40px; }
        .footer-col h4 { color: var(--white); margin-bottom: 20px; font-size: 1.2rem; position: relative; padding-bottom: 10px; }
        .footer-col h4::after { content: ''; position: absolute; width: 40px; height: 2px; background-color: var(--brand-light); bottom: 0; right: 0; }
        .footer-col ul li { margin-bottom: 10px; }
        .footer-col ul a { transition: var(--transition-smooth); }
        .footer-col ul a:hover { color: var(--brand-light); padding-right: 5px; }
        .contact-info li { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
        .contact-info i { color: var(--warm-gold); }
        .copyright { text-align: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 25px; font-size: 0.9rem; }

        /* --- Responsive --- */
        @media (max-width: 991px) {
            .mixed-grid { grid-template-columns: 1fr; }
            .news-card { flex-direction: column; }
            .news-card img { width: 100%; height: 200px; }
        }
        @media (max-width: 768px) {
            header { height: 80px; }
            .logo img { height: 45px; }
            .menu-toggle { display: block; }
            .nav-menu {
                display: none; flex-direction: column; position: absolute; top: 100%; left: 0; right: 0;
                background-color: rgba(255, 255, 255, 0.98); box-shadow: 0 10px 15px rgba(0,0,0,0.1);
                padding: 20px; gap: 15px; border-radius: 0 0 20px 20px; text-align: center;
            }
            .nav-menu.active { display: flex; }
            .nav-menu a { color: var(--brand-deep) !important; }
            .donate-btn { width: 100%; }
            .hero h1 { font-size: 2.5rem; }
            .about-cards { margin-top: 0; padding-top: 40px; }
            .section-title { font-size: 2.2rem; }
        }
    </style>
</head>
<body>

    <header id="main-header">
        <div class="container navbar">
            <div class="logo">
                <a href="/">
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ $associationName }}">
                    @else
                        <h2>{{ $associationName }}</h2>
                    @endif
                </a>
            </div>
            
            <i class="fas fa-bars menu-toggle" id="mobile-menu"></i>

            <ul class="nav-menu" id="nav-menu">
                <li><a href="/">الرئيسية</a></li>
                <li><a href="#about">عن الجمعية</a></li>
                <li><a href="#stats">الإحصائيات</a></li>
                <li><a href="#projects">المشاريع</a></li>
                <li><a href="#news">الأخبار</a></li>
                <li><a href="#partners">الشركاء</a></li>
                <li><a href="#contact" class="donate-btn">تواصل معنا</a></li>
            </ul>
        </div>
    </header>

    <section class="hero">
        <div id="slidesContainer">
            @if(isset($sliders) && is_iterable($sliders) && count($sliders) > 0)
                @foreach($sliders as $index => $slide)
                    @php
                        $slideImg = !empty($slide->image) ? asset('storage/' . ltrim($slide->image, '/')) : 'https://via.placeholder.com/1600x900/0b4b3c/ffffff?text=صورة+السلايدر';
                        $slideTitle = $slide->title ?? 'مرحباً بكم';
                        $slideSubtitle = $slide->subtitle ?? '';
                        $slideDesc = $slide->description ?? '';
                    @endphp
                    <div class="slide {{ $index === 0 ? 'active' : '' }}" style="background-image: url('{{ $slideImg }}');">
                        <div class="hero-content">
                            @if(!empty($slideSubtitle)) <h3>{{ $slideSubtitle }}</h3> @endif
                            <h1>{{ $slideTitle }}</h1>
                            @if(!empty($slideDesc)) <p>{{ $slideDesc }}</p> @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="slide active" style="background-image: url('https://via.placeholder.com/1920x1080/0b4b3c/ffffff?text=صورة+السلايدر');">
                    <div class="hero-content">
                        <h3>أهلاً بكم في</h3>
                        <h1>{{ $associationName }}</h1>
                        <p>{{ $siteDesc }}</p>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="slider-dots" id="sliderDots">
            @if(isset($sliders) && is_iterable($sliders) && count($sliders) > 1)
                @foreach($sliders as $index => $slide)
                    <div class="dot {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}"></div>
                @endforeach
            @endif
        </div>
    </section>

    <section id="about" class="about">
        <div class="container">
            <div class="about-cards">
                <div class="about-card">
                    <i class="fas fa-info-circle"></i>
                    <h3>نبذة عن الجمعية</h3>
                    <p>{{ $aboutText }}</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-eye"></i>
                    <h3>رؤيتنا</h3>
                    <p>{{ $visionText }}</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-bullseye"></i>
                    <h3>رسالتنا</h3>
                    <p>{{ $missionText }}</p>
                </div>
            </div>
        </div>
    </section>

    <section id="stats" class="stats">
        <div class="container">
            <h2 class="section-title">أرقامنا في سطور</h2>
            <div class="stats-grid">
                @if(isset($statistics) && is_iterable($statistics) && count($statistics) > 0)
                    @foreach($statistics as $stat)
                        @php
                            $statNum = $stat->number ?? 0;
                            $statTitle = $stat->title ?? 'إحصائية';
                            $statIcon = $stat->icon ?? 'fas fa-chart-bar';
                        @endphp
                        <div class="stat-item">
                            <div class="stat-circle">
                                <i class="{{ $statIcon }}"></i>
                                <h3 class="counter" data-target="{{ (int)$statNum }}">0</h3>
                            </div>
                            <p>{{ $statTitle }}</p>
                        </div>
                    @endforeach
                @else
                    <div class="stat-item">
                        <div class="stat-circle">
                            <i class="fas fa-users"></i>
                            <h3 class="counter" data-target="5200">0</h3>
                        </div>
                        <p>مستفيد</p>
                    </div>
                    <div class="stat-item">
                        <div class="stat-circle">
                            <i class="fas fa-project-diagram"></i>
                            <h3 class="counter" data-target="150">0</h3>
                        </div>
                        <p>مشروع منجز</p>
                    </div>
                    <div class="stat-item">
                        <div class="stat-circle">
                            <i class="fas fa-handshake"></i>
                            <h3 class="counter" data-target="45">0</h3>
                        </div>
                        <p>شريك نجاح</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section id="projects" class="grid-section">
        <div class="container">
            <div class="mixed-grid">
                
                <div class="projects-col">
                    <h3 class="col-title">أبرز مشاريعنا</h3>
                    <div class="card project-card">
                        <img src="https://via.placeholder.com/600x400/eeeeee/333333?text=كفالة+يتيم" alt="كفالة يتيم">
                        <div class="project-content">
                            <h3 class="news-title">مشروع كفالة يتيم</h3>
                            <p class="news-text">توفير الرعاية الشاملة للأيتام ودعمهم في مسيرتهم التعليمية والاجتماعية.</p>
                            <a href="#" class="btn-outline">تفاصيل المشروع</a>
                        </div>
                    </div>
                    <div class="card project-card">
                        <img src="https://via.placeholder.com/600x400/eeeeee/333333?text=سقيا+الماء" alt="سقيا الماء">
                        <div class="project-content">
                            <h3 class="news-title">مشروع سقيا الماء</h3>
                            <p class="news-text">توفير مياه صالحة للشرب في الأماكن العامة والمناطق النائية.</p>
                            <a href="#" class="btn-outline">تفاصيل المشروع</a>
                        </div>
                    </div>
                </div>

                <div id="news" class="news-col">
                    <h3 class="col-title">المركز الإعلامي</h3>
                    @if(isset($news) && is_iterable($news) && count($news) > 0)
                        @php $newsCount = 0; @endphp
                        @foreach($news as $item)
                            @if($newsCount >= 3) @break @endif
                            @php
                                $newsImg = !empty($item->featured_image) ? asset('storage/' . ltrim($item->featured_image, '/')) : 'https://via.placeholder.com/400x250/eeeeee/333333?text=خبر';
                                $newsTitle = $item->title ?? 'عنوان الخبر';
                                // جلب الملخص أو تنظيف المحتوى
                                $newsExcerpt = $item->excerpt ?? '';
                                if(empty($newsExcerpt) && isset($item->content)) {
                                    $newsExcerpt = \Illuminate\Support\Str::limit(strip_tags($item->content), 80);
                                }
                                $newsDate = !empty($item->published_at) ? \Carbon\Carbon::parse($item->published_at)->format('Y/m/d') : '';
                                $newsSlug = !empty($item->slug) ? '/news/' . $item->slug : '#';
                                $newsCount++;
                            @endphp
                            <div class="card news-card">
                                <img src="{{ $newsImg }}" alt="{{ $newsTitle }}">
                                <div class="news-content">
                                    @if(!empty($newsDate))
                                        <span class="news-date"><i class="far fa-clock"></i> {{ $newsDate }}</span>
                                    @endif
                                    <h3 class="news-title">{{ \Illuminate\Support\Str::limit($newsTitle, 40) }}</h3>
                                    <p class="news-text">{{ $newsExcerpt }}</p>
                                    <a href="{{ $newsSlug }}" class="btn-outline" style="padding: 4px 12px; font-size: 0.8rem;">المزيد</a>
                                </div>
                            </div>
                        @endforeach
                        <div style="text-align: center; margin-top: 15px;">
                            <a href="/news" class="donate-btn" style="padding: 10px 25px; font-size: 0.9rem;">كل الأخبار</a>
                        </div>
                    @else
                        <div class="card news-card">
                            <div class="news-content" style="text-align: center;">
                                <p class="news-text">لا توجد أخبار مضافة حالياً.</p>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>

    <section id="partners" class="partners">
        <div class="container">
            <h2 class="section-title" style="font-size: 2.2rem;">شركاء النجاح</h2>
            <div class="partners-grid">
                @if(isset($partners) && is_iterable($partners) && count($partners) > 0)
                    @foreach($partners as $partner)
                        @php
                            $partnerLogo = !empty($partner->logo) ? asset('storage/' . ltrim($partner->logo, '/')) : 'https://via.placeholder.com/150x80/ffffff/cccccc?text=شريك';
                            $partnerName = $partner->name ?? 'شريك نجاح';
                        @endphp
                        <img src="{{ $partnerLogo }}" alt="{{ $partnerName }}" class="partner-img">
                    @endforeach
                @else
                    <img src="https://via.placeholder.com/150x80/ffffff/cccccc?text=شريك+1" alt="شريك 1" class="partner-img">
                    <img src="https://via.placeholder.com/150x80/ffffff/cccccc?text=شريك+2" alt="شريك 2" class="partner-img">
                    <img src="https://via.placeholder.com/150x80/ffffff/cccccc?text=شريك+3" alt="شريك 3" class="partner-img">
                    <img src="https://via.placeholder.com/150x80/ffffff/cccccc?text=شريك+4" alt="شريك 4" class="partner-img">
                @endif
            </div>
        </div>
    </section>

    <section class="testimonials">
        <div class="container">
            <div class="testimonial-bubble">
                <i class="fas fa-quote-right testimonial-icon"></i>
                <p class="testimonial-quote">"جهود عظيمة ومباركة، تعاملنا مع الجمعية ولمسنا احترافية عالية وشفافية في تنفيذ المشاريع وايصال الدعم لمستحقيه."</p>
                <div class="testimonial-author">
                    <h4>- أحد الداعمين</h4>
                </div>
            </div>
        </div>
    </section>

    <footer id="contact">
        <div class="container footer-grid">
            <div class="footer-col">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ $associationName }}" style="max-height: 50px; margin-bottom: 20px; filter: brightness(0) invert(1);">
                @else
                    <h4>{{ $associationName }}</h4>
                @endif
                <p style="color: #aaa; font-size: 0.95rem; line-height: 1.8;">{{ $siteDesc }}</p>
                @if(!empty($safeSettings->license_number))
                    <p style="color: var(--warm-gold); font-size: 0.85rem; margin-top: 10px;">ترخيص رقم: {{ $safeSettings->license_number }}</p>
                @endif
            </div>
            <div class="footer-col">
                <h4>روابط سريعة</h4>
                <ul>
                    <li><a href="/">الرئيسية</a></li>
                    <li><a href="#about">من نحن</a></li>
                    <li><a href="#projects">المشاريع</a></li>
                    <li><a href="/news">المركز الإعلامي</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>تواصل معنا</h4>
                <ul class="contact-info">
                    @if(!empty($safeSettings->phone))
                        <li><i class="fas fa-phone"></i> <span dir="ltr">{{ $safeSettings->phone }}</span></li>
                    @endif
                    @if(!empty($safeSettings->email))
                        <li><i class="fas fa-envelope"></i> {{ $safeSettings->email }}</li>
                    @endif
                    @if(!empty($safeSettings->whatsapp_url))
                        <li><i class="fab fa-whatsapp"></i> <a href="{{ $safeSettings->whatsapp_url }}" target="_blank" style="color: inherit;">راسلنا عبر الواتساب</a></li>
                    @endif
                    @if(empty($safeSettings->phone) && empty($safeSettings->email) && empty($safeSettings->whatsapp_url))
                        <li><i class="fas fa-map-marker-alt"></i> المملكة العربية السعودية</li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>جميع الحقوق محفوظة &copy; {{ date('Y') }} لـ {{ $associationName }}</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sticky Header
            window.addEventListener('scroll', () => {
                const header = document.getElementById('main-header');
                if (header) {
                    if (window.scrollY > 50) header.classList.add('scrolled');
                    else header.classList.remove('scrolled');
                }
            });

            // Mobile Menu
            const mobileMenu = document.getElementById('mobile-menu');
            const navMenu = document.getElementById('nav-menu');
            if(mobileMenu && navMenu) {
                mobileMenu.addEventListener('click', () => navMenu.classList.toggle('active'));
            }

            // Slider
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.dot');
            let currentSlide = 0;
            let slideInterval;

            function showSlide(index) {
                slides.forEach(s => s.classList.remove('active'));
                dots.forEach(d => d.classList.remove('active'));
                if(slides[index]) slides[index].classList.add('active');
                if(dots[index]) dots[index].classList.add('active');
                currentSlide = index;
            }

            if(slides.length > 1) {
                slideInterval = setInterval(() => { showSlide((currentSlide + 1) % slides.length); }, 5000);
                dots.forEach(dot => {
                    dot.addEventListener('click', function() {
                        clearInterval(slideInterval);
                        let idx = parseInt(this.getAttribute('data-index'));
                        if(!isNaN(idx)) {
                            showSlide(idx);
                            slideInterval = setInterval(() => { showSlide((currentSlide + 1) % slides.length); }, 5000);
                        }
                    });
                });
            }

            // Counter Animation
            const counters = document.querySelectorAll('.counter');
            if(counters.length > 0) {
                const startCounting = (el) => {
                    const targetAttr = el.getAttribute('data-target');
                    if(!targetAttr) return;
                    
                    const target = +targetAttr;
                    const count = +el.innerText;
                    const inc = target / 150; // سرعة العداد
                    
                    if (count < target) {
                        el.innerText = Math.ceil(count + inc);
                        setTimeout(() => startCounting(el), 15);
                    } else {
                        el.innerText = target;
                    }
                };

                const observer = new IntersectionObserver((entries, obs) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            startCounting(entry.target);
                            obs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.5 });

                counters.forEach(counter => observer.observe(counter));
            }
        });
    </script>
</body>
</html>
