<?php

namespace App\Filament\Resources\AssociationResource\Pages;

use App\Filament\Resources\AssociationResource;
use App\Models\Association;
use Filament\Resources\Pages\Page;

class AssociationActivities extends Page
{
    protected static string $resource = AssociationResource::class;

    protected static string $view = 'filament.resources.association-resource.pages.association-activities';

    public Association $record;

    public function mount($record): void
    {
        $this->record = Association::findOrFail($record);
    }

    public function getTitle(): string
    {
        return 'سجل الحركات - ' . $this->record->name;
    }

    public function getActivitiesProperty()
    {
        return $this->record->activities()
            ->with('performedBy')
            ->latest()
            ->get();
    }
}
