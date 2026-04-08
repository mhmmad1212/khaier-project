<div dir="rtl" style="direction: rtl; text-align: right;">
@php
    $connection = \Illuminate\Support\Facades\DB::connection('tenant');

    $settings = $connection->table('site_settings')->orderBy('id', 'desc')->first();

    $siteName = $settings->site_name ?? 'جمعية الخير';
    $assocName = $settings->association_name ?? $siteName;

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
@endphp

<style>
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
});
</script>
</div>