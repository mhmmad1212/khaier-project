<?php

namespace App\Filament\Admin\Resources\PolicyResource\Pages;

use App\Filament\Admin\Resources\PolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPolicies extends ListRecords
{
    protected static string $resource = PolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
