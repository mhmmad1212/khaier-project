@extends('themes.default.layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap');

    /* خلفية الصفحة لتبرز البطاقات البيضاء */
    .assembly-page-wrapper {
        background-color: #f4f6f9;
        padding: 60px 20px;
        font-family: 'Cairo', sans-serif;
        direction: rtl;
        text-align: right;
    }

    .assembly-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-title {
        text-align: center;
        margin-bottom: 50px;
    }

    .page-title h1 {
        font-size: 32px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0;
    }

    /* شبكة الأعضاء */
    .assembly-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 25px;
    }

    /* تصميم البطاقة الاحترافي */
    .assembly-card {
        background: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .assembly-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    }

    /* الترويسة الرمادية المنحنية */
    .card-header-gray {
        background-color: #eef2f5;
        height: 75px;
        width: 100%;
        border-bottom-left-radius: 50% 15px;
        border-bottom-right-radius: 50% 15px;
    }

    /* الصورة */
    .avatar-wrapper {
        width: 86px;
        height: 86px;
        margin: -43px auto 15px;
        border-radius: 50%;
        background: #ffffff;
        padding: 4px;
        position: relative;
        z-index: 2;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        background-color: #f3f4f6;
    }

    .avatar-placeholder {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-color: #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
    }

    /* بيانات العضو */
    .member-content {
        text-align: center;
        padding: 0 15px 20px;
        flex-grow: 1;
    }

    .member-name {
        font-size: 19px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 10px 0;
    }

    .membership-badge {
        display: inline-block;
        background-color: #3b5346; /* لون زيتي داكن رسمي */
        color: #ffffff;
        padding: 4px 18px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    /* تذييل البطاقة (رقم العضوية والتاريخ) */
    .card-footer-grid {
        display: flex;
        border-top: 1px solid #e5e7eb;
        background-color: #f9fafb;
    }

    .footer-cell {
        flex: 1;
        padding: 12px 10px;
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .footer-cell:first-child {
        border-left: 1px solid #e5e7eb;
    }

    .cell-label {
        font-size: 11px;
        font-weight: 700;
        color: #4b5563;
    }

    .cell-value {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
        font-family: monospace;
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 50px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px dashed #d1d5db;
        color: #6b7280;
        font-weight: 600;
    }
</style>

<div class="assembly-page-wrapper">
    <div class="assembly-container">
        <div class="page-title">
            <h1>أعضاء الجمعية العمومية</h1>
        </div>

        @if(isset($items) && $items->count())
            <div class="assembly-grid">
                @foreach($items as $member)
                    <div class="assembly-card">
                        
                        {{-- الترويسة العلوية --}}
                        <div class="card-header-gray"></div>
                        
                        {{-- الصورة --}}
                        <div class="avatar-wrapper">
                            @php
                                $imageSrc = null;
                                if (!empty($member->photo)) {
                                    $imageSrc = asset('storage/' . $member->photo);
                                }
                            @endphp

                            @if($imageSrc)
                                <img src="{{ $imageSrc }}" class="avatar-img" alt="{{ $member->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="avatar-placeholder" style="display: none;">
                                    <svg width="40" height="40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                </div>
                            @else
                                <div class="avatar-placeholder">
                                    <svg width="40" height="40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                </div>
                            @endif
                        </div>

                        {{-- الاسم والمنصب --}}
                        <div class="member-content">
                            <h2 class="member-name">{{ $member->name ?? 'بدون اسم' }}</h2>
                            <div class="membership-badge">
                                {{ $member->position ?? 'عضوية عامة' }}
                            </div>
                        </div>

                        {{-- التفاصيل السفلية (الجدول المزدوج) --}}
                        <div class="card-footer-grid">
                            <div class="footer-cell">
                                <span class="cell-label">رقم العضوية:</span>
                                {{-- استخدام الـ ID كبديل وتنسيقه ليصبح مكون من 4 أرقام --}}
                                <span class="cell-value">{{ str_pad($member->id ?? 0, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="footer-cell">
                                <span class="cell-label">تاريخ الانضمام:</span>
                                <span class="cell-value">
                                    {{ !empty($member->join_date) ? \Carbon\Carbon::parse($member->join_date)->format('Y/m/d') : 'غير متوفر' }}
                                </span>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                لم يتم إضافة أعضاء للجمعية العمومية بعد.
            </div>
        @endif
    </div>
</div>
@endsection