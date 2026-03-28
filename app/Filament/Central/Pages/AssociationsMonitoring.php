<?php

namespace App\Filament\Central\Pages;

use App\Models\Association;
use App\Services\AssociationActivityLogger;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class AssociationsMonitoring extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-eye';
    protected static string $view = 'filament.central.pages.associations-monitoring';
    protected static ?string $navigationLabel = 'متابعة الجمعيات';
    protected static ?string $title = 'متابعة الجمعيات';
    protected static ?string $navigationGroup = 'إدارة النظام';
    protected static ?int $navigationSort = 2;

    public string $search = '';
    public string $statusFilter = '';
    public string $daysFilter = '';
    public string $subscriptionFilter = '';

    public ?int $renewAssociationId = null;
    public string $renewalType = '1_year';
    public ?string $customRenewalDate = null;

    public function getAssociationsProperty()
    {
        $query = Association::query();

        if ($this->search !== '') {
            $query->where(function (Builder $q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('domain', 'like', '%' . $this->search . '%')
                  ->orWhere('slug', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('site_status', $this->statusFilter);
        }

        if ($this->subscriptionFilter !== '') {
            $query->where('subscription_status', $this->subscriptionFilter);
        }

        if ($this->daysFilter !== '') {
            $days = (int) $this->daysFilter;
            $today = Carbon::today();
            $future = Carbon::today()->addDays($days);

            $query->whereNotNull('subscription_end_date')
                ->whereDate('subscription_end_date', '>=', $today)
                ->whereDate('subscription_end_date', '<=', $future);
        }

        return $query->orderByRaw("
                CASE
                    WHEN subscription_end_date IS NULL THEN 1
                    ELSE 0
                END
            ")
            ->orderBy('subscription_end_date')
            ->orderByDesc('id')
            ->get();
    }

    public function stopAssociation(int $id): void
    {
        $association = Association::findOrFail($id);

        $association->update([
            'site_status' => 'suspended',
        ]);

        AssociationActivityLogger::log(
            $association,
            3,
            'suspended',
            'تم إيقاف الجمعية',
            'تم تغيير حالة الموقع إلى موقوفة.'
        );

        Notification::make()
            ->title('تم إيقاف الجمعية')
            ->success()
            ->send();
    }

    public function activateAssociation(int $id): void
    {
        $association = Association::findOrFail($id);

        $association->update([
            'site_status' => 'active',
        ]);

        AssociationActivityLogger::log(
            $association,
            5,
            'activated',
            'تم تفعيل الجمعية',
            'تم تغيير حالة الموقع إلى نشطة.'
        );

        Notification::make()
            ->title('تم تفعيل الجمعية')
            ->success()
            ->send();
    }

    public function openRenewModal(int $id): void
    {
        $this->renewAssociationId = $id;
        $this->renewalType = '1_year';
        $this->customRenewalDate = null;

        $this->dispatch('open-renew-modal');
    }

    public function closeRenewModal(): void
    {
        $this->renewAssociationId = null;
        $this->renewalType = '1_year';
        $this->customRenewalDate = null;

        $this->dispatch('close-renew-modal');
    }

    public function renewAssociation(): void
    {
        abort_unless($this->renewAssociationId, 404);

        $association = Association::findOrFail($this->renewAssociationId);

        $baseDate = $association->subscription_end_date
            ? Carbon::parse($association->subscription_end_date)
            : now();

        if ($baseDate->isPast()) {
            $baseDate = now();
        }

        $newEndDate = match ($this->renewalType) {
            '1_month' => $baseDate->copy()->addMonth(),
            '6_months' => $baseDate->copy()->addMonths(6),
            '1_year' => $baseDate->copy()->addYear(),
            '2_years' => $baseDate->copy()->addYears(2),
            '3_years' => $baseDate->copy()->addYears(3),
            'custom_date' => Carbon::parse($this->customRenewalDate),
            default => $baseDate->copy()->addYear(),
        };

        $association->update([
            'subscription_status' => 'active',
            'subscription_end_date' => $newEndDate->toDateString(),
        ]);

        AssociationActivityLogger::log(
            $association,
            2,
            'renewed',
            'تم تجديد الاشتراك',
            'تم التجديد بنوع: ' . $this->renewalType . ' | تاريخ الانتهاء الجديد: ' . $newEndDate->format('Y-m-d')
        );

        $this->closeRenewModal();

        Notification::make()
            ->title('تم تجديد الاشتراك')
            ->body('تاريخ الانتهاء الجديد: ' . $newEndDate->format('Y-m-d'))
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('show_expiring_30')
                ->label('تنتهي خلال 30 يوم')
                ->action(fn () => $this->daysFilter = '30'),

            Action::make('show_expiring_60')
                ->label('تنتهي خلال 60 يوم')
                ->action(fn () => $this->daysFilter = '60'),

            Action::make('show_expiring_90')
                ->label('تنتهي خلال 90 يوم')
                ->action(fn () => $this->daysFilter = '90'),

            Action::make('reset_filters')
                ->label('تصفير الفلاتر')
                ->color('gray')
                ->action(function () {
                    $this->search = '';
                    $this->statusFilter = '';
                    $this->daysFilter = '';
                    $this->subscriptionFilter = '';
                }),
        ];
    }
}
