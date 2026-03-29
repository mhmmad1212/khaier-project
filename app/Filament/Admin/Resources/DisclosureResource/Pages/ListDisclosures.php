<?php
namespace App\Filament\Admin\Resources\DisclosureResource\Pages;
use App\Filament\Admin\Resources\DisclosureResource;
use Filament\Resources\Pages\ListRecords;
class ListDisclosures extends ListRecords {
    protected static string $resource = DisclosureResource::class;
    protected function getHeaderActions(): array { return [ \Filament\Actions\CreateAction::make() ]; }
}