<?php

namespace App\Filament\Admin\Resources\FinancialReportResource\Pages;

use App\Filament\Admin\Resources\FinancialReportResource;
use App\Filament\Support\AppliesSelectedMedia;
use Filament\Resources\Pages\CreateRecord;

class CreateFinancialReport extends CreateRecord
{
    use AppliesSelectedMedia;

    protected static string $resource = FinancialReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->applySelectedMedia($data, [
            'file_media_id' => 'file',
        ]);
    }
}
