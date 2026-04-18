@extends('themes.default.layouts.app')

@section('content')
@php
    $associationName = $siteSettings->association_name
        ?? $siteSettings->site_name
        ?? 'الجمعية';

    $beneficiaryServices = collect($beneficiaryServices ?? ($items ?? []));

    $openFileUrl = url('/page/tsgyl-mstfyd');

    $cardThemes = [
        ['card' => 'card-blue', 'iconBg' => '#eff6ff', 'iconColor' => '#2563eb', 'condBg' => '#f8fafc', 'condBorder' => '#dbeafe', 'condTitle' => '#1e40af'],
        ['card' => 'card-yellow', 'iconBg' => '#fefce8', 'iconColor' => '#ca8a04', 'condBg' => '#fefce8', 'condBorder' => '#fef08a', 'condTitle' => '#854d0e'],
        ['card' => 'card-purple', 'iconBg' => '#faf5ff', 'iconColor' => '#9333ea', 'condBg' => '#faf5ff', 'condBorder' => '#e9d5ff', 'condTitle' => '#6b21a8'],
        ['card' => 'card-green', 'iconBg' => '#ecfdf5', 'iconColor' => '#059669', 'condBg' => '#ecfdf5', 'condBorder' => '#a7f3d0', 'condTitle' => '#065f46'],
        ['card' => 'card-indigo', 'iconBg' => '#eef2ff', 'iconColor' => '#4f46e5', 'condBg' => '#eef2ff', 'condBorder' => '#c7d2fe', 'condTitle' => '#3730a3'],
        ['card' => 'card-orange', 'iconBg' => '#fff7ed', 'iconColor' => '#ea580c', 'condBg' => '#fff7ed', 'condBorder' => '#fed7aa', 'condTitle' => '#9a3412'],
    ];
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@400;500;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .radwan-services-wrapper {
        font-family: 'Noto Kufi Arabic', Tahoma, Arial, sans-serif;
        direction: rtl;
        box-sizing: border-box;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        color: #374151;
        line-height: 1.7;
    }

    .radwan-services-wrapper * {
        box-sizing: border-box;
    }

    .radwan-header-section {
        position: relative;
        background: linear-gradient(135deg, #065f46 0%, #10b981 100%);
        border-radius: 18px;
        padding: 26px 18px;
        text-align: center;
        color: white;
        margin-bottom: 28px;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
    }

    .radwan-watermarks {
        position: absolute;
        inset: 0;
        pointer-events: none;
        overflow: hidden;
        z-index: 1;
    }

    .radwan-watermark-icon {
        position: absolute;
        color: rgba(255, 255, 255, 0.04);
        animation: radwan-float 6s ease-in-out infinite;
    }

    .wm-1 { top: -20px; right: -10px; font-size: 140px; transform: rotate(-15deg); }
    .wm-2 { bottom: -30px; left: -10px; font-size: 180px; transform: rotate(15deg); animation-delay: 1s; }
    .wm-3 { top: 20px; left: 20%; font-size: 80px; transform: rotate(10deg); animation-delay: 2s; }
    .wm-4 { bottom: 20px; right: 25%; font-size: 100px; transform: rotate(-5deg); animation-delay: 3s; }

    @keyframes radwan-float {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(5deg); }
        100% { transform: translateY(0px) rotate(0deg); }
    }

    .radwan-header-content {
        position: relative;
        z-index: 2;
    }

    .radwan-header-icon {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        padding: 12px;
        border-radius: 50%;
        font-size: 24px;
        margin-bottom: 12px;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .radwan-main-title {
        font-size: 26px;
        font-weight: 800;
        margin: 0 0 8px 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .radwan-subtitle {
        font-size: 14px;
        opacity: 0.92;
        max-width: 760px;
        margin: 0 auto;
        line-height: 2.2;
    }

    .radwan-inline-open-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: #ffffff;
        color: #065f46 !important;
        text-decoration: none;
        padding: 10px 18px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 800;
        border: 1px solid rgba(255,255,255,.35);
        box-shadow: 0 8px 18px rgba(0,0,0,.12);
        transition: all .25s ease;
    }

    .radwan-inline-open-btn:hover {
        background: #ecfdf5;
        color: #047857 !important;
        transform: translateY(-1px);
    }

    .radwan-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 25px;
    }

    .radwan-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        min-width: 0;
    }

    .radwan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: #059669;
    }

    .radwan-icon-box {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px auto;
        font-size: 28px;
    }

    .radwan-card-title {
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 15px 0;
        color: #111827;
        line-height: 1.8;
    }

    .radwan-conditions {
        border-radius: 12px;
        padding: 15px;
        text-align: right;
        margin-bottom: 20px;
        flex-grow: 1;
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    .radwan-conditions h4 {
        font-size: 14px;
        font-weight: bold;
        margin: 0 0 10px 0;
    }

    .radwan-conditions .radwan-conditions-content {
        line-height: 1.9;
    }

    .radwan-conditions .radwan-conditions-content ul,
    .radwan-conditions .radwan-conditions-content ol {
        margin: 0;
        padding: 0 20px 0 0;
    }

    .radwan-conditions .radwan-conditions-content li {
        margin-bottom: 6px;
    }

    .radwan-conditions .radwan-conditions-content p:last-child {
        margin-bottom: 0;
    }

    .radwan-empty {
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        padding: 50px 20px;
        text-align: center;
        color: #64748b;
        grid-column: 1 / -1;
    }

    .radwan-buttons {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: auto;
    }

    .radwan-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: bold;
        font-size: 15px;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        font-family: inherit;
    }

    .radwan-btn-outline {
        background: transparent;
        border: 1px solid #059669;
        color: #059669;
    }

    .radwan-btn-outline:hover {
        background: #ecfdf5;
        color: #047857;
    }

    .radwan-btn-primary {
        background: #059669;
        color: #ffffff !important;
    }

    .radwan-btn-primary:hover {
        background: #047857;
    }

    .radwan-btn-secondary {
        background: #f0fdf4;
        color: #065f46 !important;
        border: 1px solid #a7f3d0;
    }

    .radwan-btn-secondary:hover {
        background: #dcfce7;
        color: #064e3b !important;
    }

    .radwan-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .radwan-modal-overlay.active {
        display: flex;
    }

    .radwan-modal-content {
        background: #fff;
        border-radius: 20px;
        width: 100%;
        max-width: 800px;
        overflow: hidden;
        position: relative;
    }

    .radwan-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }

    .radwan-modal-title {
        font-weight: bold;
        font-size: 18px;
        color: #065f46;
        margin: 0;
    }

    .radwan-modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #9ca3af;
        transition: color 0.3s;
    }

    .radwan-modal-close:hover {
        color: #ef4444;
    }

    .radwan-video-container {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
    }

    .radwan-video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
    }

    .radwan-modal-footer {
        padding: 20px;
        text-align: center;
        background: #ecfdf5;
    }

    @media (max-width: 768px) {
        .radwan-grid {
            grid-template-columns: 1fr;
        }

        .radwan-main-title {
            font-size: 22px;
        }

        .radwan-subtitle {
            font-size: 13px;
        }

        .radwan-card {
            padding: 18px;
        }
    }
