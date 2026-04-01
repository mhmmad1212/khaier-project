<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الاستعلام عن الطلب - {{ $form->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;font-family:'Tajawal',sans-serif}
        body{margin:0;background:linear-gradient(180deg,#f8fafc 0%,#eef2ff 100%);color:#0f172a}
        a{text-decoration:none}
        .container{max-width:1100px;margin:40px auto;padding:0 20px}
        .hero{margin-bottom:24px;padding:28px;border-radius:28px;background:linear-gradient(135deg,#0f766e 0%,#14b8a6 100%);color:#fff;box-shadow:0 20px 40px rgba(15,118,110,.18)}
        .hero h1{margin:0;font-size:34px;font-weight:900}
        .hero p{margin:10px 0 0;color:rgba(255,255,255,.88);font-size:15px}
        .grid{display:grid;grid-template-columns:1.1fr .9fr;gap:24px}
        .card{background:#fff;border:1px solid #e2e8f0;border-radius:28px;box-shadow:0 12px 30px rgba(15,23,42,.06);overflow:hidden}
        .card-head{padding:22px 24px;border-bottom:1px solid #eef2f7}
        .card-head h2{margin:0;font-size:22px;font-weight:900}
        .card-head p{margin:8px 0 0;color:#64748b;font-size:14px}
        .card-body{padding:24px}
        .field{margin-bottom:18px}
        .label{display:block;margin-bottom:8px;font-weight:800;color:#334155}
        input, textarea{width:100%;padding:15px 16px;border:1px solid #cbd5e1;border-radius:16px;background:#fff;font-size:15px;outline:none;transition:.2s}
        input:focus, textarea:focus{border-color:#14b8a6;box-shadow:0 0 0 4px rgba(20,184,166,.12)}
        textarea{min-height:130px;resize:vertical}
        .btn{width:100%;border:none;border-radius:16px;padding:15px 18px;font-size:15px;font-weight:900;cursor:pointer;color:#fff;background:linear-gradient(135deg,#ea580c 0%,#fb923c 100%);box-shadow:0 10px 24px rgba(234,88,12,.22)}
        .btn-secondary{background:linear-gradient(135deg,#059669 0%,#10b981 100%);box-shadow:0 10px 24px rgba(5,150,105,.22)}
        .msg{padding:15px 16px;border-radius:16px;margin-bottom:18px;font-size:14px;font-weight:700}
        .success{background:#dcfce7;color:#166534}
        .error{background:#fee2e2;color:#991b1b}
        .meta-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px}
        .meta-box{padding:18px;border-radius:20px;color:#fff;box-shadow:0 10px 24px rgba(15,23,42,.08)}
        .meta-box small{display:block;opacity:.85;font-size:12px}
        .meta-box strong{display:block;margin-top:8px;font-size:22px;font-weight:900;line-height:1.3}
        .bg-1{background:linear-gradient(135deg,#2563eb 0%,#60a5fa 100%)}
        .bg-2{background:linear-gradient(135deg,#7c3aed 0%,#a78bfa 100%)}
        .bg-3{background:linear-gradient(135deg,#ea580c 0%,#fb923c 100%)}
        .thread{margin-top:20px}
        .bubble{border-radius:22px;padding:18px 18px 16px;margin-bottom:14px}
        .bubble.staff{background:#ecfdf5;border:1px solid #a7f3d0}
        .bubble.customer{background:#eff6ff;border:1px solid #bfdbfe}
        .bubble.note{background:#f8fafc;border:1px solid #e2e8f0}
        .bubble-head{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin-bottom:10px}
        .badge{display:inline-block;padding:7px 12px;border-radius:999px;font-size:12px;font-weight:800}
        .badge.staff{background:#d1fae5;color:#065f46}
        .badge.customer{background:#dbeafe;color:#1d4ed8}
        .badge.note{background:#e5e7eb;color:#374151}
        .muted{color:#64748b;font-size:12px}
        .bubble-text{font-size:15px;line-height:1.95;color:#0f172a}
        .reply-box{margin-top:22px;padding:20px;border-radius:24px;background:#f8fafc;border:1px solid #e2e8f0}
        .empty{padding:30px;text-align:center;color:#64748b;border:1px dashed #cbd5e1;border-radius:22px;background:#fff}
        @media (max-width: 900px){
            .grid{grid-template-columns:1fr}
            .meta-grid{grid-template-columns:1fr}
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>الاستعلام عن الطلب</h1>
            <p>يمكنك متابعة حالة الطلب والاطلاع على الردود الرسمية باستخدام رقم الطلب ورقم الجوال المرتبط به.</p>
        </div>

        <div class="grid">
            <div class="card">
                <div class="card-head">
                    <h2>البحث عن الطلب</h2>
                    <p>أدخل رقم الطلب ورقم الجوال للوصول إلى حالة الطلب والرسائل المرتبطة به.</p>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="msg success">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="msg error">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ url('/forms/' . $form->slug . '/track') }}">
                        @csrf

                        <div class="field">
                            <label class="label">رقم الطلب</label>
                            <input type="text" name="reference_number" value="{{ old('reference_number', $submission->reference_number ?? '') }}" placeholder="مثال: REQ-20260401-000001">
                        </div>

                        <div class="field">
                            <label class="label">رقم الجوال</label>
                            <input type="text" name="phone" value="{{ old('phone', $submission->phone ?? '') }}" placeholder="05xxxxxxxx">
                        </div>

                        <button type="submit" class="btn">استعلام الآن</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <h2>{{ $form->name }}</h2>
                    <p>صفحة متابعة الطلبات والردود الخاصة بهذا النموذج.</p>
                </div>
                <div class="card-body">
                    <div class="empty">
                        يمكنك استخدام هذه الصفحة في أي وقت لمعرفة حالة الطلب، ومشاهدة الردود، وإضافة رد جديد إذا سمحت لك الجهة بذلك.
                    </div>
                </div>
            </div>
        </div>

        @isset($submission)
            <div style="height:24px"></div>

            @if($submission)
                @php
                    $statusLabel = match($submission->status) {
                        'new' => 'جديد',
                        'under_review' => 'قيد المراجعة',
                        'awaiting_completion' => 'بانتظار استكمال',
                        'replied' => 'تم الرد',
                        'completed' => 'مكتمل',
                        'rejected' => 'مرفوض',
                        default => $submission->status,
                    };
                @endphp

                <div class="card">
                    <div class="card-head">
                        <h2>تفاصيل الطلب</h2>
                        <p>فيما يلي معلومات الطلب والحالة الحالية وسجل التواصل الظاهر لك.</p>
                    </div>
                    <div class="card-body">
                        <div class="meta-grid">
                            <div class="meta-box bg-1">
                                <small>رقم الطلب</small>
                                <strong>{{ $submission->reference_number }}</strong>
                            </div>
                            <div class="meta-box bg-2">
                                <small>رقم الجوال</small>
                                <strong>{{ $submission->phone }}</strong>
                            </div>
                            <div class="meta-box bg-3">
                                <small>حالة الطلب</small>
                                <strong>{{ $statusLabel }}</strong>
                            </div>
                        </div>

                        <div class="thread">
                            @php
                                $visibleMessages = ($submission->messages ?? collect())->filter(fn($message) => (bool) $message->is_visible_to_customer);
                            @endphp

                            @forelse($visibleMessages as $message)
                                @php
                                    $type = $message->type;
                                    $bubbleClass = $type === 'customer_reply' ? 'customer' : ($type === 'internal_note' ? 'note' : 'staff');
                                    $badgeClass = $type === 'customer_reply' ? 'customer' : ($type === 'internal_note' ? 'note' : 'staff');
                                    $title = match($type) {
                                        'staff_reply' => 'رد الجهة',
                                        'customer_reply' => 'ردك',
                                        'internal_note' => 'ملاحظة',
                                        default => $type,
                                    };
                                @endphp

                                <div class="bubble {{ $bubbleClass }}">
                                    <div class="bubble-head">
                                        <span class="badge {{ $badgeClass }}">{{ $title }}</span>
                                        <span class="muted">{{ $message->created_at?->format('Y-m-d h:i A') }}</span>
                                    </div>
                                    <div class="bubble-text">{{ $message->message }}</div>
                                </div>
                            @empty
                                <div class="empty">لا توجد ردود ظاهرة لك حتى الآن.</div>
                            @endforelse
                        </div>

                        @if($submission->allow_customer_reply)
                            <div class="reply-box">
                                <form method="POST" action="{{ url('/forms/' . $form->slug . '/track/' . $submission->id . '/reply') }}">
                                    @csrf
                                    <input type="hidden" name="phone" value="{{ $submission->phone }}">

                                    <div class="field" style="margin-bottom:0">
                                        <label class="label">إضافة رد جديد</label>
                                        <textarea name="message" placeholder="اكتب تعليقك أو استفسارك هنا"></textarea>
                                    </div>

                                    <div style="margin-top:18px">
                                        <button type="submit" class="btn btn-secondary">إرسال الرد</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div style="height:24px"></div>
                <div class="card">
                    <div class="card-body">
                        <div class="msg error" style="margin:0">لم يتم العثور على طلب مطابق للبيانات المدخلة.</div>
                    </div>
                </div>
            @endif
        @endisset
    </div>
</body>
</html>
