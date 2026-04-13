<?php

namespace App\Filament\Admin\Pages;

use App\Models\AssociationPlan;
use App\Models\BoardMember;
use App\Models\Committee;
use App\Models\Disclosure;
use App\Models\Employee;
use App\Models\FinancialReport;
use App\Models\GeneralAssemblyMember;
use App\Models\License;
use App\Models\News;
use App\Models\Page as PageModel;
use App\Models\Policy;
use App\Models\ProgramProject;
use App\Models\Regulation;
use App\Models\Service;
use App\Models\SiteForm;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'لوحة التحكم والمتابعة';

    protected static string $view = 'filament.admin.pages.dashboard';
    protected static ?string $title = 'لوحة التحكم';
    protected static ?string $navigationLabel = 'لوحة التحكم';
    protected static ?string $slug = '/';

    public array $stats = [];
    public ?object $siteSettings = null;
    public ?object $associationInfo = null;
    public ?string $associationExpiryDate = null;

    public function getHeaderActions(): array
    {
        return [
            Action::make('visitSite')
                ->label('زيارة الموقع')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(url('/'))
                ->openUrlInNewTab(),
        ];
    }

    public function mount(): void
    {
        $this->siteSettings = SiteSetting::query()->latest('id')->first();

        $this->associationInfo = DB::connection('mysql')
            ->table('associations')
            ->where('domain', request()->getHost())
            ->first();

        $expiry = $this->associationInfo->subscription_end_date ?? null;

        if ($expiry) {
            try {
                $this->associationExpiryDate = Carbon::parse($expiry)->format('Y-m-d');
            } catch (\Throwable $e) {
                $this->associationExpiryDate = (string) $expiry;
            }
        }

        $this->stats = [
            ['label' => 'الأخبار', 'value' => class_exists(News::class) ? News::query()->count() : 0, 'icon' => '📰'],
            ['label' => 'الصفحات', 'value' => class_exists(PageModel::class) ? PageModel::query()->count() : 0, 'icon' => '📄'],
            ['label' => 'الموظفون', 'value' => class_exists(Employee::class) ? Employee::query()->count() : 0, 'icon' => '👥'],
            ['label' => 'المشاريع', 'value' => class_exists(ProgramProject::class) ? ProgramProject::query()->count() : 0, 'icon' => '📁'],
            ['label' => 'السياسات', 'value' => class_exists(Policy::class) ? Policy::query()->count() : 0, 'icon' => '📘'],
            ['label' => 'اللوائح', 'value' => class_exists(Regulation::class) ? Regulation::query()->count() : 0, 'icon' => '📚'],
            ['label' => 'الإفصاح', 'value' => class_exists(Disclosure::class) ? Disclosure::query()->count() : 0, 'icon' => '📑'],
            ['label' => 'القوائم المالية', 'value' => class_exists(FinancialReport::class) ? FinancialReport::query()->count() : 0, 'icon' => '📊'],
            ['label' => 'التراخيص', 'value' => class_exists(License::class) ? License::query()->count() : 0, 'icon' => '🪪'],
            ['label' => 'الخدمات', 'value' => class_exists(Service::class) ? Service::query()->count() : 0, 'icon' => '🛠️'],
            ['label' => 'اللجان', 'value' => class_exists(Committee::class) ? Committee::query()->count() : 0, 'icon' => '👔'],
            ['label' => 'مجلس الإدارة', 'value' => class_exists(BoardMember::class) ? BoardMember::query()->count() : 0, 'icon' => '🏛️'],
            ['label' => 'الجمعية العمومية', 'value' => class_exists(GeneralAssemblyMember::class) ? GeneralAssemblyMember::query()->count() : 0, 'icon' => '🧾'],
            ['label' => 'خطط الجمعية', 'value' => class_exists(AssociationPlan::class) ? AssociationPlan::query()->count() : 0, 'icon' => '🗂️'],
            ['label' => 'النماذج', 'value' => class_exists(SiteForm::class) ? SiteForm::query()->count() : 0, 'icon' => '📝'],
        ];
    }
}
