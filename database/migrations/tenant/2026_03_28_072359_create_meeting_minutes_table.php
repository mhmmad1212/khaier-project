<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('meeting_minutes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('board');
            $table->string('meeting_type')->default('regular');
            $table->date('meeting_date')->nullable();
            $table->text('description')->nullable();
            $table->string('file')->nullable();
            $table->unsignedBigInteger('file_media_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('meeting_minutes');
    }
};