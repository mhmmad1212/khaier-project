<?php

namespace App\Filament\Admin\Resources\GeneralAssemblyMemberResource\Pages;

use App\Filament\Admin\Resources\GeneralAssemblyMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeneralAssemblyMembers extends ListRecords
{
    protected static string $resource = GeneralAssemblyMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
