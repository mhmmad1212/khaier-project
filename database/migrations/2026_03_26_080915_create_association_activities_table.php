<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('association_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('association_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('action_code');
            $table->string('action_type', 50);
            $table->string('title');
            $table->longText('details')->nullable();
            $table->foreignId('performed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['association_id', 'action_code']);
            $table->index(['association_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('association_activities');
    }
};
