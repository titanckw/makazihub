<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'invoice_generated',
                'payment_confirmed',
                'payment_received',
                'overdue_reminder',
                'lease_expiry',
                'welcome',
                'receipt_sent',
                'custom'
            ]);
            $table->enum('channel', ['email', 'sms']);
            $table->string('recipient');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->text('provider_response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['channel', 'status']);
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications_log');
    }
};
