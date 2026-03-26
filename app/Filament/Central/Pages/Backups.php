<?php

namespace App\Filament\Central\Pages;

use App\Models\Association;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Backups extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';
    protected static ?string $navigationLabel = 'النسخ الاحتياطية';
    protected static ?string $title = 'النسخ الاحتياطية';
    protected static ?int $navigationSort = 50;
    protected static string $view = 'filament.central.pages.backups';

    public array $backups = [];

    public function mount(): void
    {
        $this->loadBackups();
    }

    public function loadBackups(): void
    {
        $base = storage_path('app/backups/tenants');
        $items = [];

        if (! is_dir($base)) {
            $this->backups = [];
            return;
        }

        $associations = Association::query()
            ->pluck('name', 'id')
            ->map(fn ($name) => (string) $name)
            ->toArray();

        foreach (glob($base . '/*/*') as $dir) {
            if (! is_dir($dir)) {
                continue;
            }

            $size = 0;
            foreach (File::allFiles($dir) as $file) {
                $size += $file->getSize();
            }

            $associationId = basename(dirname($dir));
            $rawDate = basename($dir);

            $dateDisplay = $rawDate;
            $timeDisplay = '';

            try {
                $dt = Carbon::createFromFormat('Y-m-d_H-i-s', $rawDate)->addHours(3);
                $dateDisplay = $dt->format('Y-m-d');
                $timeDisplay = $dt->format('H:i:s');
            } catch (\Throwable $e) {
            }

            $items[] = [
                'association_id' => $associationId,
                'association_name' => $associations[$associationId] ?? ('جمعية #' . $associationId),
                'date' => $rawDate,
                'date_display' => $dateDisplay,
                'time_display' => $timeDisplay,
                'path' => $dir,
                'size' => $this->formatBytes($size),
            ];
        }

        usort($items, fn ($a, $b) => strcmp($b['date'], $a['date']));
        $this->backups = $items;
    }

    public function download($path): StreamedResponse
    {
        $zipFile = storage_path('app/tmp_backup.zip');

        if (file_exists($zipFile)) {
            unlink($zipFile);
        }

        $escapedPath = escapeshellarg($path);
        $escapedZip = escapeshellarg($zipFile);

        exec("cd {$escapedPath} && zip -r {$escapedZip} .");

        return response()->download($zipFile)->deleteFileAfterSend(true);
    }

    public function delete($path): void
    {
        $escapedPath = escapeshellarg($path);
        exec("rm -rf {$escapedPath}");
        $this->loadBackups();

        Notification::make()
            ->title('تم حذف النسخة الاحتياطية')
            ->success()
            ->send();
    }

    public function runBackup(): void
    {
        exec('cd /var/www/khaier && nohup /usr/bin/php artisan backup:tenants >> storage/logs/backup-tenants.log 2>&1 &');

        Notification::make()
            ->title('تم تشغيل النسخ الاحتياطي')
            ->body('بدأت العملية في الخلفية، وسيظهر الناتج في هذه الصفحة بعد دقائق.')
            ->success()
            ->send();
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
