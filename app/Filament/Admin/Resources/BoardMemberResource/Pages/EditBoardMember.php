<?php

namespace App\Filament\Admin\Resources\BoardMemberResource\Pages;

use App\Filament\Admin\Resources\BoardMemberResource;
use App\Filament\Support\AppliesSelectedMedia;
use Filament\Resources\Pages\EditRecord;

class EditBoardMember extends EditRecord
{
    use AppliesSelectedMedia;

    protected static string $resource = BoardMemberResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->applySelectedMedia($data, [
            'photo_media_id' => 'photo',
        ]);
    }
}
