<?php

namespace App\Filament\Admin\Resources\MediaLibraryResource\Pages;

use App\Filament\Admin\Resources\MediaLibraryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMediaLibrary extends CreateRecord
{
    protected static string $resource = MediaLibraryResource::class;

    protected function getRedirectUrl(): string
    {
        $field = request('field');
        $return = request('return');

        if ($field || $return) {
            return url('/admin/media-picker') . '?' . http_build_query([
                'field' => $field,
                'return' => $return,
            ]);
        }

        return static::getResource()::getUrl('index');
    }
}
