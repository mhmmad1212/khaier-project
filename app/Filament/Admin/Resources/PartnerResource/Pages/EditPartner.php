<?php

namespace App\Filament\Admin\Resources\PartnerResource\Pages;

use App\Filament\Admin\Resources\PartnerResource;
use App\Filament\Support\AppliesSelectedMedia;
use Filament\Resources\Pages\EditRecord;

class EditPartner extends EditRecord
{
    use \App\Filament\Traits\HasBackButton;

    use AppliesSelectedMedia;

    protected static string $resource = PartnerResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->applySelectedMedia($data, [
            'logo_media_id' => 'logo',
        ]);
    }
}
