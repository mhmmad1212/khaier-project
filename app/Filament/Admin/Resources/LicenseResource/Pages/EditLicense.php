<?php

namespace App\Filament\Admin\Resources\LicenseResource\Pages;

use App\Filament\Admin\Resources\LicenseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLicense extends EditRecord
{
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = LicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
