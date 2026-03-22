<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'committees_template_key')) {
                $table->string('committees_template_key')->nullable()->after('news_index_template_key');
            }

            if (! Schema::hasColumn('site_settings', 'board_members_template_key')) {
                $table->string('board_members_template_key')->nullable()->after('committees_template_key');
            }

            if (! Schema::hasColumn('site_settings', 'general_assembly_members_template_key')) {
                $table->string('general_assembly_members_template_key')->nullable()->after('board_members_template_key');
            }

            if (! Schema::hasColumn('site_settings', 'employees_template_key')) {
                $table->string('employees_template_key')->nullable()->after('general_assembly_members_template_key');
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            foreach ([
                'committees_template_key',
                'board_members_template_key',
                'general_assembly_members_template_key',
                'employees_template_key',
            ] as $column) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
