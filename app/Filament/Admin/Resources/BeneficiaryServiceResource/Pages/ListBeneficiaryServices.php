<?php

namespace App\Filament\Admin\Resources\BeneficiaryServiceResource\Pages;

use App\Filament\Admin\Resources\BeneficiaryServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBeneficiaryServices extends ListRecords
{
    protected static string $resource = BeneficiaryServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إضافة خدمة'),
        ];
    }
}
