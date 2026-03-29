<?php

namespace App\Providers;

use Filament\Tables\Table;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // حقن تصميم احترافي لجميع الجداول في النظام
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::HEAD_END,
            fn (): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString('
                        <style>
                /* تخصيص لون رأس الجدول - برتقالي أنيق */
                .fi-ta-table thead {
                    background-color: #fff7ed !important; /* برتقالي فاتح جداً */
                    border-bottom: 2px solid #fdba74 !important; /* حد برتقالي متوسط */
                }
                .dark .fi-ta-table thead {
                    background-color: #431407 !important; /* بني/برتقالي داكن للوضع الليلي */
                    border-bottom: 2px solid #9a3412 !important;
                }
                /* تكبير خط العناوين وتغيير لونه */
                .fi-ta-header-cell .fi-ta-header-cell-label {
                    color: #9a3412 !important; /* نص برتقالي داكن فخم */
                    font-weight: 800 !important;
                    font-size: 0.95rem !important;
                }
                .dark .fi-ta-header-cell .fi-ta-header-cell-label {
                    color: #ffedd5 !important;
                }
                /* تحسين شكل الصفوف عند تمرير الماوس (Hover) */
                .fi-ta-table tbody tr:hover {
                    background-color: #fffaf5 !important; /* لمسة برتقالية خفيفة عند التمرير */
                    transition: all 0.2s ease-in-out;
                }
                .dark .fi-ta-table tbody tr:hover {
                    background-color: #2e1005 !important;
                }
            </style>
            ')
        );
        Table::configureUsing(function (Table $table): void {
            $table
                ->striped() // صفوف مخططة لراحة العين
                ->defaultPaginationPageOption(25) // عرض 25 سجل افتراضياً
                ->paginated([10, 25, 50, 100]) // خيارات عدد السجلات
                ->extremePaginationLinks() // أزرار تنقل متطورة
                ->emptyStateHeading('لا توجد بيانات هنا حتى الآن') // رسالة الجدول الفارغ
                ->emptyStateIcon('heroicon-o-folder-open'); // أيقونة الجدول الفارغ
        });
        app()->setLocale('ar');
        setlocale(LC_TIME, 'ar_SA.UTF-8', 'ar_SA', 'ar');
        View::composer('*', function ($view) {
            $request = request();

            if ($request->is('khaier') || $request->is('khaier/*')) {
                return;
            }

            try {
                $settings = SiteSetting::query()->latest('id')->first();
                $view->with('settings', $settings);
            } catch (\Throwable $e) {
            }
        });
    }
}
