<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            // Only add if not already present (safe re-run)
            if (!Schema::hasColumn('leases', 'termination_reason')) {
                $table->text('termination_reason')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('leases', 'terminated_at')) {
                $table->date('terminated_at')->nullable()->after('termination_reason');
            }
            if (!Schema::hasColumn('leases', 'property_id')) {
                $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete()->after('unit_id');
            }
        });

        // Add unit_id to tenants if not present
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete()->after('user_id');
            }
            if (!Schema::hasColumn('tenants', 'status')) {
                $table->enum('status', ['active', 'inactive', 'blacklisted'])->default('active')->after('employer');
            }
            if (!Schema::hasColumn('tenants', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn(['termination_reason', 'terminated_at']);
        });
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['unit_id', 'status', 'notes']);
        });
    }
};
