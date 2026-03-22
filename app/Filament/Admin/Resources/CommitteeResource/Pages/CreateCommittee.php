<?php

namespace App\Filament\Admin\Resources\CommitteeResource\Pages;

use App\Filament\Admin\Resources\CommitteeResource;
use App\Models\MediaItem;
use Filament\Resources\Pages\CreateRecord;

class CreateCommittee extends CreateRecord
{
    protected static string $resource = CommitteeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $mediaId = $this->data['attachment_media_id'] ?? null;

        if ($mediaId) {
            $media = MediaItem::query()->find($mediaId);

            if ($media && ! empty($media->file)) {
                $data['attachment'] = $media->file;
            }
        }

        unset($data['attachment_media_id']);

        return $data;
    }
}
