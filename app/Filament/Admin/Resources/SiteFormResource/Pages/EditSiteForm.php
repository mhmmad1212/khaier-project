<?php

namespace App\Filament\Admin\Resources\SiteFormResource\Pages;

use App\Filament\Admin\Resources\SiteFormResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSiteForm extends EditRecord
{
    protected static string $resource = SiteFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
