<?php

namespace App\Console\Commands;

use App\Models\Association;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncTenantMediaColumns extends Command
{
    protected $signature = 'tenants:sync-media-columns {--domain=}';
    protected $description = 'Add missing media-related columns to all tenant databases';

    public function handle(): int
    {
        $query = Association::query()->orderBy('id');

        if ($domain = $this->option('domain')) {
            $query->where('domain', $domain);
        }

        $associations = $query->get();

        if ($associations->isEmpty()) {
            $this->warn('No associations found.');
            return self::SUCCESS;
        }

        foreach ($associations as $association) {
            $this->line('');
            $this->info("==> {$association->domain}");

            try {
                if (
                    empty($association->database_host) ||
                    empty($association->database_port) ||
                    empty($association->database_name) ||
                    empty($association->database_username)
                ) {
                    $this->warn("skip {$association->domain} (incomplete database credentials)");
                    continue;
                }

                $this->connectTenant($association);

                $this->syncPartners();
                $this->syncSliders();
                $this->syncNews();
                $this->syncPages();
                $this->syncFinancialReports();
                $this->syncPolicies();
                $this->syncRegulations();
                $this->syncEmployees();
                $this->syncBoardMembers();
                $this->syncGeneralAssemblyMembers();
                $this->syncCommittees();
                $this->syncProgramProjects();

                $this->info("Done: {$association->domain}");
            } catch (\Throwable $e) {
                $this->error("Failed: {$association->domain}");
                $this->error($e->getMessage());
            }
        }

        return self::SUCCESS;
    }

    protected function connectTenant(Association $association): void
    {
        Config::set('database.connections.tenant.host', $association->database_host);
        Config::set('database.connections.tenant.port', $association->database_port);
        Config::set('database.connections.tenant.database', $association->database_name);
        Config::set('database.connections.tenant.username', $association->database_username);
        Config::set('database.connections.tenant.password', $association->database_password);

        DB::purge('tenant');
        DB::reconnect('tenant');
    }

    protected function hasTable(string $table): bool
    {
        return Schema::connection('tenant')->hasTable($table);
    }

    protected function addColumnIfMissing(string $table, string $column, callable $callback): void
    {
        if (! $this->hasTable($table)) {
            $this->warn("skip {$table} (table not found)");
            return;
        }

        if (Schema::connection('tenant')->hasColumn($table, $column)) {
            $this->line("exists {$table}.{$column}");
            return;
        }

        Schema::connection('tenant')->table($table, function (Blueprint $tableBlueprint) use ($callback) {
            $callback($tableBlueprint);
        });

        $this->info("added {$table}.{$column}");
    }

    protected function syncPartners(): void
    {
        $this->addColumnIfMissing('partners', 'name', fn (Blueprint $t) => $t->string('name')->nullable());
        $this->addColumnIfMissing('partners', 'logo', fn (Blueprint $t) => $t->string('logo')->nullable());
        $this->addColumnIfMissing('partners', 'logo_media_id', fn (Blueprint $t) => $t->unsignedBigInteger('logo_media_id')->nullable());
        $this->addColumnIfMissing('partners', 'url', fn (Blueprint $t) => $t->string('url')->nullable());
        $this->addColumnIfMissing('partners', 'sort_order', fn (Blueprint $t) => $t->integer('sort_order')->default(0));
        $this->addColumnIfMissing('partners', 'is_active', fn (Blueprint $t) => $t->boolean('is_active')->default(true));
    }

    protected function syncSliders(): void
    {
        $this->addColumnIfMissing('sliders', 'image_media_id', fn (Blueprint $t) => $t->unsignedBigInteger('image_media_id')->nullable());
    }

    protected function syncNews(): void
    {
        $this->addColumnIfMissing('news', 'image_media_id', fn (Blueprint $t) => $t->unsignedBigInteger('image_media_id')->nullable());
    }

    protected function syncPages(): void
    {
        $this->addColumnIfMissing('pages', 'featured_media_id', fn (Blueprint $t) => $t->unsignedBigInteger('featured_media_id')->nullable());
    }

    protected function syncFinancialReports(): void
    {
        $this->addColumnIfMissing('financial_reports', 'file_media_id', fn (Blueprint $t) => $t->unsignedBigInteger('file_media_id')->nullable());
    }

    protected function syncPolicies(): void
    {
        $this->addColumnIfMissing('policies', 'file_media_id', fn (Blueprint $t) => $t->unsignedBigInteger('file_media_id')->nullable());
    }

    protected function syncRegulations(): void
    {
        $this->addColumnIfMissing('regulations', 'file_media_id', fn (Blueprint $t) => $t->unsignedBigInteger('file_media_id')->nullable());
    }

    protected function syncEmployees(): void
    {
        $this->addColumnIfMissing('employees', 'photo_media_id', fn (Blueprint $t) => $t->unsignedBigInteger('photo_media_id')->nullable());
        $this->addColumnIfMissing('employees', 'attachment_media_id', fn (Blueprint $t) => $t->unsignedBigInteger('attachment_media_id')->nullable());
    }

    protected function syncBoardMembers(): void
    {
        $this->addColumnIfMissing('board_members', 'photo_media_id', fn (Blueprint $t) => $t->unsignedBigInteger('photo_media_id')->nullable());
    }

    protected function syncGeneralAssemblyMembers(): void
    {
        $this->addColumnIfMissing('general_assembly_members', 'photo_media_id', fn (Blueprint $t) => $t->unsignedBigInteger('photo_media_id')->nullable());
    }

    protected function syncCommittees(): void
    {
        $this->addColumnIfMissing('committees', 'attachment_media_id', fn (Blueprint $t) => $t->unsignedBigInteger('attachment_media_id')->nullable());
    }

    protected function syncProgramProjects(): void
    {
        $this->addColumnIfMissing('program_projects', 'image_media_id', fn (Blueprint $t) => $t->unsignedBigInteger('image_media_id')->nullable());
        $this->addColumnIfMissing('program_projects', 'file_media_id', fn (Blueprint $t) => $t->unsignedBigInteger('file_media_id')->nullable());
    }
}
