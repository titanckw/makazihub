<?php
// config/kenya_tax.php
//
// Kenyan rental income tax parameters, as they apply to property rent invoicing.
// Source: Income Tax Act Cap. 470 (Sec. 6A — Residential Rental Income / "Monthly
// Rental Income"), as amended by the Finance Act 2023 (rate cut 10% -> 7.5% from
// 1 Jan 2024), KRA public guidance, and the VAT Act 2013 (commercial rent VAT).
//
// IMPORTANT: A Finance Bill 2026 proposes raising the residential rate back to 10%
// and formally codifying the 30% non-resident withholding rate. These are NOT yet
// enacted as of this build. When the Finance Act 2026 is signed into law, update the
// values below — nothing else in the codebase needs to change.

return [

    // ── Residential Rental Income Tax (MRI) — Sec. 6A Income Tax Act ──────────
    // Flat final tax on GROSS residential rent received by a RESIDENT landlord.
    // No expenses/deductions allowed. Only applies within the annual gross-rent band.
    'residential' => [
        'rate' => 0.075, // 7.5% (Finance Act 2023; was 10% before 1 Jan 2024)
        'annual_threshold_min' => 288000,   // KES 288,000/yr (~24,000/mo) — below this, no MRI obligation
        'annual_threshold_max' => 15000000, // KES 15,000,000/yr — above this, standard annual income tax applies instead
    ],

    // ── Commercial Rental Income — taxed under standard regime, not MRI ───────
    // Commercial rent is NOT a flat-rate final tax. It is included in the landlord's
    // normal taxable income (individual graduated PAYE-equivalent bands, or the
    // corporate rate if the landlord is a company) and expenses ARE deductible.
    // Because this depends on the landlord's full annual financial position, it
    // cannot be computed accurately per-invoice — MakaziHub flags it for the
    // landlord's annual return instead of withholding a per-invoice amount.
    'commercial' => [
        'corporate_rate' => 0.30, // 30% corporate tax rate, for reference/reporting only
        'vat_rate' => 0.16,       // 16% VAT — charged ON TOP of rent, added to tenant's invoice,
        // only when the landlord is VAT-registered for that property
        // (mandatory once commercial rental turnover > KES 5,000,000/yr).
        'vat_registration_threshold' => 5000000,
    ],

    // ── Non-Resident Landlords — final withholding tax on rent ────────────────
    // The tenant/managing agent must withhold this from rent payable to a non-resident
    // landlord and remit it to KRA. It is final tax (no further filing by the landlord).
    // Applies to BOTH residential and commercial property owned by a non-resident.
    'non_resident' => [
        'immovable_property_rate' => 0.30, // 30% of gross rent (land/buildings)
        'movable_property_rate' => 0.15,   // 15% of gross rent (e.g. equipment leases) — not used for property rent
        // A Double Taxation Agreement (DTA) with the landlord's home country may cap
        // this rate lower. MakaziHub does not auto-apply DTA relief; it must be
        // manually elected once the landlord provides a KRA certificate of residence.
    ],

];
