<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->string('database_host')->default('127.0.0.1')->after('domain');
            $table->integer('database_port')->default(3306)->after('database_host');
            $table->string('database_name')->nullable()->after('database_port');
            $table->string('database_username')->nullable()->after('database_name');
            $table->string('database_password')->nullable()->after('database_username');
            $table->string('subscription_status')->default('active')->after('database_password');
            $table->string('theme_key')->default('default')->after('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->dropColumn([
                'database_host',
                'database_port',
                'database_name',
                'database_username',
                'database_password',
                'subscription_status',
                'theme_key',
            ]);
        });
    }
};
