@php
    use Illuminate\Support\Facades\DB;

    $association = null;
    $homeUrl = url('/');

    try {
        $association = DB::connection('mysql')
            ->table('associations')
            ->where('domain', request()->getHost())
            ->orWhere('domain', preg_replace('/^www\./i', '', request()->getHost()))
            ->first();

        if (!empty($association?->domain)) {
            $homeUrl = request()->getScheme() . '://' . $association->domain;
        }
    } catch (\Throwable $e) {
        $homeUrl = url('/');
    }
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - الصفحة غير موجودة</title>
    <style>
        body{
            margin:0;
            font-family:Tahoma, Arial, sans-serif;
            background:linear-gradient(135deg,#f8fafc 0%,#eef6f4 100%);
            color:#1f2937;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:24px;
        }
        .box{
            width:100%;
            max-width:700px;
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:24px;
            padding:40px 28px;
            text-align:center;
            box-shadow:0 20px 50px rgba(0,0,0,.06);
        }
        .code{
            font-size:84px;
            font-weight:900;
            line-height:1;
            color:#0f766e;
            margin-bottom:16px;
        }
        .title{
            font-size:28px;
            font-weight:800;
            margin-bottom:12px;
            color:#111827;
        }
        .desc{
            font-size:16px;
            line-height:1.9;
            color:#6b7280;
            max-width:540px;
            margin:0 auto 26px;
        }
        .actions{
            display:flex;
            gap:12px;
            justify-content:center;
            flex-wrap:wrap;
        }
        .btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            text-decoration:none;
            padding:12px 18px;
            border-radius:12px;
            font-weight:700;
            font-size:15px;
            transition:.2s ease;
        }
        .btn-primary{
            background:#0f766e;
            color:#fff;
        }
        .btn-secondary{
            background:#f3f4f6;
            color:#111827;
            border:1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="code">404</div>
        <div class="title">عذرًا، الصفحة غير موجودة</div>
        <div class="desc">
            يبدو أن الرابط الذي تحاول الوصول إليه غير صحيح، أو أن الصفحة قد تم نقلها أو حذفها.
            يمكنك العودة إلى الصفحة الرئيسية والمتابعة من هناك.
        </div>

        <div class="actions">
            <a href="{{ $homeUrl }}" class="btn btn-primary">العودة إلى الصفحة الرئيسية</a>
            <a href="javascript:history.back()" class="btn btn-secondary">الرجوع للصفحة السابقة</a>
        </div>
    </div>
</body>
</html>
