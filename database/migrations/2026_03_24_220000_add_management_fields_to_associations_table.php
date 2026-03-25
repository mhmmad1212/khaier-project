<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            if (! Schema::hasColumn('associations', 'domain_type')) {
                $table->string('domain_type')->default('custom_domain')->after('domain');
            }

            if (! Schema::hasColumn('associations', 'previous_domain')) {
                $table->string('previous_domain')->nullable()->after('domain_type');
            }

            if (! Schema::hasColumn('associations', 'subscription_start_date')) {
                $table->date('subscription_start_date')->nullable()->after('subscription_status');
            }

            if (! Schema::hasColumn('associations', 'subscription_end_date')) {
                $table->date('subscription_end_date')->nullable()->after('subscription_start_date');
            }

            if (! Schema::hasColumn('associations', 'creation_mode')) {
                $table->string('creation_mode')->default('empty')->after('database_password');
            }

            if (! Schema::hasColumn('associations', 'cloned_from_association_id')) {
                $table->unsignedBigInteger('cloned_from_association_id')->nullable()->after('creation_mode');
            }

            if (! Schema::hasColumn('associations', 'last_domain_changed_at')) {
                $table->timestamp('last_domain_changed_at')->nullable()->after('cloned_from_association_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $drop = [];

            foreach ([
                'domain_type',
                'previous_domain',
                'subscription_start_date',
                'subscription_end_date',
                'creation_mode',
                'cloned_from_association_id',
                'last_domain_changed_at',
            ] as $col) {
                if (Schema::hasColumn('associations', $col)) {
                    $drop[] = $col;
                }
            }

            if (! empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
