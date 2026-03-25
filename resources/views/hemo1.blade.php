<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $association->name ?? ($siteSettings->site_name ?? 'الصفحة الرئيسية') }}</title>
    <style>
        :root{
            --primary:#4fd4ff;
            --primary-dark:#22b8e6;
            --text:#1f2937;
            --muted:#6b7280;
            --bg:#f8fcff;
            --white:#ffffff;
            --border:#e5eef5;
            --shadow:0 10px 30px rgba(16, 24, 40, .08);
            --radius:22px;
        }

        *{box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{
            margin:0;
            font-family: Tahoma, Arial, sans-serif;
            background:linear-gradient(180deg,#fafdff 0%, #f4fbff 100%);
            color:var(--text);
            line-height:1.8;
        }

        a{text-decoration:none;color:inherit}
        img{max-width:100%;display:block}
        .container{width:min(1180px, calc(100% - 32px));margin:auto}

        .topbar{
            background:#fff;
            border-bottom:1px solid var(--border);
        }
        .topbar-inner{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            min-height:72px;
        }
        .brand{
            display:flex;
            align-items:center;
            gap:14px;
        }
        .brand-logo{
            width:58px;height:58px;border-radius:18px;background:#eefbff;
            display:flex;align-items:center;justify-content:center;
            border:1px solid #d8f4fb;
            overflow:hidden;
            flex-shrink:0;
        }
        .brand-logo img{
            width:100%;
            height:100%;
            object-fit:contain;
            background:#fff;
        }
        .brand-text h1{
            margin:0;
            font-size:1.1rem;
            line-height:1.4;
        }
        .brand-text p{
            margin:2px 0 0;
            font-size:.9rem;
            color:var(--muted);
        }

        .nav{
            display:flex;
            align-items:center;
            gap:18px;
            flex-wrap:wrap;
            justify-content:center;
        }
        .nav a{
            font-size:.95rem;
            color:#334155;
            padding:10px 2px;
        }
        .nav a:hover{color:var(--primary-dark)}

        .header-actions{
            display:flex;
            align-items:center;
            gap:10px;
        }
        .btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            border:none;
            padding:12px 20px;
            border-radius:999px;
            cursor:pointer;
            font-weight:700;
            transition:.2s ease;
        }
        .btn-primary{
            background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            color:#fff;
            box-shadow:0 12px 24px rgba(79,212,255,.28);
        }
        .btn-primary:hover{transform:translateY(-1px)}
        .btn-soft{
            background:#eefbff;
            color:#0f6f87;
            border:1px solid #d4f4fc;
        }

        .hero{
            padding:34px 0 26px;
        }
        .hero-grid{
            display:grid;
            grid-template-columns: 1.15fr .85fr;
            gap:24px;
            align-items:stretch;
        }
        .hero-card{
            background:var(--white);
            border:1px solid var(--border);
            border-radius:32px;
            box-shadow:var(--shadow);
            overflow:hidden;
            position:relative;
        }
        .hero-content{
            padding:42px;
            background:
                radial-gradient(circle at top left, rgba(79,212,255,.22), transparent 35%),
                linear-gradient(180deg,#ffffff 0%, #f8fdff 100%);
        }
        .eyebrow{
            display:inline-flex;
            align-items:center;
            gap:8px;
            background:#ecfbff;
            color:#0f6f87;
            border:1px solid #d4f4fc;
            border-radius:999px;
            padding:8px 14px;
            font-size:.88rem;
            margin-bottom:16px;
        }
        .hero h2{
            margin:0 0 14px;
            font-size:clamp(2rem, 4vw, 3.5rem);
            line-height:1.2;
            color:#0f172a;
        }
        .hero p{
            margin:0;
            color:#475569;
            font-size:1.02rem;
        }
        .hero-actions{
            display:flex;
            gap:12px;
            flex-wrap:wrap;
            margin-top:24px;
        }

        .social-row{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            margin-top:22px;
        }
        .social-link{
            width:42px;height:42px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;
            background:#fff;border:1px solid var(--border);
            box-shadow:0 6px 16px rgba(15,23,42,.05);
            color:#334155;font-size:.9rem;
        }

        .hero-side{
            display:grid;
            gap:16px;
        }
        .hero-side .mini-card{
            background:#fff;
            border:1px solid var(--border);
            border-radius:26px;
            box-shadow:var(--shadow);
            overflow:hidden;
        }
        .hero-side .image-wrap{
            min-height:230px;
            background:linear-gradient(135deg,#e9fbff,#f9fdff);
            display:flex;
            align-items:center;
            justify-content:center;
            padding:22px;
        }
        .hero-side .image-wrap img{
            border-radius:22px;
            object-fit:cover;
            width:100%;
            max-height:280px;
        }
        .hero-side .mini-body{
            padding:22px;
        }
        .hero-side h3{
            margin:0 0 8px;
            font-size:1.2rem;
        }
        .hero-side p{
            margin:0;
            color:var(--muted);
        }

        .section{
            padding:26px 0;
        }
        .section-head{
            display:flex;
            align-items:end;
            justify-content:space-between;
            gap:20px;
            margin-bottom:18px;
        }
        .section-head h3{
            margin:0;
            font-size:1.7rem;
        }
        .section-head p{
            margin:8px 0 0;
            color:var(--muted);
        }

        .about-grid{
            display:grid;
            grid-template-columns: .95fr 1.05fr;
            gap:24px;
            align-items:start;
        }
        .card{
            background:#fff;
            border:1px solid var(--border);
            border-radius:28px;
            box-shadow:var(--shadow);
        }
        .about-image{
            min-height:100%;
            overflow:hidden;
        }
        .about-image img{
            width:100%;
            height:100%;
            object-fit:cover;
            min-height:420px;
        }
        .about-body{
            padding:28px;
        }
        .about-body h4{
            margin:0 0 10px;
            font-size:1.45rem;
        }
        .about-body p{
            color:#475569;
            margin:0 0 14px;
        }

        .stats{
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:16px;
            margin-top:18px;
        }
        .stat{
            padding:24px 18px;
            text-align:center;
            border-radius:24px;
            background:#fff;
            border:1px solid var(--border);
            box-shadow:var(--shadow);
        }
        .stat strong{
            display:block;
            font-size:2rem;
            color:#0f172a;
            line-height:1;
            margin-bottom:10px;
        }
        .stat span{
            color:var(--muted);
            font-size:.95rem;
        }

        .accordion{
            display:grid;
            gap:12px;
            margin-top:18px;
        }
        .accordion details{
            background:#fff;
            border:1px solid var(--border);
            border-radius:20px;
            box-shadow:var(--shadow);
            overflow:hidden;
        }
        .accordion summary{
            list-style:none;
            cursor:pointer;
            padding:18px 20px;
            font-weight:700;
            position:relative;
        }
        .accordion summary::-webkit-details-marker{display:none}
        .accordion summary::after{
            content:"+";
            position:absolute;
            left:20px;
            top:50%;
            transform:translateY(-50%);
            font-size:1.3rem;
            color:#0f6f87;
        }
        .accordion details[open] summary::after{content:"−"}
        .accordion .content{
            padding:0 20px 20px;
            color:#475569;
        }

        .news-grid{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:18px;
        }
        .news-card{
            overflow:hidden;
        }
        .news-image{
            height:190px;
            background:linear-gradient(135deg,#e8faff,#f5fdff);
            border-bottom:1px solid var(--border);
        }
        .news-image img{
            width:100%;
            height:100%;
            object-fit:cover;
        }
        .news-body{
            padding:20px;
        }
        .news-body h4{
            margin:0 0 10px;
            font-size:1.12rem;
            line-height:1.6;
        }
        .news-body p{
            margin:0 0 14px;
            color:var(--muted);
            font-size:.95rem;
        }
        .news-meta{
            color:#94a3b8;
            font-size:.88rem;
        }

        .partners{
            display:grid;
            grid-template-columns:repeat(5,1fr);
            gap:16px;
        }
        .partner{
            min-height:110px;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:18px;
        }
        .partner img{
            max-height:72px;
            object-fit:contain;
        }

        .cta{
            padding:34px;
            background:
                radial-gradient(circle at top right, rgba(79,212,255,.25), transparent 25%),
                linear-gradient(135deg,#ffffff,#f3fbff);
        }
        .cta h3{
            margin:0 0 10px;
            font-size:2rem;
        }
        .cta p{
            margin:0;
            color:#475569;
        }
        .cta .hero-actions{
            margin-top:18px;
        }

        footer{
            margin-top:32px;
            padding:26px 0 40px;
            color:#64748b;
            font-size:.95rem;
        }
        .footer-card{
            padding:22px 24px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            flex-wrap:wrap;
        }

        @media (max-width: 1024px){
            .hero-grid,
            .about-grid{
                grid-template-columns:1fr;
            }
            .stats{grid-template-columns:repeat(2,1fr)}
            .news-grid{grid-template-columns:repeat(2,1fr)}
            .partners{grid-template-columns:repeat(3,1fr)}
            .topbar-inner{
                flex-direction:column;
                justify-content:center;
                padding:14px 0;
            }
        }

        @media (max-width: 640px){
            .container{width:min(100% - 20px, 100%)}
            .hero-content{padding:24px}
            .about-body{padding:20px}
            .cta{padding:22px}
            .stats,
            .news-grid,
            .partners{
                grid-template-columns:1fr;
            }
            .hero h2{font-size:2rem}
            .section-head{
                align-items:start;
                flex-direction:column;
            }
            .nav{gap:10px}
        }
    </style>
</head>
<body>
    @php
        $siteName = $siteSettings->site_name ?? $association->name ?? 'اسم الجمعية';
        $associationName = $siteSettings->association_name ?? $association->name ?? $siteName;
        $aboutText = $siteSettings->about_text ?? $siteSettings->site_description ?? 'نبذة تعريفية عن الجمعية ورسالتها ومجالات عملها.';
        $vision = $siteSettings->vision ?? 'رؤية الجمعية في خدمة المجتمع وتحقيق أثر مستدام.';
        $mission = $siteSettings->mission ?? 'رسالة الجمعية في تقديم مبادرات نوعية وخدمات مؤثرة للمستفيدين.';
        $phone = $siteSettings->phone ?? null;
        $email = $siteSettings->email ?? null;
        $address = $siteSettings->address ?? null;
        $facebook = $siteSettings->facebook ?? null;
        $twitter = $siteSettings->twitter_url ?? null;
        $instagram = $siteSettings->instagram_url ?? null;
        $youtube = $siteSettings->youtube_url ?? null;
        $whatsapp = $siteSettings->whatsapp_url ?? null;
        $logo = null;

        if (!empty($siteSettings->logo)) {
            $logo = asset('storage/' . ltrim($siteSettings->logo, '/'));
        } elseif (!empty($siteSettings->logo_media_id) && function_exists('route')) {
            $media = \App\Models\MediaItem::find($siteSettings->logo_media_id);
            if ($media && !empty($media->file)) {
                $logo = asset('storage/' . ltrim($media->file, '/'));
            }
        }

        $heroImage = null;
        if (!empty($sliders) && count($sliders) > 0) {
            $firstSlider = $sliders[0] ?? null;
            if ($firstSlider && !empty($firstSlider->image)) {
                $heroImage = asset('storage/' . ltrim($firstSlider->image, '/'));
            }
        }

        $newsCount = is_countable($news ?? null) ? count($news) : 0;
        $partnersCount = is_countable($partners ?? null) ? count($partners) : 0;
        $statsCount = is_countable($statistics ?? null) ? count($statistics) : 0;
    @endphp

    <div class="topbar">
        <div class="container topbar-inner">
            <div class="brand">
                <div class="brand-logo">
                    @if($logo)
                        <img loading="lazy" decoding="async" src="{{ $logo }}" alt="{{ $siteName }}">
                    @else
                        <div style="font-weight:700;color:#0f6f87;">{{ mb_substr($siteName, 0, 1) }}</div>
                    @endif
                </div>
                <div class="brand-text">
                    <h1>{{ $siteName }}</h1>
                    <p>{{ $associationName }}</p>
                </div>
            </div>

            <nav class="nav">
                <a href="/">الرئيسية</a>
                <a href="#about">عن الجمعية</a>
                <a href="#vision">الرؤية والرسالة</a>
                <a href="#news">الأخبار</a>
                <a href="#partners">الشركاء</a>
                <a href="#contact">تواصل معنا</a>
            </nav>

            <div class="header-actions">
                <a href="#about" class="btn btn-soft">اعرف أكثر</a>
                <a href="#contact" class="btn btn-primary">تواصل الآن</a>
            </div>
        </div>
    </div>

    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-card">
                <div class="hero-content">
                    <div class="eyebrow">جمعية أهلية • أثر مستدام • خدمة مجتمعية</div>
                    <h2>{{ $associationName }}</h2>
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($aboutText), 220) }}</p>

                    <div class="hero-actions">
                        <a href="#about" class="btn btn-primary">ابدأ التعرف على الجمعية</a>
                        <a href="#news" class="btn btn-soft">آخر الأخبار</a>
                    </div>

                    <div class="social-row">
                        @if($facebook)<a class="social-link" href="{{ $facebook }}" target="_blank">ف</a>@endif
                        @if($twitter)<a class="social-link" href="{{ $twitter }}" target="_blank">X</a>@endif
                        @if($instagram)<a class="social-link" href="{{ $instagram }}" target="_blank">IG</a>@endif
                        @if($youtube)<a class="social-link" href="{{ $youtube }}" target="_blank">YT</a>@endif
                        @if($whatsapp)<a class="social-link" href="{{ $whatsapp }}" target="_blank">WA</a>@endif
                    </div>
                </div>
            </div>

            <div class="hero-side">
                <div class="mini-card">
                    <div class="image-wrap">
                        @if($heroImage)
                            <img loading="lazy" decoding="async" src="{{ $heroImage }}" alt="{{ $associationName }}">
                        @else
                            <div style="text-align:center;color:#0f6f87;font-weight:700;">
                                صورة رئيسية للجمعية
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mini-card">
                    <div class="mini-body">
                        <h3>ملخص سريع</h3>
                        <p>
                            نعمل على تقديم مبادرات وخدمات مجتمعية نوعية، وتعزيز جودة الحياة،
                            وصناعة أثر ملموس يخدم المستفيدين ويعكس رسالة الجمعية.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="about">
        <div class="container">
            <div class="section-head">
                <div>
                    <h3>نبذة عن الجمعية</h3>
                    <p>واجهة تعريفية حديثة مستوحاة من الأسلوب البصري العصري مع المحافظة على هوية الجمعية.</p>
                </div>
            </div>

            <div class="about-grid">
                <div class="card about-image">
                    @if($heroImage)
                        <img loading="lazy" decoding="async" src="{{ $heroImage }}" alt="{{ $associationName }}">
                    @else
                        <div style="min-height:420px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#e9fbff,#f9fdff);color:#0f6f87;font-weight:700;">
                            صورة تعريفية
                        </div>
                    @endif
                </div>

                <div class="card about-body">
                    <h4>{{ $associationName }}</h4>
                    <p>{!! nl2br(e(\Illuminate\Support\Str::limit(strip_tags($aboutText), 900))) !!}</p>

                    <div class="hero-actions">
                        <a href="#vision" class="btn btn-primary">الرؤية والرسالة</a>
                        <a href="#contact" class="btn btn-soft">طرق التواصل</a>
                    </div>
                </div>
            </div>

            <div class="stats">
                <div class="stat">
                    <strong>{{ $newsCount }}</strong>
                    <span>أخبار ومنشورات</span>
                </div>
                <div class="stat">
                    <strong>{{ $partnersCount }}</strong>
                    <span>شركاء وداعمون</span>
                </div>
                <div class="stat">
                    <strong>{{ $statsCount }}</strong>
                    <span>إحصائيات ومؤشرات</span>
                </div>
                <div class="stat">
                    <strong>{{ !empty($phone) ? '24/7' : '—' }}</strong>
                    <span>قنوات تواصل متاحة</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="vision">
        <div class="container">
            <div class="section-head">
                <div>
                    <h3>التعريف بالجمعية</h3>
                    <p>محتوى مرن لعرض الرؤية والرسالة والمعلومات الأساسية بطريقة واضحة.</p>
                </div>
            </div>

            <div class="accordion">
                <details open>
                    <summary>من نحن</summary>
                    <div class="content">
                        {!! nl2br(e(strip_tags($aboutText))) !!}
                    </div>
                </details>

                <details>
                    <summary>الرؤية</summary>
                    <div class="content">
                        {!! nl2br(e(strip_tags($vision))) !!}
                    </div>
                </details>

                <details>
                    <summary>الرسالة</summary>
                    <div class="content">
                        {!! nl2br(e(strip_tags($mission))) !!}
                    </div>
                </details>

                @if($address)
                <details>
                    <summary>العنوان</summary>
                    <div class="content">
                        {!! nl2br(e($address)) !!}
                    </div>
                </details>
                @endif
            </div>
        </div>
    </section>

    <section class="section" id="news">
        <div class="container">
            <div class="section-head">
                <div>
                    <h3>آخر الأخبار</h3>
                    <p>أحدث التحديثات والمبادرات والأنشطة الخاصة بالجمعية.</p>
                </div>
            </div>

            <div class="news-grid">
                @forelse(($news ?? []) as $item)
                    <article class="card news-card">
                        <div class="news-image">
                            @if(!empty($item->featured_image))
                                <img loading="lazy" decoding="async" src="{{ asset('storage/' . ltrim($item->featured_image, '/')) }}" alt="{{ $item->title ?? 'خبر' }}">
                            @else
                                <div style="height:100%;display:flex;align-items:center;justify-content:center;color:#0f6f87;font-weight:700;">
                                    صورة الخبر
                                </div>
                            @endif
                        </div>
                        <div class="news-body">
                            <h4>{{ $item->title ?? 'عنوان الخبر' }}</h4>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->excerpt ?? $item->content ?? ''), 120) }}</p>
                            <div class="news-meta">
                                {{ !empty($item->published_at) ? \Carbon\Carbon::parse($item->published_at)->translatedFormat('Y/m/d') : '—' }}
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="card" style="padding:24px;">
                        لا توجد أخبار منشورة حاليًا.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section" id="partners">
        <div class="container">
            <div class="section-head">
                <div>
                    <h3>الشركاء والداعمون</h3>
                    <p>نفخر بشراكاتنا التي تسهم في توسيع الأثر وتحقيق الاستدامة.</p>
                </div>
            </div>

            <div class="partners">
                @forelse(($partners ?? []) as $partner)
                    <div class="card partner">
                        @if(!empty($partner->logo))
                            <img loading="lazy" decoding="async" src="{{ asset('storage/' . ltrim($partner->logo, '/')) }}" alt="{{ $partner->name ?? 'شريك' }}">
                        @else
                            <div style="color:#64748b;">{{ $partner->name ?? 'شريك' }}</div>
                        @endif
                    </div>
                @empty
                    <div class="card partner">لا يوجد شركاء مضافون بعد.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section" id="contact">
        <div class="container">
            <div class="card cta">
                <h3>جاهزون للتواصل معكم</h3>
                <p>
                    {{ $phone ? 'يمكنكم التواصل معنا عبر الهاتف أو البريد الإلكتروني، وسنسعد بخدمتكم.' : 'أضف بيانات التواصل من إعدادات الموقع لإظهارها هنا بشكل كامل.' }}
                </p>

                <div class="hero-actions">
                    @if($phone)
                        <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="btn btn-primary">{{ $phone }}</a>
                    @endif

                    @if($email)
                        <a href="mailto:{{ $email }}" class="btn btn-soft">{{ $email }}</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="card footer-card">
                <div>
                    {{ $siteName }} © {{ date('Y') }}
                </div>
                <div>
                    تصميم صفحة رئيسية حديثة
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
