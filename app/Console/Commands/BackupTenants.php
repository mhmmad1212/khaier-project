<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Association;

class BackupTenants extends Command
{
    protected $signature = 'backup:tenants';
    protected $description = 'Backup all active tenants databases + media';

    public function handle()
    {
        $basePath = storage_path('app/backups/tenants');
        if (!is_dir($basePath)) {
            mkdir($basePath, 0755, true);
        }

        $associations = Association::where('is_active', 1)->get();

        foreach ($associations as $assoc) {
            try {
                $this->info("Backing up: {$assoc->name}");

                $date = date('Y-m-d_H-i-s');
                $folder = "{$basePath}/{$assoc->id}/{$date}";

                if (!is_dir($folder)) {
                    mkdir($folder, 0755, true);
                }

                // ===== DB BACKUP =====
                $db = $assoc->database_name;
                $user = $assoc->database_username;
                $pass = $assoc->database_password;
                $host = $assoc->database_host ?? '127.0.0.1';

                $dbFile = "{$folder}/db.sql.gz";

                $cmd = "mysqldump --single-transaction -h {$host} -u {$user} -p'{$pass}' {$db} | gzip > {$dbFile}";
                exec($cmd);

                // ===== MEDIA BACKUP =====
                $mediaPath = public_path('storage');
                $mediaFile = "{$folder}/media.tar.gz";

                if (is_dir($mediaPath)) {
                    $cmdMedia = "tar -czf {$mediaFile} -C {$mediaPath} .";
                    exec($cmdMedia);
                }

                $this->info("Done: {$assoc->name}");

            } catch (\Throwable $e) {
                $this->error("Failed: {$assoc->name}");
                $this->error($e->getMessage());
            }
        }

        // ===== CLEAN OLD BACKUPS (14 days) =====
        $this->info("Cleaning old backups...");

        $days = 14;
        foreach (glob($basePath . '/*/*') as $dir) {
            if (is_dir($dir) && filemtime($dir) < time() - ($days * 86400)) {
                exec("rm -rf {$dir}");
            }
        }

        $this->info("Backup completed.");
    }
}
