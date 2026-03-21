<?php

namespace App\Filament\Central\Resources\PageTemplateResource\Pages;

use App\Filament\Central\Resources\PageTemplateResource;
use App\Services\TemplateFileGenerator;
use Filament\Resources\Pages\CreateRecord;

class CreatePageTemplate extends CreateRecord
{
    protected static string $resource = PageTemplateResource::class;

    protected function afterCreate(): void
    {
        TemplateFileGenerator::generate($this->record);
    }
}
