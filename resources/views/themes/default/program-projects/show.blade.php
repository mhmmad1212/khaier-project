<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->title }} | {{ $siteSettings->site_name ?? $siteSettings->association_name ?? 'الجمعية' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root{
            --primary: {{ $siteSettings->primary_color ?? '#ea580c' }};
            --secondary: {{ $siteSettings->secondary_color ?? '#fdba74' }};
            --button: {{ $siteSettings->button_color ?? ($siteSettings->secondary_color ?? '#fdba74') }};
            --dark:#1e293b;
            --muted:#64748b;
            --light:#f8fafc;
            --white:#fff;
            --border:#e2e8f0;
            --radius:18px;
            --shadow:0 10px 30px rgba(0,0,0,.06);
        }
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Tajawal',sans-serif}
        body{background:var(--light);color:var(--dark);line-height:1.8}
        a{text-decoration:none;color:inherit}
        img{display:block;max-width:100%}
        .container{width:min(1200px,calc(100% - 40px));margin:0 auto}
        header{background:#fff;border-bottom:3px solid var(--secondary);box-shadow:0 4px 20px rgba(0,0,0,.04);position:sticky;top:0;z-index:30}
        .nav{min-height:84px;display:flex;align-items:center;justify-content:space-between;gap:20px}
        .brand{display:flex;align-items:center;gap:14px}
        .brand img{max-height:56px;border-radius:8px;object-fit:contain}
        .brand-title{font-weight:900;color:var(--primary);line-height:1.2}
        .brand-title span{display:block;color:var(--muted);font-size:.9rem;margin-top:4px}
        .back-btn{background:linear-gradient(45deg,var(--primary),var(--secondary));color:#fff;padding:10px 20px;border-radius:999px;font-weight:800}
        .hero{
            min-height:380px;
            display:flex;
            align-items:center;
            color:#fff;
            background:
                linear-gradient(rgba(0,0,0,.5),rgba(0,0,0,.5)),
                url('{{ $project->cover_image ? asset('storage/' . ltrim($project->cover_image, '/')) : "https://via.placeholder.com/1600x800?text=Project" }}') center/cover no-repeat;
        }
        .hero-content{padding:70px 0;max-width:800px}
        .hero-badge{display:inline-block;background:rgba(255,255,255,.14);padding:8px 14px;border-radius:999px;font-weight:800;margin-bottom:16px}
        .hero h1{font-size:3rem;font-weight:900;margin-bottom:16px;line-height:1.35}
        .hero p{font-size:1.1rem;color:rgba(255,255,255,.92)}
        .wrap{padding:60px 0}
        .grid{display:grid;grid-template-columns:1.3fr .7fr;gap:28px;align-items:start}
        .card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow)}
        .main-image{width:100%;height:420px;object-fit:cover;background:#f1f5f9;border-radius:var(--radius) var(--radius) 0 0}
        .content{padding:28px}
        .section-title{font-size:1.5rem;font-weight:900;color:var(--primary);margin-bottom:16px}
        .desc{color:var(--muted);white-space:pre-line}
        .side{padding:24px;position:sticky;top:100px}
        .meta{display:grid;gap:14px}
        .meta-item{background:var(--light);border:1px solid var(--border);border-radius:14px;padding:14px 16px}
        .meta-label{display:block;color:var(--muted);font-size:.9rem;font-weight:700;margin-bottom:6px}
        .meta-value{font-weight:900}
        .donate{margin-top:18px;background:linear-gradient(135deg,rgba(234,88,12,.08),rgba(253,186,116,.18));border:1px solid rgba(234,88,12,.16);border-radius:16px;padding:18px}
        .money{font-size:1.6rem;color:var(--primary);font-weight:900;margin-top:4px}
        .donate-btn{display:block;text-align:center;margin-top:18px;background:linear-gradient(45deg,var(--primary),var(--secondary));color:#fff;padding:14px 18px;border-radius:14px;font-weight:900}
        .gallery,.attachments,.contact{margin-top:28px;padding:26px}
        .gallery-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px}
        .gallery-item{border:1px solid var(--border);border-radius:16px;overflow:hidden;background:#fff}
        .gallery-item img{width:100%;height:200px;object-fit:cover}
        .attachments-list{display:grid;gap:14px}
        .attachment{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:16px 18px;border:1px solid var(--border);border-radius:14px;background:var(--light)}
        .attachment-title{font-weight:800}
        .attachment-type{color:var(--muted);font-size:.92rem;margin-top:4px}
        .attachment-link{background:#fff;border:1px solid var(--border);padding:10px 16px;border-radius:12px;color:var(--primary);font-weight:800;white-space:nowrap}
        .contact-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px}
        .contact-box{background:var(--light);border:1px solid var(--border);border-radius:14px;padding:18px}
        .contact-box h4{color:var(--primary);font-weight:900;margin-bottom:8px}
        .contact-box p{color:var(--muted);font-weight:700}
        footer{margin-top:60px;background:#0f172a;color:#cbd5e1;padding:22px 0;text-align:center}
        @media (max-width: 992px){.grid{grid-template-columns:1fr}.side{position:static}.hero h1{font-size:2.2rem}}
        @media (max-width: 640px){.hero{min-height:320px}.hero-content{padding:50px 0}.hero h1{font-size:1.8rem}.main-image{height:280px}.attachment{flex-direction:column;align-items:flex-start}.attachment-link{width:100%;text-align:center}}
    </style>
</head>
<body>
<header>
    <div class="container nav">
        <a href="{{ url('/') }}" class="brand">
            @if(!empty($siteSettings->logo))
                <img src="{{ asset('storage/' . ltrim($siteSettings->logo, '/')) }}" alt="{{ $siteSettings->association_name ?? 'الشعار' }}">
            @endif
            <div class="brand-title">
                {{ $siteSettings->association_name ?? $siteSettings->site_name ?? 'الجمعية' }}
                <span>{{ $siteSettings->site_name ?? 'الموقع الرسمي' }}</span>
            </div>
        </a>
        <a href="{{ url('/') }}" class="back-btn">العودة للرئيسية</a>
    </div>
</header>

<section class="hero">
    <div class="container hero-content">
        <span class="hero-badge">تفاصيل المشروع</span>
        <h1>{{ $project->title }}</h1>
        <p>{{ \Illuminate\Support\Str::limit(strip_tags($project->description ?? ''), 180) }}</p>
    </div>
</section>

<section class="wrap">
    <div class="container">
        <div class="grid">
            <div>
                <div class="card">
                    <img class="main-image" src="{{ $project->cover_image ? asset('storage/' . ltrim($project->cover_image, '/')) : 'https://via.placeholder.com/1200x700?text=Project' }}" alt="{{ $project->title }}">
                    <div class="content">
                        <h2 class="section-title">وصف المشروع</h2>
                        <div class="desc">{{ $project->description ?? 'لا يوجد وصف متاح لهذا المشروع حالياً.' }}</div>
                    </div>
                </div>

                @if(isset($project->galleryImages) && $project->galleryImages->count())
                    <div class="card gallery">
                        <h2 class="section-title">معرض الصور</h2>
                        <div class="gallery-grid">
                            @foreach($project->galleryImages as $image)
                                @php
                                    $galleryUrl = null;
                                    if (!empty($image->mediaItem?->file)) {
                                        $galleryUrl = asset('storage/' . ltrim($image->mediaItem->file, '/'));
                                    } elseif (!empty($image->file)) {
                                        $galleryUrl = asset('storage/' . ltrim($image->file, '/'));
                                    }
                                @endphp
                                @if($galleryUrl)
                                    <div class="gallery-item">
                                        <img src="{{ $galleryUrl }}" alt="{{ $project->title }}">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(isset($project->attachments) && $project->attachments->count())
                    <div class="card attachments">
                        <h2 class="section-title">المرفقات والتقارير</h2>
                        <div class="attachments-list">
                            @foreach($project->attachments as $attachment)
                                @php
                                    $attachmentUrl = null;
                                    if (!empty($attachment->mediaItem?->file)) {
                                        $attachmentUrl = asset('storage/' . ltrim($attachment->mediaItem->file, '/'));
                                    }
                                @endphp
                                <div class="attachment">
                                    <div>
                                        <div class="attachment-title">{{ $attachment->title ?? 'مرفق المشروع' }}</div>
                                        <div class="attachment-type">{{ $attachment->collection ?? 'ملف مرفق' }}</div>
                                    </div>
                                    @if($attachmentUrl)
                                        <a href="{{ $attachmentUrl }}" target="_blank" class="attachment-link">عرض الملف</a>
                                    @else
                                        <span class="attachment-link" style="opacity:.6;pointer-events:none;">غير متوفر</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="card contact">
                    <h2 class="section-title">معلومات التواصل</h2>
                    <div class="contact-grid">
                        <div class="contact-box">
                            <h4>الهاتف</h4>
                            <p>{{ $siteSettings->phone ?? 'غير متوفر' }}</p>
                        </div>
                        <div class="contact-box">
                            <h4>البريد الإلكتروني</h4>
                            <p>{{ $siteSettings->email ?? 'غير متوفر' }}</p>
                        </div>
                        <div class="contact-box">
                            <h4>العنوان</h4>
                            <p>{{ $siteSettings->address ?? 'غير متوفر' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <aside>
                <div class="card side">
                    <h2 class="section-title" style="font-size:1.3rem;">معلومات المشروع</h2>
                    <div class="meta">
                        <div class="meta-item">
                            <span class="meta-label">اسم المشروع</span>
                            <div class="meta-value">{{ $project->title }}</div>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">تاريخ البداية</span>
                            <div class="meta-value">{{ $project->start_date ?? 'غير محدد' }}</div>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">تاريخ النهاية</span>
                            <div class="meta-value">{{ $project->end_date ?? 'غير محدد' }}</div>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">حالة المشروع</span>
                            <div class="meta-value">{{ $project->is_active ? 'نشط' : 'غير نشط' }}</div>
                        </div>
                    </div>

                    <div class="donate">
                        <span class="meta-label">قيمة المشروع</span>
                        <div class="money">{{ number_format((float) ($project->project_amount ?? 0), 2) }}</div>

                        <span class="meta-label" style="margin-top:16px;display:block;">مبلغ التبرعات</span>
                        <div class="money">{{ number_format((float) ($project->donation_amount ?? 0), 2) }}</div>

                        @if(!empty($project->donation_url))
                            <a href="{{ $project->donation_url }}" target="_blank" class="donate-btn">تبرع الآن</a>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<footer>
    جميع الحقوق محفوظة &copy; {{ date('Y') }} {{ $siteSettings->site_name ?? $siteSettings->association_name ?? 'الجمعية' }}
</footer>
</body>
</html>
