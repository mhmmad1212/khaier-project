<?php

namespace App\Services;

use App\Models\Association;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class AssociationProvisioningService
{
    public function provision(Association $association): array
    {
        $central = config('database.connections.mysql');

        $databaseName = $association->database_name ?: $this->generateDatabaseName($association->slug);
        $databaseHost = $association->database_host ?: ($central['host'] ?? '127.0.0.1');
        $databasePort = (string) ($association->database_port ?: ($central['port'] ?? 3306));
        $databaseUsername = $association->database_username ?: ($central['username'] ?? '');
        $databasePassword = $association->database_password ?: ($central['password'] ?? '');

        $association->forceFill([
            'database_host' => $databaseHost,
            'database_port' => $databasePort,
            'database_name' => $databaseName,
            'database_username' => $databaseUsername,
            'database_password' => $databasePassword,
        ])->save();

        try {
            $this->createDatabase($databaseName);

            if (($association->creation_mode ?? 'empty') === 'clone' && $association->cloned_from_association_id) {
                $source = Association::findOrFail($association->cloned_from_association_id);

                $this->cloneDatabase($source, $association);
                $this->cloneMediaFiles($source, $association);
                $this->updateClonedPaths($source, $association);
            } else {
                $this->runTenantMigrations($association);
            }

            return $this->resetAndCreateTenantAdmin($association);
        } catch (Throwable $e) {
            $this->rollbackProvisioning($association);
            throw $e;
        }
    }

    public function rollbackProvisioning(Association $association): void
    {
        try {
            if (! empty($association->database_name)) {
                DB::connection('mysql')->statement("DROP DATABASE IF EXISTS `{$association->database_name}`");
            }
        } catch (Throwable $e) {
        }

        try {
            $tenantDir = storage_path('app/public/tenants/' . $association->database_name);
            if (File::exists($tenantDir)) {
                File::deleteDirectory($tenantDir);
            }
        } catch (Throwable $e) {
        }

        try {
            $association->delete();
        } catch (Throwable $e) {
        }
    }

    protected function generateDatabaseName(string $slug): string
    {
        $slug = Str::of($slug)->lower()->replace('-', '_')->replace(' ', '_')->value();

        return 'assoc_' . $slug;
    }

    protected function createDatabase(string $databaseName): void
    {
        DB::connection('mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    protected function configureTenantConnection(Association $association): void
    {
        Config::set('database.connections.tenant.host', $association->database_host);
        Config::set('database.connections.tenant.port', $association->database_port);
        Config::set('database.connections.tenant.database', $association->database_name);
        Config::set('database.connections.tenant.username', $association->database_username);
        Config::set('database.connections.tenant.password', $association->database_password);

        DB::purge('tenant');
        DB::reconnect('tenant');
    }

    protected function runTenantMigrations(Association $association): void
    {
        $this->configureTenantConnection($association);

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--force' => true,
        ]);
    }

    protected function cloneDatabase(Association $source, Association $target): void
    {
        $sourceCnf = $this->makeMysqlDefaultsFile(
            $source->database_host,
            $source->database_port,
            $source->database_username,
            $source->database_password
        );

        $targetCnf = $this->makeMysqlDefaultsFile(
            $target->database_host,
            $target->database_port,
            $target->database_username,
            $target->database_password
        );

        try {
            $command = sprintf(
                'mysqldump --defaults-extra-file=%s --single-transaction --quick --routines --events --triggers %s | mysql --defaults-extra-file=%s %s',
                escapeshellarg($sourceCnf),
                escapeshellarg($source->database_name),
                escapeshellarg($targetCnf),
                escapeshellarg($target->database_name),
            );

            exec($command . ' 2>&1', $output, $code);

            if ($code !== 0) {
                throw new RuntimeException("فشل استنساخ قاعدة البيانات:\n" . implode("\n", $output));
            }
        } finally {
            @unlink($sourceCnf);
            @unlink($targetCnf);
        }

        $this->configureTenantConnection($target);
    }

    protected function makeMysqlDefaultsFile(string $host, string $port, string $username, string $password): string
    {
        $path = storage_path('app/tmp_mysql_' . Str::random(12) . '.cnf');

        $content = "[client]\n"
            . "host={$host}\n"
            . "port={$port}\n"
            . "user={$username}\n"
            . "password=\"{$password}\"\n";

        file_put_contents($path, $content);
        @chmod($path, 0600);

        return $path;
    }

    protected function cloneMediaFiles(Association $source, Association $target): void
    {
        $sourceDir = storage_path('app/public/tenants/' . $source->database_name);
        $targetDir = storage_path('app/public/tenants/' . $target->database_name);

        if (! File::exists($sourceDir)) {
            return;
        }

        File::ensureDirectoryExists($targetDir);

        foreach (File::allFiles($sourceDir) as $file) {
            $relative = ltrim(str_replace($sourceDir, '', $file->getPathname()), DIRECTORY_SEPARATOR);
            $destPath = $targetDir . DIRECTORY_SEPARATOR . $relative;

            File::ensureDirectoryExists(dirname($destPath));
            File::copy($file->getPathname(), $destPath);
        }
    }

    protected function updateClonedPaths(Association $source, Association $target): void
    {
        $this->configureTenantConnection($target);

        $schema = DB::connection('tenant')->getSchemaBuilder();

        if ($schema->hasTable('media_items')) {
            DB::connection('tenant')->statement(
                "UPDATE media_items SET file = REPLACE(file, ?, ?)",
                [$source->database_name, $target->database_name]
            );

            if ($schema->hasColumn('media_items', 'directory')) {
                DB::connection('tenant')->statement(
                    "UPDATE media_items SET directory = REPLACE(directory, ?, ?)",
                    [$source->database_name, $target->database_name]
                );
            }
        }

        foreach ([
            ['table' => 'news', 'column' => 'image'],
            ['table' => 'sliders', 'column' => 'image'],
            ['table' => 'program_projects', 'column' => 'cover_image'],
        ] as $item) {
            if ($schema->hasTable($item['table']) && $schema->hasColumn($item['table'], $item['column'])) {
                DB::connection('tenant')->statement(
                    "UPDATE {$item['table']} SET {$item['column']} = REPLACE({$item['column']}, ?, ?)",
                    [$source->database_name, $target->database_name]
                );
            }
        }
    }

    protected function resetAndCreateTenantAdmin(Association $association): array
    {
        $this->configureTenantConnection($association);

        $schema = DB::connection('tenant')->getSchemaBuilder();

        if (! $schema->hasTable('users')) {
            throw new RuntimeException('جدول users غير موجود في قاعدة الجمعية.');
        }

        DB::connection('tenant')->table('users')->delete();

        $email = $association->official_email ?: ('admin@' . $association->slug . '.local');
        $password = Str::random(10);

        DB::connection('tenant')->table('users')->insert([
            'name' => $association->name . ' - مدير',
            'email' => $email,
            'password' => Hash::make($password),
            'association_id' => null,
            'is_super_admin' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'email' => $email,
            'password' => $password,
        ];
    }
}
