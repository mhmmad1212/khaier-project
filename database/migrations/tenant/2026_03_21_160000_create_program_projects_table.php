<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->unsignedBigInteger('cover_image_media_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('project_amount', 14, 2)->nullable();
            $table->decimal('donation_amount', 14, 2)->nullable();
            $table->string('donation_url')->nullable();
            $table->string('report_file')->nullable();
            $table->unsignedBigInteger('report_media_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_projects');
    }
};
