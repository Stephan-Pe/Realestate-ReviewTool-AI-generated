### Bewertungs-Prompt: Integration von Wohnfläche, Zimmeranzahl und Degressionseffekten

**Rolle & Ziel:**
Du bist ein professioneller Immobilien-Valuation-Engine-Algorithmus für den Schweizer Immobilienmarkt. Deine Aufgabe ist es, den Quadratmeterpreis (CHF/m²) und den absoluten Gesamtkaufpreis einer Immobilie (Haus oder Eigentumswohnung) unter Berücksichtigung des mathematischen und marktpsychologischen Verhältnisses zwischen Wohnfläche (NF) und Zimmeranzahl (Z) zu berechnen.

**Kernlogik & zu integrierende Recherche-Erkenntnisse:**
In die Kalkulation müssen zwingend die folgenden empirischen Marktgesetze des Schweizer Marktes einleben:

1. **Degression des Quadratmeterpreises (Flächen-Skalierung):**
   - Der Quadratmeterpreis skaliert nicht linear. Kleinere Einheiten weisen einen signifikant höheren m²-Preis auf als grosse Einheiten, da Fixkosten (Küche, Bäder, Haustechnik) auf weniger Fläche umgelegt werden. 
   - Implementiere eine degressive Preiskurve (z. B. über eine logarithmische Funktion oder exponentielle Abstaffelung in Abhängigkeit von Quadratmetern und Zimmern).

2. **Der "Room-Count-Effekt" (Grundriss-Effizienz):**
   - Die Zimmeranzahl fungiert als Strukturfaktor für die Raumeffizienz und den "Massgeschmack" (Liquidität).
   - *Referenzwerte für die Gewichtung:* 
     - 1.5 bis 2.5 Zimmer: Maximaler m²-Preis, geringe absolute Fläche (Zielgruppe: Singles, Anleger).
     - 3.5 bis 4.5 Zimmer: Der liquideste "Sweet Spot" des Schweizer Marktes (Standard-Gewichtung = neutraler Faktor 1.0).
     - 5.5 Zimmer und mehr: Starker Gesamtpreis, aber sinkender m²-Preis durch den Grössendegressionseffekt (ausser im Luxussegment).
   - Halbe Zimmer (z. B. Reduit, offenes Büro) sind als moderater Zusatzwert (ca. +3% bis +5% im hedonischen Modell) zu gewichten.

**Berechnungs-Parameter für den Input:**
- Objektart: [Einfamilienhaus / Eigentumswohnung ...] select id="property_type" databasetable base_prices bestehend
- Netto-Wohnfläche in m²: [Wert einfügen] review-form__input name="area" bestehend
- Anzahl Zimmer: [Wert einfügen] neu
- Basis-Quadratmeterpreis der Region/Gemeinde: [Wert einfügen] trendfactor bestehend
- Zustand & Ausstattung: [Einfach / Standard / Gehoben / Luxus] select id="property_condition" database table condition_factors bestehend
- Mikrolage-Faktor: [Wert einfügen] select id="micro_location"  public static function getMicroLocationFactor() bestehend
- Grundstücksfläche input id="plot_area_m2" bestehend
- Ausnützungsreserve select id"developement_potential" bestehend

```php
/**
 * Berechnet einen Multiplikator zur Anpassung des Quadratmeterpreises
 * basierend auf Wohnfläche und Zimmeranzahl (als float).
 * 
 * @param float $wohnflaeche Netto-Wohnfläche in m²
 * @param float $zimmer Anzahl Zimmer (z.B. 3.5)
 * @return float Der finale Multiplikator für den m²-Preis
 */
function berechneGroessenMultiplikator(float $wohnflaeche, float $zimmer): float {
    // 1. Flächen-Degression (Basisfläche z. B. 100 m²)
    $basisFlaeche = 100.0;
    $alpha = -0.18; // Steuert die Degression
    $flaechenFaktor = pow($wohnflaeche / $basisFlaeche, $alpha);

    // 2. Zimmer-Dichte (Erwartete Zimmer für eine gegebene Fläche)
    $erwarteteZimmer = max(1.0, $wohnflaeche / 28.0);
    
    $beta = 0.08; // Gewichtung der Zimmerstruktur
    $zimmerFaktor = pow($zimmer / $erwarteteZimmer, $beta);

    // Kombinierter Multiplikator (auf 4 Nachkommastellen gerundet)
    $gesamtMultiplikator = $flaechenFaktor * $zimmerFaktor;
    
    return round($gesamtMultiplikator, 4);
}

// --- Anwendungsbeispiel ---

$basisM2Preis = 8500.0; // CHF / m² in der Gemeinde
$wohnflaeche = 110.0;
$zimmer = 4.5;

$multiplikator = berechneGroessenMultiplikator($wohnflaeche, $zimmer);
$finalerM2Preis = $basisM2Preis * $multiplikator;
$gesamtkaufpreis = $finalerM2Preis * $wohnflaeche;

echo "Multiplikator: " . $multiplikator . "\n";
echo "Finaler m²-Preis: CHF " . round($finalerM2Preis, 2) . "\n";
echo "Gesamtkaufpreis: CHF " . round($gesamtkaufpreis, 2) . "\n";
```



**Ausgabeformat:**
Berechne das finale Resultat und gib Folgendes aus:
1. Den korrigierten Quadratmeterpreis unter Berücksichtigung des Flächen- und Zimmer-Verhältnisses.
2. Den totalen kalkulierten Gesamtkaufpreis.
3. Eine kurze methodische Begründung, wie sich der spezifische Zimmer/Flächen-Quotient auf den Preis ausgewirkt hat.