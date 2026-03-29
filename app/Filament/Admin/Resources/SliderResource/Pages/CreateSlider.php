<?php

namespace App\Filament\Admin\Resources\SliderResource\Pages;

use App\Filament\Admin\Resources\SliderResource;
use App\Filament\Support\AppliesSelectedMedia;
use Filament\Resources\Pages\CreateRecord;

class CreateSlider extends CreateRecord
{
    use \App\Filament\Traits\HasBackButton;

    use AppliesSelectedMedia;

    protected static string $resource = SliderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->applySelectedMedia($data, [
            'image_media_id' => 'image',
        ]);
    }
}
