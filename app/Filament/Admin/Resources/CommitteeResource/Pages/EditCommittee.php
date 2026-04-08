<?php

namespace App\Filament\Admin\Resources\CommitteeResource\Pages;

use App\Filament\Admin\Resources\CommitteeResource;
use App\Models\MediaItem;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommittee extends EditRecord
{
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = CommitteeResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if (request()->filled('selected_media_id')) {
            $this->data['attachment_media_id'] = request('selected_media_id');
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $media = filled($data['attachment_media_id'] ?? null)
            ? MediaItem::query()->find($data['attachment_media_id'])
            : null;

        $data['attachment'] = $media?->file;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
