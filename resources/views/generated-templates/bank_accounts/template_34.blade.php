@extends('themes.default.layouts.app')

@section('content')
@php
    $bankAccounts = collect($bankAccounts ?? ($items ?? []));

    $buttonColor = $siteSettings->button_color
        ?? $siteSettings->primary_color
        ?? '#127962';

    $secondaryColor = $siteSettings->secondary_color
        ?? '#10b981';

    $associationName = $siteSettings->association_name
        ?? $siteSettings->site_name
        ?? 'الجمعية';
@endphp

<style>
    .bank-page-wrap{
        max-width:1200px;
        margin:40px auto;
        padding:0 16px;
        direction:rtl;
        text-align:right;
        font-family:'Cairo', Tahoma, Arial, sans-serif;
        box-sizing:border-box;
    }

    .bank-page-wrap *{
        box-sizing:border-box;
    }

    .bank-hero{
        position:relative;
        overflow:hidden;
        border-radius:30px;
        padding:46px 30px;
        margin-bottom:28px;
        color:#fff;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.16), transparent 28%),
            radial-gradient(circle at bottom left, rgba(255,255,255,.10), transparent 24%),
            linear-gradient(135deg, {{ $buttonColor }} 0%, {{ $secondaryColor }} 100%);
        box-shadow:0 22px 45px rgba(15,23,42,.16);
    }

    .bank-hero::before,
    .bank-hero::after{
        content:'';
        position:absolute;
        border-radius:999px;
        background:rgba(255,255,255,.08);
        pointer-events:none;
    }

    .bank-hero::before{
        width:230px;
        height:230px;
        top:-80px;
        left:-60px;
    }

    .bank-hero::after{
        width:180px;
        height:180px;
        bottom:-55px;
        right:-45px;
    }

    .bank-hero-content{
        position:relative;
        z-index:2;
    }

    .bank-badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:10px 16px;
        border-radius:999px;
        background:rgba(255,255,255,.14);
        border:1px solid rgba(255,255,255,.20);
        font-size:14px;
        font-weight:700;
        margin-bottom:18px;
        backdrop-filter:blur(8px);
    }

    .bank-page-title{
        margin:0 0 10px;
        font-size:36px;
        font-weight:900;
        line-height:1.5;
    }

    .bank-page-subtitle{
        margin:0;
        max-width:760px;
        font-size:16px;
        line-height:2;
        opacity:.96;
    }

    .bank-grid{
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));
        gap:24px;
    }

    .bank-card{
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:26px;
        overflow:hidden;
        box-shadow:0 14px 30px rgba(15,23,42,.06);
        transition:transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        display:flex;
        flex-direction:column;
    }

    .bank-card:hover{
        transform:translateY(-4px);
        box-shadow:0 20px 40px rgba(15,23,42,.10);
        border-color:rgba(18,121,98,.25);
    }

    .bank-card-top{
        padding:22px 22px 14px;
        display:flex;
        align-items:center;
        gap:14px;
    }

    .bank-logo-box{
        width:72px;
        height:72px;
        border-radius:20px;
        background:linear-gradient(180deg,#ffffff 0%, #f8fafc 100%);
        border:1px solid #e5e7eb;
        display:flex;
        align-items:center;
        justify-content:center;
        overflow:hidden;
        flex-shrink:0;
        box-shadow:inset 0 1px 0 rgba(255,255,255,.7);
    }

    .bank-logo-box img{
        width:100%;
        height:100%;
        object-fit:contain;
        display:block;
        padding:10px;
    }

    .bank-logo-placeholder{
        font-size:30px;
    }

    .bank-name{
        margin:0 0 6px;
        font-size:22px;
        font-weight:900;
        color:#111827;
        line-height:1.5;
    }

    .bank-name-sub{
        margin:0;
        font-size:14px;
        color:#6b7280;
        line-height:1.8;
        font-weight:600;
    }

    .bank-card-body{
        padding:0 22px 22px;
        display:flex;
        flex-direction:column;
        gap:16px;
        flex:1;
    }

    .bank-number-box{
        background:#f8fafc;
        border:1px solid #e5e7eb;
        border-radius:20px;
        padding:16px;
    }

    .bank-label{
        display:block;
        margin-bottom:8px;
        color:#6b7280;
        font-size:13px;
        font-weight:700;
    }

    .bank-number{
        color:#111827;
        font-size:20px;
        font-weight:900;
        line-height:1.9;
        word-break:break-word;
        letter-spacing:.3px;
    }

    .bank-note{
        background:#f0fdf4;
        color:#166534;
        border:1px solid #bbf7d0;
        border-radius:16px;
        padding:12px 14px;
        font-size:14px;
        line-height:1.8;
    }

    .bank-actions{
        display:flex;
        flex-wrap:wrap;
        gap:10px;
        margin-top:auto;
    }

    .bank-btn{
        appearance:none;
        border:none;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        text-decoration:none;
        cursor:pointer;
        border-radius:14px;
        padding:12px 16px;
        font-size:14px;
        font-weight:800;
        transition:.25s ease;
    }

    .bank-btn-copy{
        background:{{ $buttonColor }};
        color:#fff;
        flex:1;
    }

    .bank-btn-copy:hover{
        filter:brightness(.95);
    }

    .bank-btn-outline{
        background:#fff;
        color:{{ $buttonColor }};
        border:1px solid {{ $buttonColor }};
        min-width:120px;
    }

    .bank-btn-outline:hover{
        background:#f0fdf4;
    }

    .bank-empty{
        grid-column:1 / -1;
        background:#fff;
        border:1px dashed #cbd5e1;
        border-radius:26px;
        padding:54px 20px;
        text-align:center;
        color:#6b7280;
        box-shadow:0 10px 24px rgba(15,23,42,.04);
    }

    .bank-empty-icon{
        width:82px;
        height:82px;
        border-radius:24px;
        margin:0 auto 18px;
        background:#f8fafc;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:32px;
        color:#94a3b8;
    }

    .bank-empty-title{
        margin:0 0 8px;
        font-size:24px;
        font-weight:900;
        color:#111827;
    }

    .bank-empty-text{
        margin:0;
        font-size:15px;
        line-height:1.9;
    }

    .bank-copy-toast{
        position:fixed;
        left:20px;
        bottom:20px;
        background:#111827;
        color:#fff;
        padding:12px 16px;
        border-radius:14px;
        font-size:14px;
        font-weight:700;
        z-index:9999;
        opacity:0;
        transform:translateY(10px);
        pointer-events:none;
        transition:all .25s ease;
    }

    .bank-copy-toast.show{
        opacity:1;
        transform:translateY(0);
    }

    @media (max-width:768px){
        .bank-hero{
            padding:30px 20px;
            border-radius:24px;
        }

        .bank-page-title{
            font-size:27px;
        }

        .bank-page-subtitle{
            font-size:15px;
        }

        .bank-card-top,
        .bank-card-body{
            padding-left:18px;
            padding-right:18px;
        }

        .bank-actions{
            flex-direction:column;
        }

        .bank-btn,
        .bank-btn-outline{
            width:100%;
        }
    }
</style>

<div class="bank-page-wrap">
    <div class="bank-hero">
        <div class="bank-hero-content">
            <div class="bank-badge">
                <i class="fas fa-building-columns"></i>
                الحسابات الرسمية
            </div>

            <h1 class="bank-page-title">
                {{ $page->title ?? 'الحسابات البنكية' }}
            </h1>

            <p class="bank-page-subtitle">
                {{ $page->excerpt ?? ('يمكن الاطلاع على الحسابات البنكية الرسمية الخاصة بـ ' . $associationName . ' واستخدامها في التحويل والدعم.') }}
            </p>
        </div>
    </div>

    <div class="bank-grid">
        @forelse($bankAccounts as $bank_account)
            @php
                $logoUrl = !empty($bank_account->bank_logo)
                    ? (
                        \Illuminate\Support\Str::startsWith($bank_account->bank_logo, ['http://', 'https://'])
                            ? $bank_account->bank_logo
                            : asset('storage/' . ltrim($bank_account->bank_logo, '/'))
                    )
                    : null;
            @endphp

            <div class="bank-card">
                <div class="bank-card-top">
                    <div class="bank-logo-box">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $bank_account->bank_name }}">
                        @else
                            <div class="bank-logo-placeholder">🏦</div>
                        @endif
                    </div>

                    <div>
                        <h3 class="bank-name">{{ $bank_account->name }}</h3>
                        <p class="bank-name-sub">{{ $bank_account->bank_name }}</p>
                    </div>
                </div>

                <div class="bank-card-body">
                    <div class="bank-number-box">
                        <span class="bank-label">رقم الحساب</span>
                        <div class="bank-number">{{ $bank_account->account_number }}</div>
                    </div>

                    <div class="bank-note">
                        <i class="fas fa-circle-info" style="margin-left:6px;"></i>
                        يرجى التأكد من رقم الحساب قبل تنفيذ أي تحويل بنكي.
                    </div>

                    <div class="bank-actions">
                        <button
                            type="button"
                            class="bank-btn bank-btn-copy"
                            onclick="copyBankAccount(@js($bank_account->account_number), @js($bank_account->name))"
                        >
                            <i class="fas fa-copy"></i>
                            نسخ رقم الحساب
                        </button>

                        <button
                            type="button"
                            class="bank-btn bank-btn-outline"
                            onclick="window.print()"
                        >
                            <i class="fas fa-print"></i>
                            طباعة
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bank-empty">
                <div class="bank-empty-icon">
                    <i class="fas fa-building-columns"></i>
                </div>
                <h3 class="bank-empty-title">لا توجد حسابات بنكية حالياً</h3>
                <p class="bank-empty-text">
                    لم تتم إضافة أي حسابات بنكية في الوقت الحالي.
                </p>
            </div>
        @endforelse
    </div>
</div>

<div id="bankCopyToast" class="bank-copy-toast">تم النسخ بنجاح</div>

<script>
    function copyBankAccount(accountNumber, accountName) {
        const text = accountNumber || '';
        const toast = document.getElementById('bankCopyToast');

        if (!text) {
            return;
        }

        navigator.clipboard.writeText(text).then(function () {
            toast.textContent = 'تم نسخ رقم الحساب: ' + (accountName || '');
            toast.classList.add('show');

            setTimeout(function () {
                toast.classList.remove('show');
            }, 2200);
        });
    }
</script>
@endsection