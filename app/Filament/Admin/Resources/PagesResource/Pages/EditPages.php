<?php

namespace App\Filament\Admin\Resources\PagesResource\Pages;

use App\Filament\Admin\Resources\PagesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPages extends EditRecord
{
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = PagesResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
