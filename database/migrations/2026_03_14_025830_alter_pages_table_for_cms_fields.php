<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (! Schema::hasColumn('pages', 'excerpt')) {
                $table->text('excerpt')->nullable()->after('slug');
            }

            if (! Schema::hasColumn('pages', 'featured_image')) {
                $table->string('featured_image')->nullable()->after('excerpt');
            }

            if (! Schema::hasColumn('pages', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('content');
            }

            if (! Schema::hasColumn('pages', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }

            if (! Schema::hasColumn('pages', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $columns = [];

            foreach (['excerpt', 'featured_image', 'meta_title', 'meta_description', 'published_at'] as $column) {
                if (Schema::hasColumn('pages', $column)) {
                    $columns[] = $column;
                }
            }

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
