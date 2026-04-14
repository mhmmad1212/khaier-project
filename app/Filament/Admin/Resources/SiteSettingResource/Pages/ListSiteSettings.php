<?php

namespace App\Filament\Admin\Resources\SiteSettingResource\Pages;

use App\Filament\Admin\Resources\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Resources\Pages\ListRecords;

class ListSiteSettings extends ListRecords
{
    protected static string $resource = SiteSettingResource::class;

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

    protected function getHeaderActions(): array
    {
        return [];
    }
}
