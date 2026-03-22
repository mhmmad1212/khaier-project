<?php

namespace App\Filament\Admin\Resources\CommitteeResource\Pages;

use App\Filament\Admin\Resources\CommitteeResource;
use App\Models\MediaItem;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommittee extends EditRecord
{
    protected static string $resource = CommitteeResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['attachment_media_id'] = null;

        if (! empty($data['attachment'])) {
            $media = MediaItem::query()->where('file', $data['attachment'])->first();
            if ($media) {
                $data['attachment_media_id'] = $media->id;
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
