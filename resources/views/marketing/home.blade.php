<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة خير</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- ألوان مخصصة (تعدل حسب شعارك) -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E3A8A',   // أزرق احترافي
                        secondary: '#6366F1', // بنفسجي
                        accent: '#22C55E'     // أخضر (خير)
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800">

<!-- Navbar -->
<header class="bg-white/90 backdrop-blur sticky top-0 z-50 border-b">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-accent rounded-xl flex items-center justify-center text-white font-bold">
                خ
            </div>
            <h1 class="text-xl font-extrabold text-primary">منصة خير</h1>
        </div>

        <div class="flex items-center gap-4">
            <a href="/khaier/login" class="text-gray-600 hover:text-primary">دخول الإدارة</a>
            <a href="/admin/register" class="bg-primary text-white px-5 py-2 rounded-xl hover:bg-blue-800 shadow">
                ابدأ الآن
            </a>
        </div>
    </div>
</header>

<!-- Hero -->
<section class="bg-gradient-to-l from-primary via-secondary to-indigo-500 text-white py-28 text-center">

    <h2 class="text-5xl font-extrabold mb-6 leading-tight">
        منصة متكاملة لإدارة مواقع الجمعيات
    </h2>

    <p class="text-lg opacity-90 mb-10 max-w-2xl mx-auto">
        أنشئ موقع جمعيتك، اختر التصميم المناسب، وأدر كل شيء بسهولة من لوحة تحكم واحدة
    </p>

    <div class="flex justify-center gap-4 flex-wrap">
        <a href="/admin/register" class="bg-white text-primary px-8 py-3 rounded-xl font-bold shadow hover:bg-gray-100">
            🚀 إنشاء جمعية
        </a>

        <a href="#designs" class="border border-white px-8 py-3 rounded-xl hover:bg-white hover:text-primary">
            استعرض التصاميم
        </a>
    </div>

</section>

<!-- Value Proposition -->
<section class="py-20 max-w-7xl mx-auto px-6 text-center">
    <h3 class="text-3xl font-bold mb-10">حل رقمي متكامل للجمعيات</h3>

    <div class="grid md:grid-cols-3 gap-10">

        <div class="bg-white p-8 rounded-2xl shadow hover:shadow-xl transition">
            <h4 class="text-xl font-bold mb-3">⚡ سرعة عالية</h4>
            <p class="text-gray-600">مواقع سريعة ومحسنة لتجربة المستخدم ومحركات البحث</p>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow hover:shadow-xl transition">
            <h4 class="text-xl font-bold mb-3">🔒 أمان متقدم</h4>
            <p class="text-gray-600">حماية كاملة للبيانات مع بنية آمنة ومستقرة</p>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow hover:shadow-xl transition">
            <h4 class="text-xl font-bold mb-3">🎨 مرونة التصميم</h4>
            <p class="text-gray-600">اختر من عدة تصاميم جاهزة وقم بتخصيصها بسهولة</p>
        </div>

    </div>
</section>

<!-- Designs Section -->
<section id="designs" class="bg-gray-100 py-20 text-center">
    <h3 class="text-3xl font-bold mb-12">تصاميم متعددة تناسب جميع الجمعيات</h3>

    <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-8 px-6">

        <div class="bg-white p-6 rounded-2xl shadow">
            <h4 class="font-bold mb-2">تصميم حديث</h4>
            <p class="text-gray-600 text-sm">مناسب للجمعيات التقنية والشبابية</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow">
            <h4 class="font-bold mb-2">تصميم رسمي</h4>
            <p class="text-gray-600 text-sm">لجمعيات القطاع الحكومي والخيري</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow">
            <h4 class="font-bold mb-2">تصميم بسيط</h4>
            <p class="text-gray-600 text-sm">خفيف وسريع وسهل الاستخدام</p>
        </div>

    </div>
</section>

<!-- Steps -->
<section class="py-20 text-center">
    <h3 class="text-3xl font-bold mb-12">ابدأ خلال 3 خطوات</h3>

    <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-10 px-6">

        <div>
            <div class="text-4xl font-bold text-primary">1</div>
            <p class="mt-4">سجل الجمعية</p>
        </div>

        <div>
            <div class="text-4xl font-bold text-primary">2</div>
            <p class="mt-4">اختر التصميم</p>
        </div>

        <div>
            <div class="text-4xl font-bold text-primary">3</div>
            <p class="mt-4">انطلق بموقعك</p>
        </div>

    </div>
</section>

<!-- CTA -->
<section class="py-24 text-center bg-gradient-to-r from-primary to-secondary text-white">
    <h3 class="text-4xl font-extrabold mb-6">
        ابدأ الآن واطلق موقع جمعيتك 🚀
    </h3>

    <a href="/admin/register" class="bg-white text-primary px-10 py-4 rounded-xl font-bold hover:bg-gray-100 shadow">
        إنشاء حساب مجاني
    </a>
</section>

<!-- Footer -->
<footer class="bg-white border-t py-8 text-center">
    <p class="text-gray-500">© 2026 منصة خير - جميع الحقوق محفوظة</p>
</footer>

</body>
</html>
