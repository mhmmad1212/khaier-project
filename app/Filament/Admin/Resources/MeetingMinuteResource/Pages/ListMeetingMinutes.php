<?php
namespace App\Filament\Admin\Resources\MeetingMinuteResource\Pages;
use App\Filament\Admin\Resources\MeetingMinuteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMeetingMinutes extends ListRecords {
    protected static string $resource = MeetingMinuteResource::class;
    protected function getHeaderActions(): array {
        return [Actions\CreateAction::make()];
    }
}