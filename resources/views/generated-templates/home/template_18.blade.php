@php
    // جلب البيانات بالقوة الجبرية من قاعدة بيانات الجمعية
    $connection = \Illuminate\Support\Facades\DB::connection('tenant');
    
    // 1. جلب الإعدادات العامة
    $settings = $connection->table('site_settings')->orderBy('id', 'desc')->first();
    
    $aboutText = $settings->about_text ?? 'جمعية خيرية تعمل على خدمة المجتمع وتقديم الدعم للمحتاجين في مختلف المجالات.';
    $videoUrl = $settings->intro_video_url ?? null;
    $siteName = $settings->site_name ?? 'جمعية الخير';
    $assocName = $settings->association_name ?? $siteName;
    
    $address = $settings->address ?? 'المملكة العربية السعودية';
    $phone = $settings->phone ?? ($settings->official_phone ?? '+966 50 123 4567');
    $email = $settings->email ?? ($settings->official_email ?? 'info@charity.org');
    $desc = $settings->site_description ?? 'نعمل بشفافية عالية لتحقيق أهدافنا التنموية لخدمة المجتمع.';

    // --- معالجة الشعار الذكية ---
    $logoPath = $settings->logo ?? null;
    if (empty($logoPath) && !empty($settings->logo_media_id)) {
        $media = $connection->table('media_items')->where('id', $settings->logo_media_id)->first();
        if ($media) {
            $logoPath = $media->file ?? $media->path ?? null;
        }
    }
    
    $finalLogoUrl = null;
    if (!empty($logoPath)) {
        $finalLogoUrl = str_starts_with($logoPath, 'http') ? $logoPath : asset('storage/' . $logoPath);
    }
    // ----------------------------

    // 2. جلب القائمة الرئيسية بجميع مستوياتها
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

    // 3. جلب البرامج والمشاريع 
    $projects = collect();
    try {
        $projects = $connection->table('program_projects')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'asc')
            ->limit(4)
            ->get();
    } catch (\Exception $e) {}

    // 4. جلب الإحصائيات
    $statistics = collect();
    try {
        $statistics = $connection->table('statistics')->orderBy('sort_order', 'asc')->limit(4)->get();
    } catch (\Exception $e) {}

    // 5. جلب السلايدر
    $sliders = collect();
    try {
        $sliders = $connection->table('sliders')->where('is_active', 1)->orderBy('sort_order', 'asc')->get();
    } catch (\Exception $e) {}

    // دالة مساعدة لتجهيز الروابط
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
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
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
        body { font-family: 'Cairo', sans-serif; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #10b981; border-radius: 4px; }
        
        .slider-fade { transition: opacity 1s ease-in-out; }
        .slide-active { opacity: 1; z-index: 10; }
        .slide-inactive { opacity: 0; z-index: 0; pointer-events: none;}
        
        .group:hover .group-hover\:block { display: block; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased overflow-x-hidden">

    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <a href="/" class="flex-shrink-0 flex items-center gap-2">
                    @if($finalLogoUrl)
                        <img src="{{ $finalLogoUrl }}" alt="{{ $siteName }}" class="h-12 w-auto object-contain">
                    @else
                        <div class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-xl">🤍</div>
                        <div class="flex flex-col">
                            <span class="font-bold text-xl text-slate-900 leading-tight">{{ $siteName }}</span>
                            <span class="text-xs text-emerald-600 font-medium">نعمل معاً من أجل مجتمع أفضل</span>
                        </div>
                    @endif
                </a>
                
                <nav class="hidden md:flex items-center space-x-8 space-x-reverse">
                    <ul class="flex space-x-8 space-x-reverse m-0 p-0">
                        @if($rootItems->count() > 0)
                            @foreach($rootItems as $item)
                                @php
                                    $hasChildren = $groupedItems->has($item->id);
                                    $url = $hasChildren ? 'javascript:void(0);' : resolveMenuUrl($item, $connection);
                                @endphp
                                <li class="relative group py-6">
                                    <a href="{{ $url }}" class="text-gray-700 hover:text-emerald-600 transition-colors font-medium flex items-center gap-1">
                                        {{ $item->title }}
                                        @if($hasChildren)
                                            <i class="fas fa-chevron-down text-xs ml-1"></i>
                                        @endif
                                    </a>
                                    @if($hasChildren)
                                        <ul class="absolute top-full right-0 mt-0 w-48 bg-white rounded-xl shadow-lg border border-gray-100 hidden group-hover:block overflow-hidden z-50">
                                            @foreach($groupedItems->get($item->id) as $subItem)
                                                @php $subUrl = resolveMenuUrl($subItem, $connection); @endphp
                                                <li>
                                                    <a href="{{ $subUrl }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 border-b border-gray-50 last:border-0 transition-colors">
                                                        {{ $subItem->title }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        @else
                            <a href="#home" class="text-gray-700 hover:text-emerald-600 transition-colors font-medium">الرئيسية</a>
                            <a href="#about" class="text-gray-700 hover:text-emerald-600 transition-colors font-medium">عن الجمعية</a>
                            <a href="#projects" class="text-gray-700 hover:text-emerald-600 transition-colors font-medium">المشاريع</a>
                        @endif
                    </ul>
                </nav>

                <div class="flex items-center gap-4">
                    <a href="{{ $settings->beneficiary_portal_url ?? '#donate' }}" class="hidden md:block bg-gradient-to-r from-emerald-600 to-emerald-700 text-white px-8 py-3 rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-bold">
                        تبرع الآن
                    </a>
                    
                    <button id="mobileMenuBtn" class="md:hidden text-gray-700 hover:text-emerald-600 focus:outline-none p-2">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-100 shadow-lg absolute w-full left-0 top-full">
            <ul class="flex flex-col px-4 py-2">
                @if($rootItems->count() > 0)
                    @foreach($rootItems as $item)
                        @php
                            $hasChildren = $groupedItems->has($item->id);
                            $url = $hasChildren ? 'javascript:void(0);' : resolveMenuUrl($item, $connection);
                        @endphp
                        <li class="border-b border-gray-50 last:border-0">
                            <a href="{{ $url }}" class="block py-3 text-gray-700 font-medium hover:text-emerald-600 flex justify-between items-center {{ $hasChildren ? 'mobile-toggle' : '' }}">
                                {{ $item->title }}
                                @if($hasChildren) <i class="fas fa-chevron-down text-xs"></i> @endif
                            </a>
                            @if($hasChildren)
                                <ul class="hidden bg-slate-50 rounded-lg mb-2 overflow-hidden mobile-submenu">
                                    @foreach($groupedItems->get($item->id) as $subItem)
                                        @php $subUrl = resolveMenuUrl($subItem, $connection); @endphp
                                        <li><a href="{{ $subUrl }}" class="block py-2 px-4 text-sm text-gray-600 hover:text-emerald-600 hover:bg-emerald-100/50">{{ $subItem->title }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                @else
                    <li><a href="#home" class="block py-3 text-gray-700 font-medium">الرئيسية</a></li>
                    <li><a href="#about" class="block py-3 text-gray-700 font-medium">عن الجمعية</a></li>
                    <li><a href="#projects" class="block py-3 text-gray-700 font-medium">المشاريع</a></li>
                @endif
                <li class="py-4">
                    <a href="{{ $settings->beneficiary_portal_url ?? '#donate' }}" class="block text-center bg-emerald-600 text-white py-3 rounded-xl font-bold">تبرع الآن</a>
                </li>
            </ul>
        </div>
    </header>

    <section id="home" class="relative bg-emerald-800 h-[500px] md:h-[600px] overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgweiIgZmlsbD0ibm9uZSIvPjxwYXRoIGQ9Ik0wIDEwaDQwdjJIMHoiIGZpbGw9IiNmZmYiLz48L3N2Zz4=')] z-0"></div>
        
        <div id="slider-container" class="relative w-full h-full">
            @if(isset($sliders) && $sliders->count() > 0)
                @foreach($sliders as $index => $slider)
                    <div class="slide slider-fade absolute inset-0 w-full h-full {{ $index == 0 ? 'slide-active' : 'slide-inactive' }}">
                        <img src="{{ asset('storage/' . $slider->image) }}" class="absolute inset-0 w-full h-full object-cover" alt="Slide">
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
            @else
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4 text-2xl">👥</div>
                    <h3 class="text-3xl font-extrabold text-gray-900 mb-1">50,000+</h3>
                    <p class="text-gray-500 font-medium">مستفيد</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-4 text-2xl">❤️</div>
                    <h3 class="text-3xl font-extrabold text-gray-900 mb-1">15,000+</h3>
                    <p class="text-gray-500 font-medium">متبرع</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mb-4 text-2xl">🏆</div>
                    <h3 class="text-3xl font-extrabold text-gray-900 mb-1">200+</h3>
                    <p class="text-gray-500 font-medium">مشروع خيري</p>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-4 text-2xl">🌍</div>
                    <h3 class="text-3xl font-extrabold text-gray-900 mb-1">30+</h3>
                    <p class="text-gray-500 font-medium">مبادرة</p>
                </div>
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
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">الحوكمة والشفافية</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">نلتزم بأعلى معايير الحوكمة. نؤمن بأهمية الشفافية والمساءلة في كل ما نقوم به، ونعمل وفق أفضل الممارسات العالمية في مجال العمل الخيري</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                
                <div class="p-6 bg-slate-50 rounded-2xl text-center group hover:bg-emerald-50 hover:border-emerald-100 transition-all border border-transparent">
                    <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center text-emerald-600 shadow-sm mb-6 group-hover:scale-110 group-hover:shadow-md transition-all duration-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg mb-2">الشفافية المالية</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">نشر التقارير المالية السنوية والمراجعة الخارجية لضمان أعلى معايير الشفافية</p>
                </div>

                <div class="p-6 bg-slate-50 rounded-2xl text-center group hover:bg-emerald-50 hover:border-emerald-100 transition-all border border-transparent">
                    <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center text-emerald-600 shadow-sm mb-6 group-hover:scale-110 group-hover:shadow-md transition-all duration-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg mb-2">الامتثال والرقابة</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">الالتزام الكامل بالأنظمة واللوائح المحلية والدولية للعمل الخيري</p>
                </div>

                <div class="p-6 bg-slate-50 rounded-2xl text-center group hover:bg-emerald-50 hover:border-emerald-100 transition-all border border-transparent">
                    <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center text-emerald-600 shadow-sm mb-6 group-hover:scale-110 group-hover:shadow-md transition-all duration-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg mb-2">مجلس الإدارة</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">مجلس إدارة مستقل من الخبراء والمتخصصين في المجال الخيري</p>
                </div>

                <div class="p-6 bg-slate-50 rounded-2xl text-center group hover:bg-emerald-50 hover:border-emerald-100 transition-all border border-transparent">
                    <div class="w-20 h-20 mx-auto bg-white rounded-full flex items-center justify-center text-emerald-600 shadow-sm mb-6 group-hover:scale-110 group-hover:shadow-md transition-all duration-300">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg mb-2">قياس الأثر</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">تقييم مستمر لأثر المشاريع وتطوير الأداء بناءً على النتائج</p>
                </div>

            </div>
        </div>
    </section>

    <section id="projects" class="py-20 bg-slate-50">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-16">
                <span class="text-emerald-600 font-bold tracking-wider uppercase text-sm mb-2 block">مشاريعنا</span>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">فرص التبرع والمشاريع</h2>
                <p class="text-gray-600">نعمل على مجموعة متنوعة من المشاريع الخيرية لخدمة المجتمع وتحسين حياة المحتاجين</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @if(isset($projects) && $projects->count() > 0)
                    @foreach($projects as $project)
                        @php
                            $title = $project->title ?? $project->name ?? 'مشروع خيري';
                            $img = $project->cover_image ?? null;
                            if (empty($img) && !empty($project->cover_image_media_id)) {
                                $pm = $connection->table('media_items')->where('id', $project->cover_image_media_id)->first();
                                if ($pm) $img = $pm->file ?? $pm->path ?? null;
                            }
                            $imgUrl = $img ? asset('storage/' . $img) : 'https://via.placeholder.com/400x250?text=مشروع+خيري';
                            
                            $target = $project->project_amount ?? $project->target_amount ?? 1;
                            $collected = $project->donation_amount ?? $project->collected_amount ?? 0;
                            $percent = ($target > 0) ? min(100, round(($collected / $target) * 100)) : 0;
                        @endphp
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow group">
                            <div class="h-48 relative overflow-hidden bg-slate-100">
                                <img src="{{ $imgUrl }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $title }}">
                            </div>
                            <div class="p-6 flex-grow flex flex-col">
                                <h3 class="text-xl font-bold text-gray-900 mb-2 leading-snug">{{ Str::limit($title, 50) }}</h3>
                                <p class="text-gray-600 text-sm mb-5 line-clamp-2">
                                    {{ Str::limit(strip_tags($project->description ?? $project->short_description ?? 'مشروع خيري يهدف لخدمة المجتمع ودعم الفئات المستحقة.'), 80) }}
                                </p>
                                <div class="mt-auto mb-5 bg-slate-50 p-4 rounded-xl">
                                    <div class="flex justify-between text-sm font-bold mb-2">
                                        <span class="text-emerald-600">{{ $percent }}% مكتمل</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                                        <div class="bg-emerald-500 h-2 rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <div class="flex justify-between text-xs font-semibold text-gray-500">
                                        <span>المُجمّع: {{ number_format($collected) }} ر.س</span>
                                        <span>الهدف: {{ number_format($target == 1 && $collected == 0 ? 0 : $target) }} ر.س</span>
                                    </div>
                                </div>
                                <a href="{{ $project->donation_url ?? '/projects/'.($project->id ?? '#') }}" class="block w-full text-center bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold py-3 rounded-xl hover:bg-emerald-600 hover:text-white transition-colors">
                                    تبرع الآن
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="h-48 bg-blue-50 flex items-center justify-center text-5xl">📚</div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">التعليم للجميع</h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">توفير فرص التعليم للأطفال المحرومين من خلال المنح الدراسية والأدوات التعليمية</p>
                            <div class="mb-4">
                                <div class="flex justify-between text-sm font-semibold mb-1">
                                    <span class="text-blue-600">70%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 70%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 mt-2">
                                    <span>تم جمع: 350,000 ريال</span>
                                    <span>الهدف: 500,000 ريال</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-1 md:col-span-3 flex items-center justify-center text-gray-500">قم بإضافة مشاريع من لوحة التحكم لتظهر هنا</div>
                @endif
            </div>
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

                    <button type="button" class="w-full bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-bold text-xl py-4 rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex justify-center items-center gap-2">
                        <i class="fas fa-heart"></i>
                        إتمام التبرع جزاك الله خيراً
                    </button>
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
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-300 hover:bg-emerald-600 hover:text-white transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-300 hover:bg-emerald-600 hover:text-white transition-colors"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-300 hover:bg-emerald-600 hover:text-white transition-colors"><i class="fab fa-youtube"></i></a>
                    </div>
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
                        معلومات هامة
                        <span class="absolute bottom-0 right-0 w-12 h-1 bg-emerald-500 rounded-full"></span>
                    </h4>
                    <ul class="space-y-3 text-slate-400">
                        <li><a href="#" class="hover:text-emerald-400 transition-colors flex items-center gap-2"><i class="fas fa-angle-left text-xs"></i> سياسة الخصوصية</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition-colors flex items-center gap-2"><i class="fas fa-angle-left text-xs"></i> الشروط والأحكام</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition-colors flex items-center gap-2"><i class="fas fa-angle-left text-xs"></i> التقارير المالية</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition-colors flex items-center gap-2"><i class="fas fa-angle-left text-xs"></i> الحوكمة والشفافية</a></li>
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
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            
            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                    const icon = this.querySelector('i');
                    if (mobileMenu.classList.contains('hidden')) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    } else {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    }
                });
            }

            const mobileToggles = document.querySelectorAll('.mobile-toggle');
            mobileToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const submenu = this.nextElementSibling;
                    const icon = this.querySelector('i');
                    if (submenu) {
                        submenu.classList.toggle('hidden');
                        if (icon) {
                            icon.classList.toggle('fa-chevron-down');
                            icon.classList.toggle('fa-chevron-up');
                        }
                    }
                });
            });

            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.slider-dot');
            let currentSlide = 0;
            let slideInterval;

            if(slides.length > 1) {
                window.goToSlide = function(index) {
                    slides.forEach(s => { s.classList.remove('slide-active'); s.classList.add('slide-inactive'); });
                    dots.forEach(d => { d.classList.remove('scale-125', 'bg-white'); d.classList.add('bg-white/50'); });
                    
                    currentSlide = index;
                    slides[currentSlide].classList.remove('slide-inactive');
                    slides[currentSlide].classList.add('slide-active');
                    if(dots[currentSlide]) {
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