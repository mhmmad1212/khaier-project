<footer class="site-footer">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="brand-logo-wrap bg-white text-primary">
                        {{ mb_substr($association->name ?? 'ج', 0, 1) }}
                    </div>
                    <div>
                        <h4 class="text-white mb-1 fw-bold">{{ $association->name ?? 'جمعية البر الأهلية' }}</h4>
                    </div>
                </div>

                <p class="text-muted pe-lg-4 lh-lg">
                    نسعى جاهدين لتقديم أفضل الخدمات المجتمعية والتنموية لتمكين الأفراد والأسر المحتاجة، وفق أعلى معايير الحوكمة والشفافية.
                </p>

                <div class="d-flex gap-3 mt-4">
                    <a href="{{ $settings->twitter_url ?? '#' }}" class="text-white fs-4"><i class="bi bi-twitter-x"></i></a>
                    <a href="{{ $settings->instagram_url ?? '#' }}" class="text-white fs-4"><i class="bi bi-instagram"></i></a>
                    <a href="{{ $settings->youtube_url ?? '#' }}" class="text-white fs-4"><i class="bi bi-youtube"></i></a>
                    <a href="{{ $settings->tiktok_url ?? '#' }}" class="text-white fs-4"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h5 class="footer-title">روابط سريعة</h5>
                <ul class="footer-links">
                    <li><a href="/">الرئيسية</a></li>
                    <li><a href="/page/about">عن الجمعية</a></li>
                    <li><a href="/page/governance">الحوكمة والشفافية</a></li>
                    <li><a href="/news">المركز الإعلامي</a></li>
                    <li><a href="/page/contact">اتصل بنا</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6">
                <h5 class="footer-title">روابط هامة</h5>
                <ul class="footer-links">
                    @if(isset($footerMenuItems) && $footerMenuItems->count())
                        @foreach($footerMenuItems as $item)
                            <li><a href="{{ $item->url ?: '#' }}">{{ $item->title }}</a></li>
                        @endforeach
                    @else
                        <li><a href="/page/policies">السياسات واللوائح</a></li>
                        <li><a href="/board-members">مجلس الإدارة</a></li>
                        <li><a href="/page/reports">التقارير المالية</a></li>
                    @endif
                </ul>
            </div>

            <div class="col-lg-4 col-md-6">
                <h5 class="footer-title">تواصل معنا</h5>
                <ul class="list-unstyled text-muted lh-lg">
                    @if(!empty($settings?->address))
                        <li class="mb-3"><i class="bi bi-geo-alt-fill text-secondary me-2 fs-5"></i> {{ $settings->address }}</li>
                    @endif
                    @if(!empty($settings?->phone))
                        <li class="mb-3"><i class="bi bi-telephone-fill text-secondary me-2 fs-5"></i> <span dir="ltr">{{ $settings->phone }}</span></li>
                    @endif
                    @if(!empty($settings?->email))
                        <li class="mb-3"><i class="bi bi-envelope-fill text-secondary me-2 fs-5"></i> {{ $settings->email }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container d-flex flex-wrap justify-content-between align-items-center">
            <p class="mb-0">جميع الحقوق محفوظة لصالح {{ $association->name ?? 'الجمعية' }} &copy; {{ date('Y') }}</p>
            <p class="mb-0 mt-2 mt-md-0">تصميم وتطوير بواسطة <a href="#" class="text-white text-decoration-none fw-bold">نظام خير</a></p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

@stack('scripts')
</body>
</html>
