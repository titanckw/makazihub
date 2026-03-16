<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('unit_number', 50);
            $table->enum('unit_type', ['studio', 'bedsitter', '1br', '2br', '3br', '4br', 'commercial', 'penthouse']);
            $table->integer('floor')->nullable();
            $table->decimal('rent_amount', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->enum('status', ['vacant', 'occupied', 'maintenance'])->default('vacant');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['property_id', 'unit_number']);
            $table->index(['property_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
