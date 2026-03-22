<?php

namespace App\Filament\Admin\Resources\GeneralAssemblyMemberResource\Pages;

use App\Filament\Admin\Resources\GeneralAssemblyMemberResource;
use App\Models\MediaItem;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGeneralAssemblyMember extends EditRecord
{
    protected static string $resource = GeneralAssemblyMemberResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['photo_media_id'] = null;

        if (! empty($data['photo'])) {
            $media = MediaItem::query()->where('file', $data['photo'])->first();
            if ($media) {
                $data['photo_media_id'] = $media->id;
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
