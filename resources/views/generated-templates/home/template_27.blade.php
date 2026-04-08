
@php
    $connection = \Illuminate\Support\Facades\DB::connection('tenant');
    
    $settings = $connection->table('site_settings')->orderBy('id', 'desc')->first();
    
    // متغيرات الموقع العامة
    $siteName = $settings->site_name ?? 'جمعية البر الخيرية';
    $assocName = $settings->association_name ?? $siteName;
    $aboutText = $settings->about_text ?? 'نسعى لتقديم خدمات رعوية وتنموية رائدة بشفافية واحترافية لتمكين المستفيدين وبناء مجتمع متكافل.';
    $vision = $settings->vision ?? 'الريادة والتميز في العمل الخيري والتنموي.';
    $mission = $settings->mission ?? 'تقديم برامج ومشاريع نوعية تسهم في تمكين المستفيدين وتحقيق الاستدامة.';
    $videoUrl = $settings->intro_video_url ?? null;
    $desc = $settings->site_description ?? 'جمعية خيرية مصرحة تهدف لدعم المحتاجين وتنمية المجتمع عبر برامج مستدامة.';
    $licenseNumber = $settings->license_number ?? '123';
    
    // معلومات التواصل
    $address = $settings->address ?? 'المملكة العربية السعودية';
    $phone = $settings->phone ?? ($settings->official_phone ?? '0500000000');
    $email = $settings->email ?? ($settings->official_email ?? 'info@ber.org.sa');
    
    // روابط هامة
    $beneficiaryPortalUrl = $settings->beneficiary_portal_url ?? '#';
    $storeUrl = $settings->store_url ?? '#';
    $twitter = $settings->twitter_url ?? '#';
    $instagram = $settings->instagram_url ?? '#';
    $youtube = $settings->youtube_url ?? '#';
    $whatsapp = $settings->whatsapp_url ?? 'https://wa.me/'.$phone;
    
    // الألوان المستوحاة من جمعية المجمعة (أزرق كحلي وذهبي)
    $primary = $settings?->primary_color ?? '#0f3a61'; 
    $secondary = $settings?->secondary_color ?? '#cfa144';

    // جلب الشعار
    $logoPath = $settings->logo ?? null;
    if (empty($logoPath) && !empty($settings->logo_media_id)) {
        $media = $connection->table('media_items')->where('id', $settings->logo_media_id)->first();
        if ($media) {
            $logoPath = $media->file ?? $media->path ?? null;
        }
    }
    $finalLogoUrl = null;
    if (!empty($logoPath)) {
        $finalLogoUrl = str_starts_with($logoPath, 'http') ? $logoPath : \App\Support\Media\MediaUrl::forDiskPath('public', $logoPath);
    }

    // جلب القوائم
    $mainMenu = $connection->table('menus')->where('location', 'header')->first();
    if (!$mainMenu) $mainMenu = $connection->table('menus')->first();
    
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

    // جلب البيانات الأخرى
    $projects = collect();
    $statistics = collect();
    $sliders = collect();
    $newsItems = collect();
    $partners = collect();

    try {
        $projects = $connection->table('program_projects')->where('is_active', 1)->orderBy('sort_order', 'asc')->limit(6)->get();
        $statistics = $connection->table('statistics')->orderBy('sort_order', 'asc')->limit(4)->get();
        $sliders = $connection->table('sliders')->where('is_active', 1)->orderBy('sort_order', 'asc')->get();
        $newsItems = $connection->table('news')->where('is_active', 1)->where('status', 'published')->orderByDesc('id')->limit(3)->get();
        $partners = $connection->table('partners')->where('is_active', 1)->orderBy('sort_order', 'asc')->orderByDesc('id')->get();
    } catch (\Exception $e) {}

    if (!function_exists('resolveMenuUrl')) {
        function resolveMenuUrl($item, $connection) {
            if (!empty($item->resolved_url)) return $item->resolved_url;
            if ($item->type === 'page' && !empty($item->page_id)) {
                $page = $connection->table('pages')->where('id', $item->page_id)->first();
                return $page ? '/page/' . $page->slug : '#';
            }
            return !empty($item->url) ? $item->url : '#';
        }
    }
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteName }}</title>
    
    @if($finalLogoUrl)
    <link rel="icon" href="{{ $finalLogoUrl }}" type="image/x-icon">
    @endif

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts (Tajawal) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome & Swiper CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <!-- Tailwind Config -->
    <script>
        window.tailwind = window.tailwind || {};
        window.tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Tajawal', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '{{ $primary }}',
                            light: '{{ $primary }}e6',
                            dark: '#0a2a47', // أغمق قليلاً
                        },
                        secondary: {
                            DEFAULT: '{{ $secondary }}',
                            light: '{{ $secondary }}e6',
                            dark: '#b38836', // أغمق قليلاً
                        }
                    },
                    boxShadow: {
                        'card': '0 4px 20px rgba(0, 0, 0, 0.05)',
                        'float': '0 10px 30px rgba(15, 58, 97, 0.15)',
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #F9FAFB; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: {{ $secondary }}; border-radius: 4px; }

        .pattern-bg {
            background-image: url('data:image/svg+xml,%3Csvg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M20 20.5V18H0v-2h20v-2H0v-2h20v-2H0V8h20V6H0V4h20V2H0V0h22v20h2V0h2v20h2V0h2v20h2V0h2v20h2V0h2v20h2v2H20v-1.5zM0 20h2v20H0V20zm4 0h2v20H4V20zm4 0h2v20H8V20zm4 0h2v20h-2V20zm4 0h2v20h-2V20zm4 4v-2h20v2H20v2h20v2H20v2h20v2H20v2h20v2H20v2h20v2H20v2h20v2H20v-2h-2v-20h2v2z" fill="%23ffffff" fill-opacity="0.03" fill-rule="evenodd"/%3E%3C/svg%3E');
        }

        .marquee { overflow: hidden; white-space: nowrap; width: 100%; }
        .marquee-content { display: flex; animation: scroll 25s linear infinite; }
        .marquee-content:hover { animation-play-state: paused; }
        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(50%); } 
        }

        .img-zoom { overflow: hidden; }
        .img-zoom img { transition: transform 0.6s ease; }
        .card-hover:hover .img-zoom img { transform: scale(1.08); }
        
        .title-line { position: relative; padding-bottom: 12px; }
        .title-line::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 60px;
            height: 3px;
            background-color: {{ $secondary }};
            border-radius: 2px;
        }

        /* Swiper Customization */
        .swiper-pagination-bullet { background: #fff !important; opacity: 0.5; width: 10px; height: 10px; }
        .swiper-pagination-bullet-active { background: {{ $secondary }} !important; opacity: 1; width: 24px; border-radius: 5px; }
        .swiper-button-next, .swiper-button-prev { color: white !important; background: rgba(0,0,0,0.3); width: 40px; height: 40px; border-radius: 50%; }
        .swiper-button-next:after, .swiper-button-prev:after { font-size: 16px !important; }
    </style>
</head>
<body class="text-gray-800 antialiased overflow-x-hidden selection:bg-secondary selection:text-white flex flex-col min-h-screen">

    <!-- Top Bar -->
    <div class="bg-primary text-white py-2 text-sm hidden md:block">
        <div class="container mx-auto px-4 max-w-7xl flex justify-between items-center">
            <div class="flex gap-6 items-center">
                <a href="mailto:{{ $email }}" class="flex items-center gap-2 hover:text-secondary transition">
                    <i class="fas fa-envelope text-secondary"></i> {{ $email }}
                </a>
                <span class="w-px h-4 bg-white/20"></span>
                <a href="tel:{{ $phone }}" class="flex items-center gap-2 hover:text-secondary transition" dir="ltr">
                    <i class="fas fa-phone-alt text-secondary"></i> {{ $phone }}
                </a>
            </div>
            <div class="flex gap-4 items-center">
                <span class="text-gray-300 text-xs">تابعنا على:</span>
                <a href="{{ $twitter }}" class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center hover:bg-secondary transition-colors"><i class="fab fa-twitter"></i></a>
                <a href="{{ $instagram }}" class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center hover:bg-secondary transition-colors"><i class="fab fa-instagram"></i></a>
                <a href="{{ $youtube }}" class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center hover:bg-secondary transition-colors"><i class="fab fa-youtube"></i></a>
                <a href="{{ $whatsapp }}" class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center hover:bg-green-500 transition-colors"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50 transition-all duration-300" id="main-header">
        <div class="container mx-auto px-4 max-w-7xl flex justify-between items-center h-24">
            
            <!-- Logo -->
            <a href="/" class="flex items-center gap-4 group">
                <div class="w-16 h-16 flex items-center justify-center">
                    @if($finalLogoUrl)
                        <img src="{{ $finalLogoUrl }}" alt="{{ $assocName }}" class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform">
                    @else
                        <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center text-white text-2xl">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                    @endif
                </div>
                <div class="hidden sm:block border-r-2 border-gray-100 pr-4">
                    <h1 class="text-xl font-bold text-primary leading-tight">{{ $assocName }}</h1>
                    <p class="text-xs text-gray-500 font-medium mt-1">مسجلة برقم ({{ $licenseNumber }})</p>
                </div>
            </a>

            <!-- Desktop Nav -->
            <nav class="hidden lg:flex items-center gap-6 xl:gap-8">
                <a href="/" class="font-bold text-primary relative pb-2 after:absolute after:bottom-0 after:right-0 after:w-full after:h-0.5 after:bg-secondary">الرئيسية</a>
                
                @foreach($rootItems as $item)
                    @php
                        $children = $groupedItems->get($item->id) ?? collect();
                        $hasChildren = $children->count() > 0;
                        $url = $hasChildren ? 'javascript:void(0);' : resolveMenuUrl($item, $connection);
                    @endphp
                    <div class="relative group py-4">
                        <a href="{{ $url }}" class="font-bold text-gray-600 hover:text-primary transition-colors flex items-center gap-1">
                            {{ $item->title }}
                            @if($hasChildren) <i class="fas fa-angle-down text-xs mt-1"></i> @endif
                        </a>
                        @if($hasChildren)
                            <div class="absolute right-0 top-full mt-0 w-56 bg-white border-t-2 border-primary rounded-b-lg shadow-xl hidden group-hover:block opacity-0 group-hover:opacity-100 transition-opacity">
                                @foreach($children as $subItem)
                                    <a href="{{ resolveMenuUrl($subItem, $connection) }}" class="block px-4 py-3 text-sm font-bold text-gray-600 hover:bg-gray-50 hover:text-primary border-b border-gray-100 last:border-0 transition-colors">{{ $subItem->title }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </nav>

            <!-- Action Buttons -->
            <div class="hidden lg:flex items-center gap-3">
                <a href="{{ $beneficiaryPortalUrl }}" class="flex items-center gap-2 text-primary font-bold hover:text-secondary transition-colors px-3 py-2">
                    <i class="far fa-user text-lg"></i> دخول
                </a>
                <a href="{{ $storeUrl }}" class="bg-secondary hover:bg-secondary-dark text-white px-6 py-2.5 rounded font-bold transition-all flex items-center gap-2 shadow-md">
                    <i class="fas fa-shopping-cart"></i> المتجر
                </a>
                <a href="{{ $storeUrl }}" class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded font-bold transition-all flex items-center gap-2 shadow-md">
                    <i class="fas fa-heart"></i> تبرع
                </a>
            </div>

            <!-- Mobile Toggle -->
            <button class="lg:hidden text-primary text-2xl" id="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="fixed inset-0 bg-primary/98 backdrop-blur-md z-[100] hidden flex-col p-6" id="mobile-menu">
        <div class="flex justify-between items-center mb-8 border-b border-white/10 pb-4">
            <h2 class="text-white font-bold text-xl">{{ $assocName }}</h2>
            <button class="text-white text-2xl" id="close-menu-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex flex-col gap-4 overflow-y-auto pb-20">
            <a href="/" class="text-lg font-bold text-white hover:text-secondary">الرئيسية</a>
            @foreach($rootItems as $item)
                @php
                    $children = $groupedItems->get($item->id) ?? collect();
                    $hasChildren = $children->count() > 0;
                @endphp
                @if(!$hasChildren)
                    <a href="{{ resolveMenuUrl($item, $connection) }}" class="text-lg font-bold text-white hover:text-secondary">{{ $item->title }}</a>
                @else
                    <div class="text-lg font-bold text-white">{{ $item->title }}</div>
                    <div class="pr-4 flex flex-col gap-3 border-r-2 border-secondary/50 py-2">
                        @foreach($children as $subItem)
                            <a href="{{ resolveMenuUrl($subItem, $connection) }}" class="text-base text-gray-300 hover:text-secondary">{{ $subItem->title }}</a>
                        @endforeach
                    </div>
                @endif
            @endforeach
            <hr class="border-white/10 my-4">
            <a href="{{ $storeUrl }}" class="bg-secondary text-white text-center py-3 rounded font-bold text-lg">المتجر والتبرع</a>
            <a href="{{ $beneficiaryPortalUrl }}" class="border border-white text-white text-center py-3 rounded font-bold text-lg">بوابة المستفيدين</a>
        </div>
    </div>

    <!-- Hero Slider -->
    <section class="relative h-[500px] md:h-[600px] lg:h-[70vh] bg-primary">
        <div class="swiper heroSwiper w-full h-full">
            <div class="swiper-wrapper">
                @if(isset($sliders) && $sliders->count() > 0)
                    @foreach($sliders as $slider)
                        <div class="swiper-slide relative">
                            <img src="{{ \App\Support\Media\MediaUrl::forDiskPath('public', $slider->image) }}" class="w-full h-full object-cover" alt="Slider">
                            <div class="absolute inset-0 bg-gradient-to-l from-primary/90 via-primary/60 to-transparent"></div>
                            <div class="absolute inset-0 pattern-bg"></div>
                            
                            <div class="absolute inset-0 flex items-center">
                                <div class="container mx-auto px-4 max-w-7xl">
                                    <div class="max-w-2xl text-white">
                                        @if($slider->title)
                                            <h2 class="text-3xl md:text-5xl font-bold mb-4 leading-tight">{{ $slider->title }}</h2>
                                        @endif
                                        @if($slider->description)
                                            <p class="text-lg md:text-xl text-gray-200 mb-8 leading-relaxed">{{ $slider->description }}</p>
                                        @endif
                                        @if($slider->button_text && $slider->button_url)
                                            <a href="{{ $slider->button_url }}" class="inline-block bg-secondary hover:bg-secondary-dark text-white px-8 py-3 rounded font-bold text-lg transition-colors">
                                                {{ $slider->button_text }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback Slide -->
                    <div class="swiper-slide relative">
                        <img src="https://images.unsplash.com/photo-1591462215264-754668df41df?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover" alt="Background">
                        <div class="absolute inset-0 bg-gradient-to-l from-primary/90 via-primary/60 to-transparent"></div>
                        <div class="absolute inset-0 pattern-bg"></div>
                        <div class="absolute inset-0 flex items-center">
                            <div class="container mx-auto px-4 max-w-7xl">
                                <div class="max-w-2xl text-white">
                                    <h2 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">عطاؤك يصنع الفارق</h2>
                                    <p class="text-xl text-gray-200 mb-8 leading-relaxed">{{ $desc }}</p>
                                    <a href="{{ $storeUrl }}" class="inline-block bg-secondary hover:bg-secondary-dark text-white px-8 py-3 rounded font-bold text-lg transition-colors">
                                        تبرع الآن
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <!-- Pagination & Navigation -->
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next hidden md:flex"></div>
            <div class="swiper-button-prev hidden md:flex"></div>
        </div>
    </section>

    <!-- Quick Donate Floating Box -->
    <section class="relative z-20 -mt-16 md:-mt-20 mb-16 px-4">
        <div class="container mx-auto max-w-5xl">
            <div class="bg-white rounded-xl shadow-float p-6 md:p-8 flex flex-col md:flex-row items-center gap-6 border-t-4 border-secondary">
                <div class="md:w-1/3 flex items-center gap-4 w-full">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-full flex items-center justify-center text-2xl shrink-0">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-primary mb-1">التبرع السريع</h3>
                        <p class="text-sm text-gray-500">اختر المشروع وساهم بسهولة</p>
                    </div>
                </div>
                
                <div class="md:w-2/3 w-full">
                    <form action="{{ $storeUrl }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                        <select class="flex-1 bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-primary font-bold text-gray-700">
                            <option value="">-- اختر المشروع للتبرع --</option>
                            @foreach($projects->take(5) as $proj)
                                <option value="{{ $proj->id }}">{{ $proj->title }}</option>
                            @endforeach
                            <option value="general">تبرع عام</option>
                        </select>
                        <input type="number" placeholder="المبلغ (ر.س)" class="w-full sm:w-32 bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-primary font-bold text-center">
                        <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded font-bold transition-colors whitespace-nowrap">
                            متابعة
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    @if(isset($statistics) && $statistics->count() > 0)
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                @foreach($statistics as $stat)
                <div class="text-center p-6 border border-gray-100 rounded-lg hover:shadow-card transition-shadow">
                    <div class="text-4xl text-secondary mb-4">
                        @if($stat->icon && str_contains($stat->icon, 'heroicon'))
                            <i class="fas fa-chart-line"></i>
                        @else
                            <i class="{{ $stat->icon ?? 'fas fa-star' }}"></i>
                        @endif
                    </div>
                    <h4 class="text-3xl font-bold text-primary mb-2">{{ $stat->value }}</h4>
                    <p class="text-gray-600 font-bold">{{ $stat->title }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                
                <!-- Text Content -->
                <div>
                    <h3 class="text-secondary font-bold mb-2 flex items-center gap-2">
                        <i class="fas fa-info-circle"></i> نبذة عنا
                    </h3>
                    <h2 class="text-3xl md:text-4xl font-bold text-primary mb-6 title-line">
                        {{ $assocName }}
                    </h2>
                    <div class="text-gray-600 text-lg leading-loose mb-8 text-justify">
                        {!! $aboutText !!}
                    </div>
                    
                    <div class="grid sm:grid-cols-2 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-lg shadow-sm border-r-4 border-primary">
                            <h4 class="font-bold text-primary mb-2 text-lg"><i class="fas fa-eye text-secondary mr-1"></i> الرؤية</h4>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $vision }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-sm border-r-4 border-secondary">
                            <h4 class="font-bold text-primary mb-2 text-lg"><i class="fas fa-bullseye text-secondary mr-1"></i> الرسالة</h4>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $mission }}</p>
                        </div>
                    </div>

                    <a href="/page/about" class="inline-flex items-center gap-2 bg-white border border-gray-200 text-primary hover:bg-primary hover:text-white hover:border-primary px-6 py-2.5 rounded font-bold transition-all">
                        التفاصيل <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                </div>

                <!-- Image/Video -->
                <div class="relative">
                    <div class="relative rounded-xl overflow-hidden shadow-lg h-[400px]">
                        @if($videoUrl)
                            <img src="https://images.unsplash.com/photo-1593113563332-ce147fb3761ea?q=80&w=2069&auto=format&fit=crop" class="w-full h-full object-cover" alt="عن الجمعية">
                            <a href="{{ $videoUrl }}" target="_blank" class="absolute inset-0 bg-primary/40 flex items-center justify-center group">
                                <div class="w-20 h-20 bg-white/90 rounded-full flex items-center justify-center text-primary text-2xl shadow-lg group-hover:scale-110 group-hover:bg-secondary group-hover:text-white transition-all cursor-pointer pl-1">
                                    <i class="fas fa-play"></i>
                                </div>
                            </a>
                        @else
                            <img src="https://images.unsplash.com/photo-1593113563332-ce147fb3761ea?q=80&w=2069&auto=format&fit=crop" class="w-full h-full object-cover" alt="عن الجمعية">
                        @endif
                    </div>
                    <!-- Decorative element similar to Ber Majmaah style -->
                    <div class="absolute -bottom-5 -right-5 w-24 h-24 bg-secondary rounded-lg -z-10"></div>
                    <div class="absolute -top-5 -left-5 w-24 h-24 border-4 border-primary rounded-lg -z-10"></div>
                </div>

            </div>
        </div>
    </section>

    <!-- Projects Section (Store) -->
    <section id="projects" class="py-20 bg-white">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4 border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-3xl font-bold text-primary title-line">مشاريعنا وفرص التبرع</h2>
                    <p class="text-gray-500 mt-4">ساهم في دعم برامجنا ومشاريعنا التنموية والرعوية</p>
                </div>
                <a href="{{ $storeUrl }}" class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded font-bold transition-colors text-sm">
                    تصفح المتجر بالكامل <i class="fas fa-arrow-left mr-1"></i>
                </a>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @if(isset($projects) && $projects->count() > 0)
                    @foreach($projects as $project)
                        @php
                            $img = $project->cover_image ?? null;
                            if (empty($img) && !empty($project->cover_image_media_id)) {
                                $pm = $connection->table('media_items')->where('id', $project->cover_image_media_id)->first();
                                if ($pm) $img = $pm->file ?? $pm->path ?? null;
                            }
                            $imgUrl = $img ? \App\Support\Media\MediaUrl::forDiskPath('public', $img) : 'https://via.placeholder.com/600x400?text=مشروع';
                            $projUrl = $project->donation_url ?? ($storeUrl . '/project/' . $project->id);
                        @endphp
                        
                        <div class="bg-white rounded-lg border border-gray-100 shadow-sm hover:shadow-card transition-all card-hover flex flex-col overflow-hidden">
                            <a href="{{ $projUrl }}" class="h-56 relative img-zoom block">
                                <img src="{{ $imgUrl }}" class="w-full h-full object-cover" alt="{{ $project->title }}">
                                @if(!empty($project->category))
                                    <div class="absolute top-4 right-4 bg-secondary text-white px-3 py-1 rounded text-xs font-bold shadow-md">
                                        {{ $project->category }}
                                    </div>
                                @endif
                            </a>
                            <div class="p-6 flex-1 flex flex-col">
                                <h3 class="text-xl font-bold text-primary mb-3 line-clamp-1">
                                    <a href="{{ $projUrl }}">{{ $project->title }}</a>
                                </h3>
                                <p class="text-gray-500 text-sm mb-6 line-clamp-2 leading-relaxed">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($project->description ?? 'مشروع خيري يهدف لخدمة وتنمية المجتمع.'), 100) }}
                                </p>
                                
                                <div class="mt-auto mb-6 bg-gray-50 p-4 rounded border border-gray-100">
                                    <div class="flex justify-between text-xs font-bold mb-2">
                                        <span class="text-gray-600">المبلغ المطلوب</span>
                                        <span class="text-primary">{{ !empty($project->target_amount) ? number_format($project->target_amount) . ' ر.س' : 'مفتوح' }}</span>
                                    </div>
                                    <!-- Progress Bar -->
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 mb-1">
                                        <div class="bg-secondary h-1.5 rounded-full" style="width: 45%"></div>
                                    </div>
                                    <div class="text-left text-xs text-secondary font-bold">45%</div>
                                </div>

                                <a href="{{ $projUrl }}" class="w-full bg-primary hover:bg-primary-dark text-white text-center py-3 rounded font-bold transition-colors flex justify-center items-center gap-2">
                                    <i class="fas fa-shopping-cart"></i> تبرع للمشروع
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-10 bg-gray-50 rounded border border-dashed border-gray-300">
                        <p class="text-gray-500 font-bold">جاري تحديث المشاريع وفرص التبرع...</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section id="news" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-primary inline-block title-line">المركز الإعلامي</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @if(isset($newsItems) && $newsItems->count() > 0)
                    @foreach($newsItems as $item)
                        @php
                            $newsImage = !empty($item->image) ? \App\Support\Media\MediaUrl::forDiskPath('public', $item->image) : 'https://via.placeholder.com/600x400?text=خبر';
                            $newsUrl = !empty($item->slug) ? url('/news/' . $item->slug) : '#';
                            $newsDate = $item->published_at ?: $item->created_at;
                        @endphp
                        <div class="bg-white rounded-lg overflow-hidden shadow-sm border border-gray-100 hover:shadow-card transition-all card-hover flex flex-col">
                            <a href="{{ $newsUrl }}" class="h-48 overflow-hidden relative img-zoom block">
                                <img src="{{ $newsImage }}" class="w-full h-full object-cover" alt="{{ $item->title }}">
                                <div class="absolute bottom-0 right-0 bg-primary text-white px-4 py-2 rounded-tl-lg font-bold text-sm">
                                    {{ $newsDate ? \Carbon\Carbon::parse($newsDate)->format('Y/m/d') : '' }}
                                </div>
                            </a>
                            <div class="p-6 flex-1 flex flex-col">
                                <h3 class="text-lg font-bold text-primary mb-3 leading-snug line-clamp-2 hover:text-secondary transition-colors">
                                    <a href="{{ $newsUrl }}">{{ $item->title }}</a>
                                </h3>
                                <p class="text-gray-500 text-sm line-clamp-3 mt-auto leading-relaxed">
                                    {{ $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($item->content ?? ''), 120) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-10">
                        <p class="text-gray-500 font-bold">لا توجد أخبار منشورة حالياً.</p>
                    </div>
                @endif
            </div>
            
            <div class="text-center mt-10">
                <a href="/page/alakhbar" class="inline-block bg-white border border-primary text-primary hover:bg-primary hover:text-white px-8 py-2.5 rounded font-bold transition-all">
                    المزيد من الأخبار
                </a>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    @if(isset($partners) && $partners->count() > 0)
    <section class="py-12 bg-white border-y border-gray-100">
        <div class="container mx-auto px-4 max-w-7xl">
            <h3 class="text-center text-xl font-bold text-gray-400 mb-8">شركاء النجاح</h3>
            <div class="marquee">
                <div class="marquee-content gap-12 items-center px-4">
                    @foreach($partners as $partner)
                        @php
                            $partnerLogo = !empty($partner->logo) ? \App\Support\Media\MediaUrl::forDiskPath('public', $partner->logo) : null;
                            $partnerUrl = $partner->url ?: '#';
                        @endphp
                        <a href="{{ $partnerUrl }}" target="_blank" class="block shrink-0">
                            @if($partnerLogo)
                                <img src="{{ $partnerLogo }}" class="h-16 opacity-50 hover:opacity-100 grayscale hover:grayscale-0 transition-all object-contain" alt="{{ $partner->name }}">
                            @else
                                <div class="h-16 w-32 bg-gray-50 border border-gray-100 rounded flex items-center justify-center text-gray-400 font-bold text-sm">
                                    {{ $partner->name }}
                                </div>
                            @endif
                        </a>
                    @endforeach
                    <!-- Repeat for seamless scroll -->
                    @foreach($partners as $partner)
                        @php
                            $partnerLogo = !empty($partner->logo) ? \App\Support\Media\MediaUrl::forDiskPath('public', $partner->logo) : null;
                            $partnerUrl = $partner->url ?: '#';
                        @endphp
                        <a href="{{ $partnerUrl }}" target="_blank" class="block shrink-0">
                            @if($partnerLogo)
                                <img src="{{ $partnerLogo }}" class="h-16 opacity-50 hover:opacity-100 grayscale hover:grayscale-0 transition-all object-contain" alt="{{ $partner->name }}">
                            @else
                                <div class="h-16 w-32 bg-gray-50 border border-gray-100 rounded flex items-center justify-center text-gray-400 font-bold text-sm">
                                    {{ $partner->name }}
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer class="bg-primary text-white pt-16 mt-auto border-t-4 border-secondary">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                
                <!-- About -->
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-3 mb-6 bg-white/5 p-3 rounded w-max">
                        @if($finalLogoUrl)
                            <img src="{{ $finalLogoUrl }}" alt="{{ $assocName }}" class="h-12 object-contain bg-white rounded p-1">
                        @else
                            <i class="fas fa-hands-helping text-secondary text-2xl"></i>
                        @endif
                        <h2 class="text-xl font-bold">{{ $assocName }}</h2>
                    </div>
                    <p class="text-gray-300 text-sm leading-relaxed mb-6 text-justify">
                        {{ $desc }}
                    </p>
                    <div class="flex gap-2">
                        <a href="{{ $twitter }}" class="w-9 h-9 rounded bg-white/10 flex items-center justify-center hover:bg-secondary transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="{{ $instagram }}" class="w-9 h-9 rounded bg-white/10 flex items-center justify-center hover:bg-secondary transition-colors"><i class="fab fa-instagram"></i></a>
                        <a href="{{ $youtube }}" class="w-9 h-9 rounded bg-white/10 flex items-center justify-center hover:bg-secondary transition-colors"><i class="fab fa-youtube"></i></a>
                        <a href="{{ $whatsapp }}" class="w-9 h-9 rounded bg-white/10 flex items-center justify-center hover:bg-secondary transition-colors"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="text-lg font-bold mb-6 text-secondary border-r-2 border-secondary pr-2">روابط سريعة</h4>
                    <ul class="space-y-3">
                        <li><a href="#about" class="text-gray-300 hover:text-white transition-colors text-sm flex items-center gap-2"><i class="fas fa-angle-left text-xs text-secondary"></i> من نحن</a></li>
                        <li><a href="{{ $storeUrl }}" class="text-gray-300 hover:text-white transition-colors text-sm flex items-center gap-2"><i class="fas fa-angle-left text-xs text-secondary"></i> المتجر والتبرع</a></li>
                        <li><a href="{{ $beneficiaryPortalUrl }}" class="text-gray-300 hover:text-white transition-colors text-sm flex items-center gap-2"><i class="fas fa-angle-left text-xs text-secondary"></i> بوابة المستفيدين</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors text-sm flex items-center gap-2"><i class="fas fa-angle-left text-xs text-secondary"></i> سياسة الخصوصية</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="text-lg font-bold mb-6 text-secondary border-r-2 border-secondary pr-2">تواصل معنا</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3 text-gray-300 text-sm">
                            <i class="fas fa-map-marker-alt text-secondary mt-1"></i>
                            <span class="leading-relaxed">{{ $address }}</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-300 text-sm">
                            <i class="fas fa-phone text-secondary"></i>
                            <span dir="ltr">{{ $phone }}</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-300 text-sm">
                            <i class="fas fa-envelope text-secondary"></i>
                            <span>{{ $email }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h4 class="text-lg font-bold mb-6 text-secondary border-r-2 border-secondary pr-2">القائمة البريدية</h4>
                    <p class="text-gray-300 text-sm mb-4">اشترك ليصلك جديد برامجنا ومشاريعنا.</p>
                    <form class="flex">
                        <input type="email" placeholder="البريد الإلكتروني" class="w-full bg-white/10 border border-white/20 rounded-r px-4 py-2 text-white focus:outline-none focus:border-secondary text-sm" required>
                        <button type="submit" class="bg-secondary hover:bg-secondary-dark text-white rounded-l px-4 py-2 font-bold text-sm transition-colors">
                            اشتراك
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="bg-primary-dark py-4 border-t border-white/10">
            <div class="container mx-auto px-4 max-w-7xl flex flex-col md:flex-row justify-between items-center gap-2">
                <p class="text-gray-400 text-sm text-center md:text-right">
                    جميع الحقوق محفوظة &copy; {{ date('Y') }} لـ {{ $siteName }}
                </p>
                <p class="text-gray-500 text-xs">
                    تطوير وتشغيل <a href="https://nethamkhaier.com" target="_blank" class="hover:text-white">نظام خير</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        // Header Scroll
        const header = document.getElementById('main-header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                header.classList.add('shadow-md');
            } else {
                header.classList.remove('shadow-md');
            }
        });

        // Mobile Menu
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const closeMenuBtn = document.getElementById('close-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        if(mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', () => {
                mobileMenu.classList.remove('hidden');
                mobileMenu.classList.add('flex');
                document.body.style.overflow = 'hidden';
            });

            closeMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('flex');
                document.body.style.overflow = 'auto';
            });
        }

        // Swiper Initialization
        const swiper = new Swiper('.heroSwiper', {
            loop: true,
            effect: 'fade',
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    </script>
</body>
</html>


```
