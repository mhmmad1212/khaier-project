<?php

namespace App\Filament\Admin\Resources\FinancialReportResource\Pages;

use App\Filament\Admin\Resources\FinancialReportResource;
use App\Filament\Support\AppliesSelectedMedia;
use Filament\Resources\Pages\EditRecord;

class EditFinancialReport extends EditRecord
{
    use AppliesSelectedMedia;

    protected static string $resource = FinancialReportResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->applySelectedMedia($data, [
            'file_media_id' => 'file',
        ]);
    }
}
