<?php
// database/migrations/2026_02_26_200001_update_notifications_log_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The notifications_log table was created in Module 1.
        // We add any missing columns needed for Module 7.
        Schema::table('notifications_log', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications_log', 'raw_response')) {
                $table->text('raw_response')->nullable()->after('status');
            }
            if (!Schema::hasColumn('notifications_log', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('raw_response');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications_log', function (Blueprint $table) {
            $table->dropColumnIfExists('raw_response');
            $table->dropColumnIfExists('sent_at');
        });
    }
};
