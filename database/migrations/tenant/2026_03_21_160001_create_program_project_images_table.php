<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_project_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_project_id');
            $table->unsignedBigInteger('media_item_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('program_project_id');
            $table->index('media_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_project_images');
    }
};
