<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'home_template_key')) {
                $table->string('home_template_key')->nullable();
            }
            if (! Schema::hasColumn('site_settings', 'policies_template_key')) {
                $table->string('policies_template_key')->nullable();
            }
            if (! Schema::hasColumn('site_settings', 'financial_reports_template_key')) {
                $table->string('financial_reports_template_key')->nullable();
            }
            if (! Schema::hasColumn('site_settings', 'news_index_template_key')) {
                $table->string('news_index_template_key')->nullable();
            }
            if (! Schema::hasColumn('site_settings', 'committees_template_key')) {
                $table->string('committees_template_key')->nullable();
            }
            if (! Schema::hasColumn('site_settings', 'board_members_template_key')) {
                $table->string('board_members_template_key')->nullable();
            }
            if (! Schema::hasColumn('site_settings', 'general_assembly_members_template_key')) {
                $table->string('general_assembly_members_template_key')->nullable();
            }
            if (! Schema::hasColumn('site_settings', 'employees_template_key')) {
                $table->string('employees_template_key')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $columns = [
                'home_template_key',
                'policies_template_key',
                'financial_reports_template_key',
                'news_index_template_key',
                'committees_template_key',
                'board_members_template_key',
                'general_assembly_members_template_key',
                'employees_template_key',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
