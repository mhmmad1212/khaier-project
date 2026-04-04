<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'default_meta_title')) {
                $table->string('default_meta_title')->nullable()->after('site_description');
            }

            if (! Schema::hasColumn('site_settings', 'default_meta_description')) {
                $table->text('default_meta_description')->nullable()->after('default_meta_title');
            }

            if (! Schema::hasColumn('site_settings', 'robots_indexing')) {
                $table->string('robots_indexing')->nullable()->after('default_meta_description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            foreach (['default_meta_title', 'default_meta_description', 'robots_indexing'] as $column) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
