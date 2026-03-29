<?php

namespace App\Filament\Admin\Resources\MediaLibraryResource\Pages;

use App\Filament\Admin\Resources\MediaLibraryResource;
use Filament\Resources\Pages\EditRecord;

class EditMediaLibrary extends EditRecord
{
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = MediaLibraryResource::class;
}
