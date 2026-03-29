<?php

namespace App\Filament\Admin\Resources\CommitteeResource\Pages;

use App\Filament\Admin\Resources\CommitteeResource;
use App\Models\MediaItem;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommittee extends EditRecord
{
    use \App\Filament\Traits\HasBackButton;

    public function mount(int | string $record): void
    {
        parent::mount($record);
        if (request()->filled('selected_media_id')) {
            $this->data['attachment_media_id'] = request('selected_media_id');
            $this->data['attachment'] = request('selected_media_file');
        }
    }

    protected static string $resource = CommitteeResource::class;

    
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
