<?php
namespace App\Filament\Admin\Resources\GeneralAssemblyMemberResource\Pages;
use App\Filament\Admin\Resources\GeneralAssemblyMemberResource;
use Filament\Resources\Pages\CreateRecord;
class CreateGeneralAssemblyMember extends CreateRecord {
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = GeneralAssemblyMemberResource::class;
    public function updated($property) { session()->put('assembly_form', $this->data); }
    public function mount(): void {
        parent::mount();
        if (request()->filled('selected_media_id')) {
            if (session()->has('assembly_form')) { $this->data = array_merge($this->data, session()->get('assembly_form')); }
            $this->data['photo_media_id'] = request('selected_media_id');
            $this->data['photo'] = request('selected_media_file');
        } else { session()->forget('assembly_form'); }
    }
}
