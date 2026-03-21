@php
    $user = auth()->user();
@endphp

<div style="
    background: linear-gradient(135deg, #0f766e, #14b8a6);
    border-radius: 16px;
    padding: 16px 20px;
    color: white;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:20px;
    margin-bottom: 16px;
">

    <div>
        <div style="font-size:1.2rem;font-weight:900;">
            👋 مرحبًا {{ $user->name ?? 'مدير النظام' }}
        </div>
        <div style="opacity:.9;font-size:.85rem;">
            لوحة التحكم — إدارة المحتوى بسهولة
        </div>
    </div>

    <div style="display:flex;align-items:center;gap:10px;">
        <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
            @csrf
            <button type="submit" style="
                background: rgba(255,255,255,.15);
                border:none;
                color:white;
                padding:8px 14px;
                border-radius:10px;
                cursor:pointer;
                font-size:.85rem;
            ">
                تسجيل الخروج
            </button>
        </form>
    </div>

</div>
