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
                /* ===== 1. تصميم الجداول (البرتقالي) ===== */
                .fi-ta-table thead {
                    background-color: #fff7ed !important;
                    border-bottom: 2px solid #fdba74 !important;
                }
                .dark .fi-ta-table thead {
                    background-color: #431407 !important;
                    border-bottom: 2px solid #9a3412 !important;
                }
                .fi-ta-header-cell .fi-ta-header-cell-label {
                    color: #9a3412 !important;
                    font-weight: 800 !important;
                    font-size: 0.95rem !important;
                }
                .dark .fi-ta-header-cell .fi-ta-header-cell-label {
                    color: #ffedd5 !important;
                }
                .fi-ta-table tbody tr:hover {
                    background-color: #fffaf5 !important;
                    transition: all 0.2s ease-in-out;
                }
                .dark .fi-ta-table tbody tr:hover {
                    background-color: #2e1005 !important;
                }

                /* ===== 2. تصميم القائمة الجانبية (Sidebar) على ذوقي ===== */
                
                /* عناوين المجموعات (مثل: المحتوى والإعلام) */
                .fi-sidebar-group-label {
                    color: #ea580c !important;
                    font-weight: 800 !important;
                    font-size: 0.9rem !important;
                    border-bottom: 1px dashed #fdba74;
                    padding-bottom: 6px;
                    margin-bottom: 6px;
                    margin-top: 10px;
                }
                .dark .fi-sidebar-group-label {
                    color: #fb923c !important;
                    border-bottom: 1px dashed #9a3412;
                }

                /* أزرار القائمة العادية */
                .fi-sidebar-item-button {
                    border-radius: 12px !important; /* حواف دائرية عصرية */
                    transition: all 0.2s ease-in-out !important;
                    margin-bottom: 4px !important;
                }

                /* تأثير تمرير الماوس (Hover) */
                .fi-sidebar-item-button:hover {
                    background-color: #fff7ed !important;
                    transform: scale(1.02); /* تكبير خفيف وناعم */
                }
                .dark .fi-sidebar-item-button:hover {
                    background-color: #431407 !important;
                }

                /* العنصر النشط (الصفحة المفتوحة حالياً) */
                .fi-sidebar-item-active .fi-sidebar-item-button {
                    background: linear-gradient(to left, #ea580c, #f97316) !important; /* تدرج برتقالي فخم */
                    box-shadow: 0 4px 10px rgba(234, 88, 12, 0.3) !important; /* ظل يعطي بروز */
                    border: none !important;
                }
                .fi-sidebar-item-active .fi-sidebar-item-button * {
                    color: #ffffff !important; /* أيقونة ونص باللون الأبيض */
                    font-weight: 700 !important;
                }
                .dark .fi-sidebar-item-active .fi-sidebar-item-button {
                    background: linear-gradient(to left, #9a3412, #c2410c) !important;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4) !important;
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
