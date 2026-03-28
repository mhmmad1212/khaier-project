<?php

namespace App\Filament\Admin\Resources\BoardMemberResource\Pages;

use App\Filament\Admin\Resources\BoardMemberResource;
use App\Filament\Support\AppliesSelectedMedia;
use Filament\Resources\Pages\CreateRecord;

class CreateBoardMember extends CreateRecord
{
    use AppliesSelectedMedia;

    protected static string $resource = BoardMemberResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->applySelectedMedia($data, [
            'photo_media_id' => 'photo',
        ]);
    }
}
