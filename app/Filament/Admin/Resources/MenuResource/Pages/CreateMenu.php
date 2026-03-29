<?php

namespace App\Filament\Admin\Resources\MenuResource\Pages;

use App\Filament\Admin\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMenu extends CreateRecord
{
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = MenuResource::class;
}
