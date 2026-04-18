@php
    $connection = \Illuminate\Support\Facades\DB::connection('tenant');
    
    $settings = $connection->table('site_settings')->orderBy('id', 'desc')->first();
    
    $aboutText = $settings->about_text ?? 'جمعية خيرية تعمل على خدمة المجتمع وتقديم الدعم للمحتاجين في مختلف المجالات.';
    $videoUrl = $settings->intro_video_url ?? null;
    $siteName = $settings->site_name ?? 'جمعية الخير';
    $assocName = $settings->association_name ?? $siteName;
    
    $address = $settings->address ?? 'المملكة العربية السعودية';
    $phone = $settings->phone ?? ($settings->official_phone ?? '+966 50 123 4567');
    $email = $settings->email ?? ($settings->official_email ?? 'info@charity.org');
    $desc = $settings->site_description ?? 'نعمل بشفافية عالية لتحقيق أهدافنا التنموية لخدمة المجتمع.';

    /*
    |--------------------------------------------------------------------------
    | دوال مساعدة للوسائط: دعم R2 + دعم الروابط المباشرة + دعم الصور القديمة
    |--------------------------------------------------------------------------
    */
    if (!function_exists('resolveMediaUrlForPath')) {
        function resolveMediaUrlForPath($path, $disk = 'public')
        {
            if (empty($path)) {
                return null;
            }

            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }

            return \App\Support\Media\MediaUrl::forDiskPath($disk, $path);
        }
    }

    if (!function_exists('resolveMediaUrlFromMediaId')) {
        function resolveMediaUrlFromMediaId($mediaId, $fallbackPath = null, $fallbackDisk = 'public')
        {
            if (!empty($mediaId)) {
                try {
                    $media = \App\Models\MediaItem::query()->find($mediaId);

                    if ($media) {
                        if (!empty($media->url)) {
                            return $media->url;
                        }

                        if (!empty($media->file)) {
                            return \App\Support\Media\MediaUrl::forDiskPath($media->disk ?: $fallbackDisk, $media->file);
                        }
                    }
                } catch (\Throwable $e) {
                    //
                }
            }

            return resolveMediaUrlForPath($fallbackPath, $fallbackDisk);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | حل الشعار: دعم R2 + دعم الصور القديمة
    |--------------------------------------------------------------------------
    */
    $finalLogoUrl = resolveMediaUrlFromMediaId(
        $settings->logo_media_id ?? null,
        $settings->logo ?? null,
        'public'
    );

    $mainMenu = $connection->table('menus')->where('location', 'header')->first();
    if (!$mainMenu) {
        $mainMenu = $connection->table('menus')->first();
    }
    
    $allMenuItems = collect();
    if ($mainMenu) {
        $allMenuItems = $connection->table('menu_items')
            ->where('menu_id', $mainMenu->id)
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    $groupedItems = $allMenuItems->groupBy(function($item) {
        return empty($item->parent_id) ? 'root' : $item->parent_id;
    });
    $rootItems = $groupedItems->get('root') ?? collect();

    $projects = collect();
    try {
        $projects = $connection->table('program_projects')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'asc')
            ->limit(12)
            ->get()
            ->map(function ($project) {
                $project->final_cover_url = resolveMediaUrlFromMediaId(
                    $project->cover_image_media_id ?? null,
                    $project->cover_image ?? null,
                    'public'
                );

                return $project;
            });
    } catch (\Exception $e) {}

    $statistics = collect();
    try {
        $statistics = $connection->table('statistics')->orderBy('sort_order', 'asc')->limit(4)->get();
    } catch (\Exception $e) {}

    $sliders = collect();
    try {
        $sliders = $connection->table('sliders')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->map(function ($slider) {
                $slider->final_image_url = resolveMediaUrlFromMediaId(
                    $slider->image_media_id ?? null,
                    $slider->image ?? null,
                    'public'
                );

                return $slider;
            });
    } catch (\Exception $e) {}

    $newsItems = collect();
    try {
        $newsItems = $connection->table('news')
            ->where('is_active', 1)
            ->where('status', 'published')
            ->orderByDesc('id')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                $item->final_image_url = resolveMediaUrlFromMediaId(
                    $item->image_media_id ?? null,
                    $item->image ?? null,
                    'public'
                );

                return $item;
            });
    } catch (\Exception $e) {}

    $partners = collect();
    try {
        $partners = $connection->table('partners')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'asc')
            ->orderByDesc('id')
            ->get()
            ->map(function ($partner) {
                $partner->final_logo_url = resolveMediaUrlFromMediaId(
                    $partner->logo_media_id ?? null,
                    $partner->logo ?? null,
                    'public'
                );

                return $partner;
            });
    } catch (\Exception $e) {}

    if (!function_exists('resolveMenuUrl')) {
        function resolveMenuUrl($item, $connection) {
            if (!empty($item->resolved_url)) {
                return $item->resolved_url;
            }

            if ($item->type === 'page' && !empty($item->page_id)) {
                $page = $connection->table('pages')->where('id', $item->page_id)->first();
                return $page ? '/page/' . $page->slug : '#';
            }

            if (!empty($item->url)) {
                return $item->url;
            }

            return '#';
        }
    }

    $primary = $settings?->primary_color ?? '#127962';
    $secondary = $settings?->secondary_color ?? '#0d5948';

    $usefulLinks = [
        [
            'title' => 'منصة أبشر',
            'url' => 'https://www.absher.sa',
        ],
        [
            'title' => 'الضمان الاجتماعي',
            'url' => 'https://sbis.hrsd.gov.sa',
        ],
        [
            'title' => 'المركز الوطني لتنمية القطاع غير الربحي',
            'url' => 'https://ncnp.gov.sa',
        ],
        [
            'title' => 'التقاعد والتأمينات الاجتماعية',
            'url' => 'https://www.gosi.gov.sa',
        ],
    ];

    $socialLinks = collect([
        [
            'url' => $settings->facebook ?? null,
            'icon' => 'fab fa-facebook-f',
            'label' => 'فيسبوك',
        ],
        [
            'url' => $settings->twitter_url ?? null,
            'icon' => 'fab fa-x-twitter',
            'label' => 'إكس',
        ],
        [
            'url' => $settings->instagram_url ?? null,
            'icon' => 'fab fa-instagram',
            'label' => 'إنستغرام',
        ],
        [
            'url' => $settings->youtube_url ?? null,
            'icon' => 'fab fa-youtube',
            'label' => 'يوتيوب',
        ],
        [
            'url' => $settings->tiktok_url ?? null,
            'icon' => 'fab fa-tiktok',
            'label' => 'تيك توك',
        ],
        [
            'url' => $settings->snapchat_url ?? null,
            'icon' => 'fab fa-snapchat-ghost',
            'label' => 'سناب شات',
        ],
        [
            'url' => $settings->whatsapp_url ?? null,
            'icon' => 'fab fa-whatsapp',
            'label' => 'واتساب',
        ],
    ])->filter(fn ($item) => !empty($item['url']))->values();
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Noto Kufi Arabic', 'sans-serif'],
                    },
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Noto Kufi Arabic', sans-serif; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #10b981; border-radius: 4px; }

        .slider-fade { transition: opacity 1s ease-in-out; }
        .slide-active { opacity: 1; z-index: 10; }
        .slide-inactive { opacity: 0; z-index: 0; pointer-events: none; }

        .line-clamp-2{
            display:-webkit-box;
            -webkit-line-clamp:2;
            -webkit-box-orient:vertical;
            overflow:hidden;
        }

        .line-clamp-3{
            display:-webkit-box;
            -webkit-line-clamp:3;
            -webkit-box-orient:vertical;
            overflow:hidden;
        }

        .partners-marquee{
            width:100%;
            overflow:hidden;
        }

        .partners-track{
            display:flex;
            gap:20px;
            width:max-content;
            animation:partnersScroll 32s linear infinite;
        }

        .partner-card{
            width:220px;
            min-width:220px;
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:18px;
            padding:22px 18px;
            box-shadow:0 8px 24px rgba(15,23,42,.04);
        }

        .partner-card:hover{
            box-shadow:0 14px 30px rgba(15,23,42,.08);
        }

        .partners-marquee:hover .partners-track{
            animation-play-state:paused;
        }

        @keyframes partnersScroll{
            0%{ transform:translateX(0); }
            100%{ transform:translateX(50%); }
        }

        .projects-slider-wrap{
            overflow:hidden;
            position:relative;
        }

        .projects-track{
            display:flex;
            gap:24px;
            transition:transform .5s ease;
            will-change:transform;
        }

        .project-slide{
            min-width:calc((100% - 48px) / 3);
            max-width:calc((100% - 48px) / 3);
        }

        .news-feature-card{
            position:relative;
            overflow:hidden;
        }

        .news-feature-card::before{
            content:'';
            position:absolute;
            inset:0;
            background:linear-gradient(to top, rgba(15,23,42,.78), rgba(15,23,42,.05));
            z-index:1;
        }

        .news-feature-content{
            position:absolute;
            inset-inline:0;
            bottom:0;
            z-index:2;
            padding:24px;
        }

        @media (max-width: 1024px){
            .project-slide{
                min-width:calc((100% - 24px) / 2);
                max-width:calc((100% - 24px) / 2);
            }
        }

        @media (max-width: 640px){
            .project-slide{
                min-width:100%;
                max-width:100%;
            }
        }

        .inner-custom-header,
        .inner-custom-header *{
            direction: rtl;
            text-align: right;
            box-sizing: border-box;
            font-family: 'Noto Kufi Arabic', sans-serif;
        }

        .inner-custom-header{
            background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});
            color:#fff;
            padding:20px 0 12px;
            margin:0;
            box-shadow:0 10px 24px rgba(15,23,42,.10);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .inner-custom-header .inner-container{
            max-width: 1280px;
            margin: 0 auto;
            padding-right: 28px;
            padding-left: 28px;
        }

        .inner-custom-header .inner-brand{
            display:flex;
            align-items:center;
            gap:14px;
            text-decoration:none;
            color:#fff;
            min-width:0;
        }

        .inner-custom-header .inner-logo{
            width:64px;
            height:64px;
            border-radius:18px;
            background:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            overflow:hidden;
            flex-shrink:0;
        }

        .inner-custom-header .inner-logo img{
            width:100%;
            height:100%;
            object-fit:contain;
            padding:8px;
            background:#fff;
        }

        .inner-custom-header .inner-brand-title{
            margin:0;
            font-size:1.18rem;
            font-weight:800;
            color:#fff;
            line-height:1.8;
        }

        .inner-custom-header .inner-brand-subtitle{
            margin:4px 0 0;
            color:rgba(255,255,255,.85);
            font-size:.82rem;
            line-height:1.9;
        }

        .inner-custom-header .inner-top{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:18px;
            flex-wrap:wrap;
            padding-bottom:14px;
            border-bottom:1px solid rgba(255,255,255,.14);
            direction: rtl;
        }

        .inner-custom-header .inner-actions-wrap{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .inner-custom-header .inner-actions{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            direction: rtl;
        }

        .inner-custom-header .inner-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:11px 18px;
            border-radius:14px;
            text-decoration:none;
            font-weight:700;
            font-size:.9rem;
            border:1px solid rgba(255,255,255,.2);
            transition:.2s ease;
        }

        .inner-custom-header .inner-btn--light{
            background:#fff;
            color:{{ $primary }};
        }

        .inner-custom-header .inner-btn--ghost{
            background:rgba(255,255,255,.10);
            color:#fff;
        }

        .inner-custom-header .inner-btn:hover{
            transform:translateY(-1px);
        }

        .inner-custom-header .mobile-menu-btn{
            display:none;
            background:rgba(255,255,255,.14);
            color:#fff;
            border:1px solid rgba(255,255,255,.18);
            width:46px;
            height:46px;
            border-radius:12px;
            font-size:22px;
            align-items:center;
            justify-content:center;
            cursor:pointer;
        }

        .inner-custom-header .site-nav,
        .inner-custom-header .site-subnav{
            list-style:none;
            margin:0;
            padding:0;
        }

        .inner-custom-header .site-nav{
            display:flex;
            align-items:center;
            gap:0;
            margin-top:14px;
            flex-wrap:wrap;
            direction: rtl;
        }

        .inner-custom-header .site-nav-item{
            position:relative;
        }

        .inner-custom-header .site-nav-link{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            color:#fff !important;
            font-weight:700;
            font-size:.93rem;
            padding:1rem .95rem;
            text-decoration:none;
            position:relative;
            white-space:nowrap;
        }

        .inner-custom-header .site-nav-link:hover{
            opacity:.9;
        }

        .inner-custom-header .site-nav-link::after{
            content:'';
            position:absolute;
            right:50%;
            transform:translateX(50%);
            bottom:6px;
            width:0;
            height:3px;
            background:#fff;
            border-radius:999px;
            transition:all .3s ease;
        }

        .inner-custom-header .site-nav-link:hover::after{
            width:28px;
        }

        .inner-custom-header .site-nav-link-main{
            display:flex;
            align-items:center;
            gap:8px;
        }

        .inner-custom-header .site-nav-arrow{
            font-size:12px;
            line-height:1;
            flex:0 0 auto;
            color:#fff;
        }

        .inner-custom-header .site-subnav{
            position:absolute;
            top:100%;
            right:0;
            min-width:240px;
            background:#fff;
            border-top:4px solid {{ $primary }};
            border-radius:18px;
            box-shadow:0 12px 30px rgba(15,23,42,.10);
            padding:14px 0;
            display:none;
            z-index:1200;
            text-align:right;
        }

        .inner-custom-header .site-subnav .site-nav-link{
            color:#1f2937 !important;
            padding:11px 20px;
            font-weight:600;
            font-size:.88rem;
            text-align:right;
        }

        .inner-custom-header .site-subnav .site-nav-link:hover{
            background:#f2f8f6;
            color:{{ $primary }} !important;
        }

        .inner-custom-header .site-subnav .site-subnav{
            top:-14px;
            right:100%;
        }

        .inner-custom-header .site-nav-item.open > .site-subnav{
            display:block;
        }

        @media (max-width: 991.98px){
            .inner-custom-header .inner-container{
                padding-right: 18px;
                padding-left: 18px;
            }

            .inner-custom-header .inner-top{
                flex-wrap:nowrap;
                align-items:center;
            }

            .inner-custom-header .inner-brand{
                flex:1 1 auto;
                min-width:0;
            }

            .inner-custom-header .inner-brand-title{
                font-size:.95rem;
                line-height:1.6;
            }

            .inner-custom-header .inner-brand-subtitle{
                font-size:.72rem;
                line-height:1.6;
            }

            .inner-custom-header .inner-logo{
                width:54px;
                height:54px;
            }

            .inner-custom-header .mobile-menu-btn{
                display:inline-flex;
            }

            .inner-custom-header .inner-actions{
                display:none;
            }

            .inner-custom-header .site-nav{
                display:none;
                flex-direction:column;
                align-items:stretch;
                width:100%;
                gap:6px;
                margin-top:14px;
                background:#fff;
                border-radius:14px;
                padding:10px;
                box-shadow:0 12px 30px rgba(15,23,42,.10);
            }

            .inner-custom-header .site-nav.show{
                display:flex;
            }

            .inner-custom-header .site-nav-link{
                width:100%;
                padding:14px 12px;
                border-radius:12px;
                color:#1f2937 !important;
                background:#f8fafc;
            }

            .inner-custom-header .site-nav-link::after{
                display:none;
            }

            .inner-custom-header .site-nav-arrow{
                color:#1f2937;
            }

            .inner-custom-header .site-subnav{
                position:static !important;
                display:none;
                width:100%;
                min-width:100%;
                margin-top:6px !important;
                padding:6px 0;
                border:1px solid #e5e7eb;
                border-radius:12px;
                box-shadow:none !important;
            }

            .inner-custom-header .site-nav-item.open > .site-subnav{
                display:block !important;
            }

            .inner-custom-header .site-subnav .site-nav-link{
                padding:12px 14px;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased overflow-x-hidden">

<div dir="rtl" style="direction: rtl; text-align: right;">
<header class="inner-custom-header">
    <div class="inner-container">
        <div class="inner-top">
            <a href="/" class="inner-brand">
                <div class="inner-logo">
                    @if($finalLogoUrl)
                        <img src="{{ $finalLogoUrl }}" alt="{{ $assocName ?? 'الشعار' }}">
                    @else
                        <span style="font-size:1.4rem;font-weight:800;">{{ mb_substr($assocName ?? 'ج', 0, 1) }}</span>
                    @endif
                </div>

                <div>
                    <h2 class="inner-brand-title">{{ $assocName ?? 'جمعية رضوان الخيرية' }}</h2>
                    <p class="inner-brand-subtitle">جمعية مصرحه من وزارة الموارد البشرية والتنمية الاجتماعية</p>
                </div>
            </a>

            <div class="inner-actions-wrap">
                <button type="button" id="mobileMenuBtn" class="mobile-menu-btn" aria-label="فتح القائمة">
                    ☰
                </button>

                <div class="inner-actions">
                    <a href="/" class="inner-btn inner-btn--ghost">الرئيسية</a>
                    @if(!empty($settings?->beneficiary_portal_url))
                        <a href="{{ $settings->beneficiary_portal_url }}" class="inner-btn inner-btn--light">بوابة المستفيدين</a>
                    @elseif(!empty($settings?->store_url))
                        <a href="{{ $settings->store_url }}" class="inner-btn inner-btn--light">المتجر</a>
                    @endif
                </div>
            </div>
        </div>

        @if($rootItems->count() > 0)
            <ul id="mainMobileNav" class="site-nav">
                @foreach($rootItems as $item)
                    @php
                        $children = $groupedItems->get($item->id) ?? collect();
                        $hasChildren = $children->count() > 0;
                        $url = $hasChildren ? 'javascript:void(0);' : resolveMenuUrl($item, $connection);
                    @endphp
                    <li class="site-nav-item">
                        <a href="{{ $url }}" class="site-nav-link">
                            <span class="site-nav-link-main">
                                <span>{{ $item->title }}</span>
                            </span>
                            @if($hasChildren)
                                <span class="site-nav-arrow">▾</span>
                            @endif
                        </a>

                        @if($hasChildren)
                            <ul class="site-subnav">
                                @foreach($children as $subItem)
                                    @php
                                        $subChildren = $groupedItems->get($subItem->id) ?? collect();
                                        $subHasChildren = $subChildren->count() > 0;
                                        $subUrl = $subHasChildren ? 'javascript:void(0);' : resolveMenuUrl($subItem, $connection);
                                    @endphp
                                    <li class="site-nav-item">
                                        <a href="{{ $subUrl }}" class="site-nav-link">
                                            <span class="site-nav-link-main">
                                                <span>{{ $subItem->title }}</span>
                                            </span>
                                            @if($subHasChildren)
                                                <span class="site-nav-arrow">◂</span>
                                            @endif
                                        </a>

                                        @if($subHasChildren)
                                            <ul class="site-subnav">
                                                @foreach($subChildren as $childItem)
                                                    <li class="site-nav-item">
                                                        <a href="{{ resolveMenuUrl($childItem, $connection) }}" class="site-nav-link">
                                                            <span class="site-nav-link-main">
                                                                <span>{{ $childItem->title }}</span>
                                                            </span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</header>
</div>

<section id="home" class="relative bg-emerald-800 h-[500px] md:h-[600px] overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgweiIgZmlsbD0ibm9uZSIvPjxwYXRoIGQ9Ik0wIDEwaDQwdjJIMHoiIGZpbGw9IiNmZmYiLz48L3N2Zz4=')] z-0"></div>
    
    <div id="slider-container" class="relative w-full h-full">
        @if(isset($sliders) && $sliders->count() > 0)
            @foreach($sliders as $index => $slider)
                <div class="slide slider-fade absolute inset-0 w-full h-full {{ $index == 0 ? 'slide-active' : 'slide-inactive' }}">
                    <img
                        src="{{ $slider->final_image_url ?: 'https://via.placeholder.com/1920x1080?text=' . urlencode($slider->title ?? 'Slide') }}"
                        class="absolute inset-0 w-full h-full object-cover"
                        alt="{{ $slider->title ?? 'Slide' }}"
                    >
                    <div class="absolute inset-0 bg-emerald-900/60 mix-blend-multiply"></div>
                    
                    <div class="container mx-auto px-4 relative z-10 h-full flex flex-col justify-center items-center text-center">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6 drop-shadow-lg">
                            {{ $slider->title }}
                        </h1>
                        <p class="text-lg md:text-xl text-emerald-50 mb-10 max-w-2xl mx-auto drop-shadow-md">
                            {{ $slider->description }}
                        </p>
                        @if($slider->button_text)
                            <div class="flex flex-col sm:flex-row justify-center gap-4">
                                <a href="{{ $slider->button_url ?? '#' }}" class="bg-white text-emerald-700 px-10 py-4 rounded-xl font-bold hover:bg-emerald-50 transition-all text-lg shadow-2xl hover:shadow-emerald-500/50 transform hover:-translate-y-1 min-w-[200px] text-center">
                                    {{ $slider->button_text }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
            
            <div class="absolute bottom-8 left-0 right-0 z-20 flex justify-center gap-3">
                @foreach($sliders as $index => $slider)
                    <button onclick="goToSlide({{ $index }})" class="slider-dot w-3 h-3 rounded-full transition-all duration-300 {{ $index == 0 ? 'bg-white scale-125' : 'bg-white/50 hover:bg-white/80' }}"></button>
                @endforeach
            </div>
        @else
            <div class="absolute inset-0 w-full h-full">
                <div class="absolute inset-0 bg-emerald-700"></div>
                <div class="container mx-auto px-4 relative z-10 h-full flex flex-col justify-center items-center text-center">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                        معاً نبني مستقبلاً أفضل للجميع<br/>معاً نصنع الفرق
                    </h1>
                    <p class="text-lg md:text-xl text-emerald-100 mb-10 max-w-2xl mx-auto">
                        جمعية خيرية تعمل على خدمة المجتمع وتقديم الدعم للمحتاجين في مختلف المجالات
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="#about" class="bg-white text-emerald-700 px-10 py-4 rounded-xl font-bold hover:bg-emerald-50 transition-all text-lg shadow-2xl hover:shadow-emerald-500/50 transform hover:-translate-y-1 min-w-[200px] text-center">تعرف على الجمعية</a>
                        <a href="#projects" class="border-2 border-white/50 backdrop-blur-sm bg-white/10 text-white px-10 py-4 rounded-xl font-bold hover:bg-white hover:text-emerald-700 transition-all text-lg shadow-xl transform hover:-translate-y-1 min-w-[200px] text-center">تصفح المشاريع</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<section class="py-16 bg-white relative -mt-12 z-20 mx-4 md:mx-auto max-w-6xl rounded-2xl shadow-xl border border-gray-100">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center px-4">
        @if(isset($statistics) && $statistics->count() > 0)
            @foreach($statistics as $stat)
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-4 text-2xl">
                        @if($stat->icon && str_contains($stat->icon, 'heroicon'))
                            <x-filament::icon :icon="$stat->icon" class="w-8 h-8" />
                        @else
                            <i class="{{ $stat->icon ?? 'fas fa-chart-bar' }}"></i>
                        @endif
                    </div>
                    <h3 class="text-3xl font-extrabold text-gray-900 mb-1">{{ $stat->value }}</h3>
                    <p class="text-gray-500 font-medium">{{ $stat->title }}</p>
                </div>
            @endforeach
        @endif
    </div>
</section>

<section id="about" class="py-20 bg-slate-50">
    <div class="container mx-auto px-4 max-w-5xl">
        <div class="text-center mb-16">
            <span class="text-emerald-600 font-bold tracking-wider uppercase text-sm mb-2 block">عن الجمعية</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">من نحن</h2>
            <div class="text-gray-600 max-w-3xl mx-auto leading-relaxed text-lg">
                {!! $aboutText !!}
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 items-center">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-6 text-2xl">🎯</div>
                <h4 class="text-2xl font-bold text-gray-900 mb-3">رسالتنا</h4>
                <p class="text-gray-600 leading-relaxed">تقديم العون والمساعدة للمحتاجين وبناء مجتمع متكافل يسوده التعاون والتضامن من خلال مبادرات تنموية مستدامة تلبي الاحتياجات الأساسية.</p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-6 text-2xl">👁️</div>
                <h4 class="text-2xl font-bold text-gray-900 mb-3">رؤيتنا</h4>
                <p class="text-gray-600 leading-relaxed">أن نكون الجمعية الخيرية الرائدة والنموذج الأمثل في تقديم الدعم الشامل والمستدام للمجتمعات المحتاجة بأعلى معايير الحوكمة والشفافية.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-white border-y border-gray-100">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
            <div>
                <span class="text-emerald-600 font-bold tracking-wider uppercase text-sm mb-2 block">آخر الأخبار</span>
                <h2 class="text-3xl font-bold text-gray-900 mb-3">أخبار الجمعية</h2>
                <p class="text-gray-600 max-w-2xl">تابع آخر المستجدات والمبادرات والأنشطة التي تنفذها الجمعية لخدمة المجتمع.</p>
            </div>

            <a href="/page/alakhbar" class="inline-flex items-center justify-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold px-6 py-3 rounded-xl hover:bg-emerald-600 hover:text-white transition-colors">
                عرض المزيد
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            @if($newsItems->count() > 0)
                @php
                    $featuredNews = $newsItems->first();
                    $otherNews = $newsItems->skip(1);
                    $featuredImage = $featuredNews->final_image_url ?: 'https://via.placeholder.com/900x600?text=News';
                    $featuredUrl = !empty($featuredNews->slug) ? url('/news/' . $featuredNews->slug) : '#';
                    $featuredDate = $featuredNews->published_at ?: $featuredNews->created_at;
                @endphp

                <div class="lg:col-span-7">
                    <article class="news-feature-card rounded-3xl border border-gray-100 shadow-sm h-[420px] bg-slate-100">
                        <img src="{{ $featuredImage }}" alt="{{ $featuredNews->title ?? 'خبر' }}" class="w-full h-full object-cover">
                        <div class="news-feature-content">
                            <span class="inline-flex items-center bg-white/90 text-emerald-700 text-xs font-extrabold px-4 py-2 rounded-full mb-4">
                                خبر جديد
                            </span>

                            <div class="flex items-center gap-2 text-xs text-emerald-50 mb-3">
                                <i class="far fa-calendar-alt"></i>
                                <span>{{ $featuredDate ? \Carbon\Carbon::parse($featuredDate)->format('Y-m-d') : '-' }}</span>
                            </div>

                            <h3 class="text-2xl md:text-3xl font-extrabold text-white leading-10 mb-4 line-clamp-2">
                                <a href="{{ $featuredUrl }}">{{ $featuredNews->title ?? 'خبر' }}</a>
                            </h3>

                            <p class="text-sm md:text-base text-slate-100 leading-8 line-clamp-3 mb-5 max-w-2xl">
                                {{ $featuredNews->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($featuredNews->content ?? ''), 160) }}
                            </p>

                            <a href="{{ $featuredUrl }}" class="inline-flex items-center gap-2 bg-white text-emerald-700 font-bold px-6 py-3 rounded-xl hover:bg-emerald-50 transition-colors">
                                اقرأ المزيد
                                <i class="fas fa-arrow-left text-xs"></i>
                            </a>
                        </div>
                    </article>
                </div>

                <div class="lg:col-span-5 space-y-6">
                    @foreach($otherNews as $item)
                        @php
                            $newsImage = $item->final_image_url ?: 'https://via.placeholder.com/400x300?text=News';
                            $newsUrl = !empty($item->slug) ? url('/news/' . $item->slug) : '#';
                            $newsDate = $item->published_at ?: $item->created_at;
                        @endphp

                        <article class="bg-slate-50 rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-md transition-shadow group flex flex-col sm:flex-row">
                            <a href="{{ $newsUrl }}" class="block sm:w-44 h-44 sm:h-auto overflow-hidden bg-slate-100 shrink-0">
                                <img src="{{ $newsImage }}" alt="{{ $item->title ?? 'خبر' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </a>

                            <div class="p-5 flex-1">
                                <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                                    <i class="far fa-calendar-alt text-emerald-600"></i>
                                    <span>{{ $newsDate ? \Carbon\Carbon::parse($newsDate)->format('Y-m-d') : '-' }}</span>
                                </div>

                                <h3 class="text-lg font-extrabold text-gray-900 leading-8 mb-3 line-clamp-2">
                                    <a href="{{ $newsUrl }}">{{ $item->title ?? 'خبر' }}</a>
                                </h3>

                                <p class="text-sm text-gray-600 leading-7 line-clamp-2 mb-4">
                                    {{ $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($item->content ?? ''), 90) }}
                                </p>

                                <a href="{{ $newsUrl }}" class="inline-flex items-center gap-2 text-emerald-700 font-bold hover:text-emerald-800">
                                    اقرأ المزيد
                                    <i class="fas fa-arrow-left text-xs"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="col-span-12 text-center text-gray-500">لا توجد أخبار منشورة حالياً</div>
            @endif
        </div>
    </div>
</section>

<section id="projects" class="py-20 bg-slate-50">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
            <div>
                <span class="text-emerald-600 font-bold tracking-wider uppercase text-sm mb-2 block">مشاريعنا</span>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">فرص التبرع والمشاريع</h2>
                <p class="text-gray-600">نعمل على مجموعة متنوعة من المشاريع الخيرية لخدمة المجتمع وتحسين حياة المحتاجين</p>
            </div>

            <button id="projectsMoreBtn" type="button" class="inline-flex items-center justify-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold px-6 py-3 rounded-xl hover:bg-emerald-600 hover:text-white transition-colors">
                عرض المزيد
                <i class="fas fa-arrow-left text-xs"></i>
            </button>
        </div>

        <div class="projects-slider-wrap">
            <div id="projectsTrack" class="projects-track">
                @if(isset($projects) && $projects->count() > 0)
                    @foreach($projects as $project)
                        @php
                            $title = $project->title ?? 'مشروع خيري';
                            $imgUrl = $project->final_cover_url ?: 'https://via.placeholder.com/400x250?text=مشروع+خيري';
                        @endphp

                        <div class="project-slide">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow group h-full">
                                <div class="h-48 relative overflow-hidden bg-slate-100">
                                    <img src="{{ $imgUrl }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $title }}">
                                </div>

                                <div class="p-6 flex-grow flex flex-col">
                                    <h3 class="text-xl font-bold text-gray-900 mb-3 leading-snug">{{ \Illuminate\Support\Str::limit($title, 50) }}</h3>

                                    <p class="text-gray-600 text-sm mb-5 line-clamp-2">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($project->description ?? 'مشروع خيري يهدف لخدمة المجتمع ودعم الفئات المستحقة.'), 90) }}
                                    </p>

                                    <div class="grid grid-cols-2 gap-3 mb-5">
                                        <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                            <div class="text-xs text-gray-500 mb-1">بداية المشروع</div>
                                            <div class="text-sm font-bold text-gray-800">
                                                {{ !empty($project->start_date) ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '-' }}
                                            </div>
                                        </div>

                                        <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                            <div class="text-xs text-gray-500 mb-1">نهاية المشروع</div>
                                            <div class="text-sm font-bold text-gray-800">
                                                {{ !empty($project->end_date) ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : '-' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-auto flex gap-3">
                                        <a href="{{ url('/projects/' . ($project->id ?? '#')) }}" class="flex-1 text-center bg-emerald-600 text-white font-bold py-3 rounded-xl hover:bg-emerald-700 transition-colors">
                                            تفاصيل
                                        </a>

                                        @if(!empty($project->donation_url))
                                            <a href="{{ $project->donation_url }}" class="flex-1 text-center bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold py-3 rounded-xl hover:bg-emerald-600 hover:text-white transition-colors">
                                                تبرع الآن
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-white border-t border-gray-100 overflow-hidden">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center mb-12">
            <span class="text-emerald-600 font-bold tracking-wider uppercase text-sm mb-2 block">شركاء النجاح</span>
            <h2 class="text-3xl font-bold text-gray-900 mb-4">شركاؤنا</h2>
            <p class="text-gray-600">نفخر بالشراكة مع جهات داعمة ومؤثرة تسهم معنا في تحقيق الأثر المجتمعي.</p>
        </div>

        @if(isset($partners) && $partners->count() > 0)
            <div class="partners-marquee relative overflow-hidden">
                <div class="partners-track">
                    @foreach($partners as $partner)
                        @php
                            $partnerLogo = $partner->final_logo_url;
                            $partnerUrl = $partner->url ?: '#';
                        @endphp
                        <div class="partner-card">
                            @if($partnerUrl !== '#')
                                <a href="{{ $partnerUrl }}" target="_blank" class="block">
                            @endif

                            <div class="h-20 flex items-center justify-center">
                                @if($partnerLogo)
                                    <img src="{{ $partnerLogo }}" alt="{{ $partner->name ?? 'شريك' }}" class="max-h-16 w-auto object-contain mx-auto">
                                @else
                                    <span class="text-sm font-bold text-gray-500">{{ $partner->name ?? 'شريك' }}</span>
                                @endif
                            </div>

                            @if(!empty($partner->name))
                                <div class="mt-3 text-sm font-bold text-gray-700 text-center line-clamp-2">
                                    {{ $partner->name }}
                                </div>
                            @endif

                            @if($partnerUrl !== '#')
                                </a>
                            @endif
                        </div>
                    @endforeach

                    @foreach($partners as $partner)
                        @php
                            $partnerLogo = $partner->final_logo_url;
                            $partnerUrl = $partner->url ?: '#';
                        @endphp
                        <div class="partner-card">
                            @if($partnerUrl !== '#')
                                <a href="{{ $partnerUrl }}" target="_blank" class="block">
                            @endif

                            <div class="h-20 flex items-center justify-center">
                                @if($partnerLogo)
                                    <img src="{{ $partnerLogo }}" alt="{{ $partner->name ?? 'شريك' }}" class="max-h-16 w-auto object-contain mx-auto">
                                @else
                                    <span class="text-sm font-bold text-gray-500">{{ $partner->name ?? 'شريك' }}</span>
                                @endif
                            </div>

                            @if(!empty($partner->name))
                                <div class="mt-3 text-sm font-bold text-gray-700 text-center line-clamp-2">
                                    {{ $partner->name }}
                                </div>
                            @endif

                            @if($partnerUrl !== '#')
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

<section id="donate" class="py-20 bg-emerald-700 relative text-white overflow-hidden">
    <div class="absolute inset-0 opacity-5 bg-[url('https://www.transparenttextures.com/patterns/arabesque.png')]"></div>
    
    <div class="container mx-auto px-4 max-w-3xl relative z-10">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold mb-3 text-white">ساهم معنا في صنع الفرق</h2>
            <p class="text-emerald-100 text-lg">تبرعك يساعدنا على مواصلة عملنا الخيري وإحداث تأثير إيجابي ومستدام</p>
        </div>
        
        <div class="bg-white rounded-2xl p-6 md:p-10 text-gray-900 shadow-2xl">
            <form>
                <div class="mb-8">
                    <label class="block font-bold text-gray-800 mb-4 text-lg">اختر مبلغ التبرع السريع</label>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        <button type="button" class="amount-btn py-4 rounded-xl border-2 border-gray-200 text-gray-600 hover:border-emerald-500 hover:text-emerald-600 font-bold transition-colors">50 ر.س</button>
                        <button type="button" class="amount-btn py-4 rounded-xl border-2 border-gray-200 text-gray-600 hover:border-emerald-500 hover:text-emerald-600 font-bold transition-colors">100 ر.س</button>
                        <button type="button" class="amount-btn py-4 rounded-xl border-2 border-emerald-600 bg-emerald-50 text-emerald-700 font-bold transition-colors shadow-sm">250 ر.س</button>
                        <button type="button" class="amount-btn py-4 rounded-xl border-2 border-gray-200 text-gray-600 hover:border-emerald-500 hover:text-emerald-600 font-bold transition-colors">500 ر.س</button>
                        <button type="button" class="amount-btn py-4 rounded-xl border-2 border-gray-200 text-gray-600 hover:border-emerald-500 hover:text-emerald-600 font-bold transition-colors">1000 ر.س</button>
                    </div>
                    <div class="mt-4 relative">
                        <input type="number" placeholder="أو أدخل مبلغاً مخصصاً هنا" class="w-full px-5 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-bold text-lg bg-gray-50">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold">ر.س</span>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block font-bold text-gray-800 mb-4 text-lg">طريقة الدفع المفضلة</label>
                    <div class="grid grid-cols-3 gap-4">
                        <button type="button" class="pay-btn py-4 rounded-xl border-2 border-emerald-600 bg-emerald-50 text-emerald-700 font-bold transition-colors flex flex-col items-center gap-2">
                            <i class="fas fa-credit-card text-2xl"></i>
                            <span>بطاقة بنكية</span>
                        </button>
                        <button type="button" class="pay-btn py-4 rounded-xl border-2 border-gray-200 text-gray-600 hover:border-emerald-500 hover:text-emerald-600 font-bold transition-colors flex flex-col items-center gap-2">
                            <i class="fab fa-apple text-2xl"></i>
                            <span>Apple Pay</span>
                        </button>
                        <button type="button" class="pay-btn py-4 rounded-xl border-2 border-gray-200 text-gray-600 hover:border-emerald-500 hover:text-emerald-600 font-bold transition-colors flex flex-col items-center gap-2">
                            <i class="fas fa-university text-2xl"></i>
                            <span>حوالة بنكية</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                    <div>
                        <input type="text" placeholder="الاسم الكريم (اختياري)" class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-gray-50">
                    </div>
                    <div>
                        <input type="tel" placeholder="رقم الجوال لتصلك رسالة التأكيد" class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-gray-50" required dir="rtl">
                    </div>
                </div>

                @php
                    $donateNowUrl = $settings->store_url ?? $settings->beneficiary_portal_url ?? '#';
                @endphp
                <a href="{{ $donateNowUrl }}" class="w-full bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-bold text-xl py-4 rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                    <i class="fas fa-heart"></i>
                    إتمام التبرع جزاك الله خيراً
                </a>
                <div class="text-center text-gray-400 text-sm mt-5 flex items-center justify-center gap-2 font-medium">
                    <i class="fas fa-lock"></i>
                    <span>جميع معاملاتك المالية آمنة ومشفرة بالكامل</span>
                </div>
            </form>
        </div>
    </div>
</section>

<section id="contact" class="py-24 bg-slate-50 border-t border-gray-100">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="grid lg:grid-cols-2 gap-16">
            <div>
                <span class="text-emerald-600 font-bold tracking-wider uppercase text-sm mb-2 block">نسعد بخدمتكم</span>
                <h2 class="text-4xl font-bold text-gray-900 mb-6">تواصل معنا</h2>
                <p class="text-gray-600 mb-10 text-lg leading-relaxed">نحن هنا للإجابة على استفساراتكم ومساعدتكم في دعم أعمالنا الخيرية. لا تتردد في التواصل معنا عبر أي من القنوات التالية.</p>
                
                <div class="space-y-8">
                    <div class="flex items-start gap-5">
                        <div class="w-14 h-14 bg-white shadow-sm border border-gray-100 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="pt-1">
                            <h4 class="font-bold text-gray-900 text-lg mb-1">رقم التواصل</h4>
                            <p class="text-gray-600" dir="ltr">{{ $phone }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-5">
                        <div class="w-14 h-14 bg-white shadow-sm border border-gray-100 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="pt-1">
                            <h4 class="font-bold text-gray-900 text-lg mb-1">البريد الإلكتروني</h4>
                            <p class="text-gray-600">{{ $email }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-5">
                        <div class="w-14 h-14 bg-white shadow-sm border border-gray-100 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="pt-1">
                            <h4 class="font-bold text-gray-900 text-lg mb-1">العنوان الوطني</h4>
                            <p class="text-gray-600 leading-relaxed">{{ $address }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-8 md:p-10 rounded-3xl shadow-lg border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-900 mb-8">نموذج المراسلة المباشر</h3>
                <form class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">الاسم الكريم</label>
                        <input type="text" class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-gray-50 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">رقم الجوال أو البريد</label>
                        <input type="text" class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-gray-50 transition-all" dir="rtl">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">محتوى الرسالة</label>
                        <textarea rows="4" class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-gray-50 transition-all resize-none"></textarea>
                    </div>
                    <button type="button" class="w-full bg-emerald-600 text-white font-bold text-lg py-4 rounded-xl hover:bg-emerald-700 transition-colors shadow-md mt-2">
                        إرسال الرسالة الآن
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<footer class="bg-slate-900 text-white pt-20 pb-10 border-t-[6px] border-emerald-600">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16 border-b border-slate-800 pb-12">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3 mb-6">
                    @if($finalLogoUrl)
                        <img src="{{ $finalLogoUrl }}" alt="شعار الجمعية" class="h-16 bg-white p-2 rounded-xl">
                    @else
                        <div class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center font-bold text-xl">🤍</div>
                        <span class="font-bold text-2xl tracking-wide">{{ $siteName }}</span>
                    @endif
                </div>

                <p class="text-slate-400 mb-6 leading-loose max-w-md text-justify">
                    {{ $desc }}
                </p>

                @if($socialLinks->count())
                    <div class="flex gap-4 flex-wrap">
                        @foreach($socialLinks as $social)
                            <a href="{{ $social['url'] }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               aria-label="{{ $social['label'] }}"
                               title="{{ $social['label'] }}"
                               class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-300 hover:bg-emerald-600 hover:text-white transition-colors">
                                <i class="{{ $social['icon'] }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <div>
                <h4 class="font-bold text-lg mb-6 text-white relative inline-block pb-3">
                    روابط سريعة
                    <span class="absolute bottom-0 right-0 w-12 h-1 bg-emerald-500 rounded-full"></span>
                </h4>
                <ul class="space-y-3 text-slate-400">
                    <li><a href="#home" class="hover:text-emerald-400 transition-colors flex items-center gap-2"><i class="fas fa-angle-left text-xs"></i> الرئيسية</a></li>
                    <li><a href="#about" class="hover:text-emerald-400 transition-colors flex items-center gap-2"><i class="fas fa-angle-left text-xs"></i> عن الجمعية</a></li>
                    <li><a href="#projects" class="hover:text-emerald-400 transition-colors flex items-center gap-2"><i class="fas fa-angle-left text-xs"></i> المشاريع</a></li>
                    <li><a href="#donate" class="hover:text-emerald-400 transition-colors flex items-center gap-2"><i class="fas fa-angle-left text-xs"></i> التبرع</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-lg mb-6 text-white relative inline-block pb-3">
                    مواقع مفيدة
                    <span class="absolute bottom-0 right-0 w-12 h-1 bg-emerald-500 rounded-full"></span>
                </h4>
                <ul class="space-y-3 text-slate-400">
                    @foreach($usefulLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="hover:text-emerald-400 transition-colors flex items-center gap-2">
                                <i class="fas fa-angle-left text-xs"></i>
                                {{ $link['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        
        <div class="text-center text-slate-500 text-sm flex flex-col md:flex-row justify-between items-center gap-4">
            <p>© {{ date('Y') }} {{ $siteName }}. جميع الحقوق محفوظة.</p>
            <p>صنع بحب لخدمة المجتمع 💚</p>
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mainMobileNav = document.getElementById('mainMobileNav');

    if (mobileMenuBtn && mainMobileNav) {
        mobileMenuBtn.addEventListener('click', function () {
            mainMobileNav.classList.toggle('show');
        });
    }

    document.querySelectorAll('.inner-custom-header .site-nav-item').forEach(function (item) {
        const trigger = item.querySelector(':scope > .site-nav-link');
        const submenu = item.querySelector(':scope > .site-subnav');

        if (!trigger || !submenu) return;

        trigger.addEventListener('click', function (e) {
            if (window.innerWidth <= 991) {
                e.preventDefault();
                item.classList.toggle('open');
            }
        });

        item.addEventListener('mouseenter', function () {
            if (window.innerWidth > 991) {
                item.classList.add('open');
            }
        });

        item.addEventListener('mouseleave', function () {
            if (window.innerWidth > 991) {
                item.classList.remove('open');
            }
        });
    });

    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');
    let currentSlide = 0;
    let slideInterval;

    if (slides.length > 1) {
        window.goToSlide = function(index) {
            slides.forEach(s => { s.classList.remove('slide-active'); s.classList.add('slide-inactive'); });
            dots.forEach(d => { d.classList.remove('scale-125', 'bg-white'); d.classList.add('bg-white/50'); });
            
            currentSlide = index;
            slides[currentSlide].classList.remove('slide-inactive');
            slides[currentSlide].classList.add('slide-active');
            if (dots[currentSlide]) {
                dots[currentSlide].classList.remove('bg-white/50');
                dots[currentSlide].classList.add('bg-white', 'scale-125');
            }
            
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 5000);
        };

        function nextSlide() {
            let next = (currentSlide + 1) % slides.length;
            goToSlide(next);
        }

        slideInterval = setInterval(nextSlide, 5000);
    }

    const projectsTrack = document.getElementById('projectsTrack');
    const projectsMoreBtn = document.getElementById('projectsMoreBtn');

    if (projectsTrack && projectsMoreBtn) {
        let projectIndex = 0;

        projectsMoreBtn.addEventListener('click', function () {
            const slides = projectsTrack.querySelectorAll('.project-slide');
            if (!slides.length) return;

            const visibleCount = window.innerWidth <= 640 ? 1 : (window.innerWidth <= 1024 ? 2 : 3);
            const maxIndex = Math.max(0, slides.length - visibleCount);

            projectIndex = projectIndex >= maxIndex ? 0 : projectIndex + 1;

            const firstSlide = slides[0];
            const slideWidth = firstSlide.getBoundingClientRect().width + 24;
            projectsTrack.style.transform = `translateX(${projectIndex * slideWidth}px)`;
        });
    });
    
    const amountBtns = document.querySelectorAll('.amount-btn');
    amountBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            amountBtns.forEach(b => {
                b.classList.remove('border-emerald-600', 'bg-emerald-50', 'text-emerald-700', 'shadow-sm');
                b.classList.add('border-gray-200', 'text-gray-600');
            });
            btn.classList.remove('border-gray-200', 'text-gray-600');
            btn.classList.add('border-emerald-600', 'bg-emerald-50', 'text-emerald-700', 'shadow-sm');
        });
    });
    
    const payBtns = document.querySelectorAll('.pay-btn');
    payBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            payBtns.forEach(b => {
                b.classList.remove('border-emerald-600', 'bg-emerald-50', 'text-emerald-700');
                b.classList.add('border-gray-200', 'text-gray-600');
            });
            btn.classList.remove('border-gray-200', 'text-gray-600');
            btn.classList.add('border-emerald-600', 'bg-emerald-50', 'text-emerald-700');
        });
    });
});
</script>
</body>
</html>