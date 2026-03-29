<?php

namespace App\Filament\Admin\Resources\NewsCategoryResource\Pages;

use App\Filament\Admin\Resources\NewsCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsCategory extends CreateRecord
{
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = NewsCategoryResource::class;
}
