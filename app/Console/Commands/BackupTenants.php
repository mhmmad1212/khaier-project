<?php

namespace App\Console\Commands;

use App\Models\Association;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PDO;
use Throwable;

class BackupTenants extends Command
{
    protected $signature = 'backup:tenants';
    protected $description = 'Backup central database + all active tenants databases + tenant media';

    public function handle()
    {
        $basePath = storage_path('app/backups');
        $tenantsBasePath = $basePath . '/tenants';
        $centralBasePath = $basePath . '/central';

        File::ensureDirectoryExists($tenantsBasePath);
        File::ensureDirectoryExists($centralBasePath);

        $this->info('Starting backup...');

        try {
            $this->backupCentralDatabase($centralBasePath);
            $this->info('Central database backup completed.');
        } catch (Throwable $e) {
            $this->error('Central database backup failed.');
            $this->error($e->getMessage());
        }

        $associations = Association::query()
            ->where('is_active', 1)
            ->get();

        foreach ($associations as $assoc) {
            try {
                $this->info("Backing up tenant: {$assoc->name}");

                $date = now()->format('Y-m-d_H-i-s');
                $folder = "{$tenantsBasePath}/{$assoc->id}/{$date}";

                File::ensureDirectoryExists($folder);

                $this->backupTenantDatabase($assoc, $folder);
                $this->backupTenantMedia($assoc, $folder);

                $this->info("Done: {$assoc->name}");
            } catch (Throwable $e) {
                $this->error("Failed: {$assoc->name}");
                $this->error($e->getMessage());
            }
        }

        $this->info('Cleaning old backups...');
        $this->cleanupOldBackups($basePath, 14);

        $this->info('Backup completed.');
        return self::SUCCESS;
    }

    protected function backupCentralDatabase(string $centralBasePath): void
    {
        $config = config('database.connections.mysql');

        $database = (string) ($config['database'] ?? '');
        $username = (string) ($config['username'] ?? '');
        $password = (string) ($config['password'] ?? '');
        $host = (string) ($config['host'] ?? '127.0.0.1');
        $port = (string) ($config['port'] ?? '3306');

        if ($database === '' || $username === '') {
            throw new \RuntimeException('Central database credentials are incomplete.');
        }

        $date = now()->format('Y-m-d_H-i-s');
        $folder = "{$centralBasePath}/{$date}";
        File::ensureDirectoryExists($folder);

        $dbFile = "{$folder}/db.sql.gz";
        $this->dumpDatabase(
            host: $host,
            port: $port,
            database: $database,
            username: $username,
            password: $password,
            outputFile: $dbFile,
        );
    }

    protected function backupTenantDatabase(Association $assoc, string $folder): void
    {
        $database = (string) ($assoc->database_name ?? '');
        $username = (string) ($assoc->database_username ?? '');
        $password = (string) ($assoc->database_password ?? '');
        $host = (string) ($assoc->database_host ?? '127.0.0.1');
        $port = (string) ($assoc->database_port ?? '3306');

        if ($database === '' || $username === '') {
            throw new \RuntimeException("Tenant database credentials are incomplete for association #{$assoc->id}.");
        }

        $dbFile = "{$folder}/db.sql.gz";

        $this->dumpDatabase(
            host: $host,
            port: $port,
            database: $database,
            username: $username,
            password: $password,
            outputFile: $dbFile,
        );
    }

