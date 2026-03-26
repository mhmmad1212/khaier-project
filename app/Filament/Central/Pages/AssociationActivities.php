<?php

namespace App\Filament\Central\Pages;

use App\Models\Association;
use Filament\Pages\Page;

class AssociationActivities extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.central.pages.association-activities';

    public Association $association;

    public function mount(): void
    {
        $associationId = request()->integer('association');

        abort_unless($associationId, 404);

        $this->association = Association::findOrFail($associationId);
    }

    public function getTitle(): string
    {
        return 'سجل الحركات - ' . $this->association->name;
    }

    public function getActivitiesProperty()
    {
        return $this->association->activities()
            ->with('performedBy')
            ->latest()
            ->get();
    }
}
