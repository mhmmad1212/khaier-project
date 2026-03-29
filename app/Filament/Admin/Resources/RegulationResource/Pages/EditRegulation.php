<?php

namespace App\Filament\Admin\Resources\RegulationResource\Pages;

use App\Filament\Admin\Resources\RegulationResource;
use App\Filament\Support\AppliesSelectedMedia;
use Filament\Resources\Pages\EditRecord;

class EditRegulation extends EditRecord
{
    use \App\Filament\Traits\HasBackButton;

    use AppliesSelectedMedia;

    protected static string $resource = RegulationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->applySelectedMedia($data, [
            'file_media_id' => 'file',
        ]);
    }
}