    protected function backupTenantMedia(Association $assoc, string $folder): void
    {
        $tmpMediaRoot = "{$folder}/_media_tmp";
        File::ensureDirectoryExists($tmpMediaRoot);

        $copiedSomething = false;

        $tenantScopedMediaPath = storage_path('app/public/tenants/' . $assoc->database_name);
        if (is_dir($tenantScopedMediaPath)) {
            $target = $tmpMediaRoot . '/tenants/' . $assoc->database_name;
            File::ensureDirectoryExists(dirname($target));
            File::copyDirectory($tenantScopedMediaPath, $target);
            $copiedSomething = true;
        }

        foreach ($this->getTenantMediaFiles($assoc) as $relativePath) {
            $relativePath = ltrim($relativePath, '/');
            if ($relativePath === '') {
                continue;
            }

            $sourcePath = storage_path('app/public/' . $relativePath);
            $targetPath = $tmpMediaRoot . '/' . $relativePath;

            if (is_file($sourcePath)) {
                File::ensureDirectoryExists(dirname($targetPath));
                if (! file_exists($targetPath)) {
                    File::copy($sourcePath, $targetPath);
                }
                $copiedSomething = true;
            } elseif (is_dir($sourcePath)) {
                if (! is_dir($targetPath)) {
                    File::ensureDirectoryExists(dirname($targetPath));
                    File::copyDirectory($sourcePath, $targetPath);
                }
                $copiedSomething = true;
            }
        }

        if ($copiedSomething) {
            $mediaFile = "{$folder}/media.tar.gz";
            $cmd = 'tar -czf ' . escapeshellarg($mediaFile) . ' -C ' . escapeshellarg($tmpMediaRoot) . ' .';
            exec($cmd, $output, $status);

            if ($status !== 0) {
                throw new \RuntimeException("Failed to create media archive for association #{$assoc->id}.");
            }
        }

        File::deleteDirectory($tmpMediaRoot);
    }

    protected function getTenantMediaFiles(Association $assoc): array
    {
        $database = (string) ($assoc->database_name ?? '');
        $username = (string) ($assoc->database_username ?? '');
        $password = (string) ($assoc->database_password ?? '');
        $host = (string) ($assoc->database_host ?? '127.0.0.1');
        $port = (string) ($assoc->database_port ?? '3306');

        if ($database === '' || $username === '') {
            return [];
        }

        $pdo = $this->makePdo(
            host: $host,
            port: $port,
            database: $database,
            username: $username,
            password: $password,
        );

        if (! $this->tableExists($pdo, 'media_items')) {
            return [];
        }

        $stmt = $pdo->query("SELECT DISTINCT file FROM media_items WHERE file IS NOT NULL AND file <> ''");
        $files = $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];

        return array_values(array_unique(array_filter(array_map(
            fn ($file) => is_string($file) ? trim($file) : '',
            $files
        ))));
    }

    protected function makePdo(
        string $host,
        string $port,
        string $database,
        string $username,
        string $password
    ): PDO {
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    protected function tableExists(PDO $pdo, string $table): bool
    {
        $stmt = $pdo->prepare('SHOW TABLES LIKE ?');
        $stmt->execute([$table]);

        return (bool) $stmt->fetchColumn();
    }

    protected function dumpDatabase(
        string $host,
        string $port,
        string $database,
        string $username,
        string $password,
        string $outputFile
    ): void {
        $defaultsFile = storage_path('app/tmp_mysql_' . Str::random(12) . '.cnf');

        $cnf = "[client]\n"
            . "host={$host}\n"
            . "port={$port}\n"
            . "user={$username}\n"
            . "password={$password}\n";

        file_put_contents($defaultsFile, $cnf);
        @chmod($defaultsFile, 0600);

        try {
            $dumpCmd = 'mysqldump '
                . '--defaults-extra-file=' . escapeshellarg($defaultsFile) . ' '
                . '--single-transaction --quick --routines --events --triggers --no-tablespaces '
                . escapeshellarg($database)
                . ' | gzip > '
                . escapeshellarg($outputFile);

            $cmd = '/bin/bash -o pipefail -c ' . escapeshellarg($dumpCmd);

            exec($cmd, $output, $status);

            if ($status !== 0) {
                throw new \RuntimeException("mysqldump failed for database [{$database}]");
            }
        } finally {
            if (file_exists($defaultsFile)) {
                @unlink($defaultsFile);
            }
        }
    }

    protected function cleanupOldBackups(string $basePath, int $days): void
    {
        $threshold = time() - ($days * 86400);

        foreach (glob($basePath . '/central/*') as $dir) {
            if (is_dir($dir) && filemtime($dir) < $threshold) {
                File::deleteDirectory($dir);
            }
        }

        foreach (glob($basePath . '/tenants/*/*') as $dir) {
            if (is_dir($dir) && filemtime($dir) < $threshold) {
                File::deleteDirectory($dir);
            }
        }
    }
}
