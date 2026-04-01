<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('association_domain_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('association_id')->constrained()->cascadeOnDelete();
            $table->string('expected_host')->nullable();
            $table->string('resolved_value')->nullable();
            $table->string('dns_status')->nullable();
            $table->integer('http_status')->nullable();
            $table->string('ssl_status')->nullable();
            $table->boolean('is_pointing_correctly')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->unique('association_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('association_domain_checks');
    }
};
