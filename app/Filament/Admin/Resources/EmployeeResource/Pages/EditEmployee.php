<?php

namespace App\Filament\Admin\Resources\EmployeeResource\Pages;

use App\Filament\Admin\Resources\EmployeeResource;
use App\Filament\Support\AppliesSelectedMedia;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    use AppliesSelectedMedia;

    protected static string $resource = EmployeeResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->applySelectedMedia($data, [
            'photo_media_id' => 'photo',
        ]);
    }
}
