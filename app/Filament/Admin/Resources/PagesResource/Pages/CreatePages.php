<?php

namespace App\Filament\Admin\Resources\PagesResource\Pages;

use App\Filament\Admin\Resources\PagesResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePages extends CreateRecord
{
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = PagesResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
