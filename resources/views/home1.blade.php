@php
    // تجهيز اسم الجمعية
    $associationName = $siteSettings->association_name ?? $association->name ?? $siteSettings->site_name ?? 'اسم الجمعية';

    // تجهيز الشعار
    $logo = null;
    if (!empty($siteSettings->logo)) {
        $logo = asset('storage/' . ltrim($siteSettings->logo, '/'));
    } elseif (!empty($siteSettings->logo_media_id)) {
        $mediaLogo = \App\Models\MediaItem::find($siteSettings->logo_media_id);
        if ($mediaLogo && !empty($mediaLogo->file)) {
            $logo = asset('storage/' . ltrim($mediaLogo->file, '/'));
        }
    }
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
        /* المتغيرات الأساسية للتصميم */
        :root {
            --primary-color: #0b4b3c;
            --secondary-color: #2e8b57;
            --accent-color: #c9a45e;
            --text-dark: #333333;
            --text-light: #777777;
            --bg-light: #f9f9f9;
            --white: #ffffff;
            --transition: all 0.3s ease-in-out;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }

        body {
            background-color: var(--white);
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section-padding {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: var(--primary-color);
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            width: 60%;
            height: 3px;
            background-color: var(--accent-color);
            bottom: -10px;
            right: 20%;
            border-radius: 2px;
        }

        /* --- الهيدر (Header) --- */
        .header {
            background-color: var(--white);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo img {
            max-height: 60px;
        }

        .logo h2 {
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 25px;
        }

        .nav-links a {
            font-weight: 600;
            color: var(--primary-color);
            transition: var(--transition);
        }

        .nav-links a:hover {
            color: var(--accent-color);
        }

        .nav-actions {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: bold;
            transition: var(--transition);
            cursor: pointer;
            border: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-accent {
            background-color: var(--accent-color);
            color: var(--white);
        }

        .btn-accent:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .mobile-toggle {
            display: none;
            font-size: 1.8rem;
            color: var(--primary-color);
            cursor: pointer;
        }

        /* --- السلايدر (Slider) --- */
        .slider-section {
            position: relative;
            height: 80vh;
            min-height: 500px;
            overflow: hidden;
        }

        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(11, 75, 60, 0.6); /* غطاء لوني (Overlay) */
        }

        .slide.active {
            opacity: 1;
            z-index: 1;
        }

        .slide-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: var(--white);
            max-width: 800px;
            padding: 20px;
            transform: translateY(30px);
            opacity: 0;
            transition: all 0.8s ease-in-out 0.3s;
        }

        .slide.active .slide-content {
            transform: translateY(0);
            opacity: 1;
        }

        .slide-content h3 {
            font-size: 1.5rem;
            color: var(--accent-color);
            margin-bottom: 10px;
        }

        .slide-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .slide-content p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        .slider-dots {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: var(--transition);
        }

        .dot.active {
            background-color: var(--accent-color);
            transform: scale(1.3);
        }

        /* --- التعريف بالجمعية (About) --- */
        .about-section {
            background-color: var(--bg-light);
        }

        .about-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .about-card {
            background-color: var(--white);
            padding: 40px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
            transition: var(--transition);
        }

        .about-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .about-card i {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }

        .about-card h3 {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .about-card p {
            color: var(--text-light);
            font-size: 1rem;
        }

        /* --- الإحصائيات (Statistics) --- */
        .stats-section {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: var(--white);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            text-align: center;
        }

        .stat-item i {
            font-size: 3rem;
            color: var(--accent-color);
            margin-bottom: 15px;
        }

        .stat-item h3 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .stat-item p {
            font-size: 1.1rem;
            font-weight: 500;
            opacity: 0.9;
        }

        /* --- المشاريع (Projects - Static) --- */
        .projects-section {
            background-color: var(--white);
        }

        /* --- الأخبار (News) --- */
        .news-section {
            background-color: var(--bg-light);
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }

        .card {
            background-color: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .card-body {
            padding: 25px;
        }

        .card-date {
            display: block;
            font-size: 0.85rem;
            color: var(--accent-color);
            margin-bottom: 10px;
        }

        .card-title {
            font-size: 1.25rem;
            color: var(--primary-color);
            margin-bottom: 15px;
            font-weight: 700;
        }

        .card-excerpt {
            color: var(--text-light);
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .center-action {
            text-align: center;
            margin-top: 40px;
        }

        /* --- شركاء النجاح (Partners) --- */
        .partners-section {
            background-color: var(--white);
        }

        .partners-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 40px;
        }

        .partner-img {
            max-width: 150px;
            max-height: 80px;
            filter: grayscale(100%);
            opacity: 0.7;
            transition: var(--transition);
        }

        .partner-img:hover {
            filter: grayscale(0%);
            opacity: 1;
        }

        /* --- ماذا يقال عنا (Testimonials - Static) --- */
        .testimonials-section {
            background-color: var(--bg-light);
        }

        .testimonial-card {
            background-color: var(--white);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        }

        .testimonial-icon {
            font-size: 2.5rem;
            color: rgba(201, 164, 94, 0.3);
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .testimonial-text {
            font-size: 1.1rem;
            color: var(--text-dark);
            font-style: italic;
            margin: 20px 0;
        }

        .testimonial-author {
            font-weight: bold;
            color: var(--primary-color);
        }

        /* --- الفوتر (Footer) --- */
        .footer {
            background-color: var(--primary-color);
            color: var(--white);
            padding: 60px 0 20px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-col h4 {
            color: var(--accent-color);
            font-size: 1.3rem;
            margin-bottom: 20px;
        }

        .footer-col p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 15px;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--accent-color);
            padding-right: 5px;
        }

        .contact-info li {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            color: rgba(255, 255, 255, 0.8);
        }

        .contact-info i {
            color: var(--accent-color);
            font-size: 1.2rem;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        /* --- التجاوب (Responsive) --- */
        @media (max-width: 992px) {
            .slide-content h1 { font-size: 2.5rem; }
        }

        @media (max-width: 768px) {
            .mobile-toggle { display: block; }
            .nav-menu {
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background-color: var(--white);
                flex-direction: column;
                align-items: center;
                padding: 20px 0;
                box-shadow: 0 10px 15px rgba(0,0,0,0.05);
                display: none;
            }
            .nav-menu.active { display: flex; }
            .nav-links {
                flex-direction: column;
                text-align: center;
                gap: 15px;
                width: 100%;
                margin-bottom: 20px;
            }
            .slide-content h1 { font-size: 2rem; }
            .section-title h2 { font-size: 2rem; }
            .nav-actions { flex-direction: column; width: 80%; }
            .btn { text-align: center; width: 100%; }
        }
    </style>
</head>
<body>

    <header class="header">
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

            <i class="fas fa-bars mobile-toggle" id="mobileToggle"></i>

            <div class="nav-menu" id="navMenu">
                <ul class="nav-links">
                    <li><a href="/">الرئيسية</a></li>
                    <li><a href="#about">عن الجمعية</a></li>
                    <li><a href="#stats">الإحصائيات</a></li>
                    <li><a href="#projects">المشاريع</a></li>
                    <li><a href="/news">الأخبار</a></li>
                    <li><a href="#partners">الشركاء</a></li>
                </ul>
                <div class="nav-actions">
                    <a href="#contact" class="btn btn-primary">تواصل معنا</a>
                    <a href="#" class="btn btn-accent">تبرع الآن</a>
                </div>
            </div>
        </div>
    </header>

    <section class="slider-section">
        <div id="slidesContainer">
            @if(isset($sliders) && $sliders->count() > 0)
                @foreach($sliders as $index => $slide)
                    <div class="slide {{ $index === 0 ? 'active' : '' }}" style="background-image: url('{{ asset('storage/' . ltrim($slide->image, '/')) }}');">
                        <div class="slide-content">
                            @if(!empty($slide->subtitle)) <h3>{{ $slide->subtitle }}</h3> @endif
                            @if(!empty($slide->title)) <h1>{{ $slide->title }}</h1> @endif
                            @if(!empty($slide->description)) <p>{{ $slide->description }}</p> @endif
                            <a href="#" class="btn btn-accent" style="margin-top: 15px;">اكتشف المزيد</a>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="slide active" style="background-image: url('https://via.placeholder.com/1920x1080/0b4b3c/ffffff?text=صورة+السلايدر');">
                    <div class="slide-content">
                        <h3>أهلاً بكم في</h3>
                        <h1>{{ $associationName }}</h1>
                        <p>{{ $siteSettings->site_description ?? 'نسعى لتقديم أفضل الخدمات للمجتمع وبناء مستقبل مشرق معاً.' }}</p>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="slider-dots" id="sliderDots">
            @if(isset($sliders) && $sliders->count() > 1)
                @foreach($sliders as $index => $slide)
                    <div class="dot {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}"></div>
                @endforeach
            @endif
        </div>
    </section>

    <section id="about" class="section-padding about-section">
        <div class="container">
            <div class="section-title">
                <h2>من نحن</h2>
            </div>
            <div class="about-grid">
                <div class="about-card">
                    <i class="fas fa-info-circle"></i>
                    <h3>نبذة عن الجمعية</h3>
                    <p>{{ $siteSettings->about_text ?? 'تأسست الجمعية لتقديم خدمات رائدة تساهم في تنمية المجتمع ودعم الفئات المستهدفة بأعلى معايير الجودة والشفافية.' }}</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-eye"></i>
                    <h3>رؤيتنا</h3>
                    <p>{{ $siteSettings->vision ?? 'الريادة والتميز في العمل الخيري المؤسسي، وتحقيق الاستدامة في جميع برامجنا ومشاريعنا التنموية.' }}</p>
                </div>
                <div class="about-card">
                    <i class="fas fa-bullseye"></i>
                    <h3>رسالتنا</h3>
                    <p>{{ $siteSettings->mission ?? 'تمكين المستفيدين وتقديم الدعم اللازم لهم عبر برامج نوعية مبتكرة وشراكات فاعلة مع كافة قطاعات المجتمع.' }}</p>
                </div>
            </div>
        </div>
    </section>

    <section id="stats" class="section-padding stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <h3 class="counter" data-target="5200">0</h3>
                    <p>مستفيد</p>
                </div>
                <div class="stat-item">
                    <i class="fas fa-project-diagram"></i>
                    <h3 class="counter" data-target="150">0</h3>
                    <p>مشروع منجز</p>
                </div>
                <div class="stat-item">
                    <i class="fas fa-handshake"></i>
                    <h3 class="counter" data-target="45">0</h3>
                    <p>شريك نجاح</p>
                </div>
                <div class="stat-item">
                    <i class="fas fa-calendar-alt"></i>
                    <h3 class="counter" data-target="10">0</h3>
                    <p>سنوات من العطاء</p>
                </div>
            </div>
        </div>
    </section>

    <section id="projects" class="section-padding projects-section">
        <div class="container">
            <div class="section-title">
                <h2>أبرز مشاريعنا</h2>
            </div>
            <div class="cards-grid">
                <div class="card">
                    <img src="https://via.placeholder.com/600x400/eeeeee/333333?text=مشروع+كفالة+يتيم" alt="مشروع كفالة" class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">مشروع كفالة يتيم</h3>
                        <p class="card-excerpt">توفير الرعاية الشاملة للأيتام ودعمهم في مسيرتهم التعليمية والاجتماعية لضمان حياة كريمة.</p>
                        <a href="#" class="btn btn-primary" style="padding: 5px 15px; font-size: 0.9rem;">تفاصيل المشروع</a>
                    </div>
                </div>
                <div class="card">
                    <img src="https://via.placeholder.com/600x400/eeeeee/333333?text=مشروع+السلة+الغذائية" alt="مشروع السلة" class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">مشروع السلال الغذائية</h3>
                        <p class="card-excerpt">توزيع سلال غذائية متكاملة تلبي احتياجات الأسر المتعففة والمحتاجة بشكل دوري.</p>
                        <a href="#" class="btn btn-primary" style="padding: 5px 15px; font-size: 0.9rem;">تفاصيل المشروع</a>
                    </div>
                </div>
                <div class="card">
                    <img src="https://via.placeholder.com/600x400/eeeeee/333333?text=مشروع+سقيا+الماء" alt="مشروع سقيا" class="card-img">
                    <div class="card-body">
                        <h3 class="card-title">مشروع سقيا الماء</h3>
                        <p class="card-excerpt">توفير مياه صالحة للشرب في الأماكن العامة والمناطق النائية كصدقة جارية.</p>
                        <a href="#" class="btn btn-primary" style="padding: 5px 15px; font-size: 0.9rem;">تفاصيل المشروع</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding news-section">
        <div class="container">
            <div class="section-title">
                <h2>آخر الأخبار</h2>
            </div>
            <div class="cards-grid">
                @if(isset($news) && $news->count() > 0)
                    @foreach($news->take(3) as $item)
                        <div class="card">
                            @if(!empty($item->featured_image))
                            <img src="{{ asset('storage/' . ltrim($item->featured_image, '/')) }}" alt="{{ $item->title }}" class="card-img">
                        @else
                            <img src="https://via.placeholder.com/600x400/eeeeee/333333?text=خبر" alt="{{ $item->title }}" class="card-img">
                        @endif
                            <div class="card-body">
                                @if(!empty($item->published_at))
                                    <span class="card-date"><i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($item->published_at)->format('Y/m/d') }}</span>
                                @endif
                                <h3 class="card-title">{{ Str::limit($item->title, 50) }}</h3>
                                <p class="card-excerpt">{{ Str::limit($item->excerpt ?? strip_tags($item->content), 100) }}</p>
                                <a href="/news/{{ $item->slug }}" class="btn btn-primary" style="padding: 5px 15px; font-size: 0.9rem;">قراءة المزيد</a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="text-align: center; width: 100%; color: var(--text-light);">لا توجد أخبار مضافة حالياً.</p>
                @endif
            </div>
            <div class="center-action">
                <a href="/news" class="btn btn-accent">تصفح كل الأخبار</a>
            </div>
        </div>
    </section>

    <section class="section-padding testimonials-section">
        <div class="container">
            <div class="section-title">
                <h2>ماذا يقال عنا</h2>
            </div>
            <div class="cards-grid">
                <div class="testimonial-card">
                    <i class="fas fa-quote-right testimonial-icon"></i>
                    <p class="testimonial-text">"جهود عظيمة ومباركة، تعاملنا مع الجمعية ولمسنا احترافية عالية وشفافية في تنفيذ المشاريع وايصال الدعم لمستحقيه."</p>
                    <h4 class="testimonial-author">- أحد الداعمين</h4>
                </div>
                <div class="testimonial-card">
                    <i class="fas fa-quote-right testimonial-icon"></i>
                    <p class="testimonial-text">"نظام وتطور تقني ملحوظ يسهل على المستفيدين التواصل والتقديم على الخدمات بكل يسر وسهولة."</p>
                    <h4 class="testimonial-author">- مستفيد من البرامج</h4>
                </div>
                <div class="testimonial-card">
                    <i class="fas fa-quote-right testimonial-icon"></i>
                    <p class="testimonial-text">"شراكتنا مع الجمعية حققت أهدافنا المجتمعية بفضل التنظيم الإداري والعمل المؤسسي المتقن."</p>
                    <h4 class="testimonial-author">- جهة شريكة</h4>
                </div>
            </div>
        </div>
    </section>

    <section id="partners" class="section-padding partners-section">
        <div class="container">
            <div class="section-title">
                <h2>شركاء النجاح</h2>
            </div>
            <div class="partners-grid">
                @if(isset($partners) && $partners->count() > 0)
                    @foreach($partners as $partner)
                        <img src="{{ asset('storage/' . ltrim($partner->logo, '/')) }}" alt="{{ $partner->name }}" class="partner-img">
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

    <footer id="contact" class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ $associationName }}" style="max-height: 60px; margin-bottom: 15px; filter: brightness(0) invert(1);">
                    @else
                        <h4>{{ $associationName }}</h4>
                    @endif
                    <p>{{ $siteSettings->site_description ?? 'نسعى لتقديم العمل الخيري بأسلوب مؤسسي تقني حديث، لضمان وصول الدعم لمستحقيه بأعلى معايير الشفافية.' }}</p>
                    @if(!empty($siteSettings->license_number))
                        <p style="font-size: 0.9rem; color: var(--accent-color);">ترخيص رقم: {{ $siteSettings->license_number }}</p>
                    @endif
                </div>

                <div class="footer-col">
                    <h4>روابط سريعة</h4>
                    <ul class="footer-links">
                        <li><a href="/">الرئيسية</a></li>
                        <li><a href="#about">من نحن</a></li>
                        <li><a href="#projects">المشاريع</a></li>
                        <li><a href="/news">المركز الإعلامي</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>تواصل معنا</h4>
                    <ul class="contact-info">
                        @if(!empty($siteSettings->phone))
                            <li><i class="fas fa-phone"></i> <span dir="ltr">{{ $siteSettings->phone }}</span></li>
                        @endif
                        @if(!empty($siteSettings->email))
                            <li><i class="fas fa-envelope"></i> {{ $siteSettings->email }}</li>
                        @endif
                        @if(!empty($siteSettings->whatsapp_url))
                            <li><i class="fab fa-whatsapp"></i> <a href="{{ $siteSettings->whatsapp_url }}" target="_blank">راسلنا عبر الواتساب</a></li>
                        @endif
                        <li><i class="fas fa-map-marker-alt"></i> المملكة العربية السعودية</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>جميع الحقوق محفوظة &copy; {{ date('Y') }} لـ {{ $associationName }}</p>
            </div>
        </div>
    </footer>

    <script>
        // 1. Mobile Menu Toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const navMenu = document.getElementById('navMenu');

        if(mobileToggle && navMenu) {
            mobileToggle.addEventListener('click', () => {
                navMenu.classList.toggle('active');
            });
        }

        // 2. Simple Hero Slider
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        let currentSlide = 0;
        let slideInterval;

        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            slides[index].classList.add('active');
            if(dots[index]) dots[index].classList.add('active');
            currentSlide = index;
        }

        function nextSlide() {
            let next = (currentSlide + 1) % slides.length;
            showSlide(next);
        }

        if(slides.length > 1) {
            slideInterval = setInterval(nextSlide, 5000); // تغيير كل 5 ثواني
            
            dots.forEach(dot => {
                dot.addEventListener('click', function() {
                    clearInterval(slideInterval);
                    showSlide(parseInt(this.getAttribute('data-index')));
                    slideInterval = setInterval(nextSlide, 5000);
                });
            });
        }

        // 3. Counter Animation on Scroll
        const counters = document.querySelectorAll('.counter');
        const speed = 200; // سرعة العداد

        const startCounting = (el) => {
            const target = +el.getAttribute('data-target');
            const count = +el.innerText;
            const inc = target / speed;

            if (count < target) {
                el.innerText = Math.ceil(count + inc);
                setTimeout(() => startCounting(el), 10);
            } else {
                el.innerText = target;
            }
        };

        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.5
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    startCounting(entry.target);
                    observer.unobserve(entry.target); // تشغيل مرة واحدة فقط
                }
            });
        }, observerOptions);

        counters.forEach(counter => {
            observer.observe(counter);
        });
    </script>
</body>
</html>
