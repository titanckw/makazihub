<?php
// app/Services/KenyaRentalTaxService.php

namespace App\Services;

use App\Models\Property;

class KenyaRentalTaxService
{
    public const TYPE_RESIDENTIAL   = 'RRI';
    public const TYPE_COMMERCIAL    = 'COMMERCIAL';
    public const TYPE_NON_RESIDENT  = 'NON_RESIDENT_WHT';

    /**
     * Compute the Kenyan tax breakdown for a single invoice's gross rent.
     *
     * Returns:
     *  - tax_type:        RRI | COMMERCIAL | NON_RESIDENT_WHT
     *  - tax_rate:         decimal rate used (e.g. 0.075), null if not applicable
     *  - tax_amount:       income tax payable to KRA on this rent (landlord's obligation)
     *  - vat_amount:       VAT added on top of rent (tenant pays this in addition to rent)
     *  - net_to_landlord:  what the landlord receives after income tax + (for non-residents)
     *                      withholding is deducted
     *  - tax_note:         human-readable compliance note for the invoice/receipt
     */
    public function calculate(Property $property, float $grossRent): array
    {
        $cfg = config('kenya_tax');
        $isNonResident = $property->landlord_tax_status === 'non_resident';
        $isCommercial  = $property->property_type === 'commercial';

        // ── Non-resident landlord: final withholding tax overrides everything ──
        if ($isNonResident) {
            $rate = $cfg['non_resident']['immovable_property_rate'];
            $tax  = round($grossRent * $rate, 2);

            return [
                'tax_type' => self::TYPE_NON_RESIDENT,
                'tax_rate' => $rate,
                'tax_amount' => $tax,
                'vat_amount' => $isCommercial ? $this->commercialVat($property, $grossRent) : 0,
                'net_to_landlord' => round($grossRent - $tax, 2),
                'tax_note' => sprintf(
                    'Non-resident landlord: %s%% final withholding tax (KES %s) must be deducted '
                        . 'from rent and remitted to KRA by the tenant/agent. Lower rate may apply under a DTA.',
                    rtrim(rtrim(number_format($rate * 100, 2), '0'), '.'),
                    number_format($tax, 2)
                ),
            ];
        }

        // ── Commercial property, resident landlord ──────────────────────────
        if ($isCommercial) {
            $vat = $this->commercialVat($property, $grossRent);

            return [
                'tax_type' => self::TYPE_COMMERCIAL,
                'tax_rate' => null, // taxed under graduated/corporate rates on annual NET profit, not per-invoice
                'tax_amount' => 0,
                'vat_amount' => $vat,
                'net_to_landlord' => round($grossRent - 0, 2), // income tax not withheld per-invoice
                'tax_note' => $vat > 0
                    ? sprintf(
                        'Commercial rent: %s%% VAT (KES %s) added to invoice. Income tax is assessed '
                            . 'annually on net profit at individual/corporate rates — not a flat per-invoice charge.',
                        number_format($cfg['commercial']['vat_rate'] * 100, 0),
                        number_format($vat, 2)
                    )
                    : 'Commercial rent: income tax assessed annually on net profit at individual/corporate '
                        . 'rates (landlord not VAT-registered for this property, so no VAT added).',
            ];
        }

        // ── Residential property, resident landlord: Monthly Rental Income (MRI) ──
        $annualRent = $grossRent * 12;
        $min = $cfg['residential']['annual_threshold_min'];
        $max = $cfg['residential']['annual_threshold_max'];

        if ($annualRent < $min) {
            return [
                'tax_type' => self::TYPE_RESIDENTIAL,
                'tax_rate' => null,
                'tax_amount' => 0,
                'vat_amount' => 0,
                'net_to_landlord' => $grossRent,
                'tax_note' => 'Below the KES 288,000/yr MRI threshold — no Residential Rental Income tax obligation.',
            ];
        }

        if ($annualRent > $max) {
            return [
                'tax_type' => self::TYPE_RESIDENTIAL,
                'tax_rate' => null,
                'tax_amount' => 0,
                'vat_amount' => 0,
                'net_to_landlord' => $grossRent,
                'tax_note' => 'Above the KES 15,000,000/yr MRI ceiling — income taxed under the standard '
                    . 'annual return with deductions, not flat MRI.',
            ];
        }

        $rate = $cfg['residential']['rate'];
        $tax  = round($grossRent * $rate, 2);

        return [
            'tax_type' => self::TYPE_RESIDENTIAL,
            'tax_rate' => $rate,
            'tax_amount' => $tax,
            'vat_amount' => 0, // residential rent is VAT-exempt
            'net_to_landlord' => round($grossRent - $tax, 2),
            'tax_note' => sprintf(
                'Residential Rental Income Tax (MRI): %s%% of gross rent (KES %s) is the landlord\'s final '
                    . 'tax, payable to KRA by the 20th of the following month. Not added to tenant rent due.',
                rtrim(rtrim(number_format($rate * 100, 2), '0'), '.'),
                number_format($tax, 2)
            ),
        ];
    }

    private function commercialVat(Property $property, float $grossRent): float
    {
        if (!$property->is_vat_registered) {
            return 0;
        }

        return round($grossRent * config('kenya_tax.commercial.vat_rate'), 2);
    }
}
