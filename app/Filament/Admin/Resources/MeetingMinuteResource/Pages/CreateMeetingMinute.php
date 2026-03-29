<?php
namespace App\Filament\Admin\Resources\MeetingMinuteResource\Pages;
use App\Filament\Admin\Resources\MeetingMinuteResource;
use Filament\Resources\Pages\CreateRecord;
class CreateMeetingMinute extends CreateRecord {
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = MeetingMinuteResource::class;
    public function updated($property) { session()->put('meeting_minute_form', $this->data); }
    public function mount(): void {
        parent::mount();
        if (request()->filled('selected_media_id')) {
            if (session()->has('meeting_minute_form')) { $this->data = array_merge($this->data, session()->get('meeting_minute_form')); }
            $this->data['file_media_id'] = request('selected_media_id');
            $this->data['file'] = request('selected_media_file');
        } else { session()->forget('meeting_minute_form'); }
    }
}
