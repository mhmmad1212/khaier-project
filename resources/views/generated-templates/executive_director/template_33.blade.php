@extends('themes.default.layouts.app')

@section('content')
@php
    $executiveDirector = $executiveDirector ?? ($item ?? null);

    $buttonColor = $siteSettings->button_color
        ?? $siteSettings->primary_color
        ?? '#127962';

    $secondaryColor = $siteSettings->secondary_color
        ?? '#10b981';

    $associationName = $siteSettings->association_name
        ?? $siteSettings->site_name
        ?? 'الجمعية';

    $phone = $executiveDirector->phone ?? null;
    $email = $executiveDirector->email ?? null;

    $executiveDirectorImageUrl =
        $executiveDirector->image_url
        ?? ($executiveDirector->imageMedia->url ?? null)
        ?? (
            !empty($executiveDirector->image)
                ? (
                    \Illuminate\Support\Str::startsWith($executiveDirector->image, ['http://', 'https://'])
                        ? $executiveDirector->image
                        : \App\Support\Media\MediaUrl::forDiskPath('public', ltrim($executiveDirector->image, '/'))
                )
                : null
        );

    $phoneDigits = $phone ? preg_replace('/[^0-9]/', '', $phone) : '';
    $phoneWhatsapp = $phoneDigits
        ? (str_starts_with($phoneDigits, '0') ? '966' . ltrim($phoneDigits, '0') : $phoneDigits)
        : '';

    $whatsappUrl = $phoneWhatsapp ? 'https://wa.me/' . $phoneWhatsapp : null;
@endphp

