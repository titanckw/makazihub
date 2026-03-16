<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('lease_id')->constrained()->onDelete('restrict');
            $table->foreignId('tenant_id')->constrained()->onDelete('restrict');
            $table->foreignId('unit_id')->constrained()->onDelete('restrict');
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_due', 10, 2);
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            $table->date('due_date');
            $table->date('period_start');
            $table->date('period_end');
            $table->date('invoice_date');
            $table->string('billing_period', 20)->comment('Format: YYYY-MM e.g. 2025-01');
            $table->enum('status', ['unpaid', 'partial', 'paid', 'overdue'])->default('unpaid');
            $table->enum('generated_by', ['manual', 'auto'])->default('manual');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['property_id', 'status']);
            $table->index(['tenant_id', 'status']);
            $table->index(['lease_id', 'billing_period']);
            $table->index('status');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
