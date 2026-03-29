<?php
namespace App\Filament\Admin\Resources\DisclosureResource\Pages;
use App\Filament\Admin\Resources\DisclosureResource;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Traits\HasBackButton;
class EditDisclosure extends EditRecord {
    use HasBackButton;
    protected static string $resource = DisclosureResource::class;
    public function updated($property) { session()->put('disclosure_form_edit_' . $this->record->id, $this->data); }
    public function mount(int | string $record): void {
        parent::mount($record);
        if (request()->filled('selected_media_id')) {
            if (session()->has('disclosure_form_edit_' . $this->record->id)) { $this->data = array_merge($this->data, session()->get('disclosure_form_edit_' . $this->record->id)); }
            $this->data['file_media_id'] = request('selected_media_id');
            $this->data['file'] = request('selected_media_file');
        } else { session()->forget('disclosure_form_edit_' . $this->record->id); }
    }
}