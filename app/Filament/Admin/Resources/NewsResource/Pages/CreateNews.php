<?php

namespace App\Filament\Admin\Resources\NewsResource\Pages;

use App\Filament\Admin\Resources\NewsResource;
use App\Models\MediaItem;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = NewsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $media = filled($data['image_media_id'] ?? null)
            ? MediaItem::query()->find($data['image_media_id'])
            : null;

        $data['image'] = $media?->file;

        return $data;
    }
}
