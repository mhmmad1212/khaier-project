<?php

namespace App\Filament\Admin\Resources\GeneralAssemblyMemberResource\Pages;

use App\Filament\Admin\Resources\GeneralAssemblyMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGeneralAssemblyMember extends EditRecord
{
    protected static string $resource = GeneralAssemblyMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
