<?php
namespace App\Filament\Admin\Resources\CommitteeResource\Pages;
use App\Filament\Admin\Resources\CommitteeResource;
use Filament\Resources\Pages\CreateRecord;
class CreateCommittee extends CreateRecord {
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = CommitteeResource::class;
    public function updated($property) { session()->put('committee_form', $this->data); }
    public function mount(): void {
        parent::mount();
        if (request()->filled('selected_media_id')) {
            if (session()->has('committee_form')) { $this->data = array_merge($this->data, session()->get('committee_form')); }
            $this->data['attachment_media_id'] = request('selected_media_id');
            $this->data['attachment'] = request('selected_media_file');
        } else { session()->forget('committee_form'); }
    }
}
