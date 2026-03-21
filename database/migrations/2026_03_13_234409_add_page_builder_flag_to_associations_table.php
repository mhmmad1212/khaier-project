<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->boolean('can_use_page_builder')->default(false)->after('theme_key');
        });
    }

    public function down(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->dropColumn('can_use_page_builder');
        });
    }
};
