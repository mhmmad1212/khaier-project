<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('page_templates', 'template_content')) {
                $table->longText('template_content')->nullable()->after('view_path');
            }

            if (! Schema::hasColumn('page_templates', 'template_css')) {
                $table->longText('template_css')->nullable()->after('template_content');
            }

            if (! Schema::hasColumn('page_templates', 'template_js')) {
                $table->longText('template_js')->nullable()->after('template_css');
            }
        });
    }

    public function down(): void
    {
        Schema::table('page_templates', function (Blueprint $table) {
            $columns = [];

            foreach (['template_content', 'template_css', 'template_js'] as $column) {
                if (Schema::hasColumn('page_templates', $column)) {
                    $columns[] = $column;
                }
            }

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
