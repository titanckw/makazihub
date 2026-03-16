<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('payments', 'mpesa_receipt')) {
                $table->string('mpesa_receipt', 50)->nullable()->after('mpesa_receipt_number');
            }

            if (!Schema::hasColumn('payments', 'phone_number')) {
                $table->string('phone_number', 20)->nullable()->after('mpesa_phone');
            }

            if (!Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'mpesa_receipt')) {
                $table->dropColumn('mpesa_receipt');
            }

            if (Schema::hasColumn('payments', 'phone_number')) {
                $table->dropColumn('phone_number');
            }

            if (Schema::hasColumn('payments', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
        });
    }
};
