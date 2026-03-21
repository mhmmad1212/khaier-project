<?php

namespace App\Filament\Admin\Resources\SiteSettingResource\Pages;

use App\Filament\Admin\Resources\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\RedirectResponse;

class ListSiteSettings extends ListRecords
{
    protected static string $resource = SiteSettingResource::class;

    public function mount(): void
    {
        $record = SiteSetting::query()->latest('id')->first();

        if (! $record) {
            $record = SiteSetting::query()->create([]);
        }

        redirect()->to(SiteSettingResource::getUrl('edit', ['record' => $record]));
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
