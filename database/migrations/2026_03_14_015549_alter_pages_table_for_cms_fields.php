<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->text('excerpt')->nullable()->after('slug');
            $table->string('featured_image')->nullable()->after('excerpt');
            $table->string('status')->default('published')->after('content');
            $table->integer('sort_order')->default(0)->after('status');
            $table->string('meta_title')->nullable()->after('sort_order');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->timestamp('published_at')->nullable()->after('meta_description');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn([
                'excerpt',
                'featured_image',
                'status',
                'sort_order',
                'meta_title',
                'meta_description',
                'published_at',
            ]);
        });
    }
};
