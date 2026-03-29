<?php

namespace App\Filament\Admin\Resources\PolicyResource\Pages;

use App\Filament\Admin\Resources\PolicyResource;
use App\Filament\Support\AppliesSelectedMedia;
use Filament\Resources\Pages\CreateRecord;

class CreatePolicy extends CreateRecord
{
    use \App\Filament\Traits\HasBackButton;

    use AppliesSelectedMedia;

    protected static string $resource = PolicyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->applySelectedMedia($data, [
            'file_media_id' => 'file',
        ]);
    }
}
