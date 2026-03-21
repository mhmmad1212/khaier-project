<?php

namespace App\Filament\Admin\Resources\PagesResource\Pages;

use App\Filament\Admin\Resources\PagesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPages extends ListRecords
{
    protected static string $resource = PagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
