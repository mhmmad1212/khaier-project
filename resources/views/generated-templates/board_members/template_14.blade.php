@extends('themes.default.layouts.app')

@section('content')
<style>
    /* استدعاء خط Cairo */
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap');

    .board-wrapper {
        direction: rtl;
        text-align: right;
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
        font-family: 'Cairo', sans-serif;
    }

    /* عنوان الصفحة */
    .page-header {
        text-align: center;
        margin-bottom: 60px;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 25px;
    }

    .page-header h1 {
        font-size: 30px;
        font-weight: 800;
        color: #1f2937;
        margin: 0;
        position: relative;
        display: inline-block;
    }

    /* شبكة البطاقات */
    .board-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 40px;
    }

    /* تصميم البطاقة */
    .member-card {
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
    }

    .member-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.12);
    }

    /* خلفية علوية للبطاقة */
    .card-header-bg {
        height: 100px;
        background: linear-gradient(135deg, #115e59 0%, #14b8a6 100%);
    }

    /* صورة العضو */
    .member-avatar-wrapper {
        text-align: center;
        margin-top: -60px;
        position: relative;
        z-index: 2;
    }

    .member-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #ffffff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        background-color: #f3f4f6;
    }

    .fallback-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        color: #9ca3af;
    }

    /* بيانات العضو */
    .member-info {
        padding: 20px 25px;
        text-align: center;
        flex-grow: 1;
    }

    .member-name {
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 8px 0;
        color: #111827;
    }

    .member-position {
        font-size: 15px;
        font-weight: 600;
        color: #14b8a6;
        margin: 0 0 15px 0;
        background: rgba(20, 184, 166, 0.1);
        display: inline-block;
        padding: 5px 15px;
        border-radius: 20px;
    }

    .member-bio {
        font-size: 14px;
        color: #6b7280;
        line-height: 1.6;
        margin: 0;
    }

    /* أزرار التواصل */
    .member-actions {
        padding: 0 25px 30px 25px;
        display: flex;
        gap: 15px;
        justify-content: center;
    }

    .btn-action {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 15px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .btn-action svg {
        width: 18px;
        height: 18px;
        transition: transform 0.3s ease;
    }

    .btn-action:hover svg {
        transform: scale(1.1) rotate(5deg);
    }

    .btn-whatsapp:hover {
        transform: translateY(-3px);
        filter: brightness(1.1);
    }

    .btn-email {
        background-color: #f3f4f6;
        color: #374151;
    }

    .btn-email:hover {
        background-color: #e5e7eb;
        transform: translateY(-3px);
    }

    /* حالة عدم وجود بيانات */
    .empty-state {
        text-align: center;
        padding: 60px;
        border: 2px dashed #e5e7eb;
        border-radius: 16px;
        color: #9ca3af;
        font-size: 18px;
        font-weight: 600;
        background: #f9fafb;
    }
</style>

<div class="board-wrapper">

    <div class="page-header">
        <h1>مجلس الإدارة</h1>
    </div>

    @php
        $buttonColor = $siteSettings->button_color ?? '#16a34a';
    @endphp

    @if(isset($items) && $items->count())
        <div class="board-grid">

            @foreach($items as $member)
                <div class="member-card">
                    
                    <div class="card-header-bg"></div>
                    
                    {{-- الصورة --}}
                    <div class="member-avatar-wrapper">
                        @php
                            $imageSrc = null;
                            
                            // التحقق أولاً من العلاقة photoMedia
                            if (isset($member->photoMedia) && !empty($member->photoMedia->file)) {
                                $imageSrc = asset('storage/' . $member->photoMedia->file);
                            } 
                            // التحقق ثانياً من حقل photo المباشر
                            elseif (!empty($member->photo)) {
                                $imageSrc = asset('storage/' . $member->photo);
                            }
                        @endphp

                        @if($imageSrc)
                            {{-- استخدمنا onerror هنا حتى لو كان الرابط خطأ تتحول الصورة للأيقونة الرمزية --}}
                            <img loading="lazy" decoding="async" src="{{ $imageSrc }}" 
                                 class="member-avatar" 
                                 alt="{{ $member->name ?? 'صورة العضو' }}" 
                                 onerror="this.onerror=null; this.outerHTML='<div class=\'member-avatar fallback-avatar\'>👤</div>';">
                        @else
                            <div class="member-avatar fallback-avatar">👤</div>
                        @endif
                    </div>

                    {{-- البيانات الشخصية --}}
                    <div class="member-info">
                        <h2 class="member-name">{{ $member->name ?? 'بدون اسم' }}</h2>
                        
                        @if(!empty($member->position))
                            <p class="member-position">{{ $member->position }}</p>
                        @endif

                        @if(!empty($member->bio))
                            <p class="member-bio">{{ $member->bio }}</p>
                        @endif
                    </div>

                    {{-- وسائل التواصل --}}
                    <div class="member-actions">
                        @if(!empty($member->phone))
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $member->phone) }}" 
                               target="_blank" 
                               class="btn-action btn-whatsapp" 
                               style="background-color: {{ $buttonColor }}; color: #ffffff; box-shadow: 0 4px 12px {{ $buttonColor }}66;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                واتساب
                            </a>
                        @endif

                        @if(!empty($member->email))
                            <a href="mailto:{{ $member->email }}" class="btn-action btn-email">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                إيميل
                            </a>
                        @endif
                    </div>

                </div>
            @endforeach

        </div>
    @else
        <div class="empty-state">
            لا يوجد أعضاء مجلس إدارة
        </div>
    @endif

</div>
@endsection