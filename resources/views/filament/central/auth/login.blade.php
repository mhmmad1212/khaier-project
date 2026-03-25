<x-filament-panels::page.simple>
    <div style="max-width:460px;margin:40px auto;background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:28px;">
        <div style="margin-bottom:20px;text-align:right;">
            <h1 style="margin:0 0 8px;font-size:28px;font-weight:800;color:#111827;">تسجيل الدخول إلى لوحة المشرف</h1>
            <p style="margin:0;color:#6b7280;">أدخل بياناتك للوصول إلى القاعدة المركزية.</p>
        </div>

        <form wire:submit="authenticate">
            {{ $this->form }}

            <div style="margin-top:18px;">
                <button
                    type="submit"
                    style="
                        width:100%;
                        background:#d97706;
                        color:#fff;
                        border:none;
                        border-radius:14px;
                        padding:14px 18px;
                        font-size:15px;
                        font-weight:800;
                        cursor:pointer;
                    "
                >
                    دخول
                </button>
            </div>
        </form>

        @if (filament()->hasPasswordReset())
            <div style="margin-top:16px;text-align:right;">
                <a href="{{ filament()->getRequestPasswordResetUrl() }}" style="color:#d97706;text-decoration:none;font-size:14px;font-weight:700;">
                    نسيت كلمة المرور؟
                </a>
            </div>
        @endif
    </div>
</x-filament-panels::page.simple>
