<?php

namespace App\Filament\Admin\Resources\AssociationPlanResource\Pages;

use App\Filament\Admin\Resources\AssociationPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssociationPlans extends ListRecords
{
    protected static string $resource = AssociationPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
