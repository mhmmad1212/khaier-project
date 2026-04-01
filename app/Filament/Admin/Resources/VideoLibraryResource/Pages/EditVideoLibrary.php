<?php

namespace App\Filament\Admin\Resources\VideoLibraryResource\Pages;

use App\Filament\Admin\Resources\VideoLibraryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVideoLibrary extends EditRecord
{
    protected static string $resource = VideoLibraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
