<?php

namespace App\Filament\Admin\Resources\LicenseResource\Pages;

use App\Filament\Admin\Resources\LicenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLicense extends CreateRecord
{
    use \App\Filament\Traits\HasBackButton;

    protected static string $resource = LicenseResource::class;
}
