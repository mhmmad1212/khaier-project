<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'الموقع متوقف' }}</title>
    <style>
        body{
            margin:0;
            font-family:Tahoma, Arial, sans-serif;
            background:#f8fafc;
            display:flex;
            align-items:center;
            justify-content:center;
            min-height:100vh;
            color:#111827;
        }
        .card{
            width:min(92vw, 640px);
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:24px;
            padding:40px 28px;
            text-align:center;
            box-shadow:0 18px 40px rgba(15,23,42,.08);
        }
        h1{
            margin:0 0 14px;
            font-size:32px;
            color:#b91c1c;
        }
        p{
            margin:0;
            font-size:18px;
            line-height:2;
            color:#374151;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>{{ $title ?? 'الموقع متوقف' }}</h1>
        <p>{{ $message ?? 'الموقع متوقف، الرجاء التواصل مع نظام خيل.' }}</p>
    </div>
</body>
</html>
