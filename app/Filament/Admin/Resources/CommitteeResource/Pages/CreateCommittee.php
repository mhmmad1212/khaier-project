<?php

namespace App\Filament\Admin\Resources\CommitteeResource\Pages;

use App\Filament\Admin\Resources\CommitteeResource;
use App\Models\MediaItem;
use Filament\Resources\Pages\CreateRecord;

class CreateCommittee extends CreateRecord
{
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = CommitteeResource::class;

    public function updated($property)
    {
        session()->put('committee_form', $this->data);
    }

    public function mount(): void
    {
        parent::mount();

        if (request()->filled('selected_media_id')) {
            if (session()->has('committee_form')) {
                $this->data = array_merge($this->data, session()->get('committee_form'));
            }

            $this->data['attachment_media_id'] = request('selected_media_id');
        } else {
            session()->forget('committee_form');
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $media = filled($data['attachment_media_id'] ?? null)
            ? MediaItem::query()->find($data['attachment_media_id'])
            : null;

        $data['attachment'] = $media?->file;

        return $data;
    }
}
