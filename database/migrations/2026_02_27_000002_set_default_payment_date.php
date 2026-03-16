<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ensure column has a default so raw inserts don't fail
        // Only run this raw ALTER on MySQL; SQLite doesn't support MODIFY
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE payments MODIFY payment_date DATE NOT NULL DEFAULT CURRENT_DATE"
            );
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE payments MODIFY payment_date DATE NOT NULL"
            );
        }
    }
};
