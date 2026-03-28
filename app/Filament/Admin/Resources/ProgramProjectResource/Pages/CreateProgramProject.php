<?php

namespace App\Filament\Admin\Resources\ProgramProjectResource\Pages;

use App\Filament\Admin\Resources\ProgramProjectResource;
use App\Filament\Support\AppliesSelectedMedia;
use Filament\Resources\Pages\CreateRecord;

class CreateProgramProject extends CreateRecord
{
    use AppliesSelectedMedia;

    protected static string $resource = ProgramProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->applySelectedMedia($data, [
            'cover_image_media_id' => 'cover_image',
            'report_media_id' => 'report_file',
        ]);
    }
}
