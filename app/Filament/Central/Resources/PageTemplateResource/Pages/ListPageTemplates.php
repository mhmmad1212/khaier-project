<?php

namespace App\Filament\Central\Resources\PageTemplateResource\Pages;

use App\Filament\Central\Resources\PageTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPageTemplates extends ListRecords
{
    protected static string $resource = PageTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
