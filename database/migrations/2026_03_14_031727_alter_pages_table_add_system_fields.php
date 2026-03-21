<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('page_type')->default('content')->after('slug');
            $table->string('system_key')->nullable()->after('page_type');
            $table->boolean('allow_tenant_edit')->default(true)->after('system_key');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn([
                'page_type',
                'system_key',
                'allow_tenant_edit',
            ]);
        });
    }
};
