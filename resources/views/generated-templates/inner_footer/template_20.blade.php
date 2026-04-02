@php
    $siteSettings = $siteSettings ?? \Illuminate\Support\Facades\DB::connection('tenant')->table('site_settings')->orderByDesc('id')->first();
    $primary = $siteSettings->primary_color ?? '#127962';
    $secondary = $siteSettings->secondary_color ?? '#0d5948';
@endphp

<style>
    .inner-footer{
        margin-top:60px;
        background:linear-gradient(135deg, #0f172a 0%, #111827 100%);
        color:#fff;
        position:relative;
        overflow:hidden;
    }

    .inner-footer::before{
        content:"";
        position:absolute;
        inset:0;
        background:
            radial-gradient(circle at top left, rgba(18,121,98,.22), transparent 24%),
            radial-gradient(circle at bottom right, rgba(255,255,255,.06), transparent 22%);
        pointer-events:none;
    }

    .inner-footer-container{
        max-width:1200px;
        margin:0 auto;
        padding:46px 18px 18px;
        position:relative;
        z-index:2;
    }

    .inner-footer-grid{
        display:grid;
        grid-template-columns:1.4fr 1fr 1fr;
        gap:28px;
        margin-bottom:26px;
    }

    .inner-footer-title{
        margin:0 0 14px;
        font-size:1.15rem;
        font-weight:800;
    }

    .inner-footer-text{
        color:rgba(255,255,255,.78);
        line-height:2;
        font-size:.97rem;
    }

    .inner-footer-links{
        display:grid;
        gap:10px;
    }

    .inner-footer-links a{
        color:rgba(255,255,255,.82);
        text-decoration:none;
        transition:.2s ease;
    }

    .inner-footer-links a:hover{
        color:#fff;
        transform:translateX(-2px);
    }

    .inner-footer-contact{
        display:grid;
        gap:10px;
        color:rgba(255,255,255,.82);
        font-size:.95rem;
    }

    .inner-footer-bottom{
        border-top:1px solid rgba(255,255,255,.12);
        padding-top:16px;
        display:flex;
        justify-content:space-between;
        gap:14px;
        flex-wrap:wrap;
        color:rgba(255,255,255,.72);
        font-size:.9rem;
    }

    .inner-footer-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        background:rgba(255,255,255,.08);
        color:#fff;
        padding:10px 14px;
        border-radius:999px;
        font-weight:600;
    }

    @media (max-width: 900px){
        .inner-footer-grid{
            grid-template-columns:1fr;
        }
    }
</style>

<footer class="inner-footer">
    <div class="inner-footer-container">
        <div class="inner-footer-grid">
            <div>
                <h3 class="inner-footer-title">{{ $siteSettings->site_name ?? $siteSettings->association_name ?? 'اسم الجمعية' }}</h3>
                <div class="inner-footer-text">
                    {{ $siteSettings->site_description ?? 'منصة تعريفية لعرض محتوى الجمعية وصفحاتها الداخلية بشكل منظم واحترافي.' }}
                </div>

                @if(!empty($siteSettings?->license_number))
                    <div style="margin-top:16px;">
                        <span class="inner-footer-badge">رقم الترخيص: {{ $siteSettings->license_number }}</span>
                    </div>
                @endif
            </div>

            <div>
                <h3 class="inner-footer-title">روابط مهمة</h3>
                <div class="inner-footer-links">
                    <a href="{{ url('/') }}">الرئيسية</a>
                    <a href="{{ url('/page/policies') }}">السياسات</a>
                    <a href="{{ url('/page/regulations') }}">اللوائح</a>
                    <a href="{{ url('/page/licenses') }}">التراخيص</a>
                    <a href="{{ url('/news') }}">الأخبار</a>
                </div>
            </div>

            <div>
                <h3 class="inner-footer-title">التواصل</h3>
                <div class="inner-footer-contact">
                    @if(!empty($siteSettings?->phone))
                        <div>الهاتف: {{ $siteSettings->phone }}</div>
                    @endif

                    @if(!empty($siteSettings?->email))
                        <div>البريد: {{ $siteSettings->email }}</div>
                    @endif

                    @if(!empty($siteSettings?->address))
                        <div>العنوان: {{ $siteSettings->address }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="inner-footer-bottom">
            <div>
                © {{ now()->year }} {{ $siteSettings->site_name ?? $siteSettings->association_name ?? 'الجمعية' }}. جميع الحقوق محفوظة.
            </div>
            <div>
                تم تطوير وتجهيز الصفحات الداخلية ضمن نظام خير
            </div>
        </div>
    </div>
</footer>