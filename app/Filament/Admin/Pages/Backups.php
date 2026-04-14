<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class Backups extends Page
{
    protected static bool $shouldRegisterNavigation = false;


    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.backups';

}
