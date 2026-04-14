<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Pages\Page;

class SiteSettings extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'legacy-site-settings';
    protected static string $view = 'filament.admin.pages.site-settings';

    public function mount(): void
    {
        $record = SiteSetting::query()->latest('id')->first();

        if (! $record) {
            $record = SiteSetting::query()->create([]);
        }

        $this->redirect(
            SiteSettingResource::getUrl('edit', ['record' => $record]),
            navigate: true
        );
    }
}
