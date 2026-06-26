<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_service_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('category', [
                'laundry',
                'gas_delivery',
                'mama_fua',
                'shopping',
                'cleaning',
                'food_delivery',
                'handyman',
                'other',
            ]);
            $table->text('description');
            $table->string('logo')->nullable();
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->json('working_hours')->nullable(); // e.g. {"mon":"8am-6pm","sat":"9am-3pm"}
            $table->decimal('base_price', 10, 2)->nullable();
            $table->string('price_label')->nullable(); // e.g. "from KES 300/load"
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete(); // null = available to all properties
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_service_providers');
    }
};
