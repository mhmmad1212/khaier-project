<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            if (! Schema::hasColumn('associations', 'official_email')) {
                $table->string('official_email')->nullable()->after('domain');
            }

            if (! Schema::hasColumn('associations', 'official_phone')) {
                $table->string('official_phone')->nullable()->after('official_email');
            }

            if (! Schema::hasColumn('associations', 'site_status')) {
                $table->string('site_status')->default('active')->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $drop = [];

            foreach (['official_email', 'official_phone', 'site_status'] as $col) {
                if (Schema::hasColumn('associations', $col)) {
                    $drop[] = $col;
                }
            }

            if (! empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
