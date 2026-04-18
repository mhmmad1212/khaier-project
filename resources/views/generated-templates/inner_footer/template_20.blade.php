<div dir="rtl" style="direction: rtl; text-align: right;">
@php
    $siteSettings = $siteSettings ?? \Illuminate\Support\Facades\DB::connection('tenant')->table('site_settings')->orderByDesc('id')->first();

    $siteName = $siteSettings->site_name ?? $siteSettings->association_name ?? 'اسم الجمعية';
    $siteDescription = $siteSettings->site_description ?? 'منصة تعريفية لعرض محتوى الجمعية وصفحاتها الداخلية بشكل منظم واحترافي.';
    $phone = $siteSettings->phone ?? $siteSettings->official_phone ?? null;
    $email = $siteSettings->email ?? $siteSettings->official_email ?? null;
    $address = $siteSettings->address ?? null;
    $licenseNumber = $siteSettings->license_number ?? null;

    $twitter = $siteSettings->twitter_url ?? null;
    $x = $siteSettings->x_url ?? null;
    $facebook = $siteSettings->facebook_url ?? null;
    $instagram = $siteSettings->instagram_url ?? null;
    $youtube = $siteSettings->youtube_url ?? null;
    $linkedin = $siteSettings->linkedin_url ?? null;
    $telegram = $siteSettings->telegram_url ?? null;
    $tiktok = $siteSettings->tiktok_url ?? null;
    $snapchat = $siteSettings->snapchat_url ?? null;
    $whatsapp = $siteSettings->whatsapp_url ?? null;

    $primary = $siteSettings->primary_color ?? '#127962';

    $socialLinks = array_filter([
        [
            'url' => $x ?: $twitter,
            'label' => 'X',
            'icon' => 'fa-brands fa-x-twitter',
        ],
        [
            'url' => $facebook,
            'label' => 'Facebook',
            'icon' => 'fa-brands fa-facebook-f',
        ],
        [
            'url' => $instagram,
            'label' => 'Instagram',
            'icon' => 'fa-brands fa-instagram',
        ],
        [
            'url' => $youtube,
            'label' => 'YouTube',
            'icon' => 'fa-brands fa-youtube',
        ],
        [
            'url' => $linkedin,
            'label' => 'LinkedIn',
            'icon' => 'fa-brands fa-linkedin-in',
        ],
        [
            'url' => $telegram,
            'label' => 'Telegram',
            'icon' => 'fa-brands fa-telegram',
        ],
        [
            'url' => $tiktok,
            'label' => 'TikTok',
            'icon' => 'fa-brands fa-tiktok',
        ],
        [
            'url' => $snapchat,
            'label' => 'Snapchat',
            'icon' => 'fa-brands fa-snapchat',
        ],
        [
            'url' => $whatsapp,
            'label' => 'WhatsApp',
            'icon' => 'fa-brands fa-whatsapp',
        ],
    ], fn ($item) => !empty($item['url']));

    $importantLinks = array_filter([
        ['title' => 'الرئيسية', 'url' => url('/')],
        ['title' => 'الأخبار', 'url' => url('page/alakhbar')],
        ['title' => 'السياسات', 'url' => url('/page/alsyasat')],
        ['title' => 'اللوائح', 'url' => url('page/alloayh')],
        ['title' => 'التراخيص', 'url' => url('page/trakhys-algmaay')],
    ], fn ($item) => !empty($item['url']));

    $serviceLinks = array_filter([
        ['title' => 'بوابة الموظفين', 'url' => $siteSettings->beneficiary_portal_url ?? null],
        ['title' => 'دخول المستفيدين', 'url' => $siteSettings->beneficiary_login_url ?? null],
        ['title' => 'تسجيل مستفيد جديد', 'url' => $siteSettings->beneficiary_register_url ?? null],
        ['title' => 'المتجر', 'url' => $siteSettings->store_url ?? null],
    ], fn ($item) => !empty($item['url']));
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;500;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .inner-pro-footer,
    .inner-pro-footer *{
        direction: rtl;
        text-align: right;
        box-sizing: border-box;
        font-family: 'Noto Kufi Arabic', sans-serif;
    }

    .inner-pro-footer{
        margin-top: 60px;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.10), transparent 22%),
            linear-gradient(135deg, #123c35 0%, #0f766e 45%, #115e59 100%);
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .inner-pro-footer::before{
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at top right, rgba(18,121,98,.18), transparent 24%),
            radial-gradient(circle at bottom left, rgba(255,255,255,.06), transparent 20%);
        pointer-events: none;
    }

    .inner-pro-footer-container{
        max-width: 1280px;
        margin: 0 auto;
        padding: 48px 28px 18px;
        position: relative;
        z-index: 2;
    }

    .inner-pro-footer-grid{
        display: grid;
        grid-template-columns: 1.4fr 1fr 1fr 1fr;
        gap: 28px;
        margin-bottom: 28px;
        direction: rtl;
    }

    .inner-pro-footer-title{
        margin: 0 0 16px;
        font-size: 1rem;
        font-weight: 800;
        color: #ffffff;
    }

    .inner-pro-footer-text{
        color: rgba(255,255,255,.84);
        line-height: 2.1;
        font-size: .88rem;
    }

    .inner-pro-footer-links{
        display: grid;
        gap: 10px;
    }

    .inner-pro-footer-links a{
        color: rgba(255,255,255,.84);
        text-decoration: none;
        transition: .2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: .85rem;
    }

    .inner-pro-footer-links a::before{
        content: "\f104";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        font-size: .75rem;
        opacity: .9;
    }

    .inner-pro-footer-links a:hover{
        color: #ffffff;
        transform: translateX(-2px);
    }

    .inner-pro-footer-contact{
        display: grid;
        gap: 10px;
        color: rgba(255,255,255,.84);
        font-size: .85rem;
        line-height: 2;
    }

    .inner-pro-footer-contact-item{
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .inner-pro-footer-contact-item i{
        margin-top: 6px;
        color: rgba(255,255,255,.95);
        min-width: 16px;
    }

    .inner-pro-footer-social{
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 18px;
        direction: rtl;
    }

    .inner-pro-footer-social a{
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        background: rgba(255,255,255,.10);
        color: #fff;
        font-size: 1rem;
        transition: .2s ease;
        border: 1px solid rgba(255,255,255,.10);
    }

    .inner-pro-footer-social a:hover{
        background: {{ $primary }};
        border-color: {{ $primary }};
        transform: translateY(-2px);
    }

    .inner-pro-footer-badge{
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,.10);
        color: #fff;
        padding: 10px 14px;
        border-radius: 999px;
        font-weight: 700;
        margin-top: 18px;
        font-size: .82rem;
        border: 1px solid rgba(255,255,255,.10);
    }

    .inner-pro-footer-bottom{
        border-top: 1px solid rgba(255,255,255,.14);
        padding-top: 16px;
        display: flex;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        color: rgba(255,255,255,.75);
        font-size: .8rem;
        direction: rtl;
    }

    .inner-pro-footer-bottom a{
        color: #ffffff;
        text-decoration: none;
        font-weight: 700;
    }

    .inner-pro-footer-bottom a:hover{
        color: #d1fae5;
    }

    @media (max-width: 992px){
        .inner-pro-footer-container{
            padding-right: 18px;
            padding-left: 18px;
        }

        .inner-pro-footer-grid{
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 700px){
        .inner-pro-footer-grid{
            grid-template-columns: 1fr;
        }
    }
</style>

<footer class="inner-pro-footer">
    <div class="inner-pro-footer-container">
        <div class="inner-pro-footer-grid">
            <div>
                <h3 class="inner-pro-footer-title">{{ $siteName }}</h3>

                <div class="inner-pro-footer-text">
                    {{ $siteDescription }}
                </div>

                @if($licenseNumber)
                    <div class="inner-pro-footer-badge">
                        <i class="fa-solid fa-certificate"></i>
                        رقم الترخيص: {{ $licenseNumber }}
                    </div>
                @endif

                @if(count($socialLinks))
                    <div class="inner-pro-footer-social">
                        @foreach($socialLinks as $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}">
                                <i class="{{ $social['icon'] }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h3 class="inner-pro-footer-title">روابط مهمة</h3>
                <div class="inner-pro-footer-links">
                    @foreach($importantLinks as $link)
                        <a href="{{ $link['url'] }}">{{ $link['title'] }}</a>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="inner-pro-footer-title">الخدمات والبوابات</h3>
                <div class="inner-pro-footer-links">
                    @forelse($serviceLinks as $link)
                        <a href="{{ $link['url'] }}">{{ $link['title'] }}</a>
                    @empty
                        <span class="inner-pro-footer-text">لا توجد روابط مضافة حالياً.</span>
                    @endforelse
                </div>
            </div>

            <div>
                <h3 class="inner-pro-footer-title">تواصل معنا</h3>
                <div class="inner-pro-footer-contact">
                    @if($phone)
                        <div class="inner-pro-footer-contact-item">
                            <i class="fa-solid fa-phone"></i>
                            <span>الهاتف: {{ $phone }}</span>
                        </div>
                    @endif

                    @if($email)
                        <div class="inner-pro-footer-contact-item">
                            <i class="fa-solid fa-envelope"></i>
                            <span>البريد الإلكتروني: {{ $email }}</span>
                        </div>
                    @endif

                    @if($address)
                        <div class="inner-pro-footer-contact-item">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>العنوان: {{ $address }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="inner-pro-footer-bottom">
            <div>
                © {{ now()->year }} {{ $siteName }}. جميع الحقوق محفوظة.
            </div>
            <div>
                تم التطوير والتجهيز عبر <a href="#">نظام خير</a>
            </div>
        </div>
    </div>
</footer>
</div>