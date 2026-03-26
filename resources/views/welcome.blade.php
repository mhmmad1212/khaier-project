<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة الجمعيات</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 font-sans text-gray-800">

    <!-- Navbar -->
    <header class="bg-white/90 backdrop-blur sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <h1 class="text-xl sm:text-2xl font-extrabold text-blue-600">منصة الجمعيات</h1>

            <div class="flex items-center gap-3">
                <a href="/admin" class="hidden sm:inline text-gray-600 hover:text-blue-600 transition">
                    دخول لوحة المشرف
                </a>
                <a href="/admin/register" class="bg-blue-600 text-white px-4 sm:px-5 py-2 rounded-xl hover:bg-blue-700 shadow transition">
                    ابدأ الآن
                </a>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative overflow-hidden bg-gradient-to-l from-blue-600 via-indigo-600 to-purple-600 text-white">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-20 sm:py-28 text-center">
            <span class="inline-block bg-white/15 border border-white/20 rounded-full px-4 py-2 text-sm mb-6">
                حل رقمي متكامل لإدارة الجمعيات باحترافية
            </span>

            <h2 class="text-3xl sm:text-5xl font-extrabold leading-tight mb-6">
                أنشئ موقع جمعيتك وأدره
                <span class="block mt-2">من لوحة تحكم واحدة بسهولة</span>
            </h2>

            <p class="text-base sm:text-lg opacity-90 max-w-3xl mx-auto mb-10 leading-8">
                منصة متخصصة تساعد الجمعيات على إنشاء مواقعها، إدارة الأخبار والمشاريع،
                تنظيم المحتوى، والتحكم الكامل في الواجهة والإعدادات من مكان واحد.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/admin/register" class="w-full sm:w-auto bg-white text-blue-700 px-8 py-3 rounded-xl font-bold hover:bg-gray-100 transition shadow-lg">
                    إنشاء جمعية جديدة
                </a>

                <a href="#features" class="w-full sm:w-auto border border-white px-8 py-3 rounded-xl hover:bg-white hover:text-blue-700 transition">
                    تعرّف على المزايا
                </a>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-12 bg-white border-b border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
            <div class="bg-gray-50 rounded-2xl p-6">
                <h3 class="text-3xl font-extrabold text-blue-600 mb-2">سريع</h3>
                <p class="text-gray-600">إطلاق موقع الجمعية خلال وقت قصير</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6">
                <h3 class="text-3xl font-extrabold text-blue-600 mb-2">مرن</h3>
                <p class="text-gray-600">إدارة محتوى وأقسام متعددة بسهولة</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6">
                <h3 class="text-3xl font-extrabold text-blue-600 mb-2">متكامل</h3>
                <p class="text-gray-600">لوحة تحكم وموقع عام في نظام واحد</p>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-14">
                <h3 class="text-3xl sm:text-4xl font-extrabold mb-4">لماذا منصة الجمعيات؟</h3>
                <p class="text-gray-600 max-w-2xl mx-auto leading-8">
                    لأنك تحتاج حلاً واضحًا، سهل الإدارة، ويخدم الجمعية من الناحية التشغيلية والتسويقية.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition">
                    <div class="text-3xl mb-4">🖥️</div>
                    <h4 class="text-xl font-bold mb-3">لوحة تحكم احترافية</h4>
                    <p class="text-gray-600 leading-8">
                        إدارة سهلة للأخبار، الصفحات، البيانات، والمحتوى الداخلي من خلال لوحة واضحة وسريعة.
                    </p>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition">
                    <div class="text-3xl mb-4">🏢</div>
                    <h4 class="text-xl font-bold mb-3">موقع خاص لكل جمعية</h4>
                    <p class="text-gray-600 leading-8">
                        كل جمعية تحصل على واجهتها الخاصة ومحتواها المستقل بشكل منظم وقابل للتوسع.
                    </p>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition">
                    <div class="text-3xl mb-4">⚙️</div>
                    <h4 class="text-xl font-bold mb-3">إدارة متكاملة للمحتوى</h4>
                    <p class="text-gray-600 leading-8">
                        تنظيم الأخبار والمشاريع والأقسام والهوية البصرية ضمن منصة واحدة تخدم العمل اليومي.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="py-20 bg-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-14">
                <h3 class="text-3xl sm:text-4xl font-extrabold mb-4">كيف تبدأ؟</h3>
                <p class="text-gray-600">ثلاث خطوات بسيطة للانطلاق</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 text-center shadow-sm">
                    <div class="w-14 h-14 mx-auto mb-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-2xl font-extrabold">1</div>
                    <h4 class="font-bold text-xl mb-3">سجل الجمعية</h4>
                    <p class="text-gray-600 leading-8">أنشئ حساب الجمعية وابدأ إعداد بياناتها الأساسية.</p>
                </div>

                <div class="bg-white rounded-2xl p-8 text-center shadow-sm">
                    <div class="w-14 h-14 mx-auto mb-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-2xl font-extrabold">2</div>
                    <h4 class="font-bold text-xl mb-3">ادخل لوحة التحكم</h4>
                    <p class="text-gray-600 leading-8">أدر الصفحات، الأخبار، والمحتوى من لوحة تحكم منظمة.</p>
                </div>

                <div class="bg-white rounded-2xl p-8 text-center shadow-sm">
                    <div class="w-14 h-14 mx-auto mb-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-2xl font-extrabold">3</div>
                    <h4 class="font-bold text-xl mb-3">انطلق بموقعك</h4>
                    <p class="text-gray-600 leading-8">يصبح لديك موقع جاهز يعرض هوية الجمعية وخدماتها ومحتواها.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-24 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <h3 class="text-3xl sm:text-4xl font-extrabold mb-6">
                ابدأ اليوم في بناء حضور رقمي احترافي لجمعيتك
            </h3>
            <p class="text-white/90 mb-8 leading-8">
                اجمع بين سهولة الإدارة والمظهر الاحترافي في منصة واحدة مصممة خصيصًا للجمعيات.
            </p>
            <a href="/admin/register" class="inline-block bg-white text-blue-700 px-10 py-4 rounded-xl font-bold hover:bg-gray-100 transition shadow-lg">
                إنشاء حساب الآن
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-8 text-center">
        <p class="text-gray-500">© 2026 منصة الجمعيات - جميع الحقوق محفوظة</p>
    </footer>

</body>
</html>