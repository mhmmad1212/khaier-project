<?php
namespace App\Filament\Admin\Resources\MeetingMinuteResource\Pages;
use App\Filament\Admin\Resources\MeetingMinuteResource;
use Filament\Resources\Pages\EditRecord;

class EditMeetingMinute extends EditRecord {
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = MeetingMinuteResource::class;
    public function mount(int | string $record): void {
        parent::mount($record);
        if (request()->filled('selected_media_id')) {
            $this->data['file_media_id'] = request('selected_media_id');
            $this->data['file'] = request('selected_media_file');
        }
    }
}