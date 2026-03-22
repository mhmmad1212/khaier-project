<?php

namespace App\Filament\Admin\Resources\GeneralAssemblyMemberResource\Pages;

use App\Filament\Admin\Resources\GeneralAssemblyMemberResource;
use App\Models\MediaItem;
use Filament\Resources\Pages\CreateRecord;

class CreateGeneralAssemblyMember extends CreateRecord
{
    protected static string $resource = GeneralAssemblyMemberResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $mediaId = $this->data['photo_media_id'] ?? null;

        if ($mediaId) {
            $media = MediaItem::query()->find($mediaId);

            if ($media && ! empty($media->file)) {
                $data['photo'] = $media->file;
            }
        }

        unset($data['photo_media_id']);

        return $data;
    }
}
