<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            // Residency status of the landlord/owner of this property — drives which
            // Kenyan rental income tax regime applies (KRA Income Tax Act, Sec. 6A & 35).
            $table->enum('landlord_tax_status', ['resident', 'non_resident'])
                ->default('resident')
                ->after('property_type');

            // Whether the landlord is VAT-registered for this property's commercial rent
            // (mandatory once commercial rental turnover exceeds KES 5,000,000/year — VAT Act).
            $table->boolean('is_vat_registered')->default(false)->after('landlord_tax_status');

            // Landlord's KRA PIN, used on tax-relevant documents/withholding certificates.
            $table->string('landlord_pin', 20)->nullable()->after('is_vat_registered');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['landlord_tax_status', 'is_vat_registered', 'landlord_pin']);
        });
    }
};
