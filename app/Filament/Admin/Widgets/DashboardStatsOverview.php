<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Employee;
use App\Models\FinancialReport;
use App\Models\News;
use App\Models\Page;
use App\Models\Policy;
use App\Models\Regulation;
use Filament\Widgets\Widget;

class DashboardStatsOverview extends Widget
{
    protected static string $view = 'filament.admin.widgets.dashboard-stats-overview';

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        return [
            'stats' => [
                [
                    'label' => 'الأخبار',
                    'value' => News::query()->count(),
                    'today' => News::query()->whereDate('created_at', today())->count(),
                    'icon' => '📰',
                    'icon_bg' => '📰',
                    'class' => 'dashboard-card-green',
                    'desc' => 'إجمالي الأخبار',
                    'url' => url('/admin/news'),
                ],
                [
                    'label' => 'السياسات',
                    'value' => Policy::query()->count(),
                    'today' => Policy::query()->whereDate('created_at', today())->count(),
                    'icon' => '📄',
                    'icon_bg' => '📄',
                    'class' => 'dashboard-card-blue',
                    'desc' => 'إجمالي السياسات',
                    'url' => url('/admin/policies'),
                ],
                [
                    'label' => 'اللوائح',
                    'value' => Regulation::query()->count(),
                    'today' => Regulation::query()->whereDate('created_at', today())->count(),
                    'icon' => '📑',
                    'icon_bg' => '📑',
                    'class' => 'dashboard-card-amber',
                    'desc' => 'إجمالي اللوائح',
                    'url' => url('/admin/regulations'),
                ],
                [
                    'label' => 'القوائم المالية',
                    'value' => FinancialReport::query()->count(),
                    'today' => FinancialReport::query()->whereDate('created_at', today())->count(),
                    'icon' => '💰',
                    'icon_bg' => '💰',
                    'class' => 'dashboard-card-red',
                    'desc' => 'إجمالي القوائم المالية',
                    'url' => url('/admin/financial-reports'),
                ],
                [
                    'label' => 'الصفحات',
                    'value' => Page::query()->count(),
                    'today' => Page::query()->whereDate('created_at', today())->count(),
                    'icon' => '📚',
                    'icon_bg' => '📚',
                    'class' => 'dashboard-card-indigo',
                    'desc' => 'إجمالي الصفحات',
                    'url' => url('/admin/pages'),
                ],
                [
                    'label' => 'الموظفون',
                    'value' => Employee::query()->count(),
                    'today' => Employee::query()->whereDate('created_at', today())->count(),
                    'icon' => '👥',
                    'icon_bg' => '👥',
                    'class' => 'dashboard-card-slate',
                    'desc' => 'إجمالي الموظفين',
                    'url' => url('/admin/employees'),
                ],
            ],
        ];
    }
}
