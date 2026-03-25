<?php

namespace App\Filament\Central\Pages;

use App\Models\Association;
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

        Notification::make()
            ->title('تم تفعيل الجمعية')
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
