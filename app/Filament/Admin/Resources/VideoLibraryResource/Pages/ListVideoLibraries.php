<?php

namespace App\Filament\Admin\Resources\VideoLibraryResource\Pages;

use App\Filament\Admin\Resources\VideoLibraryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVideoLibraries extends ListRecords
{
    protected static string $resource = VideoLibraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
