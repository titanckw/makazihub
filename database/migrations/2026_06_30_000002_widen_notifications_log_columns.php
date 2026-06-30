<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The original notifications_log table restricted `type` and `channel`
     * to fixed enum lists (email/sms only) and required `recipient`.
     * New in-app features (chat, leave, attendance) need to log "system"
     * (in-app) notifications too, so we widen these columns.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE notifications_log MODIFY type VARCHAR(50) NOT NULL");
            DB::statement("ALTER TABLE notifications_log MODIFY channel VARCHAR(20) NOT NULL");
            DB::statement("ALTER TABLE notifications_log MODIFY recipient VARCHAR(255) NULL");
        }
        // SQLite (used in testing) does not enforce Laravel's enum() as a
        // hard constraint the same way, so no action is required there.
    }

    public function down(): void
    {
        // Intentionally left blank — reverting to the old constrained enum
        // would risk truncating data created by the new feature set.
    }
};
