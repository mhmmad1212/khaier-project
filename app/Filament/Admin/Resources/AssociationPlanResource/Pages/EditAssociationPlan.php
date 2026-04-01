<?php

namespace App\Filament\Admin\Resources\AssociationPlanResource\Pages;

use App\Filament\Admin\Resources\AssociationPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssociationPlan extends EditRecord
{
    protected static string $resource = AssociationPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
