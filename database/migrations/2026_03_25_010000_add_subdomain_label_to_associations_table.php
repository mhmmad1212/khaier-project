<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            if (! Schema::hasColumn('associations', 'subdomain_label')) {
                $table->string('subdomain_label')->nullable()->after('domain_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            if (Schema::hasColumn('associations', 'subdomain_label')) {
                $table->dropColumn('subdomain_label');
            }
        });
    }
};
