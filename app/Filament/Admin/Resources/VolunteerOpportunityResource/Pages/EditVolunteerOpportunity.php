<?php

namespace App\Filament\Admin\Resources\VolunteerOpportunityResource\Pages;

use App\Filament\Admin\Resources\VolunteerOpportunityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVolunteerOpportunity extends EditRecord
{
    protected static string $resource = VolunteerOpportunityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
