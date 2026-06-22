<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications_log', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications_log', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications_log', function (Blueprint $table) {
            if (Schema::hasColumn('notifications_log', 'read_at')) {
                $table->dropColumn('read_at');
            }
        });
    }
};
