<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFeedbackSupportToTenants extends Command
{
    protected $signature = 'tenants:add-feedback-support';
    protected $description = 'Add feedback table and feedback_template_key to all tenant databases';

    public function handle(): int
    {
        $associations = DB::connection('mysql')
            ->table('associations')
            ->where('is_active', 1)
            ->get();

        if ($associations->isEmpty()) {
            $this->warn('No active associations found.');
            return self::SUCCESS;
        }

        foreach ($associations as $association) {
            if (
                empty($association->database_host) ||
                empty($association->database_port) ||
                empty($association->database_name) ||
                empty($association->database_username)
            ) {
                $this->warn("Skipped {$association->name}: incomplete database connection data.");
                continue;
            }

            $tenantConnection = 'tenant_runtime';

            try {
                Config::set("database.connections.{$tenantConnection}", [
                    'driver' => 'mysql',
                    'host' => $association->database_host ?: '127.0.0.1',
                    'port' => $association->database_port ?: 3306,
                    'database' => $association->database_name,
                    'username' => $association->database_username,
                    'password' => $association->database_password,
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'prefix_indexes' => true,
                    'strict' => true,
                    'engine' => null,
                ]);

                DB::purge($tenantConnection);
                DB::reconnect($tenantConnection);

                $schema = Schema::connection($tenantConnection);

                if (! $schema->hasTable('feedback')) {
                    $schema->create('feedback', function (Blueprint $table) {
                        $table->id();
                        $table->string('title');
                        $table->text('description')->nullable();
                        $table->text('file')->nullable();
                        $table->unsignedBigInteger('file_media_id')->nullable();
                        $table->integer('sort_order')->default(0);
                        $table->boolean('is_active')->default(true);
                        $table->timestamps();
                    });

                    $this->info("Created feedback table for: {$association->name} ({$association->database_name})");
                } else {
                    $this->line("feedback table already exists for: {$association->name} ({$association->database_name})");
                }

                if ($schema->hasTable('site_settings') && ! $schema->hasColumn('site_settings', 'feedback_template_key')) {
                    $schema->table('site_settings', function (Blueprint $table) {
                        $table->string('feedback_template_key')->nullable();
                    });

                    $this->info("Added feedback_template_key to site_settings for: {$association->name}");
                } else {
                    $this->line("feedback_template_key already exists or site_settings missing for: {$association->name}");
                }
            } catch (\Throwable $e) {
                $this->error("Failed for {$association->name} ({$association->database_name}): {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
