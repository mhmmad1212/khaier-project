<?php

namespace App\Filament\Admin\Pages;

use App\Models\Employee;
use App\Models\News;
use App\Models\Page as PageModel;
use App\Models\ProgramProject;
use App\Models\SiteSetting;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.admin.pages.dashboard';
    protected static ?string $title = 'لوحة التحكم';
    protected static ?string $navigationLabel = 'لوحة التحكم';
    protected static ?string $slug = '/';

    public array $stats = [];
    public ?object $siteSettings = null;

    public function mount(): void
    {
        $this->siteSettings = SiteSetting::query()->latest('id')->first();

        $this->stats = [
            [
                'label' => 'الأخبار',
                'value' => class_exists(News::class) ? News::query()->count() : 0,
                'icon' => '📰',
            ],
            [
                'label' => 'الصفحات',
                'value' => class_exists(Page::class) ? PageModel::query()->count() : 0,
                'icon' => '📄',
            ],
            [
                'label' => 'الموظفون',
                'value' => class_exists(Employee::class) ? Employee::query()->count() : 0,
                'icon' => '👥',
            ],
            [
                'label' => 'المشاريع',
                'value' => class_exists(ProgramProject::class) ? ProgramProject::query()->count() : 0,
                'icon' => '📁',
            ],
        ];
    }
}
