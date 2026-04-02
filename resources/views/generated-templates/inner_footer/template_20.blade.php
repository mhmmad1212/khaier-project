<div dir="rtl" style="direction: rtl; text-align: right;">
@php
    $siteSettings = $siteSettings ?? \Illuminate\Support\Facades\DB::connection('tenant')->table('site_settings')->orderByDesc('id')->first();

    $siteName = $siteSettings->site_name ?? $siteSettings->association_name ?? 'اسم الجمعية';
    $siteDescription = $siteSettings->site_description ?? 'منصة تعريفية لعرض محتوى الجمعية وصفحاتها الداخلية بشكل منظم واحترافي.';
    $phone = $siteSettings->phone ?? null;
    $email = $siteSettings->email ?? null;
    $address = $siteSettings->address ?? null;
    $licenseNumber = $siteSettings->license_number ?? null;

    $twitter = $siteSettings->twitter_url ?? null;
    $instagram = $siteSettings->instagram_url ?? null;
    $youtube = $siteSettings->youtube_url ?? null;
    $tiktok = $siteSettings->tiktok_url ?? null;
    $snapchat = $siteSettings->snapchat_url ?? null;
    $whatsapp = $siteSettings->whatsapp_url ?? null;

    $primary = $siteSettings->primary_color ?? '#127962';
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;500;700;800&display=swap');

    .inner-pro-footer,
    .inner-pro-footer *{
        direction: rtl;
        text-align: right;
        box-sizing: border-box;
        font-family: 'Noto Kufi Arabic', sans-serif;
    }

    .inner-pro-footer{
        margin-top:60px;
        background:linear-gradient(135deg, #0f172a 0%, #111827 100%);
        color:#fff;
        position:relative;
        overflow:hidden;
    }

    .inner-pro-footer::before{
        content:"";
        position:absolute;
        inset:0;
        background:
            radial-gradient(circle at top right, rgba(18,121,98,.20), transparent 24%),
            radial-gradient(circle at bottom left, rgba(255,255,255,.05), transparent 20%);
        pointer-events:none;
    }

    .inner-pro-footer-container{
        max-width:1280px;
        margin:0 auto;
        padding:48px 28px 18px;
        position:relative;
        z-index:2;
    }

    .inner-pro-footer-grid{
        display:grid;
        grid-template-columns:1.4fr 1fr 1fr 1fr;
        gap:28px;
        margin-bottom:28px;
        direction: rtl;
    }

    .inner-pro-footer-title{
        margin:0 0 16px;
        font-size:1rem;
        font-weight:800;
        color:#fff;
    }

    .inner-pro-footer-text{
        color:rgba(255,255,255,.78);
        line-height:2.1;
        font-size:.88rem;
    }

    .inner-pro-footer-links{
        display:grid;
        gap:10px;
    }

    .inner-pro-footer-links a{
        color:rgba(255,255,255,.82);
        text-decoration:none;
        transition:.2s ease;
        display:inline-flex;
        align-items:center;
        gap:8px;
        font-size:.85rem;
    }

    .inner-pro-footer-links a:hover{
        color:#fff;
        transform:translateX(-2px);
    }

    .inner-pro-footer-contact{
        display:grid;
        gap:10px;
        color:rgba(255,255,255,.82);
        font-size:.85rem;
        line-height:2;
    }

    .inner-pro-footer-social{
        display:flex;
        gap:10px;
        flex-wrap:wrap;
        margin-top:18px;
        direction: rtl;
    }

    .inner-pro-footer-social a{
        width:42px;
        height:42px;
        border-radius:12px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        text-decoration:none;
        background:rgba(255,255,255,.08);
        color:#fff;
        font-size:1rem;
        transition:.2s ease;
        border:1px solid rgba(255,255,255,.08);
    }

    .inner-pro-footer-social a:hover{
        background:{{ $primary }};
        transform:translateY(-2px);
    }

    .inner-pro-footer-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        background:rgba(255,255,255,.08);
        color:#fff;
        padding:10px 14px;
        border-radius:999px;
        font-weight:700;
        margin-top:18px;
        font-size:.82rem;
    }

    .inner-pro-footer-bottom{
        border-top:1px solid rgba(255,255,255,.12);
        padding-top:16px;
        display:flex;
        justify-content:space-between;
        gap:14px;
        flex-wrap:wrap;
        color:rgba(255,255,255,.72);
        font-size:.8rem;
        direction: rtl;
    }

    .inner-pro-footer-bottom a{
        color:#fff;
        text-decoration:none;
        font-weight:700;
    }

    @media (max-width: 992px){
        .inner-pro-footer-container{
            padding-right:18px;
            padding-left:18px;
        }

        .inner-pro-footer-grid{
            grid-template-columns:1fr 1fr;
        }
    }

    @media (max-width: 700px){
        .inner-pro-footer-grid{
            grid-template-columns:1fr;
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
                        رقم الترخيص: {{ $licenseNumber }}
                    </div>
                @endif

                <div class="inner-pro-footer-social">
                    @if($twitter)
                        <a href="{{ $twitter }}" target="_blank" aria-label="Twitter">𝕏</a>
                    @endif
                    @if($instagram)
                        <a href="{{ $instagram }}" target="_blank" aria-label="Instagram">◎</a>
                    @endif
                    @if($youtube)
                        <a href="{{ $youtube }}" target="_blank" aria-label="YouTube">▶</a>
                    @endif
                    @if($tiktok)
                        <a href="{{ $tiktok }}" target="_blank" aria-label="TikTok">♪</a>
                    @endif
                    @if($snapchat)
                        <a href="{{ $snapchat }}" target="_blank" aria-label="Snapchat">◉</a>
                    @endif
                    @if($whatsapp)
                        <a href="{{ $whatsapp }}" target="_blank" aria-label="WhatsApp">✆</a>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="inner-pro-footer-title">روابط مهمة</h3>
                <div class="inner-pro-footer-links">
                    <a href="{{ url('/') }}">الرئيسية</a>
                    <a href="{{ url('/news') }}">الأخبار</a>
                    <a href="{{ url('/page/policies') }}">السياسات</a>
                    <a href="{{ url('/page/regulations') }}">اللوائح</a>
                    <a href="{{ url('/page/licenses') }}">التراخيص</a>
                </div>
            </div>

            <div>
                <h3 class="inner-pro-footer-title">الخدمات والبوابات</h3>
                <div class="inner-pro-footer-links">
                    @if(!empty($siteSettings?->beneficiary_portal_url))
                        <a href="{{ $siteSettings->beneficiary_portal_url }}">بوابة المستفيدين</a>
                    @endif
                    @if(!empty($siteSettings?->beneficiary_login_url))
                        <a href="{{ $siteSettings->beneficiary_login_url }}">دخول المستفيدين</a>
                    @endif
                    @if(!empty($siteSettings?->beneficiary_register_url))
                        <a href="{{ $siteSettings->beneficiary_register_url }}">تسجيل مستفيد جديد</a>
                    @endif
                    @if(!empty($siteSettings?->store_url))
                        <a href="{{ $siteSettings->store_url }}">المتجر</a>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="inner-pro-footer-title">تواصل معنا</h3>
                <div class="inner-pro-footer-contact">
                    @if($phone)
                        <div>الهاتف: {{ $phone }}</div>
                    @endif
                    @if($email)
                        <div>البريد الإلكتروني: {{ $email }}</div>
                    @endif
                    @if($address)
                        <div>العنوان: {{ $address }}</div>
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