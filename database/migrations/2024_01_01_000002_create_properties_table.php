<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manager_id')->constrained('users')->onDelete('restrict');
            $table->string('name');
            $table->text('address');
            $table->string('city', 100);
            $table->string('county', 100);
            $table->enum('property_type', ['apartment', 'maisonette', 'commercial', 'bedsitter', 'townhouse']);
            $table->integer('total_units')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('manager_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
