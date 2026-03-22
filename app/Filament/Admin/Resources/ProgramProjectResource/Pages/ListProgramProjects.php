<?php

namespace App\Filament\Admin\Resources\ProgramProjectResource\Pages;

use App\Filament\Admin\Resources\ProgramProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgramProjects extends ListRecords
{
    protected static string $resource = ProgramProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إضافة مشروع أو برنامج'),
        ];
    }
}
