<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->boolean('can_edit_system_pages')
                ->default(false)
                ->after('can_use_page_builder');
        });
    }

    public function down(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->dropColumn('can_edit_system_pages');
        });
    }
};
