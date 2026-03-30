@php
    // جلب البيانات بالقوة الجبرية من قاعدة بيانات الجمعية
    $connection = \Illuminate\Support\Facades\DB::connection('tenant');
    
    // 1. جلب الإعدادات العامة
    $settings = $connection->table('site_settings')->orderBy('id', 'desc')->first();
    
    $aboutText = $settings->about_text ?? 'نحن نسعى لتقديم أفضل الخدمات المجتمعية والخيرية التي تهدف إلى الارتقاء بالمجتمع وتوفير بيئة داعمة ومستدامة.';
    $videoUrl = $settings->intro_video_url ?? null;
    $siteName = $settings->site_name ?? 'الجمعية';
    $assocName = $settings->association_name ?? $siteName;
    $primaryColor = $settings->primary_color ?? '#ea580c';
    $secondaryColor = $settings->secondary_color ?? '#fdba74';
    $address = $settings->address ?? 'المملكة العربية السعودية';
    $phone = $settings->phone ?? ($settings->official_phone ?? 'غير متوفر');
    $email = $settings->email ?? ($settings->official_email ?? 'غير متوفر');
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

    // 3. جلب البرامج والمشاريع من الجدول الصحيح (program_projects)
    $projects = collect();
    try {
        $projects = $connection->table('program_projects')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'asc')
            ->limit(4)
            ->get();
    } catch (\Exception $e) {
        // تجاهل الأخطاء الصامتة
    }

    // دالة مساعدة لتجهيز الروابط
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

    // دالة بناء القوائم اللانهائية
    if (!function_exists('buildInfiniteMenu')) {
        function buildInfiniteMenu($items, $groupedItems, $connection, $isMobile, $level = 1) {
            $html = '';
            foreach ($items as $item) {
                $hasChildren = $groupedItems->has($item->id);
                
                // 🛑 التعديل هنا: إذا كان "أب" نعطل الرابط تماماً، وإذا كان "ابن" نعطيه رابطه الحقيقي 🛑
                $url = $hasChildren ? 'javascript:void(0);' : resolveMenuUrl($item, $connection);
                
                $iconHtml = '';
                if (!empty($item->icon)) {
                    if (str_contains($item->icon, 'heroicon')) {
                        $iconHtml = \Illuminate\Support\Facades\Blade::render('<x-filament::icon icon="'.$item->icon.'" style="width: 1.2rem; height: 1.2rem; display: inline-block; margin-left: 8px; vertical-align: middle; color: var(--primary);" />');
                    } else {
                        $iconHtml = '<i class="'.$item->icon.'" style="margin-left: 8px; color: var(--primary); font-size: 1.1rem;"></i>';
                    }
                }

                if ($isMobile) {
                    $html .= '<li>';
                    $html .= '<a href="'.$url.'" class="'.($hasChildren ? 'mobile-dropdown-toggle' : '').'" target="'.($item->target ?? '_self').'">';
                    $html .= '<div style="display:flex; align-items:center;">' . $iconHtml . $item->title . '</div>';
                    if ($hasChildren) $html .= '<i class="fas fa-chevron-down" style="font-size:0.8rem; color: var(--text-muted);"></i>';
                    $html .= '</a>';
                    if ($hasChildren) {
                        $html .= '<ul class="mobile-dropdown-menu" style="padding-right: 15px;">';
                        $html .= buildInfiniteMenu($groupedItems->get($item->id), $groupedItems, $connection, true, $level + 1);
                        $html .= '</ul>';
                    }
                    $html .= '</li>';
                } else {
                    $liClass = $hasChildren ? 'has-dropdown' : '';
                    $html .= '<li class="'.$liClass.'">';
                    // منع المؤشر من إظهار يد (Click) إذا كان الرابط معطل واختياري إضافة كلاس
                    $html .= '<a href="'.$url.'" target="'.($item->target ?? '_self').'" style="'.($hasChildren ? 'cursor: default;' : '').'">';
                    $html .= '<div style="display:flex; align-items:center;">' . $iconHtml . $item->title . '</div>';
                    if ($hasChildren) {
                        $arrowClass = $level === 1 ? 'fa-chevron-down' : 'fa-chevron-left';
                        $html .= '<i class="fas '.$arrowClass.'" style="font-size:0.7rem; color:var(--text-muted);"></i>';
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
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: {{ $primaryColor }};
            --secondary: {{ $secondaryColor }};
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --border-radius: 16px;
            --transition: all 0.3s ease-in-out;
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 10px 25px -3px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 40px -5px rgba(0, 0, 0, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Tajawal', sans-serif; }
        body { background-color: var(--bg-light); color: var(--text-dark); line-height: 1.6; overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }
        ul { list-style: none; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        
        .section-title { text-align: center; margin-bottom: 50px; }
        .section-title h2 { font-size: 2.2rem; color: var(--primary); font-weight: 800; margin-bottom: 10px; position: relative; display: inline-block; }
        .section-title h2::after { content: ''; position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 60px; height: 4px; background-color: var(--secondary); border-radius: 2px; }

        /* =========================================
           Header
           ========================================= */
        header { position: absolute; top: 25px; left: 0; width: 100%; z-index: 1000; }
        .navbar { display: flex; justify-content: space-between; align-items: center; height: 75px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 50px 0 0 50px; padding: 0; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 95%; max-width: 1300px; margin: 0 auto; position: relative; }
        .header-right { width: 180px; height: 100%; position: relative; }
        .logo-card { position: absolute; top: -25px; right: 0; width: 100%; background: #ffffff; padding: 15px 20px 25px; border-radius: 0 0 25px 25px; box-shadow: -5px 15px 25px rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: center; z-index: 1001; transition: var(--transition); }
        .logo-card:hover { padding-bottom: 35px; }
        .logo-card img { max-height: 80px; object-fit: contain; }
        
        .header-center { flex: 1; display: flex; justify-content: center; }
        .desktop-menu { display: flex; gap: 20px; align-items: center; margin: 0; }
        .desktop-menu > li { position: relative; }
        .desktop-menu a { display: flex; align-items: center; justify-content: space-between; font-weight: 700; color: var(--text-dark); transition: var(--transition); font-size: 1.05rem; position: relative; padding: 5px 0;}
        .desktop-menu > li > a:hover { color: var(--primary); }
        .desktop-menu > li > a::after { content: ''; position: absolute; bottom: -5px; right: 0; width: 0; height: 3px; background: var(--secondary); transition: var(--transition); border-radius: 2px;}
        .desktop-menu > li > a:hover::after { width: 100%; left: 0; }

        .has-dropdown { position: relative; }
        .dropdown-menu { position: absolute; top: 100%; right: 0; background: #ffffff; min-width: 240px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); border-radius: 15px; padding: 10px 0; opacity: 0; visibility: hidden; transform: translateY(15px); transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55); z-index: 1000; border: 1px solid #f1f5f9; }
        .dropdown-menu.sub-menu { top: 0; right: 100%; margin-right: 5px; transform: translateX(15px); }
        .has-dropdown:hover > .dropdown-menu { opacity: 1; visibility: visible; transform: translate(0, 0); }
        .dropdown-menu li { width: 100%; border-bottom: 1px solid #f8fafc; position: relative;}
        .dropdown-menu li:last-child { border-bottom: none; }
        .dropdown-menu a { display: flex; justify-content: space-between; padding: 12px 20px; color: var(--text-dark); font-size: 0.95rem !important; font-weight: 600 !important; cursor: pointer !important; }
        .dropdown-menu a::after { display: none !important; } 
        .dropdown-menu a:hover { background: #f8fafc; color: var(--primary); padding-right: 25px; }
        
        .header-left { padding-left: 15px; display: flex; align-items: center; }
        .btn-donate { background: linear-gradient(45deg, var(--primary), var(--secondary)); color: white !important; padding: 12px 30px; border-radius: 40px; font-weight: 800; box-shadow: 0 4px 15px rgba(234, 88, 12, 0.3); transition: var(--transition); display: flex; align-items: center; white-space: nowrap;}
        .btn-donate:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(234, 88, 12, 0.4); }
        .mobile-menu-btn { display: none; background: transparent; border: none; font-size: 1.8rem; color: var(--primary); cursor: pointer; transition: var(--transition); margin-right: auto;}
        .mobile-menu-btn:hover { color: var(--secondary); }
        .mobile-menu { display: none; }

        /* =========================================
           Animated Hero Slider
           ========================================= */
        .hero-slider { position: relative; height: 100vh; min-height: 600px; overflow: hidden; background-color: var(--text-dark); }
        .slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 1s ease-in-out; display: flex; align-items: center; justify-content: center; background-size: cover; background-position: center center; z-index: 0; }
        .slide.active { opacity: 1; z-index: 1; }
        .slide::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.6) 100%); z-index: 1; }
        .hero-content { position: relative; z-index: 2; text-align: center; color: white; max-width: 800px; padding: 100px 20px 0; transform: translateY(20px); opacity: 0; transition: all 1s ease-in-out 0.5s; }
        .slide.active .hero-content { transform: translateY(0); opacity: 1; }
        .hero-content h1 { font-size: 4rem; font-weight: 800; margin-bottom: 20px; text-shadow: 2px 4px 15px rgba(0,0,0,0.6); }
        .hero-content p { font-size: 1.5rem; margin-bottom: 35px; text-shadow: 1px 2px 8px rgba(0,0,0,0.6); }
        .hero-btn { display: inline-block; background-color: var(--secondary); color: white; padding: 15px 45px; font-size: 1.2rem; font-weight: 800; border-radius: 50px; transition: var(--transition); border: 2px solid transparent; box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
        .hero-btn:hover { background-color: transparent; border-color: var(--secondary); color: var(--secondary); transform: translateY(-5px); }
        .slider-dots { position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%); display: flex; gap: 15px; z-index: 10; }
        .dot { width: 15px; height: 15px; border-radius: 50%; background-color: rgba(255,255,255,0.3); cursor: pointer; transition: var(--transition); border: 2px solid transparent; backdrop-filter: blur(5px);}
        .dot:hover { background-color: rgba(255,255,255,0.8); }
        .dot.active { background-color: var(--primary); border-color: white; transform: scale(1.4); }

        /* =========================================
           Sections
           ========================================= */
        .about-section { padding: 100px 0; background-color: var(--bg-white); }
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center; }
        .about-text-box { padding: 50px; border: 2px dashed var(--primary); border-radius: 30px; background-color: var(--bg-light); position: relative; box-shadow: var(--shadow-sm); }
        .about-text-box h3 { color: var(--primary); font-size: 2.2rem; margin-bottom: 25px; }
        .about-text-box p { color: var(--text-muted); font-size: 1.15rem; text-align: justify; white-space: pre-line; line-height: 1.8;}
        .video-box { border-radius: 30px; overflow: hidden; box-shadow: var(--shadow-lg); border: 5px solid white; position: relative; padding-bottom: 56.25%; height: 0; }
        .video-box iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }

        .stats-section { padding: 100px 0; background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), url('https://www.edarabia.com/ar/wp-content/uploads/2019/12/zakat-islamic-law.jpg') center/cover fixed; color: white; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; }
        .stat-card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.1); padding: 45px 20px; border-radius: 25px; text-align: center; transition: var(--transition); }
        .stat-card:hover { transform: translateY(-10px); background: rgba(255, 255, 255, 0.15); border-color: var(--secondary); box-shadow: 0 15px 35px rgba(0,0,0,0.5); }
        .stat-number { font-size: 3.8rem; font-weight: 800; margin-bottom: 10px; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
        .stat-title { font-size: 1.3rem; font-weight: 500; color: #cbd5e1; }

        .news-section { padding: 100px 0; background-color: var(--bg-light); }
        .news-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 35px; margin-bottom: 50px; }
        .news-card { background-color: var(--bg-white); border-radius: 25px; overflow: hidden; box-shadow: var(--shadow-md); transition: var(--transition); border-bottom: 5px solid transparent; }
        .news-card:hover { transform: translateY(-15px); box-shadow: var(--shadow-lg); border-bottom-color: var(--secondary); }
        .news-img { width: 100%; height: 240px; object-fit: cover; }
        .news-content { padding: 30px; }
        .news-date { color: var(--secondary); font-size: 0.95rem; font-weight: 700; margin-bottom: 12px; display: block; }
        .news-title { font-size: 1.4rem; color: var(--primary); margin-bottom: 15px; font-weight: 800; }
        .news-excerpt { color: var(--text-muted); margin-bottom: 25px; font-size: 1rem; line-height: 1.7;}
        .news-link { display: inline-block; color: var(--primary); font-weight: 800; transition: var(--transition); }
        .news-link:hover { color: var(--secondary); padding-right: 5px;}
        .btn-view-all { display: block; width: fit-content; margin: 0 auto; background-color: var(--primary); color: white; padding: 15px 40px; border-radius: 50px; font-weight: 800; font-size: 1.15rem; transition: var(--transition); box-shadow: 0 10px 20px rgba(234,88,12,0.2);}
        .btn-view-all:hover { background-color: var(--secondary); transform: translateY(-4px); box-shadow: 0 12px 25px rgba(234,88,12,0.4);}

        /* =========================================
           Projects Section
           ========================================= */
        .projects-section { padding: 100px 0; background-color: var(--bg-white); }
        .projects-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 35px; margin-bottom: 50px; }
        .project-card { background-color: var(--bg-light); border-radius: 25px; overflow: hidden; box-shadow: var(--shadow-sm); transition: var(--transition); border: 1px solid #f1f5f9; display: flex; flex-direction: column;}
        .project-card:hover { transform: translateY(-15px); box-shadow: var(--shadow-lg); border-color: var(--secondary); }
        .project-img-wrapper { position: relative; width: 100%; height: 220px; }
        .project-img { width: 100%; height: 100%; object-fit: cover; }
        .project-badge { position: absolute; top: 15px; right: 15px; background: var(--primary); color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; z-index: 1; box-shadow: 0 4px 10px rgba(0,0,0,0.2);}
        .project-content { padding: 25px; flex-grow: 1; display: flex; flex-direction: column; }
        .project-title { font-size: 1.35rem; color: var(--primary); margin-bottom: 20px; font-weight: 800; line-height: 1.4;}
        .project-meta { display: flex; flex-direction: column; gap: 12px; margin-bottom: 25px; color: var(--text-muted); font-size: 0.95rem; font-weight: 500;}
        .project-meta div { display: flex; align-items: center; gap: 12px; }
        .project-meta i { color: var(--secondary); width: 20px; text-align: center; font-size: 1.1rem;}
        .project-amount { font-weight: 800; color: var(--primary); font-size: 1.2rem; margin-right: 5px;}
        .progress-wrapper { background-color: #e2e8f0; border-radius: 10px; height: 10px; width: 100%; overflow: hidden; margin-top: auto; margin-bottom: 8px;}
        .progress-bar { background: linear-gradient(90deg, var(--secondary), var(--primary)); height: 100%; border-radius: 10px; transition: width 1s ease-in-out; }
        .progress-text { display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 20px;}
        .project-btn { margin-top: auto; display: block; text-align: center; background-color: transparent; color: var(--primary); padding: 12px; border-radius: 15px; font-weight: 800; transition: var(--transition); border: 2px solid var(--primary); }
        .project-btn:hover { background-color: var(--primary); color: white; }

        /* =========================================
           Partners Section 
           ========================================= */
        .partners-section { padding: 80px 0; background-color: var(--bg-light); border-top: 1px solid #e2e8f0; }
        .marquee-wrapper { overflow: hidden; position: relative; width: 100%; padding: 20px 0; }
        .marquee-content { display: flex; gap: 40px; animation: marquee 25s linear infinite; width: max-content; }
        .partner-box { width: 160px; height: 110px; display: flex; align-items: center; justify-content: center; border: 2px solid #f1f5f9; border-radius: 20px; padding: 20px; background-color: var(--bg-white); transition: var(--transition); box-shadow: var(--shadow-sm);}
        .partner-box:hover { border-color: var(--secondary); box-shadow: var(--shadow-md); transform: translateY(-5px);}
        .partner-box img { max-width: 100%; max-height: 100%; object-fit: contain; transition: var(--transition); }
        .partner-box:hover img { transform: scale(1.05); }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(50%); } }

        /* =========================================
           Footer
           ========================================= */
        footer { background-color: var(--text-dark); color: white; padding: 80px 0 30px; position: relative; }
        footer::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 6px; background: linear-gradient(to right, var(--primary), var(--secondary)); }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 50px; margin-bottom: 50px; }
        img.footer-logo-img { display: block !important; width: auto !important; max-width: 160px !important; height: 65px !important; object-fit: contain !important; background-color: #ffffff !important; padding: 10px 20px !important; border-radius: 12px !important; margin-bottom: 25px !important; box-shadow: 0 5px 15px rgba(0,0,0,0.3) !important;}
        .footer-title { font-size: 1.3rem; font-weight: 800; color: var(--secondary); margin-bottom: 25px; position: relative; padding-bottom: 10px;}
        .footer-title::after { content: ''; position: absolute; bottom: 0; right: 0; width: 40px; height: 3px; background: var(--primary); border-radius: 2px;}
        .footer-links li { margin-bottom: 15px; }
        .footer-links a { color: #cbd5e1; transition: var(--transition); font-size: 1.05rem;}
        .footer-links a:hover { color: var(--secondary); padding-right: 10px; }
        .footer-contact li { display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px; color: #cbd5e1; font-size: 1.05rem;}
        .footer-contact i { color: var(--secondary); margin-top: 5px; font-size: 1.2rem;}
        .footer-bottom { text-align: center; padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); color: #94a3b8; font-size: 1rem; }

        /* =========================================
           Mobile Styles
           ========================================= */
        @media (max-width: 992px) {
            .header-center { display: none; } 
            .desktop-btn { display: none !important; }
            .mobile-menu-btn { display: block; margin-right: 15px;}
            .navbar { border-radius: 30px 0 0 30px; } 
            .header-right { width: 130px; }
            .logo-card { padding: 15px; border-radius: 0 0 20px 20px;}
            .logo-card img { max-height: 60px; }

            .mobile-menu { display: none; flex-direction: column; position: absolute; top: 90px; left: 5%; right: 5%; width: 90%; background: rgba(255,255,255,0.98); box-shadow: 0 15px 30px rgba(0,0,0,0.15); padding: 10px 0; gap: 0; border-radius: 20px; z-index: 1001; }
            .mobile-menu.active { display: flex; }
            .mobile-menu > li { width: 100%; border-bottom: 1px solid #f1f5f9; }
            .mobile-menu > li:last-child { border-bottom: none; }
            .mobile-menu a { display: flex; justify-content: space-between; align-items: center; padding: 15px 25px; width: 100%; text-align: right; color: var(--text-dark); font-weight: 700;}
            
            .mobile-dropdown-menu { display: none; background: #f8fafc; padding: 0; margin: 0; border-top: 1px solid #f1f5f9;}
            .mobile-dropdown-menu.open { display: block; }
            .mobile-dropdown-menu li { border-bottom: 1px solid #e2e8f0; }
            .mobile-dropdown-menu li:last-child { border-bottom: none; }
            .mobile-dropdown-menu a { padding: 12px 25px !important; font-size: 0.95rem !important; font-weight: 600 !important; color: var(--text-muted); }
            .mobile-dropdown-menu .mobile-dropdown-menu a { font-size: 0.9rem !important; font-weight: 500 !important; }
            .mobile-dropdown-menu a:hover { color: var(--primary); background: #f1f5f9; }
        }

        @media (max-width: 768px) { 
            header { top: 15px; }
            .navbar { height: 65px; border-radius: 25px 0 0 25px; padding-left: 10px;}
            .header-right { width: 100px; }
            .logo-card { top: -15px; padding: 10px; border-radius: 0 0 15px 15px; }
            .logo-card img { max-height: 50px; }
            .mobile-menu { top: 80px; }
            .hero-slider { height: 100vh; min-height: 500px; }
            .slide { background-size: cover; background-position: center; }
            .hero-content { padding-top: 120px; } 
            .hero-content h1 { font-size: 2.5rem; line-height: 1.3;}
            .hero-content p { font-size: 1.1rem; }
            .about-grid { grid-template-columns: 1fr; } 
        }
    </style>
</head>
<body>

    <header>
        <div class="navbar">
            <div class="header-right">
                <a href="/" class="logo-card">
                    @if($finalLogoUrl)
                        <img src="{{ $finalLogoUrl }}" alt="{{ $siteName }}">
                    @else
                        <h2 style="color: var(--primary); font-size: 1.1rem; margin:0; text-align:center;">{{ $siteName }}</h2>
                    @endif
                </a>
            </div>
            
            <div class="header-center">
                <ul class="desktop-menu">
                    @if($rootItems->count() > 0)
                        {!! buildInfiniteMenu($rootItems, $groupedItems, $connection, false) !!}
                    @else
                        <li><a href="/">الرئيسية</a></li>
                        <li><a href="#about">عن الجمعية</a></li>
                        <li><a href="/news">الأخبار</a></li>
                    @endif
                </ul>
            </div>

            <div class="header-left">
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="{{ $settings->beneficiary_portal_url ?? '#' }}" class="btn-donate desktop-btn">بوابة المستفيدين</a>
            </div>
        </div>

        <ul class="mobile-menu" id="navLinksMobile">
            @if($rootItems->count() > 0)
                {!! buildInfiniteMenu($rootItems, $groupedItems, $connection, true) !!}
            @else
                <li><a href="/">الرئيسية</a></li>
                <li><a href="#about">عن الجمعية</a></li>
                <li><a href="/news">الأخبار</a></li>
            @endif
        </ul>
    </header>

    <section class="hero-slider">
        @if(isset($sliders) && $sliders->count() > 0)
            @foreach($sliders as $index => $slider)
                <div class="slide {{ $index === 0 ? 'active' : '' }}" style="background-image: url('{{ asset('storage/' . $slider->image) }}');">
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
                    <h1>أهلاً بكم في موقعنا</h1>
                    <p>نسعى لتقديم أفضل الخدمات المجتمعية والخيرية بشفافية عالية.</p>
                </div>
            </div>
        @endif
    </section>

    <section id="about" class="about-section">
        <div class="container about-grid">
            <div class="about-text-box">
                <h3>عن {{ $assocName }}</h3>
                <p>{{ $aboutText }}</p>
            </div>
            <div class="video-container">
                @if($videoUrl)
                    @php
                        $embedUrl = $videoUrl;
                        if (str_contains($embedUrl, 'youtube.com/watch?v=')) {
                            $embedUrl = str_replace('watch?v=', 'embed/', $embedUrl);
                            $embedUrl = explode('&', $embedUrl)[0];
                        } elseif (str_contains($embedUrl, 'youtu.be/')) {
                            $embedUrl = str_replace('youtu.be/', 'youtube.com/embed/', $embedUrl);
                            $embedUrl = explode('?', $embedUrl)[0];
                        }
                    @endphp
                    <div class="video-box">
                        <iframe src="{{ $embedUrl }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                @else
                    <div class="video-box" style="display:flex; align-items:center; justify-content:center; background:#1e293b; color:white; flex-direction:column;">
                        <i class="fas fa-video fa-3x" style="color: var(--secondary); margin-bottom: 15px;"></i>
                        <p>الفيديو التعريفي غير متوفر حالياً</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="container">
            <div class="section-title">
                <h2 style="color: white; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">إحصائيات وإنجازات</h2>
            </div>
            <div class="stats-grid">
                @if(isset($statistics) && $statistics->count() > 0)
                    @foreach($statistics as $stat)
                        <div class="stat-card">
                            <div style="margin-bottom: 20px; display: flex; justify-content: center;">
                                @if($stat->icon && str_contains($stat->icon, 'heroicon'))
                                    <x-filament::icon :icon="$stat->icon" style="width: 4rem; height: 4rem; color: var(--secondary); filter: drop-shadow(2px 4px 6px rgba(0,0,0,0.5));" />
                                @else
                                    <i class="{{ $stat->icon ?? 'fas fa-chart-bar' }}" style="font-size: 4rem; color: var(--secondary); text-shadow: 2px 4px 6px rgba(0,0,0,0.5);"></i>
                                @endif
                            </div>
                            <div class="stat-number">{{ $stat->value }}</div>
                            <div class="stat-title">{{ $stat->title }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="stat-card">
                        <i class="fas fa-users stat-icon" style="font-size: 4rem; color: var(--secondary); margin-bottom: 20px; display:block;"></i>
                        <div class="stat-number">0</div>
                        <div class="stat-title">مستفيد مسجل</div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section id="news" class="news-section">
        <div class="container">
            <div class="section-title">
                <h2>أحدث الأخبار</h2>
            </div>
            <div class="news-grid">
                @if(isset($news) && $news->count() > 0)
                    @foreach($news as $item)
                        <div class="news-card">
                            <img src="{{ $item->image ? asset('storage/' . $item->image) : 'https://via.placeholder.com/400x250' }}" alt="{{ $item->title }}" class="news-img">
                            <div class="news-content">
                                <span class="news-date">{{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('Y-m-d') : '' }}</span>
                                <h3 class="news-title">{{ $item->title }}</h3>
                                <p class="news-excerpt">{{ Str::limit(strip_tags($item->excerpt ?? $item->content), 100) }}</p>
                                <a href="/news/{{ $item->slug ?? $item->id }}" class="news-link">اقرأ المزيد <i class="fas fa-arrow-left"></i></a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="text-align: center; color: var(--text-muted); grid-column: 1 / -1;">لا توجد أخبار مضافة حالياً.</p>
                @endif
            </div>
            <a href="/news" class="btn-view-all">عرض كل الأخبار</a>
        </div>
    </section>

    <section id="projects" class="projects-section">
        <div class="container">
            <div class="section-title">
                <h2>البرامج والمشاريع</h2>
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
                            $imgUrl = $img ? asset('storage/' . $img) : 'https://via.placeholder.com/400x250?text=مشروع+خيري';
                            
                            $target = $project->project_amount ?? 1;
                            $collected = $project->donation_amount ?? 0;
                            $percent = ($target > 0) ? min(100, round(($collected / $target) * 100)) : 0;
                            
                            $startDate = $project->start_date ?? null;
                            $endDate = $project->end_date ?? null;
                        @endphp
                        <div class="project-card">
                            <div class="project-img-wrapper">
                                <img src="{{ $imgUrl }}" alt="{{ $title }}" class="project-img">
                                <span class="project-badge">مشروع خيري</span>
                            </div>
                            
                            <div class="project-content">
                                <h3 class="project-title">{{ Str::limit($title, 50) }}</h3>
                                
                                <div class="project-meta">
                                    <div><i class="fas fa-hand-holding-usd"></i> <span>المبلغ المستهدف: <span class="project-amount">{{ number_format($target == 1 && $collected == 0 ? 0 : $target) }}</span> ريال</span></div>
                                    @if($startDate)
                                    <div><i class="fas fa-calendar-alt"></i> <span>البداية: {{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }}</span></div>
                                    @endif
                                    @if($endDate)
                                    <div><i class="fas fa-flag-checkered"></i> <span>النهاية: {{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}</span></div>
                                    @endif
                                </div>
                                
                                @if($target > 0 && $collected > 0)
                                    <div class="progress-wrapper">
                                        <div class="progress-bar" style="width: {{ $percent }}%;"></div>
                                    </div>
                                    <div class="progress-text">
                                        <span>تم جمع: {{ number_format($collected) }} ريال</span>
                                        <span style="color: var(--primary);">{{ $percent }}%</span>
                                    </div>
                                @endif
                                
                                <a href="{{ $project->donation_url ?? '/projects/'.($project->id ?? '#') }}" class="project-btn" target="_blank">تفاصيل المشروع</a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="text-align: center; color: var(--text-muted); grid-column: 1 / -1;">لا توجد برامج أو مشاريع مضافة حالياً.</p>
                @endif
            </div>
            @if(isset($projects) && $projects->count() > 0)
            <a href="/projects" class="btn-view-all">كافة المشاريع</a>
            @endif
        </div>
    </section>

    <section class="partners-section">
        <div class="container">
            <div class="section-title">
                <h2>شركاء النجاح</h2>
            </div>
            <div class="marquee-wrapper">
                <div class="marquee-content">
                    @if(isset($partners) && $partners->count() > 0)
                        @foreach($partners as $partner)
                            <div class="partner-box"><img src="{{ asset('storage/' . $partner->logo) }}" alt="{{ $partner->name }}"></div>
                        @endforeach
                        @foreach($partners as $partner)
                            <div class="partner-box"><img src="{{ asset('storage/' . $partner->logo) }}" alt="{{ $partner->name }}"></div>
                        @endforeach
                    @else
                        <div class="partner-box"><img src="https://via.placeholder.com/100x60?text=شريك" alt="شريك"></div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <footer id="contact">
        <div class="container">
            <div class="footer-grid">
                <div>
                    @if($finalLogoUrl)
                        <img src="{{ $finalLogoUrl }}" alt="الشعار" class="footer-logo-img">
                    @endif
                    <p style="color: #94a3b8; font-size: 1.05rem; line-height: 1.8;">{{ $desc }}</p>
                </div>
                <div>
                    <h4 class="footer-title">روابط هامة</h4>
                    <ul class="footer-links">
                        <li><a href="/">الرئيسية</a></li>
                        <li><a href="/news">الأخبار</a></li>
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
                جميع الحقوق محفوظة &copy; {{ date('Y') }} {{ $siteName }}
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            const mobileDropdownToggles = document.querySelectorAll('.mobile-dropdown-toggle');
            mobileDropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    // 🛑 هنا نمنع الرابط من الانتقال لصفحة ثانية إذا كان "أب" 🛑
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