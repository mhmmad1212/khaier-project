<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->text('excerpt')->nullable()->after('slug');
            $table->string('status')->default('published')->after('content');
            $table->string('meta_title')->nullable()->after('status');
            $table->text('meta_description')->nullable()->after('meta_title');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn([
                'excerpt',
                'status',
                'meta_title',
                'meta_description',
            ]);
        });
    }
};
