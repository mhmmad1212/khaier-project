<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\File;

class Backups extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';
    protected static string $view = 'filament.pages.backups';
    protected static ?string $navigationLabel = 'النسخ الاحتياطية';

    public $backups = [];

    public function mount()
    {
        $base = storage_path('app/backups/tenants');

        if (!is_dir($base)) return;

        foreach (glob($base . '/*/*') as $dir) {
            $this->backups[] = [
                'path' => $dir,
                'name' => basename(dirname($dir)),
                'date' => basename($dir),
                'size' => $this->formatSize($this->folderSize($dir)),
            ];
        }
    }

    private function folderSize($dir)
    {
        $size = 0;
        foreach (File::allFiles($dir) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }

    private function formatSize($bytes)
    {
        return round($bytes / 1024 / 1024, 2) . ' MB';
    }
}
