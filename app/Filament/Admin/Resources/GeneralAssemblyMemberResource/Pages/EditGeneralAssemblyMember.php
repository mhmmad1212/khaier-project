<?php

namespace App\Filament\Admin\Resources\GeneralAssemblyMemberResource\Pages;

use App\Filament\Admin\Resources\GeneralAssemblyMemberResource;
use App\Models\MediaItem;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGeneralAssemblyMember extends EditRecord
{
    use \App\Filament\Traits\HasBackButton;

    public function mount(int | string $record): void
    {
        parent::mount($record);
        if (request()->filled('selected_media_id')) {
            $this->data['photo_media_id'] = request('selected_media_id');
            $this->data['photo'] = request('selected_media_file');
        }
    }

    protected static string $resource = GeneralAssemblyMemberResource::class;

    
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
