<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class Backups extends Page
{
    protected static ?string $navigationGroup = 'إعدادات النظام';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.backups';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
