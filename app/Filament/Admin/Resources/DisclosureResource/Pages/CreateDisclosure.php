<?php
namespace App\Filament\Admin\Resources\DisclosureResource\Pages;
use App\Filament\Admin\Resources\DisclosureResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Traits\HasBackButton;
class CreateDisclosure extends CreateRecord {
    use HasBackButton;
    protected static string $resource = DisclosureResource::class;
    public function updated($property) { session()->put('disclosure_form', $this->data); }
    public function mount(): void {
        parent::mount();
        if (request()->filled('selected_media_id')) {
            if (session()->has('disclosure_form')) { $this->data = array_merge($this->data, session()->get('disclosure_form')); }
            $this->data['file_media_id'] = request('selected_media_id');
            $this->data['file'] = request('selected_media_file');
        } else { session()->forget('disclosure_form'); }
    }
}