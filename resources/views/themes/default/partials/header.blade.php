<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
    $associationName = $association->name ?? 'الموقع الرسمي للجمعية';

    $resolvedTitle = null;

    if (isset($page) && is_object($page)) {
        $resolvedTitle = $page->meta_title ?: $page->title;
    } elseif (isset($news) && is_object($news)) {
        $resolvedTitle = $news->meta_title ?: $news->title;
    } elseif (isset($project) && is_object($project)) {
        $resolvedTitle = $project->meta_title ?: $project->title;
    }

    if (blank($resolvedTitle)) {
        $resolvedTitle = trim($__env->yieldContent('title'));
    }

    if (blank($resolvedTitle)) {
        $resolvedTitle = $associationName;
    }

    if (!str_contains($resolvedTitle, $associationName)) {
        $resolvedTitle .= ' | ' . $associationName;
    }
@endphp

<title>{{ $resolvedTitle }}</title>
@include('themes.default.partials.seo-meta')

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    @if(!request()->is('admin*'))
<style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap');

        :root{
            --primary:#127962;
            --primary-dark:#0d5948;
            --secondary:#d4af37;
            --dark:#1f2937;
            --text-muted:#6b7280;
            --light-bg:#f6f8fb;
            --white:#ffffff;
            --shadow-sm:0 2px 6px rgba(0,0,0,.05);
            --shadow-md:0 12px 30px rgba(15,23,42,.10);
            --radius:16px;
            --transition:all .3s ease;
        }

        body{
            font-family:'Tajawal',sans-serif;
            background:var(--light-bg);
            color:var(--dark);
            overflow-x:hidden;
            margin:0;
        }

        a{ text-decoration:none; transition:var(--transition); }

        .topbar{
            background:var(--primary-dark);
            color:#fff;
            font-size:.92rem;
            position:relative;
            z-index:1040;
        }

        .topbar a{ color:rgba(255,255,255,.92); }
        .topbar a:hover{ color:var(--secondary); }
        .topbar .contact-info i{
            color:var(--secondary);
            margin-inline-end:6px;
        }

        .header-main{
            background:rgba(255,255,255,.96);
            box-shadow:var(--shadow-sm);
            position:sticky;
            top:0;
            z-index:1030;
            transition:var(--transition);
            border-bottom:1px solid rgba(0,0,0,.04);
        }

        .home-header{
            position:absolute;
            top:44px;
            right:0;
            left:0;
            background:rgba(255,255,255,.16);
            backdrop-filter:blur(10px);
            -webkit-backdrop-filter:blur(10px);
            border-bottom:1px solid rgba(255,255,255,.18);
            box-shadow:none;
        }

        .navbar{ padding:.55rem 0; }

        .navbar-brand{
            display:flex;
            align-items:center;
            gap:14px;
            margin:0;
        }

        .brand-logo-wrap{
            width:58px;
            height:58px;
            border-radius:18px;
            display:flex;
            align-items:center;
            justify-content:center;
            border:1px solid rgba(255,255,255,.18);
            background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            color:#fff;
            font-size:25px;
            font-weight:800;
            flex:0 0 auto;
            box-shadow:var(--shadow-md);
        }

        .brand-text h1{
            font-size:1.22rem;
            font-weight:800;
            margin:0;
            line-height:1.35;
            color:var(--primary-dark);
        }

        .home-header .brand-text h1,
        .home-header .brand-text p,
        .home-header .navbar-nav .nav-link{
            color:#fff;
        }

        .brand-text p{
            font-size:.82rem;
            color:var(--text-muted);
            margin:0;
        }

        .navbar-nav .nav-link{
            color:var(--dark);
            font-weight:700;
            font-size:1rem;
            padding:1rem .95rem;
            position:relative;
            display:flex;
            align-items:center;
            gap:8px;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active{
            color:var(--primary);
        }

        .home-header .navbar-nav .nav-link:hover,
        .home-header .navbar-nav .nav-link.active{
            color:#fff;
            opacity:.9;
        }

        .navbar-nav .nav-link::after{
            content:'';
            position:absolute;
            right:50%;
            transform:translateX(50%);
            bottom:6px;
            width:0;
            height:3px;
            background:var(--secondary);
            border-radius:999px;
            transition:var(--transition);
        }

        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after{
            width:30px;
        }

        .dropdown-menu{
            border:none;
            box-shadow:var(--shadow-md);
            border-radius:18px;
            padding:14px 0;
            min-width:240px;
            border-top:4px solid var(--primary);
        }

        .dropdown-item{
            padding:11px 20px;
            font-weight:600;
            font-size:.95rem;
            color:var(--dark);
            display:flex;
            align-items:center;
            gap:8px;
        }

        .dropdown-item:hover{
            background:#f2f8f6;
            color:var(--primary);
            padding-inline-start:26px;
        }

        .dropdown-submenu{
            position:relative;
        }

        
        .dropdown-submenu{
            position:relative;
        }

        .dropdown-submenu > .dropdown-menu{
            top:0;
            right:100%;
            margin-top:-14px;
        }

        .menu-link-content{
            display:flex;
            align-items:center;
            gap:8px;
        }

        .has-submenu{
            display:flex !important;
            align-items:center;
            justify-content:space-between;
            gap:10px;
        }

        .menu-arrow{
            font-size:12px;
            line-height:1;
            flex:0 0 auto;
        }

        .dropdown-menu .dropdown-submenu > .dropdown-menu{
            top:-14px;
            right:100%;
        }

        .dropdown-menu li{
            position:relative;
        }

        
        .site-nav,
        .site-subnav{
            list-style:none;
            margin:0;
            padding:0;
        }

        .site-nav{
            display:flex;
            align-items:center;
            gap:0;
        }

        .site-nav-item{
            position:relative;
        }

        .site-nav-link{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            color:var(--dark);
            font-weight:700;
            font-size:1rem;
            padding:1rem .95rem;
            text-decoration:none;
            position:relative;
            white-space:nowrap;
        }

        .site-nav-link:hover{
            color:var(--primary);
        }

        .site-nav-link-main{
            display:flex;
            align-items:center;
            gap:8px;
        }

        .site-nav-arrow{
            font-size:12px;
            line-height:1;
            flex:0 0 auto;
        }

        .site-subnav{
            position:absolute;
            top:100%;
            right:0;
            min-width:240px;
            background:#fff;
            border-top:4px solid var(--primary);
            border-radius:18px;
            box-shadow:var(--shadow-md);
            padding:14px 0;
            display:none;
            z-index:1200;
        }

        .site-subnav .site-nav-link{
            padding:11px 20px;
            font-weight:600;
            font-size:.95rem;
        }

        .site-subnav .site-nav-link:hover{
            background:#f2f8f6;
            color:var(--primary);
        }

        .site-subnav .site-nav-item{
            position:relative;
        }

        .site-subnav .site-subnav{
            top:-14px;
            right:100%;
        }

        .site-nav-item.open > .site-subnav{
            display:block;
        }


        @media (max-width: 991.98px){
            .dropdown-submenu > .dropdown-menu,
            .dropdown-menu .dropdown-submenu > .dropdown-menu{
                position:static !important;
                right:auto !important;
                top:auto !important;
                margin-top:8px !important;
                margin-right:0 !important;
                box-shadow:none !important;
                border:1px solid #e5e7eb !important;
                border-radius:12px !important;
            }

            .dropdown-menu{
                padding:8px 0;
            }

            .dropdown-menu .dropdown-item{
                padding-right:16px;
            }

            .submenu-depth-2 .dropdown-item{ padding-right:22px; }
            .submenu-depth-3 .dropdown-item{ padding-right:30px; }
            .submenu-depth-4 .dropdown-item{ padding-right:38px; }
            .submenu-depth-5 .dropdown-item{ padding-right:46px; }
        }
.dropdown-submenu > .dropdown-menu{
            top:0;
            right:100%;
            margin-top:-14px;
        }

        .has-children{
            display:flex !important;
            align-items:center;
            gap:8px;
        }

        .has-children::after{
            display:inline-block !important;
            margin-right:6px;
            margin-left:0 !important;
            vertical-align:middle;
        }

        .navbar-nav > li > .has-children::after{
            content:'▾';
            border:0 !important;
            font-size:12px;
            line-height:1;
        }

        .dropdown-menu .has-children::after{
            content:'◂';
            border:0 !important;
            font-size:12px;
            line-height:1;
            margin-right:auto;
        }

        @media (max-width: 991.98px){
            .dropdown-submenu > .dropdown-menu{
                right:0;
                margin-top:0;
            }

            .site-nav-wrapper{
                width:100%;
                margin-top:8px;
            }

            .site-nav{
                display:flex;
                flex-direction:column;
                align-items:stretch;
                width:100%;
                gap:6px;
            }

            .site-nav-item{
                width:100%;
            }

            .site-nav-link{
                width:100%;
                display:flex;
                align-items:center;
                justify-content:space-between;
                padding:14px 12px;
                border-radius:12px;
                font-size:1rem;
                line-height:1.5;
            }

            .site-nav-link-main{
                display:flex;
                align-items:center;
                gap:10px;
                min-width:0;
            }

            .site-nav-arrow{
                font-size:14px;
                margin-right:8px;
                flex:0 0 auto;
            }

            .site-subnav{
                position:static !important;
                display:none;
                width:100%;
                min-width:100%;
                margin-top:6px !important;
                margin-right:0 !important;
                padding:6px 0;
                border:1px solid #e5e7eb;
                border-radius:12px;
                box-shadow:none !important;
                background:#fff;
            }

            .site-nav-item.open > .site-subnav{
                display:block !important;
            }

            .site-subnav .site-nav-link{
                padding:12px 14px;
                font-size:.96rem;
                border-radius:10px;
            }

            .level-1 .site-nav-link{
                padding-right:18px;
            }

            .level-2 .site-nav-link{
                padding-right:30px;
            }

            .level-3 .site-nav-link{
                padding-right:42px;
            }

            .level-4 .site-nav-link{
                padding-right:54px;
            }

            .level-5 .site-nav-link{
                padding-right:66px;
            }

            .navbar-nav .nav-link{
                padding:.8rem 0;
            }

            .collapse.navbar-collapse{
                padding-top:10px;
            }

            .btn-head{
                width:100%;
                justify-content:center;
            }
        }

        .btn-head{
            background:var(--primary);
            color:#fff;
            border:2px solid var(--primary);
            border-radius:999px;
            padding:10px 22px;
            font-weight:700;
            display:inline-flex;
            align-items:center;
            gap:8px;
        }

        .btn-head:hover{
            background:transparent;
            color:var(--primary);
        }

        .home-header .btn-head{
            background:#fff;
            border-color:#fff;
            color:var(--primary-dark);
        }

        .home-header .btn-head:hover{
            background:transparent;
            color:#fff;
            border-color:#fff;
        }

        .content-wrapper{ min-height:60vh; }

        .site-footer{
            background:#111827;
            color:#e5e7eb;
            padding-top:60px;
            margin-top:70px;
        }

        .footer-title{
            color:#fff;
            font-weight:700;
            font-size:1.2rem;
            margin-bottom:25px;
            position:relative;
            padding-bottom:10px;
        }

        .footer-title::after{
            content:'';
            position:absolute;
            bottom:0;
            right:0;
            width:42px;
            height:3px;
            background:var(--secondary);
        }

        .footer-links{
            list-style:none;
            padding:0;
            margin:0;
        }

        .footer-links li{ margin-bottom:15px; }

        .footer-links a{
            color:#9ca3af;
            font-size:.95rem;
            display:inline-flex;
            align-items:center;
            gap:8px;
        }

        .footer-links a::before{
            content:"\\F285";
            font-family:"bootstrap-icons";
            font-size:.8rem;
            color:var(--secondary);
        }

        .footer-links a:hover{
            color:var(--secondary);
            transform:translateX(-5px);
        }

        .footer-bottom{
            background:#030712;
            padding:20px 0;
            margin-top:40px;
            font-size:.9rem;
            color:#6b7280;
        }

        @media (max-width: 991.98px){
            .home-header{
                position:relative;
                top:0;
                background:rgba(255,255,255,.96);
            }

            .home-header .brand-text h1,
            .home-header .brand-text p,
            .home-header .navbar-nav .nav-link{
                color:var(--dark);
            }

            .home-header .btn-head{
                background:var(--primary);
                color:#fff;
                border-color:var(--primary);
            }

            .site-nav{
                display:block;
                width:100%;
            }

            .site-nav-link{
                padding:.8rem 0;
                width:100%;
            }

            .site-subnav{
                position:static !important;
                display:none;
                min-width:100%;
                box-shadow:none;
                border:1px solid #e5e7eb;
                border-radius:12px;
                margin-top:8px;
                padding:8px 0;
            }

            .site-nav-item.open > .site-subnav{
                display:block;
            }

            .site-subnav .site-nav-link{
                padding:10px 14px;
            }

            .level-1 .site-nav-link{ padding-right:18px; }
            .level-2 .site-nav-link{ padding-right:28px; }
            .level-3 .site-nav-link{ padding-right:38px; }
            .level-4 .site-nav-link{ padding-right:48px; }

            .navbar-nav .nav-link{ padding:.8rem 0; }

            .brand-text h1{ font-size:1.05rem; }
        }
    
        /* OAI_FORCED_THEME */
        :root{
            --primary: {{ $settings?->primary_color ?: '#0f766e' }} !important;
            --primary-dark: {{ $settings?->secondary_color ?: '#0b5f59' }} !important;
            --secondary: {{ $settings?->secondary_color ?: '#14b8a6' }} !important;
            --btn-color: {{ $settings?->button_color ?: ($settings?->primary_color ?: '#0f766e') }} !important;
        }
        .topbar{
            background: var(--primary) !important;
            color:#fff !important;
        }
        .topbar a,
        .topbar span,
        .topbar i{
            color:#fff !important;
        }
        .header-main,
        .home-header{
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important;
        }
        .btn-head{
            background: var(--btn-color) !important;
            border-color: var(--btn-color) !important;
            color:#fff !important;
        }
        .btn-head:hover{
            background: transparent !important;
            color: var(--btn-color) !important;
            border-color: var(--btn-color) !important;
        }
        .brand-logo-wrap{
            background:#fff !important;
            overflow:hidden !important;
            padding:0 !important;
            display:flex !important;
            align-items:center !important;
            justify-content:center !important;
        }
        .brand-logo-wrap img{
            width:100% !important;
            height:100% !important;
            object-fit:contain !important;
            display:block !important;
        }

</style>
@endif

    @stack('styles')

@if(!request()->routeIs('website.home'))
<style>
    /* Fix inner pages spacing */
    .content-wrapper{
        padding-top: 0 !important;
        margin-top: 0 !important;
    }

    .content-wrapper > section:first-child,
    .content-wrapper > div:first-child,
    .content-wrapper .page-wrap:first-child{
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .site-footer{
        margin-top: 0 !important;
    }
</style>
@endif
</head>
<body>
@php
    $settings = \App\Models\SiteSetting::query()->latest('id')->first();
    $logoMedia = $settings?->logoMedia;
    $logoUrl = $logoMedia && !empty($logoMedia->url) ? \App\Support\Media\MediaUrl::forDiskPath('public', $logoMedia->url) : null;

    $settings = $settings ?? \App\Models\SiteSetting::query()->latest('id')->first();

    if (!isset($mainMenuItems)) {
        $mainMenu = \App\Models\Menu::query()->where('location', 'main')->first();
        $mainMenuItems = $mainMenu
            ? \App\Models\MenuItem::query()
                ->where('menu_id', $mainMenu->id)
                ->whereNull('parent_id')
                ->with(['children' => fn ($query) => $query->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get()
            : collect();
    }

    if (!isset($footerMenuItems)) {
        $footerMenu = \App\Models\Menu::query()->where('location', 'footer')->first();
        $footerMenuItems = $footerMenu
            ? \App\Models\MenuItem::query()
                ->where('menu_id', $footerMenu->id)
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->get()
            : collect();
    }

    $isHome = request()->routeIs('website.home');
@endphp

<div class="topbar d-none d-lg-block">
    <div class="container d-flex align-items-center justify-content-between">
        <div class="contact-info d-flex gap-4 py-2">
            @if(!empty($settings?->phone))
                <span><i class="bi bi-telephone-fill"></i> {{ $settings->phone }}</span>
            @endif
            @if(!empty($settings?->email))
                <span><i class="bi bi-envelope-fill"></i> {{ $settings->email }}</span>
            @endif
        </div>

        <div class="d-flex gap-3 align-items-center py-2">
            <a href="/admin"><i class="bi bi-box-arrow-in-left"></i> تسجيل الدخول</a>
        </div>
    </div>
</div>

<header class="header-main {{ $isHome ? 'home-header' : '' }}">
    <div class="container">
        <nav class="navbar navbar-expand-lg">
            <a class="navbar-brand" href="/">
                <div class="brand-logo-wrap">
                    @if($logoUrl)
                        <img decoding="async" src="{{ $logoUrl }}" alt="{{ $association->name ?? 'الشعار' }}">
                    @else
                        {{ mb_substr($association->name ?? 'ج', 0, 1) }}
                    @endif
                </div>

                <div class="brand-text d-none d-sm-block">
                    <h1>{{ $association->name ?? 'جمعية البر الأهلية' }}</h1>
                    <p>مسجلة بالمركز الوطني برقم ({{ $settings->license_number ?? '1234' }})</p>
                </div>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                @if($mainMenuItems->count())
                    @include('themes.default.partials.navigation-tree', ['items' => $mainMenuItems])
                @else
                    <ul class="site-nav">
                        <li class="site-nav-item">
                            <a class="site-nav-link" href="/">
                                <span class="site-nav-link-main">
                                    <i class="bi bi-house"></i>
                                    <span>الرئيسية</span>
                                </span>
                            </a>
                        </li>
                        <li class="site-nav-item">
                            <a class="site-nav-link" href="/news">
                                <span class="site-nav-link-main">
                                    <i class="bi bi-newspaper"></i>
                                    <span>الأخبار</span>
                                </span>
                            </a>
                        </li>
                    </ul>
                @endif
                <div class="d-flex gap-2 mt-3 mt-lg-0 ms-lg-4">
                    <a href="/news" class="btn btn-head"><i class="bi bi-newspaper"></i> المركز الإعلامي</a>
                </div>
            </div>
        </nav>
    </div>
</header>


@if(!request()->is('admin*'))
<style>
    /* FORCE_SITE_THEME_FROM_SETTINGS */
    :root{
        --primary: {{ $settings?->primary_color ?: '#0f766e' }};
        --secondary: {{ $settings?->secondary_color ?: '#14b8a6' }};
        --button: {{ $settings?->button_color ?: ($settings?->primary_color ?: '#0f766e') }};
    }

    body .topbar{
        background: var(--primary) !important;
    }

    body .header-main,
    body .home-header{
        background: linear-gradient(135deg, var(--primary), var(--secondary)) !important;
    }

    body .btn-head,
    body .btn.btn-head,
    body a.btn-head{
        background: var(--button) !important;
        border-color: var(--button) !important;
        color: #fff !important;
    }

    body .btn-head:hover,
    body .btn.btn-head:hover,
    body a.btn-head:hover{
        background: transparent !important;
        color: var(--button) !important;
        border-color: var(--button) !important;
    }

    body .navbar-nav .nav-link:hover,
    body .navbar-nav .nav-link.active,
    body .dropdown-menu .dropdown-item:hover{
        color: var(--primary) !important;
    }

    body .dropdown-menu{
        border-top: 3px solid var(--primary) !important;
    }

    body .brand-text h1{
        color: #fff !important;
    }

    body .brand-text p{
        color: rgba(255,255,255,.9) !important;
    }
</style>
@endif

