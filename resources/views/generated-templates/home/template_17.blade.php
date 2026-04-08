@php
    // جلب البيانات بالقوة الجبرية من قاعدة بيانات الجمعية
    $connection = \Illuminate\Support\Facades\DB::connection('tenant');
    
    // 1. جلب الإعدادات العامة
    $settings = $connection->table('site_settings')->orderBy('id', 'desc')->first();
    
    $aboutText = $settings->about_text ?? 'نحن نسعى لتقديم أفضل الخدمات المجتمعية والخيرية التي تهدف إلى الارتقاء بالمجتمع وتوفير بيئة داعمة ومستدامة.';
    $videoUrl = $settings->intro_video_url ?? null;
    $siteName = $settings->site_name ?? 'الجمعية';
    $assocName = $settings->association_name ?? $siteName;
    
    // ألوان رسمية مطابقة لهوية جمعية البر
    $primaryColor = $settings->primary_color ?? '#355e3b'; // أخضر داكن رسمي
    $secondaryColor = $settings->secondary_color ?? '#b48600'; // ذهبي
    
    $address = $settings->address ?? 'المملكة العربية السعودية';
    $phone = $settings->phone ?? ($settings->official_phone ?? '0500087575');
    $email = $settings->email ?? ($settings->official_email ?? 'info@domain.com');
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
        $finalLogoUrl = str_starts_with($logoPath, 'http') ? $logoPath : \App\Support\Media\MediaUrl::forDiskPath('public', $logoPath);
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

    // 3. جلب المشاريع والأخبار
    $projects = collect();
    $news = collect();
    try {
        $projects = $connection->table('program_projects')->where('is_active', 1)->orderBy('sort_order', 'asc')->limit(6)->get();
    } catch (\Exception $e) {}
    
    try {
        // جلب 5 أخبار لتطابق التصميم (1 كبير + 4 صغار)
        $news = $connection->table('news')->where('is_active', 1)->orderBy('id', 'desc')->limit(5)->get();
    } catch (\Exception $e) {}

    // دالة بناء الروابط
    if (!function_exists('resolveMenuUrl')) {
        function resolveMenuUrl($item, $connection) {
            $url = $item->resolved_url ?? $item->url ?? '#';
            if($item->type === 'page' && !empty($item->page_id) && $url === '#') {
                $page = $connection->table('pages')->where('id', $item->page_id)->first();
                $url = $page ? '/p/' . $page->slug : '#';
            }
            return $url;
        }
    }

    // دالة بناء القوائم
    if (!function_exists('buildInfiniteMenu')) {
        function buildInfiniteMenu($items, $groupedItems, $connection, $isMobile, $level = 1) {
            $html = '';
            foreach ($items as $item) {
                $hasChildren = $groupedItems->has($item->id);
                $url = $hasChildren ? 'javascript:void(0);' : resolveMenuUrl($item, $connection);
                
                $iconHtml = '';
                if (!empty($item->icon)) {
                    if (str_contains($item->icon, 'heroicon')) {
                        $iconHtml = \Illuminate\Support\Facades\Blade::render('<x-filament::icon icon="'.$item->icon.'" style="width: 1.2rem; height: 1.2rem;" />');
                    } else {
                        $iconHtml = '<i class="'.$item->icon.'"></i>';
                    }
                }

                if ($isMobile) {
                    $html .= '<li>';
                    $html .= '<a href="'.$url.'" class="'.($hasChildren ? 'mobile-dropdown-toggle' : '').'" target="'.($item->target ?? '_self').'">';
                    $html .= '<div class="menu-item-content">' . $iconHtml . '<span>' . $item->title . '</span></div>';
                    if ($hasChildren) $html .= '<i class="fas fa-chevron-down arrow-icon"></i>';
                    $html .= '</a>';
                    if ($hasChildren) {
                        $html .= '<ul class="mobile-dropdown-menu">';
                        $html .= buildInfiniteMenu($groupedItems->get($item->id), $groupedItems, $connection, true, $level + 1);
                        $html .= '</ul>';
                    }
                    $html .= '</li>';
                } else {
                    $liClass = $hasChildren ? 'has-dropdown' : '';
                    $html .= '<li class="'.$liClass.'">';
                    $html .= '<a href="'.$url.'" target="'.($item->target ?? '_self').'" style="'.($hasChildren ? 'cursor: default;' : '').'">';
                    $html .= '<div class="menu-item-content">' . $iconHtml . '<span>' . $item->title . '</span></div>';
                    if ($hasChildren) {
                        $arrowClass = $level === 1 ? 'fa-angle-down' : 'fa-angle-left';
                        $html .= '<i class="fas '.$arrowClass.' arrow-icon"></i>';
                    }
                    $html .= '</a>';
                    if ($hasChildren) {
                        $ulClass = $level === 1 ? 'dropdown-menu' : 'dropdown-menu sub-menu';
                        $html .= '<ul class="'.$ulClass.'">';
                        $html .= buildInfiniteMenu($groupedItems->get($item->id), $groupedItems, $connection, false, $level + 1);
                        $html .= '</ul>';
                    }
                    $html .= '</li>';
                }
            }
            return $html;
        }
    }
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteName }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: {{ $primaryColor }};
            --secondary: {{ $secondaryColor }};
            --text-dark: #1e293b;
            --text-gray: #475569;
            --text-muted: #64748b;
            --bg-light: #f8fafc; 
            --bg-white: #ffffff;
            --border-color: #e2e8f0;
            --transition: all 0.3s ease;
            --shadow-sm: 0 2px 5px rgba(0,0,0,0.05);
            --shadow-md: 0 10px 20px rgba(0, 0, 0, 0.08);
            --radius-md: 8px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', sans-serif; }
        body { background-color: var(--bg-white); color: var(--text-gray); line-height: 1.6; overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }
        ul { list-style: none; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; position: relative;}
        
        .section-header { text-align: center; margin-bottom: 40px; }
        .section-header h2 { font-size: 2.2rem; color: var(--primary); font-weight: 800; margin-bottom: 15px; position: relative; display: inline-block; }
        .section-header h2::after { content: ''; position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 50px; height: 4px; background-color: var(--secondary); border-radius: 2px; }

        /* =========================================
           Desktop Header (الهيدر الأبيض + العلامة المائية)
           ========================================= */
        .site-header {
            background: var(--bg-white);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow-sm);
        }

        /* الجزء العلوي الأبيض + الزخرفة المائية */
        .header-top {
            background-color: var(--bg-white);
            padding: 15px 0;
            position: relative;
            z-index: 10;
        }
        
        .header-top::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://www.transparenttextures.com/patterns/arabesque.png');
            background-repeat: repeat;
            opacity: 0.15; /* شفافية خفيفة جداً للزخرفة */
            z-index: -1;
            pointer-events: none;
        }

        .header-top .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-right-side { display: flex; align-items: center; gap: 40px; }
        .logo-box { display: block; }
        .logo-wrapper img { max-height: 80px; object-fit: contain; }
        
        .contact-widget {
            display: flex;
            align-items: center;
            gap: 15px;
            border-right: 1px solid var(--border-color); 
            padding-right: 30px;
        }
        .contact-details { display: flex; flex-direction: column; text-align: right; }
        .contact-details .title { font-size: 0.95rem; color: var(--text-gray); font-weight: 700; }
        .contact-details .phone { font-size: 1.25rem; color: var(--text-dark); font-weight: 800; direction: ltr; }
        .contact-widget .icon-circle {
            width: 45px; height: 45px;
            background-color: var(--primary);
            color: #ffffff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            box-shadow: var(--shadow-sm);
        }

        .header-left-side { display: flex; align-items: center; }
        .account-btn {
            background-color: var(--primary);
            color: #ffffff !important;
            padding: 10px 25px;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
        }
        .account-btn:hover { background-color: var(--secondary); }

        /* شريط القائمة الملون (الديسكتوب) */
        .header-nav { background-color: var(--primary); }
        .desktop-menu { display: flex; gap: 5px; margin: 0; padding: 0; justify-content: flex-start; }
        .desktop-menu > li > a {
            color: #ffffff;
            font-size: 1.05rem;
            font-weight: 600;
            padding: 15px 20px;
            display: flex; align-items: center; gap: 8px;
            transition: var(--transition);
        }
        .desktop-menu > li > a:hover { background-color: rgba(255, 255, 255, 0.1); }
        
        .has-dropdown { position: relative; }
        .dropdown-menu { position: absolute; top: 100%; right: 0; background: var(--primary); min-width: 220px; box-shadow: var(--shadow-md); padding: 5px 0; opacity: 0; visibility: hidden; transform: translateY(10px); transition: var(--transition); z-index: 1000; border-top: 1px solid rgba(255,255,255,0.1);}
        .dropdown-menu.sub-menu { top: 0; right: 100%; border-top: none; border-right: 1px solid rgba(255,255,255,0.1); }
        .has-dropdown:hover > .dropdown-menu { opacity: 1; visibility: visible; transform: translate(0, 0); }
        .dropdown-menu li { border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .dropdown-menu li:last-child { border-bottom: none; }
        .dropdown-menu a { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; color: #ffffff; font-size: 0.95rem !important; font-weight: 600 !important; }
        .dropdown-menu a:hover { background: rgba(255, 255, 255, 0.1); padding-right: 25px; }

        /* =========================================
           Mobile Header (نسخة طبق الأصل من الصورة)
           ========================================= */
        .mobile-header-wrapper { display: none; }
        .mobile-menu { display: none; background: var(--bg-white); border-top: 1px solid var(--border-color); position: absolute; top: 100%; left: 0; width: 100%; box-shadow: var(--shadow-md); z-index: 999; }
        .mobile-menu.active { display: block; }
        .mobile-menu > li { border-bottom: 1px solid var(--border-color); }
        .mobile-menu a { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; color: var(--text-dark); font-weight: 700; }
        .mobile-dropdown-menu { display: none; background: var(--bg-light); border-top: 1px solid var(--border-color); }
        .mobile-dropdown-menu.open { display: block; }
        .mobile-dropdown-menu a { padding: 12px 30px; font-size: 0.95rem; font-weight: 600; color: var(--text-gray); border-bottom: 1px solid var(--border-color); }

        /* =========================================
           Hero Slider
           ========================================= */
        .hero-slider { position: relative; height: 80vh; min-height: 500px; background: var(--primary); overflow: hidden; }
        .slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 1s ease; display: flex; align-items: center; justify-content: center; background-size: cover; background-position: center; z-index: 0; }
        .slide.active { opacity: 1; z-index: 1; }
        .slide::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.4); z-index: 1; }
        .hero-content { position: relative; z-index: 2; text-align: center; color: white; max-width: 900px; padding: 0 20px; transform: translateY(30px); opacity: 0; transition: all 1s ease 0.3s; }
        .slide.active .hero-content { transform: translateY(0); opacity: 1; }
        .hero-content h1 { font-size: 3.5rem; font-weight: 800; margin-bottom: 20px; line-height: 1.3; text-shadow: 2px 2px 10px rgba(0,0,0,0.5);}
        .hero-content p { font-size: 1.3rem; margin-bottom: 30px; color: #f1f5f9; text-shadow: 1px 1px 5px rgba(0,0,0,0.5);}
        .hero-btn { display: inline-block; background-color: var(--secondary); color: #fff; padding: 15px 40px; font-size: 1.1rem; font-weight: 800; border-radius: var(--radius-md); transition: var(--transition); border: 2px solid var(--secondary); }
        .hero-btn:hover { background-color: transparent; color: var(--secondary); }
        .slider-controls { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 10; }
        .dot { width: 12px; height: 12px; border-radius: 50%; background: rgba(255,255,255,0.4); cursor: pointer; transition: var(--transition); }
        .dot.active { background: var(--secondary); transform: scale(1.3); }

        /* =========================================
           Projects Section
           ========================================= */
        .projects-section { padding: 80px 0; background: var(--bg-light); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);}
        .projects-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; margin-bottom: 40px; position: relative; z-index: 2;}
        .project-card { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: var(--radius-md); overflow: hidden; transition: var(--transition); display: flex; flex-direction: column; }
        .project-card:hover { box-shadow: var(--shadow-md); border-color: var(--primary); transform: translateY(-3px); }
        
        .project-img-box { position: relative; height: 200px; width: 100%; border-bottom: 1px solid var(--border-color);}
        .project-img-box img { width: 100%; height: 100%; object-fit: cover; }
        .project-tag { position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.95); color: var(--primary); padding: 5px 15px; border-radius: 4px; font-weight: 700; font-size: 0.85rem; box-shadow: var(--shadow-sm); }
        
        .project-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .project-title { font-size: 1.15rem; font-weight: 700; color: var(--text-dark); margin-bottom: 15px; line-height: 1.5; }
        
        .project-stats { margin-top: auto; }
        .stats-info { display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; }
        .stats-info .target { color: var(--text-gray); }
        .stats-info .percent { color: var(--success); font-weight: 700;}
        
        .progress-container { width: 100%; height: 6px; background: var(--border-color); border-radius: 3px; overflow: hidden; margin-bottom: 8px; }
        .progress-bar { height: 100%; background-color: var(--success); border-radius: 3px; }
        .stats-collected { font-size: 0.85rem; color: var(--primary); font-weight: 700; }

        .project-footer { padding: 0; border-top: 1px solid var(--border-color); background: var(--bg-white); transition: var(--transition);}
        .btn-donate-card { display: block; width: 100%; text-align: center; color: var(--primary); font-weight: 800; font-size: 1.1rem; transition: var(--transition); padding: 15px;}
        .project-card:hover .project-footer, .project-card:hover .btn-donate-card { background: var(--primary); color: #ffffff; }

        .btn-outline { display: block; width: fit-content; margin: 0 auto; background: transparent; color: var(--primary); border: 2px solid var(--primary); padding: 10px 30px; border-radius: var(--radius-md); font-weight: 800; transition: var(--transition); }
        .btn-outline:hover { background: var(--primary); color: #ffffff; }

        /* =========================================
           News Section (تصميم جمعية البر المطابق للصورة)
           ========================================= */
        .news-section { 
            padding: 80px 0; 
            background-color: var(--primary); /* أخضر داكن مثل الصورة */
            color: #ffffff;
        }
        
        .news-header-title {
            text-align: right;
            margin-bottom: 30px;
        }
        .news-header-title h2 {
            font-size: 2rem;
            color: #ffffff;
            font-weight: 700;
        }

        .news-layout {
            display: grid;
            grid-template-columns: 1fr 1.2fr; /* الكارت الكبير يمين، الكروت الصغيرة يسار */
            gap: 25px;
        }

        /* الكارت الكبير المتميز (يمين) */
        .news-featured {
            background: #ffffff;
            border-radius: var(--radius-md);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            color: var(--text-dark);
            text-decoration: none;
            transition: var(--transition);
        }
        .news-featured:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); }
        .news-featured img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        .news-featured .content {
            padding: 25px;
            text-align: center; /* النص بالمنتصف حسب الصورة */
        }
        .news-featured h3 {
            font-size: 1.3rem;
            color: var(--primary);
            font-weight: 800;
            margin-bottom: 10px;
            line-height: 1.5;
        }
        .news-featured .date {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* الكروت الصغيرة المتراصة (يسار) */
        .news-stacked {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .news-small-card {
            background: #ffffff;
            border-radius: var(--radius-md);
            overflow: hidden;
            display: flex;
            height: 110px; /* طول ثابت للكروت الصغيرة */
            transition: var(--transition);
            color: var(--text-dark);
        }
        .news-small-card:hover { transform: translateX(-5px); box-shadow: var(--shadow-md); }
        .news-small-card img {
            width: 140px;
            height: 100%;
            object-fit: cover;
            /* الصورة على اليمين تلقائياً لأن الاتجاه RTL */
        }
        .news-small-card .content {
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex-grow: 1;
            text-align: right;
        }
        .news-small-card h4 {
            font-size: 1rem;
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 8px;
            line-height: 1.4;
        }
        .news-small-card .date {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .news-footer-action {
            margin-top: 30px;
            text-align: left; /* الزر الذهبي يسار */
        }
        .btn-gold {
            display: inline-block;
            background-color: var(--secondary);
            color: #ffffff;
            padding: 10px 30px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 1rem;
            transition: var(--transition);
        }
        .btn-gold:hover { background-color: #9a7300; }

        /* =========================================
           Partners Section
           ========================================= */
        .partners-section { padding: 60px 0; background: var(--bg-white); }
        .marquee-container { overflow: hidden; white-space: nowrap; position: relative; }
        .marquee-container:hover .marquee-content { animation-play-state: paused; }
        .marquee-content { display: inline-flex; gap: 40px; animation: marquee 25s linear infinite; }
        .partner-logo { width: 220px; height: 130px; background: var(--bg-light); border: 1px solid var(--border-color); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; padding: 15px; transition: var(--transition); }
        .partner-logo:hover { border-color: var(--primary); box-shadow: var(--shadow-sm); transform: translateY(-5px);}
        .partner-logo img { max-width: 100%; max-height: 100%; object-fit: contain; filter: grayscale(100%); transition: var(--transition); opacity: 0.7; }
        .partner-logo:hover img { filter: grayscale(0%); opacity: 1; }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(50%); } }

        /* =========================================
           Footer
           ========================================= */
        footer { background-color: #1e293b; color: #cbd5e1; padding: 60px 0 20px; border-top: 5px solid var(--secondary); }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1.5fr; gap: 40px; margin-bottom: 40px; }
        .footer-about img { max-width: 180px; background: white; padding: 10px; border-radius: var(--radius-md); margin-bottom: 20px; }
        .footer-about p { line-height: 1.8; font-size: 0.95rem; margin-bottom: 20px; }
        .footer-title { color: white; font-size: 1.2rem; font-weight: 800; margin-bottom: 25px; position: relative; padding-bottom: 10px; }
        .footer-title::after { content: ''; position: absolute; bottom: 0; right: 0; width: 30px; height: 3px; background: var(--secondary); }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a { color: #cbd5e1; transition: var(--transition); font-size: 0.95rem; display: flex; align-items: center; gap: 8px; }
        .footer-links a::before { content: '\f104'; font-family: 'Font Awesome 5 Free'; font-weight: 900; font-size: 0.8rem; color: var(--secondary); }
        .footer-links a:hover { color: white; transform: translateX(-5px); }
        .footer-contact li { display: flex; align-items: flex-start; gap: 15px; margin-bottom: 15px; font-size: 0.95rem; }
        .footer-contact i { color: var(--secondary); margin-top: 5px; font-size: 1.1rem; width: 20px; text-align: center;}
        .footer-bottom { text-align: center; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.9rem; color: #94a3b8;}

        /* =========================================
           Responsive & Mobile Header 
           ========================================= */
        @media (max-width: 992px) {
            .hideDesktop { display: block !important; }
            .hideMobile { display: none !important; }
            
            /* إخفاء الهيدر الديسكتوب العادي */
            .header-top, .header-nav { display: none; }
            
            /* إظهار هيدر الموبايل المطابق للصورة */
            .mobile-header-wrapper {
                display: block;
                position: absolute;
                top: 20px;
                left: 0;
                width: 100%;
                z-index: 1000;
                padding: 0 15px;
            }
            .mobile-header-inner {
                background-color: var(--bg-white);
                height: 70px;
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 20px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                position: relative;
            }
            .mobile-menu-btn {
                display: block;
                color: var(--primary);
                font-size: 1.8rem;
                padding: 5px;
            }
            /* الشعار بارز ونازل تحت الشريط */
            .mobile-logo-card {
                position: absolute;
                top: -5px;
                right: 20px;
                background: var(--bg-white);
                padding: 10px 15px;
                border-radius: 15px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                height: 90px;
                min-width: 90px;
            }
            .mobile-logo-card img {
                max-height: 60px;
            }

            .news-layout { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; gap: 30px; }
            .hero-slider { height: 60vh; min-height: 450px;}
        }
        @media (min-width: 993px) {
            .hideDesktop { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="hideMobile">
        <header class="site-header">
            <div class="header-top">
                <div class="container">
                    <div class="header-right-side">
                        <a href="/" class="logo-box">
                            @if($finalLogoUrl)
                                <div class="logo-wrapper"><img src="{{ $finalLogoUrl }}" alt="{{ $siteName }}"></div>
                            @else
                                <div class="logo-wrapper"><h2 style="color:var(--primary); font-weight:800; margin:0;">{{ $siteName }}</h2></div>
                            @endif
                        </a>

                        <div class="contact-widget">
                            <div class="contact-details">
                                <span class="title">اتصل بنا</span>
                                <span class="phone">{{ $phone }}</span>
                            </div>
                            <div class="icon-circle">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                        </div>
                    </div>

                    <div class="header-left-side">
                        <a href="{{ $settings->beneficiary_portal_url ?? '#' }}" class="account-btn">
                            <i class="fas fa-chevron-down"></i>
                            <span>حسابي الشخصي</span>
                            <i class="fas fa-user"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="header-nav">
                <div class="container">
                    <ul class="desktop-menu">
                        @if($rootItems->count() > 0)
                            {!! buildInfiniteMenu($rootItems, $groupedItems, $connection, false) !!}
                        @else
                            <li><a href="/"><div class="menu-item-content"><i class="fas fa-home"></i><span>الرئيسية</span></div></a></li>
                            <li><a href="#projects"><div class="menu-item-content"><i class="fas fa-box-open"></i><span>مشاريع التبرع</span></div></a></li>
                            <li><a href="#news"><div class="menu-item-content"><i class="fas fa-bullhorn"></i><span>المركز الإعلامي</span></div></a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </header>
    </div>

    <div class="mobile-header-wrapper hideDesktop">
        <div class="mobile-header-inner">
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>

            <a href="/" class="mobile-logo-card">
                @if($finalLogoUrl)
                    <img src="{{ $finalLogoUrl }}" alt="{{ $siteName }}">
                @else
                    <h2 style="color:var(--primary); font-size:1rem; margin:0;">{{ $siteName }}</h2>
                @endif
            </a>
        </div>

        <ul class="mobile-menu" id="navLinksMobile" style="margin-top: 15px; border-radius: 15px; overflow: hidden;">
            @if($rootItems->count() > 0)
                {!! buildInfiniteMenu($rootItems, $groupedItems, $connection, true) !!}
            @else
                <li><a href="/">الرئيسية</a></li>
                <li><a href="#projects">مشاريع التبرع</a></li>
                <li><a href="#news">المركز الإعلامي</a></li>
            @endif
        </ul>
    </div>

    <section class="hero-slider">
        @if(isset($sliders) && $sliders->count() > 0)
            @foreach($sliders as $index => $slider)
                <div class="slide {{ $index === 0 ? 'active' : '' }}" style="background-image: url('{{ \App\Support\Media\MediaUrl::forDiskPath('public', $slider->image) }}');">
                    <div class="hero-content">
                        <h1>{{ $slider->title }}</h1>
                        <p>{{ $slider->description }}</p>
                        @if($slider->button_text)
                            <a href="{{ $slider->button_url ?? '#' }}" class="hero-btn">{{ $slider->button_text }}</a>
                        @endif
                    </div>
                </div>
            @endforeach
            <div class="slider-dots">
                @foreach($sliders as $index => $slider)
                    <div class="dot {{ $index === 0 ? 'active' : '' }}" onclick="goToSlide({{ $index }})"></div>
                @endforeach
            </div>
        @else
            <div class="slide active" style="background-color: var(--primary);">
                <div class="hero-content">
                    <br><br> <h1>أهلاً بكم في {{ $siteName }}</h1>
                    <p>نسعى لتقديم أفضل الخدمات المجتمعية والخيرية بشفافية عالية.</p>
                    <a href="#projects" class="hero-btn">تصفح مشاريعنا</a>
                </div>
            </div>
        @endif
    </section>

    <section id="projects" class="projects-section">
        <div class="container">
            <div class="section-header">
                <h2>مشاريع التبرع</h2>
            </div>

            <div class="projects-grid">
                @if(isset($projects) && $projects->count() > 0)
                    @foreach($projects as $project)
                        @php
                            $title = $project->title ?? 'مشروع خيري';
                            $img = $project->cover_image ?? null;
                            if (empty($img) && !empty($project->cover_image_media_id)) {
                                $pm = $connection->table('media_items')->where('id', $project->cover_image_media_id)->first();
                                if ($pm) $img = $pm->file ?? $pm->path ?? null;
                            }
                            $imgUrl = $img ? \App\Support\Media\MediaUrl::forDiskPath('public', $img) : 'https://via.placeholder.com/400x250?text=مشروع+خيري';
                            
                            $target = $project->project_amount ?? 1;
                            $collected = $project->donation_amount ?? 0;
                            $percent = ($target > 0) ? min(100, round(($collected / $target) * 100)) : 0;
                        @endphp
                        <div class="project-card">
                            <div class="project-img-box">
                                <img src="{{ $imgUrl }}" alt="{{ $title }}">
                                <span class="project-tag">مشروع خيري</span>
                            </div>
                            <div class="project-body">
                                <h3 class="project-title">{{ Str::limit($title, 55) }}</h3>
                                <div class="project-stats">
                                    <div class="stats-info">
                                        <span class="target">المستهدف: {{ number_format($target == 1 && $collected == 0 ? 0 : $target) }} ر.س</span>
                                        <span class="percent">{{ $percent }}%</span>
                                    </div>
                                    <div class="progress-container"><div class="progress-bar" style="width: {{ $percent }}%;"></div></div>
                                    <div class="stats-info" style="margin-top: 8px;">
                                        <span class="stats-collected">تم جمع: {{ number_format($collected) }} ر.س</span>
                                    </div>
                                </div>
                            </div>
                            <div class="project-footer">
                                <a href="{{ $project->donation_url ?? '/projects/'.($project->id ?? '#') }}" class="btn-donate-card" target="_blank">تبرع الآن</a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="text-align: center; color: var(--text-muted); grid-column: 1 / -1; padding: 40px;">لا توجد برامج أو مشاريع مضافة حالياً.</p>
                @endif
            </div>
            @if(isset($projects) && $projects->count() > 0)
            <a href="/projects" class="btn-outline">عرض كافة المشاريع</a>
            @endif
        </div>
    </section>

    <section id="news" class="news-section">
        <div class="container">
            <div class="news-header-title">
                <h2>آخر الأخبار</h2>
            </div>

            <div class="news-layout">
                @if(isset($news) && $news->count() > 0)
                    @php $featuredNews = $news->first(); @endphp
                    <a href="/news/{{ $featuredNews->slug ?? $featuredNews->id }}" class="news-featured">
                        <img src="{{ $featuredNews->image ? \App\Support\Media\MediaUrl::forDiskPath('public', $featuredNews->image) : 'https://via.placeholder.com/600x400' }}" alt="{{ $featuredNews->title }}">
                        <div class="content">
                            <h3>{{ $featuredNews->title }}</h3>
                            <span class="date">{{ $featuredNews->published_at ? \Carbon\Carbon::parse($featuredNews->published_at)->format('l, d F Y') : '' }}</span>
                        </div>
                    </a>

                    <div class="news-stacked">
                        @foreach($news->skip(1)->take(4) as $item)
                            <a href="/news/{{ $item->slug ?? $item->id }}" class="news-small-card">
                                <img src="{{ $item->image ? \App\Support\Media\MediaUrl::forDiskPath('public', $item->image) : 'https://via.placeholder.com/150x150' }}" alt="{{ $item->title }}">
                                <div class="content">
                                    <h4>{{ Str::limit($item->title, 60) }}</h4>
                                    <span class="date">{{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('l, d F Y') : '' }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p style="text-align: center; color: #fff; grid-column: 1 / -1;">لا توجد أخبار مضافة حالياً.</p>
                @endif
            </div>
            
            @if(isset($news) && $news->count() > 0)
            <div class="news-footer-action">
                <a href="/news" class="btn-gold">اقرأ المزيد</a>
            </div>
            @endif
        </div>
    </section>

    <section class="partners-section">
        <div class="container">
            <div class="section-header" style="margin-bottom: 30px;">
                <h2>شركاء النماء</h2>
            </div>
            <div class="marquee-container">
                <div class="marquee-content">
                    @if(isset($partners) && $partners->count() > 0)
                        @foreach($partners as $partner)
                            <div class="partner-logo"><img src="{{ \App\Support\Media\MediaUrl::forDiskPath('public', $partner->logo) }}" alt="{{ $partner->name }}" title="{{ $partner->name }}"></div>
                        @endforeach
                        @foreach($partners as $partner)
                            <div class="partner-logo"><img src="{{ \App\Support\Media\MediaUrl::forDiskPath('public', $partner->logo) }}" alt="{{ $partner->name }}" title="{{ $partner->name }}"></div>
                        @endforeach
                    @else
                        <div class="partner-logo"><img src="https://via.placeholder.com/150x80?text=شريك" alt="شريك"></div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <footer id="contact">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    @if($finalLogoUrl)
                        <img src="{{ $finalLogoUrl }}" alt="الشعار">
                    @else
                        <h3 style="color:white; margin-bottom:15px;">{{ $siteName }}</h3>
                    @endif
                    <p>{{ $desc }}</p>
                </div>
                <div>
                    <h4 class="footer-title">روابط هامة</h4>
                    <ul class="footer-links">
                        <li><a href="/">الرئيسية</a></li>
                        <li><a href="#about">من نحن</a></li>
                        <li><a href="#projects">مشاريعنا</a></li>
                        <li><a href="#news">المركز الإعلامي</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-title">تواصل معنا</h4>
                    <ul class="footer-contact">
                        <li><i class="fas fa-map-marker-alt"></i> <span>{{ $address }}</span></li>
                        <li><i class="fas fa-phone-alt"></i> <span dir="ltr">{{ $phone }}</span></li>
                        <li><i class="fas fa-envelope"></i> <span>{{ $email }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                جميع الحقوق محفوظة &copy; {{ date('Y') }} لـ {{ $siteName }}
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Menu Toggle
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const navLinksMobile = document.getElementById('navLinksMobile');
            
            if (mobileMenuBtn && navLinksMobile) {
                mobileMenuBtn.addEventListener('click', function() {
                    navLinksMobile.classList.toggle('active');
                    const icon = this.querySelector('i');
                    if(navLinksMobile.classList.contains('active')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    } else {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                });
            }

            // Mobile Dropdown Toggle
            const mobileDropdownToggles = document.querySelectorAll('.mobile-dropdown-toggle');
            mobileDropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault(); 
                    const submenu = this.nextElementSibling;
                    if(submenu && submenu.classList.contains('mobile-dropdown-menu')) {
                        submenu.classList.toggle('open');
                        const icon = this.querySelector('i.fa-chevron-down, i.fa-chevron-up');
                        if(icon) {
                            if(submenu.classList.contains('open')) {
                                icon.classList.remove('fa-chevron-down');
                                icon.classList.add('fa-chevron-up');
                            } else {
                                icon.classList.remove('fa-chevron-up');
                                icon.classList.add('fa-chevron-down');
                            }
                        }
                    }
                });
            });

            // Hero Slider Logic
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.dot');
            if(slides.length > 1) {
                let currentSlide = 0;
                let slideInterval;

                window.showSlide = function(index) {
                    slides.forEach(s => s.classList.remove('active'));
                    dots.forEach(d => d.classList.remove('active'));
                    currentSlide = index;
                    slides[currentSlide].classList.add('active');
                    if(dots[currentSlide]) dots[currentSlide].classList.add('active');
                };

                function nextSlide() {
                    showSlide((currentSlide + 1) % slides.length);
                }

                window.goToSlide = function(index) {
                    showSlide(index);
                    clearInterval(slideInterval);
                    slideInterval = setInterval(nextSlide, 5000);
                };

                slideInterval = setInterval(nextSlide, 5000);
            }
        });
    </script>
</body>
</html>