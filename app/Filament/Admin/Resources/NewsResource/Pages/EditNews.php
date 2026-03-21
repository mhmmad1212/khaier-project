<?php

namespace App\Filament\Admin\Resources\NewsResource\Pages;

use App\Filament\Admin\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditNews extends EditRecord
{
    protected static string $resource = NewsResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $removeCurrentImage = (bool) ($this->data['remove_current_image'] ?? false);
        $oldImage = $this->record->image;
        $newImage = $data['image'] ?? null;

        if ($removeCurrentImage && $oldImage && ! $newImage) {
            if (Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            $data['image'] = null;
        }

        if ($newImage && $oldImage && $newImage !== $oldImage) {
            if (Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
