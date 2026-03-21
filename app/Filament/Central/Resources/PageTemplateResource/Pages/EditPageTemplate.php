<?php

namespace App\Filament\Central\Resources\PageTemplateResource\Pages;

use App\Filament\Central\Resources\PageTemplateResource;
use App\Services\TemplateFileGenerator;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPageTemplate extends EditRecord
{
    protected static string $resource = PageTemplateResource::class;

    protected function afterSave(): void
    {
        TemplateFileGenerator::generate($this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
