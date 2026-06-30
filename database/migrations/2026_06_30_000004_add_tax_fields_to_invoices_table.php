<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Which Kenyan rental tax regime this invoice's rent falls under.
            // RRI = Residential Rental Income (MRI), COMMERCIAL = standard/corporate rates,
            // NON_RESIDENT_WHT = final withholding tax on rent to non-resident landlords.
            $table->string('tax_type', 30)->nullable()->after('billing_period');

            // Income-tax rate applied for landlord remittance purposes (e.g. 0.075 = 7.5%).
            // This is NOT added to the tenant's amount due — it is the landlord's KRA obligation,
            // tracked here so MakaziHub can compute what the landlord nets after tax.
            $table->decimal('tax_rate', 5, 4)->nullable()->after('tax_type');

            // Computed income-tax amount payable to KRA on this invoice's gross rent.
            $table->decimal('tax_amount', 12, 2)->default(0)->after('tax_rate');

            // VAT charged on top of commercial rent (16%), only when the landlord is
            // VAT-registered. This DOES form part of the tenant's amount due.
            $table->decimal('vat_amount', 12, 2)->default(0)->after('tax_amount');

            // Net amount the landlord receives after income tax is deducted/remitted.
            $table->decimal('net_to_landlord', 12, 2)->nullable()->after('vat_amount');

            // Free-text compliance note shown on invoice/receipt (threshold, regime, etc).
            $table->string('tax_note', 255)->nullable()->after('net_to_landlord');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['tax_type', 'tax_rate', 'tax_amount', 'vat_amount', 'net_to_landlord', 'tax_note']);
        });
    }
};