</style>

<div class="radwan-services-wrapper">
    <div class="radwan-header-section">
        <div class="radwan-watermarks">
            <i class="fas fa-globe radwan-watermark-icon wm-1"></i>
            <i class="fas fa-laptop-code radwan-watermark-icon wm-2"></i>
            <i class="fas fa-network-wired radwan-watermark-icon wm-3"></i>
            <i class="fas fa-mobile-screen radwan-watermark-icon wm-4"></i>
        </div>

        <div class="radwan-header-content">
            <div class="radwan-header-icon">
                <i class="fas fa-desktop"></i>
            </div>

            <h2 class="radwan-main-title">
                {{ $page->title ?? 'بوابة الخدمات الإلكترونية' }}
            </h2>

            <p class="radwan-subtitle">
                @if(!empty($page->excerpt))
                    {{ $page->excerpt }}
                @else
                    مرحباً بك في بوابة المستفيدين لـ {{ $associationName }} . اختر الخدمة المطلوبة واطلع على الشروط، ثم قدم طلبك بكل سهولة وأمان علماً بان الخدمة تحتاج فتح ملف بالجمعية اذا لم يكون لديك ملف نأمل فتح ملف من خلال هذا الزر
                    <br><br>
                    <a href="{{ $openFileUrl }}" class="radwan-inline-open-btn">
                        <i class="fas fa-folder-plus"></i>
                        فتح ملف بالجمعية
                    </a>
                @endif
            </p>
        </div>
    </div>

    <div class="radwan-grid">
        @forelse($beneficiaryServices as $index => $beneficiary_service)
            @php
                $theme = $cardThemes[$index % count($cardThemes)];
                $guideUrl = $beneficiary_service->guide_url ?? null;
                $applicationUrl = $beneficiary_service->application_url ?? null;
            @endphp

            <div class="radwan-card {{ $theme['card'] }}">
                <div class="radwan-icon-box" style="background-color: {{ $theme['iconBg'] }}; color: {{ $theme['iconColor'] }};">
                    @if(!empty($beneficiary_service->icon))
                        <x-filament::icon :icon="$beneficiary_service->icon" class="w-8 h-8" />
                    @else
                        <i class="fas fa-desktop"></i>
                    @endif
                </div>

                <h3 class="radwan-card-title">{{ $beneficiary_service->name }}</h3>

                <div class="radwan-conditions" style="background-color: {{ $theme['condBg'] }}; border: 1px solid {{ $theme['condBorder'] }};">
                    <h4 style="color: {{ $theme['condTitle'] }};">
                        <i class="fas fa-list-check" style="margin-left:5px;"></i>
                        الشروط المطلوبة:
                    </h4>

                    <div class="radwan-conditions-content">
                        {!! $beneficiary_service->conditions !!}
                    </div>
                </div>

                <div class="radwan-buttons">
                    @if(!empty($guideUrl))
                        <button
                            type="button"
                            onclick="radwanOpenVideo(@js($guideUrl), @js($beneficiary_service->name), @js($applicationUrl))"
                            class="radwan-btn radwan-btn-outline"
                        >
                            <i class="fas fa-play-circle"></i>
                            شرح الطريقة
                        </button>
                    @endif

                    @if(!empty($applicationUrl))
                        <a href="{{ $applicationUrl }}" target="_blank" class="radwan-btn radwan-btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            تقديم الطلب
                        </a>
                    @endif

                    <a href="{{ $openFileUrl }}" class="radwan-btn radwan-btn-secondary">
                        <i class="fas fa-folder-plus"></i>
                        فتح ملف بالجمعية
                    </a>
                </div>
            </div>
        @empty
            <div class="radwan-empty">
                لا توجد خدمات مستفيدين مضافة حالياً.
            </div>
        @endforelse
    </div>
