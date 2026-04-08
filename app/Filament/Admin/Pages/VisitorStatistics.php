<?php

namespace App\Filament\Admin\Pages;

use App\Models\PageVisit;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class VisitorStatistics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'إحصائيات الزوار';
    protected static ?string $title = 'إحصائيات الزوار';
    protected static ?string $navigationGroup = 'التقارير والإحصائيات';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.admin.pages.visitor-statistics';

    public array $stats = [];
    public array $topUrls = [];

    public function mount(): void
    {
        $this->loadStats();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh_statistics')
                ->label('تحديث الإحصائيات')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function (): void {
                    $this->refreshStats();

                    Notification::make()
                        ->title('تم تحديث الإحصائيات')
                        ->success()
                        ->send();
                }),

            Action::make('reset_statistics')
                ->label('تصفير الإحصائيات')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('تصفير الإحصائيات')
                ->modalDescription('سيتم حذف جميع إحصائيات الدخول والصفحات الحالية والبدء من جديد. هل تريد المتابعة؟')
                ->modalSubmitActionLabel('نعم، صفّر الإحصائيات')
                ->action(function (): void {
                    DB::connection('tenant')->transaction(function () {
                        PageVisit::query()->delete();
                    });

                    $this->loadStats();

                    Notification::make()
                        ->title('تم تصفير الإحصائيات بنجاح')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function refreshStats(): void
    {
        $this->loadStats();
    }

    protected function loadStats(): void
    {
        $this->stats = [
            'all' => PageVisit::query()->count(),
            'today' => PageVisit::query()->whereDate('created_at', now()->toDateString())->count(),
            'home' => PageVisit::query()->where('type', 'home')->count(),
            'pages' => PageVisit::query()->where('type', 'page')->count(),
            'news' => PageVisit::query()->where('type', 'news')->count(),
            'projects' => PageVisit::query()->where('type', 'project')->count(),
        ];

        $this->topUrls = PageVisit::query()
            ->select('url', 'type', DB::raw('COUNT(*) as visits'))
            ->groupBy('url', 'type')
            ->orderByDesc('visits')
            ->limit(20)
            ->get()
            ->toArray();
    }
}
