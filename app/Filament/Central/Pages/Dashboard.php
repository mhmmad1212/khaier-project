<?php

namespace App\Filament\Central\Pages;

use App\Models\Association;
use App\Models\User;
use App\Models\PageTemplate;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'لوحة التحكم';
    protected static ?string $navigationLabel = 'لوحة التحكم';
    protected static ?string $slug = '/';
    protected static string $view = 'filament.central.pages.dashboard';

    public array $stats = [];
    public array $expiringAssociations = [];

    public function mount(): void
    {
        $today = Carbon::today();
        $after30 = $today->copy()->addDays(30);
        $after60 = $today->copy()->addDays(60);

        $this->stats = [
            'total_associations' => Association::query()->count(),
            'active_associations' => Association::query()->where('is_active', true)->count(),
            'inactive_associations' => Association::query()->where('is_active', false)->count(),
            'expired_associations' => Association::query()
                ->whereNotNull('subscription_end_date')
                ->whereDate('subscription_end_date', '<', $today)
                ->count(),
            'expiring_30_days' => Association::query()
                ->whereNotNull('subscription_end_date')
                ->whereDate('subscription_end_date', '>=', $today)
                ->whereDate('subscription_end_date', '<=', $after30)
                ->count(),
            'expiring_60_days' => Association::query()
                ->whereNotNull('subscription_end_date')
                ->whereDate('subscription_end_date', '>=', $today)
                ->whereDate('subscription_end_date', '<=', $after60)
                ->count(),
            'custom_domains' => Association::query()->where('domain_type', 'custom')->count(),
            'subdomains' => Association::query()->where('domain_type', 'subdomain')->count(),
            'total_users' => User::query()->count(),
            'total_templates' => class_exists(PageTemplate::class) ? PageTemplate::query()->count() : 0,
        ];

        $this->expiringAssociations = Association::query()
            ->select([
                'id',
                'name',
                'domain',
                'domain_type',
                'subscription_status',
                'subscription_end_date',
                'is_active',
            ])
            ->whereNotNull('subscription_end_date')
            ->orderBy('subscription_end_date')
            ->limit(10)
            ->get()
            ->map(function ($association) use ($today) {
                $daysLeft = null;

                if ($association->subscription_end_date) {
                    $daysLeft = $today->diffInDays(
                        Carbon::parse($association->subscription_end_date),
                        false
                    );
                }

                return [
                    'id' => $association->id,
                    'name' => $association->name,
                    'domain' => $association->domain,
                    'domain_type' => $association->domain_type,
                    'subscription_status' => $association->subscription_status,
                    'subscription_end_date' => optional($association->subscription_end_date)?->format('Y-m-d'),
                    'is_active' => (bool) $association->is_active,
                    'days_left' => $daysLeft,
                ];
            })
            ->toArray();
    }

    public function getHeading(): string
    {
        return 'لوحة تحكم المشرف';
    }
}
