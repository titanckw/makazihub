<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('manager_id')->constrained('users')->onDelete('cascade');
            $table->string('role');          // e.g. caretaker, cleaner, security, accountant
            $table->string('id_number', 20)->nullable();
            $table->string('department')->nullable();
            $table->string('employment_type')->default('full_time'); // full_time, part_time, contract
            $table->date('start_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();

            $table->index('manager_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
