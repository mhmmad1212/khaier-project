<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('tenant')->table('site_settings', function (Blueprint $table) {
            if (! Schema::connection('tenant')->hasColumn('site_settings', 'feedback_template_key')) {
                $table->string('feedback_template_key')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->table('site_settings', function (Blueprint $table) {
            if (Schema::connection('tenant')->hasColumn('site_settings', 'feedback_template_key')) {
                $table->dropColumn('feedback_template_key');
            }
        });
    }
};
