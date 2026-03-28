<?php

namespace App\Filament\Admin\Resources\MediaLibraryResource\Pages;

use App\Filament\Admin\Resources\MediaLibraryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMediaLibrary extends ListRecords
{
    protected static string $resource = MediaLibraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('رفع جديد'),
        ];
    }
}
