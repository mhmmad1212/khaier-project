<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'program_projects_index_template_key')) {
                $table->string('program_projects_index_template_key')->nullable()->after('page_template_key');
            }

            if (! Schema::hasColumn('site_settings', 'program_projects_show_template_key')) {
                $table->string('program_projects_show_template_key')->nullable()->after('program_projects_index_template_key');
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (Schema::hasColumn('site_settings', 'program_projects_show_template_key')) {
                $table->dropColumn('program_projects_show_template_key');
            }

            if (Schema::hasColumn('site_settings', 'program_projects_index_template_key')) {
                $table->dropColumn('program_projects_index_template_key');
            }
        });
    }
};
