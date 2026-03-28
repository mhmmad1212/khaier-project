<?php

namespace App\Filament\Admin\Resources\SliderResource\Pages;

use App\Filament\Admin\Resources\SliderResource;
use App\Filament\Support\AppliesSelectedMedia;
use Filament\Resources\Pages\EditRecord;

class EditSlider extends EditRecord
{
    use AppliesSelectedMedia;

    protected static string $resource = SliderResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->applySelectedMedia($data, [
            'image_media_id' => 'image',
        ]);
    }
}
