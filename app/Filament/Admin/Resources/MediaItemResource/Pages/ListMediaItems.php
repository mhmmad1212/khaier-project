<?php

namespace App\Filament\Admin\Resources\MediaItemResource\Pages;

use App\Filament\Admin\Resources\MediaItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMediaItems extends ListRecords
{
    protected static string $resource = MediaItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
