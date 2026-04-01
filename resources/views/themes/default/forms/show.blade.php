<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box;font-family:'Tajawal',sans-serif}
        body{margin:0;background:linear-gradient(180deg,#f8fafc 0%,#eef2ff 100%);color:#0f172a}
        .container{max-width:1100px;margin:40px auto;padding:0 20px}
        .hero{margin-bottom:24px;padding:30px;border-radius:30px;background:linear-gradient(135deg,#0f766e 0%,#14b8a6 100%);color:#fff;box-shadow:0 20px 40px rgba(15,118,110,.18)}
        .hero h1{margin:0;font-size:34px;font-weight:900}
        .hero p{margin:10px 0 0;color:rgba(255,255,255,.88);font-size:15px;line-height:1.9}
        .layout{display:grid;grid-template-columns:1.15fr .85fr;gap:24px}
        .card{background:#fff;border:1px solid #e2e8f0;border-radius:28px;box-shadow:0 12px 30px rgba(15,23,42,.06);overflow:hidden}
        .card-head{padding:22px 24px;border-bottom:1px solid #eef2f7}
        .card-head h2{margin:0;font-size:22px;font-weight:900}
        .card-head p{margin:8px 0 0;color:#64748b;font-size:14px}
        .card-body{padding:24px}
        .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:18px}
        .full{grid-column:1/-1}
        label{display:block;margin-bottom:8px;font-weight:800;color:#334155}
        input, textarea, select{width:100%;padding:15px 16px;border:1px solid #cbd5e1;border-radius:16px;background:#fff;font-size:15px;outline:none;transition:.2s}
        input:focus, textarea:focus, select:focus{border-color:#14b8a6;box-shadow:0 0 0 4px rgba(20,184,166,.12)}
        textarea{min-height:140px;resize:vertical}
        .btn{width:100%;border:none;border-radius:16px;padding:15px 18px;font-size:15px;font-weight:900;cursor:pointer;color:#fff;background:linear-gradient(135deg,#ea580c 0%,#fb923c 100%);box-shadow:0 10px 24px rgba(234,88,12,.22)}
        .success{background:#dcfce7;color:#166534;padding:15px 16px;border-radius:16px;margin-bottom:18px;font-weight:700}
        .error{color:#b91c1c;font-size:13px;margin-top:6px}
        .side-box{padding:18px;border-radius:22px;margin-bottom:14px;color:#fff}
        .side-1{background:linear-gradient(135deg,#2563eb 0%,#60a5fa 100%)}
        .side-2{background:linear-gradient(135deg,#7c3aed 0%,#a78bfa 100%)}
        .side-3{background:linear-gradient(135deg,#d97706 0%,#f59e0b 100%)}
        .side-box small{display:block;opacity:.85;font-size:12px}
        .side-box strong{display:block;margin-top:8px;font-size:20px;font-weight:900;line-height:1.5}
        .track-link{display:inline-flex;align-items:center;justify-content:center;width:100%;margin-top:14px;padding:14px 18px;border-radius:16px;background:linear-gradient(135deg,#059669 0%,#10b981 100%);color:#fff;font-weight:900}
        .note{padding:14px 16px;border-radius:16px;background:#f8fafc;color:#475569;line-height:1.9;border:1px solid #e2e8f0}
        @media (max-width: 900px){
            .layout{grid-template-columns:1fr}
        }
        @media (max-width: 768px){
            .grid{grid-template-columns:1fr}
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>{{ $form->name }}</h1>
            <p>{{ $form->description ?: 'يرجى تعبئة النموذج التالي بدقة. بعد الإرسال سيتم إنشاء رقم طلب يمكنك استخدامه لاحقًا للاستعلام ومتابعة الحالة.' }}</p>
        </div>

        <div class="layout">
            <div class="card">
                <div class="card-head">
                    <h2>تعبئة النموذج</h2>
                    <p>أدخل بياناتك بشكل صحيح. رقم الجوال إلزامي لتمكين متابعة الطلب لاحقًا.</p>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="success">
                            <div>{{ session('success') }}</div>
                            @if(session('reference_number'))
                                <div style="margin-top:8px;font-weight:900;">رقم الطلب: {{ session('reference_number') }}</div>
                            @endif
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/forms/' . $form->slug) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="grid">
                            <div class="full">
                                <label>رقم الجوال *</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="05xxxxxxxx">
                                @error('phone')
                                    <div class="error">{{ $message }}</div>
                                @enderror
                            </div>

                            @foreach($form->fields as $field)
                                <div class="{{ $field->width === 'half' ? '' : 'full' }}">
                                    <label>{{ $field->label }} @if($field->is_required) * @endif</label>

                                    @if($field->type === 'textarea')
                                        <textarea name="{{ $field->name }}" placeholder="{{ $field->placeholder }}">{{ old($field->name) }}</textarea>
                                    @elseif($field->type === 'select')
                                        <select name="{{ $field->name }}">
                                            <option value="">اختر</option>
                                            @foreach(($field->options ?? []) as $option)
                                                <option value="{{ $option }}" @selected(old($field->name) == $option)>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($field->type === 'file')
                                        <input type="file" name="{{ $field->name }}">
                                    @else
                                        <input
                                            type="{{ in_array($field->type, ['email','number','date','url']) ? $field->type : 'text' }}"
                                            name="{{ $field->name }}"
                                            value="{{ old($field->name) }}"
                                            placeholder="{{ $field->placeholder }}"
                                        >
                                    @endif

                                    @error($field->name)
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <div style="margin-top:24px">
                            <button type="submit" class="btn">{{ $form->submit_button_text ?: 'إرسال' }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div>
                <div class="side-box side-1">
                    <small>متابعة الطلب</small>
                    <strong>يمكنك الاستعلام لاحقًا برقم الطلب ورقم الجوال</strong>
                </div>

                <div class="side-box side-2">
                    <small>الردود الرسمية</small>
                    <strong>ستظهر لك الردود المرسلة من الجهة مباشرة من صفحة الاستعلام</strong>
                </div>

                <div class="side-box side-3">
                    <small>رفع المرفقات</small>
                    <strong>إذا كان النموذج يحتوي على مرفق فيمكنك رفعه مباشرة أثناء التقديم</strong>
                </div>

                <div class="note">
                    بعد الإرسال سيظهر لك رقم الطلب. احتفظ به لاستخدامه لاحقًا عند متابعة الطلب أو الاستفسار عنه.
                </div>

                <a href="{{ url('/forms/' . $form->slug . '/track') }}" class="track-link">
                    الاستعلام عن الطلب
                </a>
            </div>
        </div>
    </div>
</body>
</html>
