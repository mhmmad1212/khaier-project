<?php

namespace App\Filament\Admin\Resources\MediaItemResource\Pages;

use App\Filament\Admin\Resources\MediaItemResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateMediaItem extends CreateRecord
{
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = MediaItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['file']) && Storage::disk('public')->exists($data['file'])) {
            $mime = Storage::disk('public')->mimeType($data['file']);
            $data['disk'] = 'public';
            $data['directory'] = dirname($data['file']);
            $data['mime_type'] = $mime;
            $data['extension'] = pathinfo($data['file'], PATHINFO_EXTENSION);
            $data['size'] = Storage::disk('public')->size($data['file']);
            $data['is_image'] = str_starts_with((string) $mime, 'image/');
            $data['title'] = $data['title'] ?: basename($data['file']);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
