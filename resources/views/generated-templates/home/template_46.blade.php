@php
    $connection = \Illuminate\Support\Facades\DB::connection('tenant');

    $settings = $connection->table('site_settings')->orderBy('id', 'desc')->first();

    $aboutText = $settings->about_text ?? 'جمعية خيرية تعمل على خدمة المجتمع وتقديم الدعم للمحتاجين في مختلف المجالات.';
    $videoUrl = $settings->intro_video_url ?? null;
    $siteName = $settings->site_name ?? 'جمعية مكنون لتحفيظ القرآن الكريم';
    $assocName = $settings->association_name ?? $siteName;

    $address = $settings->address ?? 'المملكة العربية السعودية';
    $phone = $settings->phone ?? ($settings->official_phone ?? '+966 50 123 4567');
    $email = $settings->email ?? ($settings->official_email ?? 'info@quranm.org.sa');
    $desc = $settings->site_description ?? 'جمعية مكنون لتحفيظ القرآن الكريم بالرياض، مسجلة بوزارة الموارد البشرية والتنمية الاجتماعية.';

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
    | الشعار
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

    $groupedItems = $allMenuItems->groupBy(function ($item) {
        return empty($item->parent_id) ? 'root' : $item->parent_id;
    });
    $rootItems = $groupedItems->get('root') ?? collect();

    $projects = collect();
    try {
        $projects = $connection->table('program_projects')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'asc')
            ->limit(12)
            ->get();
    } catch (\Exception $e) {}

    $statistics = collect();
    try {
        $statistics = $connection->table('statistics')->orderBy('sort_order', 'asc')->limit(4)->get();
    } catch (\Exception $e) {}

    /*
    |--------------------------------------------------------------------------
    | السلايدر - تم تعديلها لدعم R2 + الصور القديمة
    |--------------------------------------------------------------------------
    */
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
            ->get();
    } catch (\Exception $e) {}

    $partners = collect();
    try {
        $partners = $connection->table('partners')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'asc')
            ->orderByDesc('id')
            ->get();
    } catch (\Exception $e) {}

    if (!function_exists('resolveMenuUrl')) {
        function resolveMenuUrl($item, $connection)
        {
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
    ];

    $socialLinks = collect([
        [
            'url' => $settings->twitter_url ?? null,
            'icon' => 'fab fa-twitter',
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

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: Tajawal -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Tajawal"', 'sans-serif'],
                    },
                    colors: {
                        maknoon: {
                            green: '#005f4a',
                            gold: '#c99b38',
                            dark: '#004233',
                            gray: '#f5f7f6',
                        }
                    },
                    boxShadow: {
                        'card': '0 2px 15px rgba(0,0,0,0.08)',
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #ffffff; }

        .slider-fade { transition: opacity 0.8s ease-in-out; }
        .slide-active { opacity: 1; z-index: 10; position: relative; }
        .slide-inactive { opacity: 0; z-index: 0; position: absolute; top: 0; left: 0; right: 0; bottom: 0; }

        .section-title {
            color: #005f4a;
            font-size: 2rem;
            font-weight: 800;
            position: relative;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: #c99b38;
        }

        .project-card {
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .project-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transform: translateY(-3px);
        }

        .nav-item:hover .dropdown-menu { display: block; }

        .arch-frame {
            position: relative;
            padding: 2.5rem 1rem;
            border: 2px solid rgba(201, 155, 56, 0.4);
            border-radius: 80px 80px 15px 15px;
            transition: all 0.4s ease;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(2px);
        }

        .arch-frame:hover {
            border-color: #c99b38;
            background: rgba(201, 155, 56, 0.1);
            transform: translateY(-8px);
        }
    </style>
</head>
<body class="text-gray-800 antialiased relative">

    <div class="fixed inset-0 pointer-events-none z-[-1] overflow-hidden opacity-[0.03]">
        <div class="absolute -top-32 -right-32 w-[500px] h-[700px] border-[30px] border-maknoon-green rounded-t-[250px]"></div>
        <div class="absolute top-[30%] -left-32 w-[400px] h-[600px] border-[25px] border-maknoon-gold rounded-t-[200px]"></div>
        <div class="absolute -bottom-32 right-[20%] w-[600px] h-[400px] border-[20px] border-maknoon-green rounded-t-[300px]"></div>
    </div>

    <!-- الشريط العلوي -->
    <div class="bg-maknoon-green text-white py-1.5 text-sm hidden md:block">
        <div class="container mx-auto px-4 max-w-[1200px] flex justify-between items-center">
            <div class="flex items-center space-x-6 space-x-reverse text-gray-200">
                @if($email)
                    <a href="mailto:{{ $email }}" class="hover:text-maknoon-gold transition-colors flex items-center gap-2">
                        <i class="fas fa-envelope text-maknoon-gold"></i> <span>{{ $email }}</span>
                    </a>
                @endif
                @if($phone)
                    <a href="tel:{{ $phone }}" class="hover:text-maknoon-gold transition-colors flex items-center gap-2" dir="ltr">
                        <i class="fas fa-phone-alt text-maknoon-gold"></i> <span>{{ $phone }}</span>
                    </a>
                @endif
            </div>
            <div class="flex items-center gap-6">
                <div class="flex gap-4">
                    @foreach($socialLinks as $social)
                        <a href="{{ $social['url'] }}" target="_blank" class="text-white hover:text-maknoon-gold transition-colors text-base" title="{{ $social['label'] }}">
                            <i class="{{ $social['icon'] }}"></i>
                        </a>
                    @endforeach
                </div>

                @if(!empty($settings?->beneficiary_portal_url))
                    <div class="border-r border-white/30 pr-4">
                        <a href="{{ $settings->beneficiary_portal_url }}" class="hover:text-maknoon-gold transition-colors font-bold flex items-center gap-2">
                            <i class="fas fa-user"></i> تسجيل الدخول
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- الهيدر -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 max-w-[1200px]">
            <div class="flex justify-between items-center h-[90px]">

                <a href="/" class="flex-shrink-0 flex items-center">
                    @if($finalLogoUrl)
                        <img src="{{ $finalLogoUrl }}" alt="{{ $assocName }}" class="h-14 w-auto object-contain">
                    @else
                        <h1 class="font-extrabold text-2xl text-maknoon-green">{{ $assocName }}</h1>
                    @endif
                </a>

                <nav class="hidden lg:flex items-center space-x-8 space-x-reverse h-full">
                    <a href="/" class="text-maknoon-gold font-bold text-[15px]">الرئيسية</a>

                    @foreach($rootItems as $item)
                        @php
                            $children = $groupedItems->get($item->id) ?? collect();
                            $hasChildren = $children->count() > 0;
                            $url = $hasChildren ? 'javascript:void(0);' : resolveMenuUrl($item, $connection);
                        @endphp

                        <div class="relative nav-item h-full flex items-center">
                            <a href="{{ $url }}" class="text-[#333] font-bold text-[15px] hover:text-maknoon-green transition-colors flex items-center gap-1">
                                {{ $item->title }}
                                @if($hasChildren)
                                    <i class="fas fa-chevron-down text-[10px] mt-1 text-gray-400"></i>
                                @endif
                            </a>

                            @if($hasChildren)
                                <div class="dropdown-menu hidden absolute top-[90px] right-0 bg-white shadow-lg border-t-2 border-maknoon-green min-w-[200px] z-50">
                                    <ul class="py-2">
                                        @foreach($children as $subItem)
                                            <li>
                                                <a href="{{ resolveMenuUrl($subItem, $connection) }}" class="block px-4 py-2 text-[14px] text-gray-700 hover:bg-gray-50 hover:text-maknoon-green transition-colors">
                                                    {{ $subItem->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </nav>

                <div class="hidden lg:flex items-center">
                    @if(!empty($settings?->store_url))
                        <a href="{{ $settings->store_url }}" class="bg-maknoon-gold text-white px-6 py-2.5 rounded text-[15px] font-bold hover:bg-yellow-600 transition-colors shadow-sm flex items-center gap-2">
                            تبرع الان
                        </a>
                    @endif
                </div>

                <button id="mobile-menu-btn" class="lg:hidden text-maknoon-green text-2xl focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- قائمة الجوال -->
        <div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-gray-100 absolute w-full shadow-lg z-50">
            <div class="flex flex-col px-4 py-4 space-y-2">
                <a href="/" class="text-maknoon-gold font-bold py-2 border-b border-gray-50">الرئيسية</a>

                @foreach($rootItems as $item)
                    @php
                        $children = $groupedItems->get($item->id) ?? collect();
                        $hasChildren = $children->count() > 0;
                        $url = $hasChildren ? 'javascript:void(0);' : resolveMenuUrl($item, $connection);
                    @endphp

                    <div>
                        <a href="{{ $url }}" class="text-gray-800 font-bold block py-2 flex justify-between items-center border-b border-gray-50" @if($hasChildren) onclick="this.nextElementSibling.classList.toggle('hidden')" @endif>
                            {{ $item->title }}
                            @if($hasChildren)
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            @endif
                        </a>

                        @if($hasChildren)
                            <div class="hidden bg-gray-50 p-2 space-y-2 mt-1">
                                @foreach($children as $subItem)
                                    <a href="{{ resolveMenuUrl($subItem, $connection) }}" class="block px-4 py-1.5 text-sm text-gray-600 hover:text-maknoon-green">
                                        {{ $subItem->title }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="pt-4 flex flex-col gap-3">
                    @if(!empty($settings?->store_url))
                        <a href="{{ $settings->store_url }}" class="text-center bg-maknoon-gold text-white px-4 py-2 rounded font-bold">تبرع الان</a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- السلايدر -->
    <section class="relative bg-gray-100">
        <div id="slider-container" class="relative w-full h-[300px] md:h-[500px] overflow-hidden">
            @if(isset($sliders) && $sliders->count() > 0)
                @foreach($sliders as $index => $slider)
                    <div class="slide slider-fade w-full h-full {{ $index == 0 ? 'slide-active' : 'slide-inactive' }}">
                        <img
                            src="{{ $slider->final_image_url ?: 'https://via.placeholder.com/1920x500?text=' . urlencode($slider->title ?? 'Slider') }}"
                            alt="{{ $slider->title }}"
                            class="w-full h-full object-cover"
                        >

                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

                        <div class="absolute bottom-10 inset-x-0 flex flex-col justify-end items-center text-center px-4 z-20">
                            <h2 class="text-2xl md:text-4xl font-bold text-white mb-2 drop-shadow-md">
                                {{ $slider->title }}
                            </h2>
                            @if($slider->description)
                                <p class="text-sm md:text-lg text-gray-200 mb-4 max-w-2xl drop-shadow">
                                    {{ $slider->description }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if($sliders->count() > 1)
                    <button onclick="prevSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/40 text-white w-10 h-10 rounded-full flex items-center justify-center z-30 transition">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <button onclick="nextSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/40 text-white w-10 h-10 rounded-full flex items-center justify-center z-30 transition">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="absolute bottom-4 left-0 right-0 z-30 flex justify-center gap-2">
                        @foreach($sliders as $index => $slider)
                            <button onclick="goToSlide({{ $index }})" class="slider-dot w-2.5 h-2.5 rounded-full transition-all duration-300 {{ $index == 0 ? 'bg-maknoon-gold' : 'bg-white/70' }}"></button>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="w-full h-full bg-maknoon-green flex items-center justify-center">
                    <img src="https://via.placeholder.com/1920x500?text={{ urlencode($assocName) }}" class="w-full h-full object-cover opacity-50">
                </div>
            @endif
        </div>
    </section>

    <!-- صندوق التبرع -->
    <section class="relative z-40 -mt-10 mb-12 px-4">
        <div class="container mx-auto max-w-[1000px]">
            <div class="bg-white rounded-xl shadow-card p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 border border-gray-100">
                <div class="flex items-center gap-5 w-full md:w-1/2">
                    <div class="w-14 h-14 bg-maknoon-green/10 text-maknoon-green rounded-full flex items-center justify-center text-2xl flex-shrink-0">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-maknoon-green mb-1">التبرع السريع</h3>
                        <p class="text-gray-500 text-sm">ساهم معنا الآن بالتبرع لجمعية {{ $assocName }}</p>
                    </div>
                </div>

                <div class="w-full md:w-1/2 flex gap-3 items-center justify-end">
                    <div class="relative w-full max-w-[200px]">
                        <input type="number" placeholder="المبلغ" class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:outline-none focus:border-maknoon-green focus:ring-1 focus:ring-maknoon-green transition text-left" dir="ltr">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">ر.س</span>
                    </div>
                    @php
                        $donateNowUrl = $settings->store_url ?? '#';
                    @endphp
                    <a href="{{ $donateNowUrl }}" class="bg-maknoon-gold text-white px-8 py-3 rounded-lg font-bold hover:bg-yellow-600 transition shadow-md text-center whitespace-nowrap">
                        تبرع الان
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- المشاريع -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4 max-w-[1200px]">
            <h2 class="section-title">المشاريع</h2>

            @if(isset($projects) && $projects->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($projects->take(6) as $project)
                        @php
                            $imgUrl = resolveMediaUrlFromMediaId(
                                $project->cover_image_media_id ?? null,
                                $project->cover_image ?? null,
                                'public'
                            ) ?: 'https://via.placeholder.com/600x400?text=مشروع';

                            $title = $project->title ?? 'مشروع خيري';
                        @endphp

                        <div class="project-card bg-white flex flex-col">
                            <div class="relative h-48 overflow-hidden bg-gray-100 group">
                                <img src="{{ $imgUrl }}" alt="{{ $title }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                                @if(!empty($project->category_name))
                                    <span class="absolute top-3 right-3 bg-maknoon-green text-white text-[11px] font-bold px-2 py-1 rounded">
                                        {{ $project->category_name }}
                                    </span>
                                @endif
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="text-lg font-bold text-[#333] mb-2 line-clamp-2">{{ $title }}</h3>
                                <p class="text-gray-500 text-sm mb-4 flex-1 line-clamp-2">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($project->description ?? ''), 100) }}
                                </p>

                                <div class="mb-4 bg-gray-50 rounded-lg p-3 flex justify-between items-center border border-gray-100">
                                    <div class="text-center w-1/2 border-l border-gray-200">
                                        <span class="block text-[11px] text-gray-500 mb-1 font-bold">بداية المشروع</span>
                                        <span class="block text-sm font-bold text-maknoon-green" dir="ltr">
                                            {{ !empty($project->start_date) ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '-' }}
                                        </span>
                                    </div>
                                    <div class="text-center w-1/2">
                                        <span class="block text-[11px] text-gray-500 mb-1 font-bold">نهاية المشروع</span>
                                        <span class="block text-sm font-bold text-maknoon-green" dir="ltr">
                                            {{ !empty($project->end_date) ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : '-' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-auto grid grid-cols-2 gap-2">
                                    @if(!empty($project->donation_url))
                                        <a href="{{ $project->donation_url }}" class="text-center bg-maknoon-green text-white py-2.5 rounded text-sm font-bold hover:bg-[#004233] transition-colors">
                                            تبرع الان
                                        </a>
                                        <a href="{{ url('/projects/' . ($project->id ?? '#')) }}" class="text-center bg-gray-100 text-gray-700 py-2.5 rounded text-sm font-bold hover:bg-gray-200 transition-colors border border-gray-200">
                                            التفاصيل
                                        </a>
                                    @else
                                        <a href="{{ url('/projects/' . ($project->id ?? '#')) }}" class="col-span-2 text-center bg-maknoon-green text-white py-2.5 rounded text-sm font-bold hover:bg-[#004233] transition-colors">
                                            التفاصيل
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-10">
                    <a href="/projects" class="inline-block bg-white text-maknoon-green border-2 border-maknoon-green px-8 py-2 rounded font-bold hover:bg-maknoon-green hover:text-white transition-colors">
                        عرض المزيد
                    </a>
                </div>
            @else
                <div class="text-center text-gray-500 py-8">لا توجد مشاريع مضافة حالياً.</div>
            @endif
        </div>
    </section>

    <!-- الإحصائيات -->
    @if(isset($statistics) && $statistics->count() > 0)
    <section class="py-20 relative bg-maknoon-green bg-fixed bg-center bg-cover" style="background-image: url('https://images.unsplash.com/photo-1584281722883-9b0d28ce14dc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');">
        <div class="absolute inset-0 bg-maknoon-green/95"></div>

        <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>

        <div class="container mx-auto px-4 max-w-[1200px] relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-10">
                @foreach($statistics as $stat)
                    <div class="arch-frame text-center text-white">
                        <div class="text-maknoon-gold text-4xl lg:text-5xl mb-4">
                            @if($stat->icon && str_contains($stat->icon, 'heroicon'))
                                <x-filament::icon :icon="$stat->icon" class="w-10 h-10 lg:w-12 lg:h-12 mx-auto" />
                            @else
                                <i class="{{ $stat->icon ?? 'fas fa-chart-line' }}"></i>
                            @endif
                        </div>
                        <h3 class="text-3xl lg:text-4xl font-bold mb-2" dir="ltr">{{ $stat->value }}</h3>
                        <p class="text-sm lg:text-lg text-gray-200 font-medium">{{ $stat->title }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- الأخبار -->
    <section class="py-16 bg-maknoon-gray">
        <div class="container mx-auto px-4 max-w-[1200px]">
            <h2 class="section-title">أحدث الأخبار</h2>

            @if(isset($newsItems) && $newsItems->count() > 0)
                <div class="grid md:grid-cols-3 gap-6">
                    @foreach($newsItems as $item)
                        @php
                            $newsImage = resolveMediaUrlForPath($item->image ?? null, 'public') ?: 'https://via.placeholder.com/600x400?text=خبر';
                            $newsUrl = !empty($item->slug) ? url('/news/' . $item->slug) : '#';
                            $newsDate = $item->published_at ?: $item->created_at;
                        @endphp
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 group">
                            <a href="{{ $newsUrl }}" class="block relative h-48 overflow-hidden">
                                <img src="{{ $newsImage }}" alt="{{ $item->title ?? 'خبر' }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                            </a>
                            <div class="p-5">
                                <div class="text-xs text-maknoon-gold mb-2 font-bold">
                                    <i class="far fa-calendar-alt"></i> {{ $newsDate ? \Carbon\Carbon::parse($newsDate)->format('Y-m-d') : '-' }}
                                </div>
                                <h3 class="text-base font-bold text-[#333] mb-3 line-clamp-2 hover:text-maknoon-green transition-colors">
                                    <a href="{{ $newsUrl }}">{{ $item->title ?? 'خبر' }}</a>
                                </h3>
                                <p class="text-gray-500 text-sm line-clamp-2 mb-4">
                                    {{ $item->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($item->content ?? ''), 90) }}
                                </p>
                                <a href="{{ $newsUrl }}" class="text-maknoon-green font-bold text-sm flex items-center gap-1 hover:text-maknoon-gold transition-colors">
                                    اقرأ المزيد <i class="fas fa-angle-left text-xs"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-10">
                    <a href="/news" class="inline-block bg-white text-maknoon-green border-2 border-maknoon-green px-8 py-2 rounded font-bold hover:bg-maknoon-green hover:text-white transition-colors">
                        المزيد من الأخبار
                    </a>
                </div>
            @else
                <div class="text-center text-gray-500">لا توجد أخبار منشورة حالياً</div>
            @endif
        </div>
    </section>

    <!-- الشركاء -->
    @if(isset($partners) && $partners->count() > 0)
    <section class="py-16 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4 max-w-[1200px]">
            <div class="text-center mb-10">
                <h2 class="text-3xl section-title inline-block">شركاء النجاح</h2>
                <p class="text-gray-500 mt-2 font-bold">نفخر بشراكاتنا الاستراتيجية التي تساهم في تحقيق أهدافنا</p>
            </div>
            <div class="overflow-hidden relative w-full py-4">
                <div class="flex gap-6 items-center animate-[scroll_40s_linear_infinite] w-max px-4">
                    @foreach($partners as $partner)
                        @php
                            $partnerLogo = resolveMediaUrlForPath($partner->logo ?? null, 'public');
                        @endphp
                        @if($partnerLogo)
                            <div class="w-48 h-28 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center p-4 hover:shadow-md hover:border-maknoon-gold transition-all duration-300 group flex-shrink-0">
                                <img src="{{ $partnerLogo }}" alt="{{ $partner->name ?? 'شريك' }}" class="max-h-full max-w-full object-contain grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all">
                            </div>
                        @endif
                    @endforeach

                    @foreach($partners as $partner)
                        @php
                            $partnerLogo = resolveMediaUrlForPath($partner->logo ?? null, 'public');
                        @endphp
                        @if($partnerLogo)
                            <div class="w-48 h-28 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center p-4 hover:shadow-md hover:border-maknoon-gold transition-all duration-300 group flex-shrink-0">
                                <img src="{{ $partnerLogo }}" alt="{{ $partner->name ?? 'شريك' }}" class="max-h-full max-w-full object-contain grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <style>
            @keyframes scroll {
                0% { transform: translateX(0); }
                100% { transform: translateX(50%); }
            }
        </style>
    </section>
    @endif

    <!-- الفوتر -->
    <footer class="bg-maknoon-dark text-gray-300 pt-16 pb-6 border-t-4 border-maknoon-gold">
        <div class="container mx-auto px-4 max-w-[1200px]">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">

                <div>
                    <div class="mb-6 bg-white inline-block p-3 rounded shadow">
                        @if($finalLogoUrl)
                            <img src="{{ $finalLogoUrl }}" alt="{{ $assocName }}" class="h-12 w-auto object-contain">
                        @else
                            <h3 class="font-extrabold text-lg text-maknoon-green">{{ $assocName }}</h3>
                        @endif
                    </div>
                    <p class="text-sm leading-loose mb-6 text-gray-400 text-justify">
                        {{ \Illuminate\Support\Str::limit($desc, 150) }}
                    </p>
                    <div class="flex gap-2">
                        @foreach($socialLinks as $social)
                            <a href="{{ $social['url'] }}" target="_blank" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center hover:bg-maknoon-gold hover:text-white transition-colors" title="{{ $social['label'] }}">
                                <i class="{{ $social['icon'] }} text-sm"></i>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-bold text-lg mb-6 relative pb-2 after:content-[''] after:absolute after:bottom-0 after:right-0 after:w-12 after:h-0.5 after:bg-maknoon-gold">
                        روابط سريعة
                    </h4>
                    <ul class="space-y-3 text-sm">
                        @foreach($rootItems->take(5) as $item)
                            <li>
                                <a href="{{ resolveMenuUrl($item, $connection) }}" class="hover:text-maknoon-gold transition-colors flex items-center gap-2">
                                    <i class="fas fa-angle-left text-xs text-maknoon-gold"></i> {{ $item->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold text-lg mb-6 relative pb-2 after:content-[''] after:absolute after:bottom-0 after:right-0 after:w-12 after:h-0.5 after:bg-maknoon-gold">
                        مواقع مهمة
                    </h4>
                    <ul class="space-y-3 text-sm">
                        @foreach($usefulLinks as $link)
                            <li>
                                <a href="{{ $link['url'] }}" target="_blank" class="hover:text-maknoon-gold transition-colors flex items-center gap-2">
                                    <i class="fas fa-external-link-alt text-xs text-maknoon-gold"></i> {{ $link['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold text-lg mb-6 relative pb-2 after:content-[''] after:absolute after:bottom-0 after:right-0 after:w-12 after:h-0.5 after:bg-maknoon-gold">
                        بيانات التواصل
                    </h4>
                    <ul class="space-y-4 text-sm text-gray-300">
                        @if($address)
                            <li class="flex items-start gap-3">
                                <i class="fas fa-map-marker-alt mt-1 text-maknoon-gold"></i>
                                <span>{{ $address }}</span>
                            </li>
                        @endif
                        @if($phone)
                            <li class="flex items-center gap-3">
                                <i class="fas fa-phone text-maknoon-gold"></i>
                                <span dir="ltr">{{ $phone }}</span>
                            </li>
                        @endif
                        @if($email)
                            <li class="flex items-center gap-3">
                                <i class="fas fa-envelope text-maknoon-gold"></i>
                                <span>{{ $email }}</span>
                            </li>
                        @endif
                    </ul>

                    <div class="mt-6 bg-white/5 p-2 rounded inline-block">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/cc/Vision_2030_logo_Ar.svg/320px-Vision_2030_logo_Ar.svg.png" alt="رؤية 2030" class="h-10 opacity-70">
                    </div>
                </div>

            </div>

            <div class="border-t border-white/10 pt-6 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-400">
                <p>جميع الحقوق محفوظة &copy; {{ date('Y') }} {{ $assocName }}</p>
                <div class="flex items-center gap-2">
                    <span>مشغل بواسطة</span>
                    <strong class="text-white text-sm">نظام خير</strong>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        if (btn && menu) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        }

        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dot');
        let currentSlide = 0;
        let slideInterval;

        if (slides.length > 1) {
            window.goToSlide = function(index) {
                slides.forEach(s => {
                    s.classList.remove('slide-active');
                    s.classList.add('slide-inactive');
                });

                dots.forEach(d => {
                    d.classList.remove('bg-maknoon-gold');
                    d.classList.add('bg-white/70');
                });

                currentSlide = index;
                slides[currentSlide].classList.remove('slide-inactive');
                slides[currentSlide].classList.add('slide-active');

                if (dots[currentSlide]) {
                    dots[currentSlide].classList.remove('bg-white/70');
                    dots[currentSlide].classList.add('bg-maknoon-gold');
                }

                resetInterval();
            };

            window.nextSlide = function() {
                let next = (currentSlide + 1) % slides.length;
                goToSlide(next);
            };

            window.prevSlide = function() {
                let prev = (currentSlide - 1 + slides.length) % slides.length;
                goToSlide(prev);
            };

            function resetInterval() {
                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 5000);
            }

            resetInterval();
        }
    </script>
</body>
</html>