<style>
    .exec-wrapper {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 16px;
        direction: rtl;
        text-align: right;
        font-family: 'Cairo', Tahoma, Arial, sans-serif;
        box-sizing: border-box;
    }

    .exec-wrapper * {
        box-sizing: border-box;
    }

    .exec-hero {
        position: relative;
        overflow: hidden;
        border-radius: 30px;
        padding: 44px 30px;
        margin-bottom: 30px;
        color: #fff;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.16), transparent 28%),
            radial-gradient(circle at bottom left, rgba(255,255,255,.10), transparent 24%),
            linear-gradient(135deg, {{ $buttonColor }} 0%, {{ $secondaryColor }} 100%);
        box-shadow: 0 22px 45px rgba(15, 23, 42, 0.18);
    }

    .exec-hero::before,
    .exec-hero::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.07);
        pointer-events: none;
    }

    .exec-hero::before {
        width: 240px;
        height: 240px;
        top: -80px;
        left: -70px;
    }

    .exec-hero::after {
        width: 180px;
        height: 180px;
        bottom: -55px;
        right: -45px;
    }

    .exec-hero-content {
        position: relative;
        z-index: 2;
    }

    .exec-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.22);
        padding: 10px 16px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 18px;
        backdrop-filter: blur(8px);
    }

    .exec-page-title {
        margin: 0 0 10px;
        font-size: 36px;
        font-weight: 900;
        line-height: 1.5;
    }

    .exec-page-subtitle {
        margin: 0;
        max-width: 780px;
        font-size: 16px;
        line-height: 2;
        opacity: .96;
    }

    .exec-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.07);
    }

    .exec-main-grid {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 0;
    }

    .exec-image-side {
        background: linear-gradient(180deg, #f8fafc 0%, #eefaf5 100%);
        padding: 28px;
        border-left: 1px solid #eef2f7;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .exec-image-frame {
        width: 100%;
        max-width: 270px;
        aspect-ratio: 1 / 1;
        border-radius: 28px;
        overflow: hidden;
        background: linear-gradient(135deg, {{ $buttonColor }} 0%, {{ $secondaryColor }} 100%);
        box-shadow: 0 18px 35px rgba(16, 185, 129, 0.20);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .exec-image-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .exec-image-placeholder {
        color: #fff;
        font-size: 54px;
    }

    .exec-image-caption {
        margin-top: 16px;
        font-size: 14px;
        color: #64748b;
        text-align: center;
        line-height: 1.8;
    }

    .exec-content-side {
        padding: 30px 30px 28px;
        background: linear-gradient(180deg, #ffffff 0%, #fcfdfd 100%);
    }

    .exec-topbar {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 22px;
    }

    .exec-name {
        margin: 0 0 8px;
        font-size: 32px;
        font-weight: 900;
        color: #111827;
        line-height: 1.5;
    }

    .exec-role {
        margin: 0;
        color: #6b7280;
        font-size: 15px;
        font-weight: 700;
    }

    .exec-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .exec-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        padding: 12px 18px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 800;
        transition: .25s ease;
        border: 1px solid transparent;
        min-width: 160px;
    }

    .exec-btn-primary {
        background: {{ $buttonColor }};
        color: #fff;
        border-color: {{ $buttonColor }};
    }

    .exec-btn-primary:hover {
        filter: brightness(0.95);
        transform: translateY(-1px);
        color: #fff;
    }

    .exec-btn-outline {
        background: #fff;
        color: {{ $buttonColor }};
        border-color: {{ $buttonColor }};
    }

    .exec-btn-outline:hover {
        background: #f0fdf4;
        color: {{ $buttonColor }};
    }

    .exec-meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .exec-meta-card {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 16px 18px;
    }

    .exec-meta-label {
        display: block;
        margin-bottom: 8px;
        color: #6b7280;
        font-size: 13px;
        font-weight: 700;
    }

    .exec-meta-value {
        color: #111827;
        font-size: 16px;
        font-weight: 800;
        line-height: 1.8;
        word-break: break-word;
    }

    .exec-bio-box {
        background: #fff;
        border: 1px solid #edf2f7;
        border-radius: 22px;
        padding: 22px;
    }

    .exec-section-title {
        margin: 0 0 14px;
        font-size: 22px;
        font-weight: 900;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .exec-bio {
        color: #374151;
        line-height: 2.05;
        font-size: 16px;
    }

    .exec-bio p:last-child {
        margin-bottom: 0;
    }

    .exec-empty {
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 26px;
        padding: 56px 24px;
        text-align: center;
        color: #6b7280;
        box-shadow: 0 12px 24px rgba(15,23,42,.04);
    }

    .exec-empty-icon {
        width: 82px;
        height: 82px;
        margin: 0 auto 18px;
        border-radius: 24px;
        background: #f8fafc;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
    }

    .exec-empty-title {
        margin: 0 0 8px;
        font-size: 24px;
        font-weight: 900;
        color: #111827;
    }

    .exec-empty-text {
        margin: 0;
        font-size: 15px;
        line-height: 1.9;
    }

    @media (max-width: 900px) {
        .exec-main-grid {
            grid-template-columns: 1fr;
        }

        .exec-image-side {
            border-left: none;
            border-bottom: 1px solid #eef2f7;
        }
    }

    @media (max-width: 768px) {
        .exec-hero {
            padding: 30px 20px;
            border-radius: 24px;
        }

        .exec-page-title {
            font-size: 27px;
        }

        .exec-content-side,
        .exec-image-side {
            padding: 20px;
        }

        .exec-name {
            font-size: 25px;
        }

        .exec-actions {
            width: 100%;
        }

        .exec-btn {
            width: 100%;
        }
    }
</style>

<div class="exec-wrapper">
    <div class="exec-hero">
        <div class="exec-hero-content">
            <div class="exec-badge">
                <i class="fas fa-briefcase"></i>
                صفحة تعريفية
            </div>

            <h1 class="exec-page-title">
                {{ $page->title ?? 'المدير التنفيذي' }}
            </h1>

            <p class="exec-page-subtitle">
                {{ $page->excerpt ?? ('تعرف على المدير التنفيذي في ' . $associationName . ' واطلع على وسائل التواصل والنبذة التعريفية.') }}
            </p>
        </div>
    </div>

    @if($executiveDirector)
        <div class="exec-card">
            <div class="exec-main-grid">
                <div class="exec-image-side">
                    <div class="exec-image-frame">
                        @if(!empty($executiveDirectorImageUrl))
                            <img src="{{ $executiveDirectorImageUrl }}" alt="{{ $executiveDirector->name ?: 'المدير التنفيذي' }}">
                        @else
                            <div class="exec-image-placeholder">
                                <i class="fas fa-user-tie"></i>
                            </div>
                        @endif
                    </div>

                    <div class="exec-image-caption">
                        {{ $executiveDirector->name ?: 'المدير التنفيذي' }}
                    </div>
                </div>

                <div class="exec-content-side">
                    <div class="exec-topbar">
                        <div>
                            <h2 class="exec-name">
                                {{ $executiveDirector->name ?: 'بدون اسم' }}
                            </h2>
                            <p class="exec-role">المدير التنفيذي</p>
                        </div>

                        @if(!empty($email) || !empty($whatsappUrl) || !empty($phone))
                            <div class="exec-actions">
                                @if(!empty($email))
                                    <a href="mailto:{{ $email }}" class="exec-btn exec-btn-primary">
                                        <i class="fas fa-envelope"></i>
                                        مراسلة بالبريد
                                    </a>
                                @endif

                                @if(!empty($whatsappUrl))
                                    <a href="{{ $whatsappUrl }}" target="_blank" class="exec-btn exec-btn-outline">
                                        <i class="fab fa-whatsapp"></i>
                                        تواصل واتساب
                                    </a>
                                @elseif(!empty($phone))
                                    <a href="tel:{{ $phone }}" class="exec-btn exec-btn-outline">
                                        <i class="fas fa-phone"></i>
                                        اتصال مباشر
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="exec-meta-grid">
                        @if(!empty($phone))
                            <div class="exec-meta-card">
                                <span class="exec-meta-label">رقم التواصل</span>
                                <div class="exec-meta-value">{{ $phone }}</div>
                            </div>
                        @endif

                        @if(!empty($email))
                            <div class="exec-meta-card">
                                <span class="exec-meta-label">البريد الإلكتروني</span>
                                <div class="exec-meta-value">{{ $email }}</div>
                            </div>
                        @endif
                    </div>

                    <div class="exec-bio-box">
                        <h3 class="exec-section-title">
                            <i class="fas fa-circle-info" style="color: {{ $buttonColor }};"></i>
                            نبذة عن المدير التنفيذي
                        </h3>

                        @if(!empty($executiveDirector->bio))
                            <div class="exec-bio">
                                {!! $executiveDirector->bio !!}
                            </div>
                        @else
                            <div class="exec-bio" style="color:#6b7280;">
                                لا توجد نبذة مضافة حاليًا.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="exec-empty">
            <div class="exec-empty-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <h3 class="exec-empty-title">لا توجد بيانات حالياً</h3>
            <p class="exec-empty-text">
                لم تتم إضافة بيانات المدير التنفيذي بعد.
            </p>
        </div>
    @endif
</div>
@endsection