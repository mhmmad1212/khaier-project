<?php

namespace App\Filament\Admin\Resources\BeneficiaryServiceResource\Pages;

use App\Filament\Admin\Resources\BeneficiaryServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBeneficiaryService extends EditRecord
{
    protected static string $resource = BeneficiaryServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
