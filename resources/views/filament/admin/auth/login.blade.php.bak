@php
    $siteSettings = null;
    $logo = null;

    try {
        $siteSettings = \Illuminate\Support\Facades\DB::connection('tenant')
            ->table('site_settings')
            ->orderByDesc('id')
            ->first();

        if ($siteSettings && !empty($siteSettings->logo_media_id)) {
            $media = \App\Models\MediaItem::query()->find($siteSettings->logo_media_id);
            if ($media && !empty($media->file)) {
                $logo = asset('storage/' . ltrim($media->file, '/'));
            }
        }
    } catch (\Throwable $e) {
        $siteSettings = null;
        $logo = null;
    }

    $buttonColor = data_get($siteSettings, 'button_color')
        ?: data_get($siteSettings, 'primary_color')
        ?: '#0f766e';
@endphp

<x-filament-panels::page.simple>
    <style>
        .fi-simple-header,
        .fi-logo {
            display: none !important;
        }

        .fi-simple-layout,
        .fi-simple-main-ctn,
        .fi-simple-page {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .fi-simple-main {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            border: none !important;
        }

        .khaier-top-header {
            max-width: 1180px;
            margin: 12px auto 8px;
            padding: 16px 24px;
            border-radius: 18px;
            background: linear-gradient(135deg, #ffffff, #f8fafc);
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            text-align: center;
            direction: rtl;
        }

        .khaier-top-header .title {
            font-size: 15px;
            color: #6b7280;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .khaier-top-header .heading {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
            line-height: 1.8;
        }

        .khaier-login-page {
            min-height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px 16px 24px;
            direction: rtl;
            background:
                radial-gradient(circle at top right, rgba(15, 118, 110, 0.10), transparent 28%),
                radial-gradient(circle at bottom left, rgba(20, 184, 166, 0.08), transparent 24%),
                linear-gradient(135deg, #f8fafc 0%, #eef7f5 100%);
        }

        .khaier-login-wrap {
            width: 100%;
            max-width: 1180px;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.10);
        }

        .khaier-login-side {
            background: linear-gradient(135deg, {{ $buttonColor }} 0%, #0f172a 100%);
            color: #ffffff;
            padding: 56px 42px;
            position: relative;
            overflow: hidden;
        }

        .khaier-login-side::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top left, rgba(255,255,255,0.18), transparent 24%),
                radial-gradient(circle at bottom right, rgba(255,255,255,0.10), transparent 22%);
            pointer-events: none;
        }

        .khaier-login-brand {
            position: relative;
            z-index: 1;
        }

        .khaier-login-logo {
            width: 96px;
            height: 96px;
            border-radius: 24px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.18);
        }

        .khaier-login-logo img {
            width: 76px;
            height: 76px;
            object-fit: contain;
            display: block;
        }

        .khaier-login-logo-fallback {
            font-size: 34px;
            font-weight: 800;
            color: #ffffff;
        }

        .khaier-login-title {
            font-size: 34px;
            line-height: 1.6;
            font-weight: 800;
            margin: 0 0 16px;
            color: #ffffff;
        }

        .khaier-login-text {
            font-size: 16px;
            line-height: 2;
            color: rgba(255,255,255,0.92);
            margin: 0;
        }

        .khaier-login-panel {
            padding: 40px 34px 34px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .khaier-login-panel-inner {
            width: 100%;
            max-width: 430px;
            margin: 0 auto;
            text-align: right;
        }

        .khaier-login-heading-wrap {
            margin-bottom: 22px;
        }

        .khaier-login-heading {
            font-size: 32px;
            font-weight: 800;
            color: #111827;
            margin: 0 0 8px;
            line-height: 1.4;
        }

        .khaier-login-subheading {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
            line-height: 1.9;
        }

        .khaier-login-panel form {
            width: 100%;
        }

        .khaier-login-panel .fi-input-wrp,
        .khaier-login-panel .fi-input,
        .khaier-login-panel .fi-select-input {
            border-radius: 14px !important;
        }

        .khaier-login-panel button[type="submit"] {
            background: {{ $buttonColor }} !important;
            border-color: {{ $buttonColor }} !important;
        }

        .khaier-login-panel [data-filament-field-wrapper] label,
        .khaier-login-panel .fi-checkbox-list-option-label,
        .khaier-login-panel .fi-fo-checkbox label {
            text-align: right !important;
        }

        @media (max-width: 900px) {
            .khaier-login-wrap {
                grid-template-columns: 1fr;
            }

            .khaier-login-side {
                padding: 38px 26px;
            }

            .khaier-login-title {
                font-size: 28px;
            }

            .khaier-login-panel {
                padding: 28px 20px 24px;
            }

            .khaier-top-header {
                margin: 10px 12px 8px;
            }
        }
    </style>

    <div class="khaier-top-header">
        <div class="title">لوحة الجمعية</div>
        <div class="heading">مرحبًا بكم في تسجيل الدخول لمواقع الخير الإلكترونية</div>
    </div>

    <div class="khaier-login-page">
        <div class="khaier-login-wrap">
            <div class="khaier-login-side">
                <div class="khaier-login-brand">
                    <div class="khaier-login-logo">
                        @if($logo)
                            <img loading="lazy" decoding="async" src="{{ $logo }}" alt="شعار خير">
                        @else
                            <div class="khaier-login-logo-fallback">خير</div>
                        @endif
                    </div>

                    <h1 class="khaier-login-title">
                        مرحبًا بكم في تسجيل الدخول لمواقع الخير الإلكترونية
                    </h1>

                    <p class="khaier-login-text">
                        بوابة موحدة لإدارة مواقع الجمعيات، ومتابعة المحتوى، والتحكم في التصاميم والصفحات والخدمات بسهولة واحترافية.
                    </p>
                </div>
            </div>

            <div class="khaier-login-panel">
                <div class="khaier-login-panel-inner">
                    <div class="khaier-login-heading-wrap">
                        <h2 class="khaier-login-heading">تسجيل الدخول</h2>
                        <p class="khaier-login-subheading">أدخل بياناتك للوصول إلى لوحة التحكم.</p>
                    </div>

                    <form wire:submit="authenticate">
                        {{ $this->form }}

                        <div style="margin-top: 18px;">
                            <button
                                type="submit"
                                style="
                                    width: 100%;
                                    background: {{ $buttonColor }};
                                    color: #fff;
                                    border: none;
                                    border-radius: 14px;
                                    padding: 14px 18px;
                                    font-size: 15px;
                                    font-weight: 800;
                                    cursor: pointer;
                                "
                            >
                                دخول
                            </button>
                        </div>
                    </form>

                    @if (filament()->hasPasswordReset())
                        <div style="margin-top: 16px; text-align: right;">
                            <a
                                href="{{ filament()->getRequestPasswordResetUrl() }}"
                                style="color: {{ $buttonColor }}; text-decoration: none; font-size: 14px; font-weight: 700;"
                            >
                                نسيت كلمة المرور؟
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page.simple>
