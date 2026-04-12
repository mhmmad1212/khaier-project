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

    $executiveDirectorImageUrl = $executiveDirector?->imageMedia?->url
        ?? (
            !empty($executiveDirector?->image)
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
        max-width: 1150px;
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
        border-radius: 28px;
        padding: 42px 28px;
        margin-bottom: 28px;
        color: #fff;
        background: linear-gradient(135deg, {{ $buttonColor }} 0%, {{ $secondaryColor }} 100%);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.16);
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
        width: 220px;
        height: 220px;
        top: -70px;
        left: -60px;
    }

    .exec-hero::after {
        width: 160px;
        height: 160px;
        bottom: -50px;
        right: -40px;
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
    }

    .exec-page-title {
        margin: 0 0 10px;
        font-size: 34px;
        font-weight: 900;
        line-height: 1.5;
    }

    .exec-page-subtitle {
        margin: 0;
        max-width: 760px;
        font-size: 16px;
        line-height: 2;
        opacity: .95;
    }

    .exec-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.07);
    }

    .exec-card-top {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
        align-items: center;
        justify-content: space-between;
        padding: 30px 28px 22px;
        border-bottom: 1px solid #eef2f7;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .exec-profile {
        display: flex;
        align-items: center;
        gap: 18px;
        min-width: 280px;
    }

    .exec-avatar {
        width: 96px;
        height: 96px;
        border-radius: 24px;
        background: linear-gradient(135deg, {{ $buttonColor }} 0%, {{ $secondaryColor }} 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 34px;
        box-shadow: 0 10px 24px rgba(16, 185, 129, 0.24);
        flex-shrink: 0;
        overflow: hidden;
    }

    .exec-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .exec-name {
        margin: 0 0 6px;
        font-size: 29px;
        font-weight: 900;
        color: #111827;
        line-height: 1.5;
    }

    .exec-role {
        margin: 0;
        color: #6b7280;
        font-size: 15px;
        font-weight: 600;
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
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        padding: 24px 28px 0;
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

    .exec-bio-wrap {
        padding: 28px;
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
        border-radius: 24px;
        padding: 54px 24px;
        text-align: center;
        color: #6b7280;
        box-shadow: 0 12px 24px rgba(15,23,42,.04);
    }

    .exec-empty-icon {
        width: 78px;
        height: 78px;
        margin: 0 auto 18px;
        border-radius: 22px;
        background: #f8fafc;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
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

    @media (max-width: 768px) {
        .exec-hero {
            padding: 30px 20px;
            border-radius: 22px;
        }

        .exec-page-title {
            font-size: 26px;
        }

        .exec-card-top,
        .exec-bio-wrap,
        .exec-meta-grid {
            padding-left: 18px;
            padding-right: 18px;
        }

        .exec-name {
            font-size: 24px;
        }

        .exec-avatar {
            width: 78px;
            height: 78px;
            font-size: 28px;
            border-radius: 20px;
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
            <div class="exec-card-top">
                <div class="exec-profile">
                    <div class="exec-avatar">
                        @if(!empty($executiveDirectorImageUrl))
                            <img src="{{ $executiveDirectorImageUrl }}" alt="{{ $executiveDirector->name ?: 'المدير التنفيذي' }}">
                        @else
                            <i class="fas fa-user-tie"></i>
                        @endif
                    </div>

                    <div>
                        <h2 class="exec-name">
                            {{ $executiveDirector->name ?: 'بدون اسم' }}
                        </h2>
                        <p class="exec-role">المدير التنفيذي</p>
                    </div>
                </div>

                @if(!empty($email) || !empty($whatsappUrl))
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

            <div class="exec-bio-wrap">
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
