<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('restrict');
            $table->foreignId('tenant_id')->constrained()->onDelete('restrict');
            $table->enum('payment_method', ['mpesa', 'cash', 'bank_transfer', 'cheque']);
            $table->decimal('amount', 10, 2);
            $table->string('mpesa_transaction_id', 50)->nullable()->unique();
            $table->string('mpesa_phone', 20)->nullable();
            $table->string('mpesa_receipt_number', 50)->nullable();
            $table->string('reference', 100)->nullable();
            $table->date('payment_date');
            $table->enum('status', ['pending', 'confirmed', 'failed', 'reversed'])->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['invoice_id', 'status']);
            $table->index(['tenant_id', 'status']);
            $table->index('payment_date');
            $table->index('mpesa_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};