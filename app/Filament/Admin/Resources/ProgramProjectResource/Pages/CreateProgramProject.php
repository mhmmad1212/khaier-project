<?php

namespace App\Filament\Admin\Resources\ProgramProjectResource\Pages;

use App\Filament\Admin\Resources\ProgramProjectResource;
use App\Models\MediaItem;
use Filament\Resources\Pages\CreateRecord;

class CreateProgramProject extends CreateRecord
{
    protected static string $resource = ProgramProjectResource::class;

    protected array $galleryData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->galleryData = $data['gallery'] ?? [];
        unset($data['gallery']);

        if (! empty($data['cover_image_media_id'])) {
            $media = MediaItem::query()->find($data['cover_image_media_id']);
            if ($media && ! empty($media->file)) {
                $data['cover_image'] = $media->file;
            }
        }

        if (! empty($data['report_media_id'])) {
            $media = MediaItem::query()->find($data['report_media_id']);
            if ($media && ! empty($media->file)) {
                $data['report_file'] = $media->file;
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        foreach ($this->galleryData as $item) {
            if (! empty($item['media_item_id'])) {
                $this->record->galleryImages()->create([
                    'media_item_id' => $item['media_item_id'],
                    'sort_order' => $item['sort_order'] ?? 0,
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
