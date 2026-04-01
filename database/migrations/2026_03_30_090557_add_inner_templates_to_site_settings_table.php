<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Disabled on this server because tenant schema updates are executed manually per association DB.
    }

    public function down(): void
    {
        //
    }
};
