<?php

namespace App\Filament\Admin\Resources\VolunteerOpportunityResource\Pages;

use App\Filament\Admin\Resources\VolunteerOpportunityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVolunteerOpportunities extends ListRecords
{
    protected static string $resource = VolunteerOpportunityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة فرصة تطوع'),
        ];
    }
}
