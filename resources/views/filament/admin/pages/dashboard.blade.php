@php
    $primary = data_get($siteSettings ?? null, 'primary_color') ?: '#0f766e';
    $secondary = data_get($siteSettings ?? null, 'secondary_color') ?: '#ecfeff';
    $button = data_get($siteSettings ?? null, 'button_color') ?: $primary;
    $siteName = data_get($siteSettings ?? null, 'association_name') ?: data_get($siteSettings ?? null, 'site_name') ?: 'جمعيتكم';
@endphp

<x-filament-panels::page>
    <style>
        .khaier-dashboard {
            direction: rtl;
        }

        .khaier-hero {
            background: linear-gradient(135deg, {{ $primary }} 0%, #0f172a 100%);
            border-radius: 24px;
            padding: 32px 28px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .12);
            margin-bottom: 24px;
        }

        .khaier-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top left, rgba(255,255,255,.18), transparent 26%),
                radial-gradient(circle at bottom right, rgba(255,255,255,.10), transparent 22%);
            pointer-events: none;
        }

        .khaier-hero-content {
            position: relative;
            z-index: 1;
        }

        .khaier-hero h1 {
            margin: 0 0 10px;
            font-size: 32px;
            font-weight: 800;
            color: #fff;
        }

        .khaier-hero p {
            margin: 0;
            font-size: 15px;
            line-height: 1.9;
            color: rgba(255,255,255,.92);
            max-width: 780px;
        }

        .khaier-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .khaier-stat-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            padding: 22px 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .khaier-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, .09);
        }

        .khaier-stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .khaier-stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: {{ $secondary }};
        }

        .khaier-stat-value {
            font-size: 30px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .khaier-stat-label {
            font-size: 15px;
            font-weight: 700;
            color: #4b5563;
        }

        .khaier-sections {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 20px;
        }

        .khaier-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        }

        .khaier-card h2 {
            margin: 0 0 18px;
            font-size: 22px;
            font-weight: 800;
            color: #111827;
        }

        .khaier-quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
        }

        .khaier-link {
            display: block;
            text-decoration: none;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 18px 16px;
            transition: all .2s ease;
            color: #111827;
        }

        .khaier-link:hover {
            border-color: {{ $button }};
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, .07);
        }

        .khaier-link-title {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .khaier-link-sub {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.7;
        }

        .khaier-info-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .khaier-info-item {
            padding: 14px 16px;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid #eef2f7;
        }

        .khaier-info-item strong {
            display: block;
            margin-bottom: 4px;
            color: #111827;
            font-size: 14px;
        }

        .khaier-info-item span {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.8;
        }

        @media (max-width: 900px) {
            .khaier-sections {
                grid-template-columns: 1fr;
            }

            .khaier-hero h1 {
                font-size: 26px;
            }
        }
    </style>

    <div class="khaier-dashboard">
        <div class="khaier-hero">
            <div class="khaier-hero-content">
                <h1>مرحبًا بك في لوحة تحكم {{ $siteName }}</h1>
                <p>
                    من هنا يمكنك إدارة المحتوى والتصاميم والصفحات والأخبار والمشاريع والخدمات بسهولة،
                    ومتابعة حالة الموقع والبيانات الأساسية بشكل سريع وواضح.
                </p>
            </div>
        </div>

        <div class="khaier-stats">
            @foreach($this->stats as $stat)
                <div class="khaier-stat-card">
                    <div class="khaier-stat-top">
                        <div class="khaier-stat-icon">{{ $stat['icon'] }}</div>
                        <div class="khaier-stat-value">{{ number_format($stat['value']) }}</div>
                    </div>
                    <div class="khaier-stat-label">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="khaier-sections">
            <div class="khaier-card">
                <h2>روابط سريعة</h2>
                <div class="khaier-quick-links">
                    <a href="{{ url('/admin/news') }}" class="khaier-link">
                        <div class="khaier-link-title">إدارة الأخبار</div>
                        <div class="khaier-link-sub">إضافة الأخبار وتعديلها ونشرها</div>
                    </a>

                    <a href="{{ url('/admin/pages') }}" class="khaier-link">
                        <div class="khaier-link-title">إدارة الصفحات</div>
                        <div class="khaier-link-sub">الصفحات الداخلية والصفحات النظامية</div>
                    </a>

                    <a href="{{ url('/admin/program-projects') }}" class="khaier-link">
                        <div class="khaier-link-title">إدارة المشاريع</div>
                        <div class="khaier-link-sub">إضافة المشاريع وتحديث بياناتها</div>
                    </a>

                    <a href="{{ url('/admin/site-settings/2/edit') }}" class="khaier-link">
                        <div class="khaier-link-title">إعدادات الموقع</div>
                        <div class="khaier-link-sub">الهوية والألوان والتصاميم</div>
                    </a>
                </div>
            </div>

            <div class="khaier-card">
                <h2>معلومات سريعة</h2>
                <div class="khaier-info-list">
                    <div class="khaier-info-item">
                        <strong>اسم الجمعية</strong>
                        <span>{{ data_get($siteSettings ?? null, 'association_name') ?: 'غير محدد' }}</span>
                    </div>

                    <div class="khaier-info-item">
                        <strong>اسم الموقع</strong>
                        <span>{{ data_get($siteSettings ?? null, 'site_name') ?: 'غير محدد' }}</span>
                    </div>

                    <div class="khaier-info-item">
                        <strong>البريد الإلكتروني</strong>
                        <span>{{ data_get($siteSettings ?? null, 'email') ?: 'غير محدد' }}</span>
                    </div>

                    <div class="khaier-info-item">
                        <strong>رقم الهاتف</strong>
                        <span>{{ data_get($siteSettings ?? null, 'phone') ?: 'غير محدد' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
