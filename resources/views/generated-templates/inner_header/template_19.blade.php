@php
    $settings = \App\Models\SiteSetting::query()->latest('id')->first();
    $logoMedia = $settings?->logoMedia;
    $logoUrl = $logoMedia && !empty($logoMedia->file) ? asset('storage/' . $logoMedia->file) : null;

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

    $primary = $settings?->primary_color ?: '#127962';
    $secondary = $settings?->secondary_color ?: '#0d5948';
@endphp

<style>
    .inner-custom-header{
        background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }});
        color:#fff;
        padding:0;
        margin:0 !important;
        box-shadow:0 10px 24px rgba(15,23,42,.10);
    }

    .inner-custom-header .inner-brand{
        display:flex;
        align-items:center;
        gap:14px;
        text-decoration:none;
        color:#fff;
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
        margin:0 !important;
        font-size:1.4rem;
        font-weight:800;
        color:#fff;
    }

    .inner-custom-header .inner-brand-subtitle{
        margin:4px 0 0;
        color:rgba(255,255,255,.85);
        font-size:.95rem;
    }

    .inner-custom-header .inner-top{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:18px;
        flex-wrap:wrap;
        padding-bottom:0;
        border-bottom:1px solid rgba(255,255,255,.14);
    }

    .inner-custom-header .inner-actions{
        display:flex;
        gap:10px;
        flex-wrap:wrap;
    }

    .inner-custom-header .inner-btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:11px 18px;
        border-radius:14px;
        text-decoration:none;
        font-weight:700;
        border:1px solid rgba(255,255,255,.2);
    }

    .inner-custom-header .inner-btn--light{
        background:#fff;
        color:{{ $primary }};
    }

    .inner-custom-header .inner-btn--ghost{
        background:rgba(255,255,255,.10);
        color:#fff;
    }

    .inner-custom-header .site-nav,
    .inner-custom-header .site-subnav{
        list-style:none;
        margin:0 !important;
        padding:0;
    }

    .inner-custom-header .site-nav{
        display:flex;
        align-items:center;
        gap:0;
        margin-top:0;
        flex-wrap:wrap;
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
        font-size:1rem;
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
    }

    .inner-custom-header .site-subnav .site-nav-link{
        color:#1f2937 !important;
        padding:11px 20px;
        font-weight:600;
        font-size:.95rem;
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
        .inner-custom-header .site-nav{
            display:flex;
            flex-direction:column;
            align-items:stretch;
            width:100%;
            gap:6px;
        }

        .inner-custom-header .site-nav-link{
            width:100%;
            padding:14px 12px;
            border-radius:12px;
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
    <div class="container">
        <div class="inner-top">
            <a href="/" class="inner-brand">
                <div class="inner-logo">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $association->name ?? 'الشعار' }}">
                    @else
                        <span style="font-size:1.4rem;font-weight:800;">{{ mb_substr($association->name ?? 'ج', 0, 1) }}</span>
                    @endif
                </div>

                <div>
                    <h2 class="inner-brand-title">{{ $association->name ?? 'جمعية رضوان الخيرية' }}</h2>
                    <p class="inner-brand-subtitle">جمعية مصرحه من وزارة الموارد البشرية والتنمية الاجتماعية</p>
                </div>
            </a>

            <div class="inner-actions">
                <a href="/" class="inner-btn inner-btn--ghost">الرئيسية</a>
                @if(!empty($settings?->beneficiary_register_url))
                    <a href="{{ $settings->beneficiary_register_url }}" class="inner-btn inner-btn--light">تسجيل مستفيد</a>
                @endif
            </div>
        </div>

        @if($mainMenuItems->count())
            @include('themes.default.partials.navigation-tree', ['items' => $mainMenuItems])
        @endif
    </div>
</header>


<script>
document.addEventListener('DOMContentLoaded', function () {
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