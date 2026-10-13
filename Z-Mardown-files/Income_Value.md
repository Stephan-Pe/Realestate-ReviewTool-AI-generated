# Feature Specification: Income Value Calculation for Multi-Family Houses (Mehrfamilienhaus)

## 🎯 Task Goal
Implement income-value-based market valuation (Ertragswert-Methode) for the property type "Mehrfamilienhaus" (MFH). 

When `property_type === 'Mehrfamilienhaus'`, the calculation bypasses standard area/room inputs, collects unit count and monthly gross rent, computes the capitalization-based yield value (Ertragswert), and blends it into a final weighted market value (Mischwert).

---

## 🏗️ Architecture & Component Scope

### 1. Frontend Modifications (`index.html`)
* **Dynamic Form Fields:**
  * Add input `number_of_apartments` (Label: *Anzahl Wohnungen*): Integer only, min `1`, step `1`.
  * Add input `gross_rent_monthly` (Label: *Bruttomiete pro Monat*): Float/Currency, step `0.01` or `1`.
* **Visibility Rules (`property_type` toggling):**
  * **SHOW** `number_of_apartments` and `gross_rent_monthly` **ONLY IF** `property_type === 'Mehrfamilienhaus'`.
  * **HIDE** standard `area` (Nettowohnfläche) and `rooms` (Zimmer) fields **IF** `property_type === 'Mehrfamilienhaus'`.
* **Save / Submission Form:**
  * Include hidden inputs for `number_of_apartments` and `gross_rent_monthly` inside the save/submit form to persist valuation state.

---

### 2. Frontend Logic (`reviewTool.js`)
* **`updatePropertyTypeFields()`**:
  * Add conditionally toggled CSS classes (`d-none` / `hidden`) for `.mfh-only-field` and `.standard-property-field`.
  * Clear or reset values when switching between property types if necessary.
* **`performCalculation()`**:
  * Collect `number_of_apartments` and `gross_rent_monthly` from DOM inputs.
  * Append these parameters to the payload object sent via AJAX / Fetch to the backend valuation handler.
* **`displayResult()`**:
  * Populate hidden save-form fields with calculated/returned values for `number_of_apartments` and `gross_rent_monthly`.
  * Keep the primary evaluation output UI container unchanged across all property types.
* **`editValuation()`**:
  * When loading an existing valuation for editing, restore `number_of_apartments` and `gross_rent_monthly` into input fields and trigger `updatePropertyTypeFields()`.

---

### 3. Backend Calculation Logic (`PHP`)

Refactor and fix the MFH valuation calculation helper function:

```php
/**
 * Calculates the yield value (Ertragswert) and weighted mixed market value for a Multi-Family House (MFH).
 *
 * @param float $marktwertCalculated Preliminary calculated market/substance value (in CHF)
 * @param float $bruttoMieteMonat     Gross monthly rental income (in CHF)
 * @param float $unterhaltsQuote      Maintenance & management cost ratio (default: 0.20 = 20%)
 * @param float $kapitalisierungsSatz Capitalization rate / Cap rate (default: 0.040 = 4.0%)
 * @param float $gewichtungErtrag     Weighting factor for yield value vs substance value (default: 0.75 = 75%)
 * @return array Calculated financial metrics
 */
function calculateMfhValue(
    float $marktwertCalculated,
    float $bruttoMieteMonat,
    float $unterhaltsQuote = 0.20,
    float $kapitalisierungsSatz = 0.040,
    float $gewichtungErtrag = 0.75
): array {
    // 1. Calculate annual gross and net rental income
    $bruttoMieteJahr = $bruttoMieteMonat * 12;
    $nettoMieteJahr  = $bruttoMieteJahr * (1.0 - $unterhaltsQuote);

    // 2. Determine Income Value (Ertragswert = Net Annual Rent / Cap Rate)
    $ertragswert = ($kapitalisierungsSatz > 0) ? ($nettoMieteJahr / $kapitalisierungsSatz) : 0.0;

    // 3. Calculate Weighted Average Market Value (Mischwert)
    $gewichtungMarktwert = 1.0 - $gewichtungErtrag;
    $mischwert = ($ertragswert * $gewichtungErtrag) + ($marktwertCalculated * $gewichtungMarktwert);

    // 4. Calculate Key Indicators
    $bruttoRendite = ($marktwertCalculated > 0) ? (($bruttoMieteJahr / $marktwertCalculated) * 100) : 0.0;

    return [
        'brutto_miete_pa'    => round($bruttoMieteJahr, 2),
        'netto_miete_pa'     => round($nettoMieteJahr, 2),
        'ertragswert'        => round($ertragswert, 2),
        'mischwert_final'    => round($mischwert, 2),
        'brutto_rendite_pct' => round($bruttoRendite, 2)
    ];
}

// Example Execution:
// MFH with CHF 10,000 monthly rent and preliminary base market value of CHF 2,800,000
// $result = calculateMfhValue(2800000.00, 10000.00, 0.20, 0.040, 0.75);

```sql
-- Migration: Add fields for Multi-Family House (Mehrfamilienhaus) valuation data
ALTER TABLE `valuations` 
ADD COLUMN `number_of_apartments` INT UNSIGNED NULL DEFAULT NULL AFTER `rooms`,
ADD COLUMN `gross_rent_monthly` DECIMAL(10, 2) UNSIGNED NULL DEFAULT NULL AFTER `number_of_apartments`;
```
