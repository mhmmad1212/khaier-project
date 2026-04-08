@php
    $connection = \Illuminate\Support\Facades\DB::connection('tenant');
    
    $settings = $connection->table('site_settings')->orderBy('id', 'desc')->first();
    
    // متغيرات الموقع العامة (مدمجة من الكود الأصلي وملف الإكسل)
    $siteName = $settings->site_name ?? 'أوقاف الخير';
    $assocName = $settings->association_name ?? $siteName;
    $aboutText = $settings->about_text ?? 'مؤسسة وقفية رائدة، نبني جسوراً من الخير لخدمة المجتمع وتنميته.';
    $vision = $settings->vision ?? 'الريادة في العمل الوقفي والتنموي المستدام.';
    $mission = $settings->mission ?? 'تقديم برامج نوعية تلبي احتياجات المجتمع عبر مبادرات مستدامة.';
    $videoUrl = $settings->intro_video_url ?? null;
    $desc = $settings->site_description ?? 'نسعى لتعظيم الأثر المجتمعي من خلال إدارة الأوقاف وتنمية الموارد.';
    $licenseNumber = $settings->license_number ?? '1234';
    
    // معلومات التواصل
    $address = $settings->address ?? 'المملكة العربية السعودية';
    $phone = $settings->phone ?? ($settings->official_phone ?? '+966 92 000 0000');
    $email = $settings->email ?? ($settings->official_email ?? 'info@waqf.org.sa');
    
    // روابط هامة
    $beneficiaryPortalUrl = $settings->beneficiary_portal_url ?? '#';
    $storeUrl = $settings->store_url ?? '#';
    $twitter = $settings->twitter_url ?? '#';
    $instagram = $settings->instagram_url ?? '#';
    $youtube = $settings->youtube_url ?? '#';
    
    // الألوان الديناميكية
    $primary = $settings?->primary_color ?? '#0B4A3F'; // اللون الزيتي الافتراضي
    $secondary = $settings?->secondary_color ?? '#D4AF37'; // اللون الذهبي الافتراضي

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

    // جلب البيانات الأخرى (مشاريع، أخبار، إحصائيات، شركاء، سلايدر)
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
    
    <!-- الشعار في المتصفح -->
    @if($finalLogoUrl)
    <link rel="icon" href="{{ $finalLogoUrl }}" type="image/x-icon">
    @endif

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts (Tajawal) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- إعدادات Tailwind بالألوان الديناميكية من قاعدة البيانات -->
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
                            light: '{{ $primary }}e6', // شفافية 90%
                            dark: '{{ $primary }}',
                        },
                        secondary: {
                            DEFAULT: '{{ $secondary }}',
                            light: '{{ $secondary }}e6',
                            dark: '{{ $secondary }}',
                        }
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(0,0,0,0.08)',
                        'gold': '0 10px 25px -5px {{ $secondary }}66',
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #F8F9FA; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: {{ $secondary }}; border-radius: 4px; }

        .pattern-overlay {
            background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
        }

        .marquee-container { overflow: hidden; white-space: nowrap; width: 100%; position: relative; }
        .marquee-content { display: flex; animation: marquee 30s linear infinite; }
        .marquee-content:hover { animation-play-state: paused; }
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(50%); } 
        }

        .text-gradient {
            background: linear-gradient(to left, {{ $secondary }}, {{ $secondary }}cc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .img-hover-zoom { overflow: hidden; }
        .img-hover-zoom img { transition: transform 0.5s ease; }
        .img-hover-zoom:hover img { transform: scale(1.08); }

        .slide-fade { transition: opacity 1s ease-in-out; }
        .slide-active { opacity: 1; z-index: 10; }
        .slide-inactive { opacity: 0; z-index: 0; pointer-events: none; }
    </style>
</head>
<body class="text-neutral-800 antialiased overflow-x-hidden selection:bg-secondary selection:text-white">

    <!-- Header / Navbar -->
    <header class="fixed w-full top-0 z-50 transition-all duration-300" id="main-header">
        <!-- Top bar -->
        <div class="bg-black/90 text-white/80 py-2 text-sm hidden md:block">
            <div class="container mx-auto px-4 max-w-7xl flex justify-between items-center">
                <div class="flex gap-6">
                    <span class="flex items-center gap-2"><i class="fas fa-envelope text-secondary"></i> {{ $email }}</span>
                    <span class="flex items-center gap-2" dir="ltr"><i class="fas fa-phone text-secondary"></i> {{ $phone }}</span>
                </div>
                <div class="flex gap-4">
                    <a href="{{ $twitter }}" class="hover:text-secondary transition-colors"><i class="fab fa-twitter"></i></a>
                    <a href="{{ $instagram }}" class="hover:text-secondary transition-colors"><i class="fab fa-instagram"></i></a>
                    <a href="{{ $youtube }}" class="hover:text-secondary transition-colors"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>

        <!-- Main Navbar -->
        <div class="bg-white/95 backdrop-blur-md shadow-sm border-b border-gray-100">
            <div class="container mx-auto px-4 max-w-7xl flex justify-between items-center h-20">
                
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center overflow-hidden shadow-lg group-hover:scale-105 transition-transform">
                        @if($finalLogoUrl)
                            <img src="{{ $finalLogoUrl }}" alt="{{ $assocName }}" class="w-full h-full object-contain bg-white">
                        @else
                            <i class="fas fa-kaaba text-secondary text-2xl"></i>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-primary leading-tight">{{ $assocName }}</h1>
                        <p class="text-xs text-neutral-500 font-medium">ترخيص رقم: {{ $licenseNumber }}</p>
                    </div>
                </a>

                <!-- Desktop Nav (Dynamic) -->
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="/" class="font-bold text-primary border-b-2 border-secondary pb-1">الرئيسية</a>
                    @foreach($rootItems as $item)
                        @php
                            $children = $groupedItems->get($item->id) ?? collect();
                            $hasChildren = $children->count() > 0;
                            $url = $hasChildren ? 'javascript:void(0);' : resolveMenuUrl($item, $connection);
                        @endphp
                        <div class="relative group">
                            <a href="{{ $url }}" class="font-bold text-neutral-600 hover:text-primary transition-colors flex items-center gap-1">
                                {{ $item->title }}
                                @if($hasChildren) <i class="fas fa-chevron-down text-xs mt-1"></i> @endif
                            </a>
                            @if($hasChildren)
                                <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-lg shadow-lg hidden group-hover:block opacity-0 group-hover:opacity-100 transition-opacity">
                                    @foreach($children as $subItem)
                                        <a href="{{ resolveMenuUrl($subItem, $connection) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary font-bold border-b border-gray-50 last:border-0">{{ $subItem->title }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </nav>

                <!-- Action Buttons -->
                <div class="hidden lg:flex items-center gap-4">
                    <a href="{{ $beneficiaryPortalUrl }}" class="text-primary font-bold hover:text-secondary transition-colors flex items-center gap-2">
                        <i class="far fa-user-circle text-lg"></i> دخول المستفيدين
                    </a>
                    <a href="{{ $storeUrl }}" class="bg-secondary hover:bg-secondary-dark text-white px-6 py-2.5 rounded-lg font-bold shadow-gold transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                        <i class="fas fa-hand-holding-heart"></i> المتجر الخيري
                    </a>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="lg:hidden text-primary text-2xl focus:outline-none" id="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="fixed inset-0 bg-primary/95 backdrop-blur-sm z-40 hidden flex-col items-center justify-center gap-6" id="mobile-menu">
        <button class="absolute top-6 left-6 text-white text-3xl" id="close-menu-btn">
            <i class="fas fa-times"></i>
        </button>
        <a href="/" class="text-2xl font-bold text-white hover:text-secondary">الرئيسية</a>
        @foreach($rootItems as $item)
            <a href="{{ resolveMenuUrl($item, $connection) }}" class="text-2xl font-bold text-white hover:text-secondary">{{ $item->title }}</a>
        @endforeach
        <a href="{{ $storeUrl }}" class="mt-4 bg-secondary text-white px-8 py-3 rounded-xl font-bold text-xl shadow-gold">المتجر الخيري</a>
        <a href="{{ $beneficiaryPortalUrl }}" class="mt-2 text-white font-bold text-lg underline decoration-secondary">بوابة المستفيدين</a>
    </div>

    <!-- Hero Section (Dynamic Slider) -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden bg-primary h-[80vh] md:h-auto flex items-center">
        <!-- Background Sliders -->
        <div id="hero-sliders" class="absolute inset-0 z-0">
            @if(isset($sliders) && $sliders->count() > 0)
                @foreach($sliders as $index => $slider)
                    <div class="hero-slide slide-fade absolute inset-0 w-full h-full {{ $index == 0 ? 'slide-active' : 'slide-inactive' }}">
                        <img src="{{ \App\Support\Media\MediaUrl::forDiskPath('public', $slider->image) }}" class="w-full h-full object-cover opacity-30" alt="Slide">
                    </div>
                @endforeach
            @else
                <div class="absolute inset-0 w-full h-full">
                    <img src="https://images.unsplash.com/photo-1591462215264-754668df41df?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover opacity-20" alt="Islamic Background">
                </div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-primary via-primary/80 to-transparent"></div>
            <div class="absolute inset-0 pattern-overlay opacity-30"></div>
        </div>

        <div class="container mx-auto px-4 max-w-7xl relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                
                <!-- Hero Content -->
                <div class="text-white text-center lg:text-right">
                    <span class="inline-block py-1 px-4 rounded-full bg-secondary/20 border border-secondary/50 text-secondary font-bold text-sm mb-6">
                        <i class="fas fa-star mr-1"></i> جهة معتمدة بترخيص رقم {{ $licenseNumber }}
                    </span>
                    <h1 class="text-4xl lg:text-6xl font-black leading-tight mb-6 drop-shadow-lg">
                        صدقتك الجارية.. <br>
                        <span class="text-gradient">أثرٌ يمتد عبر الأجيال</span>
                    </h1>
                    <p class="text-lg lg:text-xl text-neutral-200 mb-10 leading-relaxed max-w-xl mx-auto lg:mx-0 drop-shadow-md">
                        {{ $desc }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ $storeUrl }}" class="bg-secondary hover:bg-secondary-dark text-white px-8 py-4 rounded-xl font-bold text-lg shadow-gold transition-all transform hover:-translate-y-1">
                            تصفح المشاريع للتبرع
                        </a>
                        <a href="#about" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/30 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all">
                            تعرف علينا أكثر
                        </a>
                    </div>
                </div>

                <!-- Quick Donate Box -->
                <div class="bg-white rounded-3xl p-8 shadow-2xl relative hidden md:block">
                    <div class="absolute -top-6 -right-6 w-20 h-20 bg-secondary rounded-full flex items-center justify-center text-white text-3xl shadow-lg">
                        <i class="fas fa-donate"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-primary mb-2">تبرع سريع</h3>
                    <p class="text-neutral-500 text-sm mb-6">اختر المشروع والمبلغ لتوثيق عطائك</p>

                    <form class="space-y-5" action="{{ $storeUrl }}" method="GET">
                        <div>
                            <label class="block text-sm font-bold text-neutral-700 mb-2">اختر المشروع</label>
                            <select class="w-full bg-neutral-50 border border-neutral-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent font-medium">
                                @foreach($projects->take(4) as $proj)
                                    <option value="{{ $proj->id }}">{{ $proj->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-neutral-700 mb-2">مبلغ التبرع (ر.س)</label>
                            <div class="grid grid-cols-3 gap-2 mb-3">
                                <button type="button" class="amount-btn bg-primary text-white border-2 border-primary rounded-lg py-2 font-bold transition-colors">100</button>
                                <button type="button" class="amount-btn bg-white text-primary border-2 border-neutral-200 rounded-lg py-2 font-bold hover:border-primary transition-colors">250</button>
                                <button type="button" class="amount-btn bg-white text-primary border-2 border-neutral-200 rounded-lg py-2 font-bold hover:border-primary transition-colors">500</button>
                            </div>
                            <input type="number" placeholder="أو أدخل مبلغاً آخر" class="w-full bg-neutral-50 border border-neutral-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent">
                        </div>

                        <button type="submit" class="w-full bg-primary hover:bg-primary-light text-white font-bold text-lg py-4 rounded-xl transition-all shadow-md mt-4">
                            متابعة الدفع
                        </button>
                        
                        <div class="flex items-center justify-center gap-3 text-neutral-400 text-sm mt-4">
                            <i class="fab fa-cc-visa text-2xl"></i>
                            <i class="fab fa-cc-mastercard text-2xl"></i>
                            <i class="fab fa-apple text-2xl text-black"></i>
                            <i class="fas fa-shield-alt text-lg text-green-500"></i> دفع آمن
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- Stats Section (Dynamic) -->
    @if(isset($statistics) && $statistics->count() > 0)
    <section class="relative z-20 -mt-10 mb-16">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="bg-white rounded-2xl shadow-soft border border-neutral-100 p-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 divide-x divide-x-reverse divide-neutral-100">
                    @foreach($statistics as $stat)
                    <div class="text-center px-4 flex flex-col items-center">
                        <div class="w-12 h-12 mx-auto bg-primary/10 text-primary rounded-full flex items-center justify-center text-xl mb-4">
                            @if($stat->icon && str_contains($stat->icon, 'heroicon'))
                                <!-- Placeholder if you use filament icons -->
                                <i class="fas fa-chart-line"></i>
                            @else
                                <i class="{{ $stat->icon ?? 'fas fa-chart-bar' }}"></i>
                            @endif
                        </div>
                        <h4 class="text-3xl font-black text-primary mb-1">{{ $stat->value }}</h4>
                        <p class="text-sm font-bold text-neutral-500">{{ $stat->title }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- About Section -->
    <section id="about" class="py-16 bg-neutral-50 relative overflow-hidden">
        <div class="absolute -left-20 top-20 w-64 h-64 bg-secondary/10 rounded-full blur-3xl"></div>
        <div class="container mx-auto px-4 max-w-7xl relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                
                <div class="relative">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl img-hover-zoom border-4 border-white h-[400px]">
                        @if($videoUrl)
                            <img src="https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?q=80&w=2076&auto=format&fit=crop" class="w-full h-full object-cover" alt="عن الجمعية">
                            <a href="{{ $videoUrl }}" target="_blank" class="absolute inset-0 bg-primary/30 flex items-center justify-center group">
                                <button class="w-20 h-20 bg-white/90 rounded-full flex items-center justify-center text-primary text-2xl shadow-lg group-hover:scale-110 group-hover:bg-secondary transition-all cursor-pointer pl-1">
                                    <i class="fas fa-play"></i>
                                </button>
                            </a>
                        @else
                            <img src="https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?q=80&w=2076&auto=format&fit=crop" class="w-full h-full object-cover" alt="عن الجمعية">
                        @endif
                    </div>
                    <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-secondary rounded-3xl -z-10"></div>
                </div>

                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-10 h-1 bg-secondary rounded-full"></span>
                        <h3 class="text-secondary font-bold text-lg">من نحن</h3>
                    </div>
                    <h2 class="text-3xl lg:text-4xl font-black text-primary mb-6 leading-snug">
                        {{ $assocName }}
                    </h2>
                    <div class="text-neutral-600 text-lg leading-relaxed mb-8 text-justify prose prose-emerald max-w-none">
                        {!! $aboutText !!}
                    </div>
                    
                    <div class="grid sm:grid-cols-2 gap-6 mb-8">
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-neutral-100 flex gap-4 items-start">
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary text-xl shrink-0">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-primary mb-1">رؤيتنا</h4>
                                <p class="text-sm text-neutral-500 leading-relaxed">{{ $vision }}</p>
                            </div>
                        </div>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-neutral-100 flex gap-4 items-start">
                            <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center text-secondary text-xl shrink-0">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-primary mb-1">رسالتنا</h4>
                                <p class="text-sm text-neutral-500 leading-relaxed">{{ $mission }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Projects Section (Dynamic) -->
    <section id="projects" class="py-20 bg-white">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-8 h-1 bg-secondary rounded-full"></span>
                        <h3 class="text-secondary font-bold">فرص الأجر</h3>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black text-primary">المشاريع والمبادرات</h2>
                </div>
                <a href="{{ $storeUrl }}" class="bg-neutral-100 hover:bg-neutral-200 text-primary px-6 py-2.5 rounded-lg font-bold transition-colors">
                    عرض جميع المشاريع في المتجر
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
                            $imgUrl = $img ? \App\Support\Media\MediaUrl::forDiskPath('public', $img) : 'https://via.placeholder.com/600x400?text=مشروع+خيري';
                            $projUrl = $project->donation_url ?? ($storeUrl . '/project/' . $project->id);
                        @endphp
                        <div class="bg-white rounded-3xl border border-neutral-100 shadow-soft overflow-hidden group flex flex-col">
                            <a href="{{ $projUrl }}" class="h-56 relative img-hover-zoom block">
                                <img src="{{ $imgUrl }}" class="w-full h-full object-cover" alt="{{ $project->title }}">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </a>
                            <div class="p-6 flex-1 flex flex-col">
                                <h3 class="text-xl font-bold text-primary mb-3 line-clamp-1 group-hover:text-secondary transition-colors">
                                    <a href="{{ $projUrl }}">{{ $project->title }}</a>
                                </h3>
                                <p class="text-neutral-500 text-sm mb-6 line-clamp-2 leading-relaxed">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($project->description ?? 'ساهم معنا في دعم هذا المشروع لخدمة وتنمية المجتمع.'), 90) }}
                                </p>
                                
                                <div class="mb-6 mt-auto">
                                    <div class="flex justify-between text-xs font-bold mb-2">
                                        <span class="text-primary">مستهدف التبرع</span>
                                        <span class="text-neutral-500">{{ !empty($project->target_amount) ? number_format($project->target_amount) . ' ر.س' : 'مفتوح' }}</span>
                                    </div>
                                    <div class="w-full bg-neutral-100 rounded-full h-2">
                                        <div class="bg-secondary h-2 rounded-full" style="width: 50%"></div>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <a href="{{ $projUrl }}" class="flex-1 bg-primary hover:bg-primary-light text-white text-center py-3 rounded-xl font-bold transition-colors flex justify-center items-center gap-2">
                                        <i class="fas fa-hand-holding-heart"></i> تبرع الآن
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-10 text-neutral-400 font-bold text-lg">
                        لا توجد مشاريع مضافة حالياً.
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- News Section (Dynamic) -->
    <section id="news" class="py-20 bg-neutral-50 border-t border-neutral-100">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="text-center mb-12">
                <div class="flex items-center justify-center gap-3 mb-2">
                    <span class="w-8 h-1 bg-secondary rounded-full"></span>
                    <h3 class="text-secondary font-bold">المركز الإعلامي</h3>
                    <span class="w-8 h-1 bg-secondary rounded-full"></span>
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-primary">أحدث الأخبار والإصدارات</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @if(isset($newsItems) && $newsItems->count() > 0)
                    @foreach($newsItems as $item)
                        @php
                            $newsImage = !empty($item->image) ? \App\Support\Media\MediaUrl::forDiskPath('public', $item->image) : 'https://via.placeholder.com/600x400?text=خبر';
                            $newsUrl = !empty($item->slug) ? url('/news/' . $item->slug) : '#';
                            $newsDate = $item->published_at ?: $item->created_at;
                        @endphp
                        <a href="{{ $newsUrl }}" class="bg-white rounded-2xl overflow-hidden shadow-sm border border-neutral-100 hover:shadow-lg transition-shadow group flex flex-col">
                            <div class="h-48 overflow-hidden relative">
                                <img src="{{ $newsImage }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $item->title }}">
                            </div>
                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex items-center gap-2 text-xs text-neutral-400 font-bold mb-3">
                                    <i class="far fa-calendar-alt text-secondary"></i> 
                                    {{ $newsDate ? \Carbon\Carbon::parse($newsDate)->format('Y-m-d') : '-' }}
                                </div>
                                <h3 class="text-lg font-bold text-primary mb-3 leading-snug group-hover:text-secondary transition-colors line-clamp-2">
                                    {{ $item->title }}
                                </h3>
                                <p class="text-neutral-500 text-sm line-clamp-2 mt-auto">
                                    {{ $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($item->content ?? ''), 90) }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-10 text-neutral-400 font-bold text-lg">
                        لا توجد أخبار مضافة حالياً.
                    </div>
                @endif
            </div>
            
            <div class="text-center mt-10">
                <a href="/page/alakhbar" class="inline-block border-2 border-primary text-primary hover:bg-primary hover:text-white px-8 py-3 rounded-xl font-bold transition-all">
                    تصفح جميع الأخبار
                </a>
            </div>
        </div>
    </section>

    <!-- Partners Section (Dynamic) -->
    @if(isset($partners) && $partners->count() > 0)
    <section class="py-16 bg-white border-y border-neutral-100">
        <div class="container mx-auto px-4 max-w-7xl text-center mb-8">
            <h3 class="text-xl font-bold text-neutral-400">شركاء النجاح والعطاء</h3>
        </div>
        <div class="marquee-container">
            <!-- نكرر المحتوى مرتين لجعل الحركة مستمرة (Infinite scroll) -->
            <div class="marquee-content flex gap-12 items-center px-6">
                @foreach($partners as $partner)
                    @php
                        $partnerLogo = !empty($partner->logo) ? \App\Support\Media\MediaUrl::forDiskPath('public', $partner->logo) : null;
                        $partnerUrl = $partner->url ?: '#';
                    @endphp
                    <a href="{{ $partnerUrl }}" target="_blank" class="block shrink-0">
                        @if($partnerLogo)
                            <img src="{{ $partnerLogo }}" class="h-16 opacity-60 hover:opacity-100 grayscale hover:grayscale-0 transition-all object-contain" alt="{{ $partner->name }}">
                        @else
                            <div class="h-16 w-32 bg-neutral-50 border border-neutral-100 rounded-lg flex items-center justify-center text-neutral-500 font-bold hover:bg-primary hover:text-white transition-colors">
                                {{ $partner->name }}
                            </div>
                        @endif
                    </a>
                @endforeach
                <!-- التكرار -->
                @foreach($partners as $partner)
                    @php
                        $partnerLogo = !empty($partner->logo) ? \App\Support\Media\MediaUrl::forDiskPath('public', $partner->logo) : null;
                        $partnerUrl = $partner->url ?: '#';
                    @endphp
                    <a href="{{ $partnerUrl }}" target="_blank" class="block shrink-0">
                        @if($partnerLogo)
                            <img src="{{ $partnerLogo }}" class="h-16 opacity-60 hover:opacity-100 grayscale hover:grayscale-0 transition-all object-contain" alt="{{ $partner->name }}">
                        @else
                            <div class="h-16 w-32 bg-neutral-50 border border-neutral-100 rounded-lg flex items-center justify-center text-neutral-500 font-bold hover:bg-primary hover:text-white transition-colors">
                                {{ $partner->name }}
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Footer -->
    <footer class="bg-black/95 text-white pt-20 border-t-4 border-secondary relative overflow-hidden">
        <div class="absolute inset-0 bg-primary opacity-20 mix-blend-multiply"></div>
        <div class="absolute inset-0 pattern-overlay opacity-5"></div>
        <div class="container mx-auto px-4 max-w-7xl relative z-10">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                
                <!-- About -->
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        @if($finalLogoUrl)
                            <img src="{{ $finalLogoUrl }}" alt="{{ $assocName }}" class="h-14 bg-white rounded-lg p-1">
                        @else
                            <div class="w-10 h-10 bg-secondary rounded-lg flex items-center justify-center text-primary text-xl">
                                <i class="fas fa-kaaba"></i>
                            </div>
                            <h2 class="text-2xl font-black text-white">{{ $assocName }}</h2>
                        @endif
                    </div>
                    <p class="text-neutral-400 text-sm leading-loose mb-6">
                        {{ $desc }}
                    </p>
                    <div class="flex gap-3">
                        <a href="{{ $twitter }}" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-secondary hover:text-primary transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="{{ $instagram }}" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-secondary hover:text-primary transition-colors"><i class="fab fa-instagram"></i></a>
                        <a href="{{ $youtube }}" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-secondary hover:text-primary transition-colors"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="text-lg font-bold text-white mb-6 border-r-4 border-secondary pr-3">روابط هامة</h4>
                    <ul class="space-y-3">
                        <li><a href="#about" class="text-neutral-400 hover:text-secondary transition-colors text-sm flex items-center gap-2"><i class="fas fa-chevron-left text-xs"></i> من نحن</a></li>
                        <li><a href="{{ $storeUrl }}" class="text-neutral-400 hover:text-secondary transition-colors text-sm flex items-center gap-2"><i class="fas fa-chevron-left text-xs"></i> مشاريع الوقف</a></li>
                        <li><a href="{{ $beneficiaryPortalUrl }}" class="text-neutral-400 hover:text-secondary transition-colors text-sm flex items-center gap-2"><i class="fas fa-chevron-left text-xs"></i> بوابة المستفيدين</a></li>
                        <li><a href="#" class="text-neutral-400 hover:text-secondary transition-colors text-sm flex items-center gap-2"><i class="fas fa-chevron-left text-xs"></i> الحوكمة والشفافية</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="text-lg font-bold text-white mb-6 border-r-4 border-secondary pr-3">تواصل معنا</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3 text-neutral-400 text-sm">
                            <i class="fas fa-map-marker-alt text-secondary mt-1"></i>
                            <span class="leading-relaxed">{{ $address }}</span>
                        </li>
                        <li class="flex items-center gap-3 text-neutral-400 text-sm">
                            <i class="fas fa-phone text-secondary"></i>
                            <span dir="ltr">{{ $phone }}</span>
                        </li>
                        <li class="flex items-center gap-3 text-neutral-400 text-sm">
                            <i class="fas fa-envelope text-secondary"></i>
                            <span>{{ $email }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h4 class="text-lg font-bold text-white mb-6 border-r-4 border-secondary pr-3">النشرة البريدية</h4>
                    <p class="text-neutral-400 text-sm mb-4">اشترك ليصلك جديد مشاريعنا وأخبار الوقف.</p>
                    <form class="flex flex-col gap-3">
                        <input type="email" placeholder="البريد الإلكتروني" class="bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-secondary text-sm text-left" dir="ltr">
                        <button type="button" class="bg-secondary hover:bg-secondary-dark text-white rounded-lg px-4 py-3 font-bold text-sm transition-colors text-primary">
                            اشتراك
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="bg-black py-6">
            <div class="container mx-auto px-4 max-w-7xl flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-neutral-500 text-sm text-center md:text-right">
                    &copy; {{ date('Y') }} جميع الحقوق محفوظة لـ {{ $siteName }}.
                </p>
                <div class="flex gap-4 text-sm text-neutral-500">
                    <a href="#" class="hover:text-white transition-colors">سياسة الخصوصية</a>
                    <span>|</span>
                    <a href="#" class="hover:text-white transition-colors">شروط الاستخدام</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Header Scroll Effect
        const header = document.getElementById('main-header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('shadow-md');
            } else {
                header.classList.remove('shadow-md');
            }
        });

        // Mobile Menu Toggle
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

        // Quick Donate Button Toggle
        const amountBtns = document.querySelectorAll('.amount-btn');
        amountBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                amountBtns.forEach(b => {
                    b.classList.remove('bg-primary', 'text-white', 'border-primary');
                    b.classList.add('bg-white', 'text-primary', 'border-neutral-200');
                });
                this.classList.remove('bg-white', 'text-primary', 'border-neutral-200');
                this.classList.add('bg-primary', 'text-white', 'border-primary');
            });
        });

        // Hero Slider Logic (Auto Slide)
        const slides = document.querySelectorAll('.hero-slide');
        if (slides.length > 1) {
            let currentSlide = 0;
            setInterval(() => {
                slides[currentSlide].classList.remove('slide-active');
                slides[currentSlide].classList.add('slide-inactive');
                
                currentSlide = (currentSlide + 1) % slides.length;
                
                slides[currentSlide].classList.remove('slide-inactive');
                slides[currentSlide].classList.add('slide-active');
            }, 6000);
        }
    </script>
</body>
</html>