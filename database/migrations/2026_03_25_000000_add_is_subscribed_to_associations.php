<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            if (! Schema::hasColumn('associations', 'is_subscribed')) {
                $table->boolean('is_subscribed')->default(true)->after('site_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            if (Schema::hasColumn('associations', 'is_subscribed')) {
                $table->dropColumn('is_subscribed');
            }
        });
    }
};
