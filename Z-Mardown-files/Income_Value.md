# Ertragswert Mehrfamilienhaus / Income value of a multi-family house



```php
/**
 * Berechnet den Ertragswert und den gewichteten Mischwert für ein MFH / Calculates the income value and the weighted average value for a multi-family house
 */
function calculateMfhValue(
    float $marktwertCalculated, // Bisher errechneter Markt-/Substanzwert / Current market/substance value
    float $zinsanteil,          // Zinsanteil der Miete / Interest rate of the rent
    float $kostenquote,           // Kostenquote der Miete / Cost quote of the rent

    float $bruttoMieteMonat,    // Bruttomiete pro Monat / total Monthly rent / Monthly rent
    float $kostenquoteJahr,        // Kostenquote der Miete pro Jahr / Annual cost quote of the rent

    float $unterhaltsQuote = 0.20,     // 20% für Unterhalt & Verwaltung / 20% for maintenance & management
    float $gewichtungKosten = 0.25,     // 25% Kosten-Gewichtung / 25% for costs

    float $kapitalisierungsSatz = 0.040, // 4.0% Cap-Rate / 4.0% Cap-Rate

    float $gewichtungErtrag = 0.75     // 75% Ertragswert-Gewichtung / 75% for earnings

): array {
    // 1. Jahresmiete & Nettomiete berechnen / 2. Anual rent & Calculate net rent
    $bruttoMieteMonat = $kostenquoteJahr / 12;

    $bruttoMieteJahr = $bruttoMieteMonat * 12;
    $nettoMieteJahr  = $bruttoMieteJahr * (1 - $unterhaltsQuote);

    // 2. Ertragswert ermitteln / Determine the income value
    $ertragswert = $nettoMieteJahr / $kapitalisierungsSatz;

    // 3. Gewichteten Mischwert berechnen / Calculate weighted average value
    $gewichtungMarktwert = 1.0 - $gewichtungErtrag;
    $mischwert = ($ertragswert * $gewichtungErtrag) + ($marktwertCalculated * $gewichtungMarktwert);

    // 4. Kennzahlen für Auswertung / Key figures for evaluation
    $bruttoRendite = ($bruttoMieteJahr / $marktwertCalculated) * 100;

    return [
        'brutto_miete_pa'    => round($bruttoMieteJahr, 2),
        'netto_miete_pa'     => round($nettoMieteJahr, 2),
        'ertragswert'        => round($ertragswert, 2),
        'mischwert_final'    => round($mischwert, 2),
        'brutto_rendite_pct' => round($bruttoRendite, 2)
    ];
}

// Beispielaufruf: Example call:
// Mehrfamilienhaus mit CHF 10'000 Monatsmiete und bisher geschätztem Marktwert von CHF 2.8 Mio. / Multi-family house with a monthly rent of CHF 10,000 and a previously estimated market value of CHF 2.8 million.
$result = calculateMfhValue(2800000, 10000, 0.20, 0.040, 0.75);
```