</div>

<div id="radwanVideoModal" class="radwan-modal-overlay">
    <div class="radwan-modal-content">
        <div class="radwan-modal-header">
            <h3 id="radwanModalTitle" class="radwan-modal-title">شرح الخدمة</h3>
            <button onclick="radwanCloseVideo()" class="radwan-modal-close" type="button">
                <i class="fas fa-times-circle"></i>
            </button>
        </div>

        <div class="radwan-video-container">
            <iframe id="radwanVideoFrame" src="" allow="autoplay; encrypted-media" allowfullscreen></iframe>
        </div>

        <div class="radwan-modal-footer">
            <a id="radwanModalApplyBtn" href="#" target="_blank" class="radwan-btn radwan-btn-primary" style="padding: 12px 30px;">
                <i class="fas fa-paper-plane"></i>
                الانتقال للبوابة وتقديم الطلب الآن
            </a>
        </div>
    </div>
</div>

<script>
    function radwanNormalizeYoutubeUrl(url) {
        if (!url) return null;

        try {
            const parsed = new URL(url);

            if (parsed.hostname.includes('youtu.be')) {
                const id = parsed.pathname.replace('/', '');
                return id ? `https://www.youtube.com/embed/${id}?autoplay=1` : null;
            }

            if (parsed.hostname.includes('youtube.com')) {
                if (parsed.pathname.includes('/embed/')) {
                    return `${parsed.origin}${parsed.pathname}${parsed.search ? parsed.search + '&autoplay=1' : '?autoplay=1'}`;
                }

                const id = parsed.searchParams.get('v');
                return id ? `https://www.youtube.com/embed/${id}?autoplay=1` : null;
            }

            return url;
        } catch (e) {
            return url;
        }
    }

    function radwanOpenVideo(videoUrl, serviceName, applicationUrl) {
        const frame = document.getElementById('radwanVideoFrame');
        const modal = document.getElementById('radwanVideoModal');
        const title = document.getElementById('radwanModalTitle');
        const applyBtn = document.getElementById('radwanModalApplyBtn');

        const normalizedUrl = radwanNormalizeYoutubeUrl(videoUrl);

        frame.src = normalizedUrl || '';
        title.textContent = serviceName ? `شرح الخدمة - ${serviceName}` : 'شرح الخدمة';

        if (applicationUrl) {
            applyBtn.href = applicationUrl;
            applyBtn.style.display = 'inline-flex';
        } else {
            applyBtn.href = '#';
            applyBtn.style.display = 'none';
        }

        modal.classList.add('active');
    }

    function radwanCloseVideo() {
        document.getElementById('radwanVideoFrame').src = '';
        document.getElementById('radwanVideoModal').classList.remove('active');
    }

    document.getElementById('radwanVideoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            radwanCloseVideo();
        }
    });
</script>
@endsection