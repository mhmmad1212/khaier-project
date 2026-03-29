<?php

namespace App\Filament\Admin\Resources\SiteSettingResource\Pages;

use App\Filament\Admin\Resources\SiteSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSiteSetting extends CreateRecord
{
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = SiteSettingResource::class;

    public function mount(): void
    {
        $record = \App\Models\SiteSetting::query()->latest('id')->first();

        if (! $record) {
            $record = \App\Models\SiteSetting::query()->create([]);
        }

        redirect()->to(SiteSettingResource::getUrl('edit', ['record' => $record]));
    }
}
