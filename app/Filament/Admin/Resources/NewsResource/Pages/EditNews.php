<?php

namespace App\Filament\Admin\Resources\NewsResource\Pages;

use App\Filament\Admin\Resources\NewsResource;
use App\Models\MediaItem;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNews extends EditRecord
{
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = NewsResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $media = filled($data['image_media_id'] ?? null)
            ? MediaItem::query()->find($data['image_media_id'])
            : null;

        $data['image'] = $media?->file;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
