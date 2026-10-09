# Immobilien-Bewertungstool — Berechnungsanalyse

## Übersicht der Berechnungsformel

```
price_per_m² = base_price × location_factor × condition_factor × equipment_factor × residence_factor
total_value    = price_per_m² × area
```

---

## Schritt-für-Schritt Berechnungsfluss

### Schritt 1: Basispreis (getBasePrice)
**Datei:** `App/Models/Home.php` → `getBasePrice()`

Referenzpreis pro m² für eine Immobilie in einem "normalen" Gebiet und "standard" Zustand:

| Objektart | Basispreis (CHF/m²) |
|---|---|
| Einfamilienhaus | 8,188 |
| Mehrfamilienhaus | 6,772 |
| Wohnung | 9,026 |
| Reihenhaus | 7,400 |
| Doppelhaus | 7,900 |
| Grundstück | 1,000 |

### Schritt 2: Standortfaktor (location_factor) ⚠️ BUG
**Datei:** `App/Models/Home.php` → `calculateValuation()` (Zeile ~334)

```php
$locationFactor = is_array($location) && isset($location['factor']) ? (float) $location['factor'] : 1.00;
```

**PROBLEM:** Die Datenbanktabelle `locations` hat **KEINE** Spalte namens `factor`. Sie hat nur `trend_factor`.

- `isset($location['factor'])` ist **IMMER** `false`
- Daher ist `$locationFactor` **IMMER** `1.00`
- Der Standortfaktor wird **NIEMALS** aus der Datenbank gelesen

**BEHEBUNG:** Ändern Sie `$location['factor']` zu `$location['trend_factor']`

### Schritt 3: Zustandsfaktor (getConditionFactor)
**Datei:** `App/Models/Home.php` → `getConditionFactor()`

| Zustand | Faktor | Bedeutung |
|---|---|---|
| neuwertig | 1.20 | +20% Aufschlag (wie-neu) |
| renoviert | 1.10 | +10% Aufschlag (kürzlich renoviert) |
| gepflegt | 1.00 | **Baseline** (100% des Basiswerts) |
| sanierungsbedürftig | 0.85 | -15% Abschlag (grössere Reparaturen nötig) |
| renierungsbedürftig | 0.70 | -30% Abschlag (erhebliche Schäden) |

### Schritt 4: Ausstattungsfaktor (getEquipmentFactor)
**Datei:** `App/Models/Home.php` → `getEquipmentFactor()`

| Ausstattung | Faktor | Bedeutung |
|---|---|---|
| luxus | 1.30 | +30% Aufschlag (hochwertige Ausstattung) |
| gehoben | 1.15 | +15% Aufschlag (überdurchschnittlich) |
| standard | 1.00 | **Baseline** (100% des Basiswerts) |
| einfach | 0.85 | -15% Abschlag (einfache Ausstattung) |

### Schritt 5: Wohnsitzfaktor (getResidenceStatusFactor)
**Datei:** `App/Models/Home.php` → `getResidenceStatusFactor()`

Berücksichtigt schweizerische Wohnsitzgesetze (Wohnsitzpflicht nach 2012).

**Tabelle `residence_status_factors`:**

| status | trend_threshold | Faktor | Bedeutung |
|---|---|---|---|
| erstwohnsitz | 0.00 | 0.800 | -20% (Wohnsitzpflicht schränkt Käuferkreis ein) |
| feriendomizil | 1.00 | 1.000 | Normalgebiet (kein Aufschlag) |
| feriendomizil | 1.01 | 1.400 | +40% Tourismusgebiet (Aufschlag für Ferienimmobilien) |

**Logik:**
- **Erstwohnsitz:** Immer `trend_threshold = 0.00` → Faktor = **0.800**
- **Feriendomizil:** Abhängig vom `trend_factor` des Ortes:
  - `trend_factor > 1.0` → `threshold = 1.01` → Faktor = **1.400** (Teuer in Tourismusgebieten)
  - `trend_factor ≤ 1.0` → `threshold = 1.00` → Faktor = **1.000** (Normalpreis)

---

## Beispielrechnung

**Eingabe:**
- PLZ: 7260 (Davos)
- Objektart: Einfamilienhaus
- Wohnfläche: 150 m²
- Zustand: gepflegt
- Ausstattung: standard
- Wohnsitz: erstwohnsitz

**Berechnung:**

| Schritt | Wert | Quelle |
|---|---|---|
| 1. base_price | 8,188.00 CHF/m² | Einfamilienhaus |
| 2. location_factor | **1.00** ⚠️ (sollte 1.60 sein) | Davos = trend_factor 1.60 |
| 3. condition_factor | 1.00 | gepflegt |
| 4. equipment_factor | 1.00 | standard |
| 5. residence_factor | 0.80 | erstwohnsitz |
| **price_per_m²** | **6,550.40** | 8188 × 1.00 × 1.00 × 1.00 × 0.80 |
| **total_value** | **982,560.00 CHF** | 6,550.40 × 150m² |

**Korrekt (wenn location_factor = 1.60 wäre):**

| Schritt | Wert | Quelle |
|---|---|---|
| 1. base_price | 8,188.00 CHF/m² | Einfamilienhaus |
| 2. location_factor | **1.60** | Davos = trend_factor 1.60 |
| 3. condition_factor | 1.00 | gepflegt |
| 4. equipment_factor | 1.00 | standard |
| 5. residence_factor | 0.80 | erstwohnsitz |
| **price_per_m²** | **10,480.64** | 8188 × 1.60 × 1.00 × 1.00 × 0.80 |
| **total_value** | **1,572,096.00 CHF** | 10,480.64 × 150m² |

**Differenz: 589,536 CHF (~60% weniger Wert!)**

---

## ROOT CAUSE: Warum Standortfaktor immer 1.00 ist

### Das Problem

In `App/Models/Home.php` Zeile ~334:

```php
$locationFactor = is_array($location) && isset($location['factor']) ? (float) $location['factor'] : 1.00;
```

Die `locations` Tabelle hat **keine Spalte `factor`**:

```sql
CREATE TABLE `locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `plz` varchar(10) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(80) NOT NULL,
  `trend_factor` decimal(4,2) NOT NULL DEFAULT 1.00,  ← Dies ist die Spalte!
  `country` varchar(5) NOT NULL DEFAULT 'CH',
  ...
);
```

Da `$location['factor']` nie existiert, ist `isset($location['factor'])` immer `false`, und der Default-Wert `1.00` wird immer verwendet.

### Die Lösung

**Ändern Sie Zeile ~334 in `App/Models/Home.php`:**

```php
// FEHLERHAFT:
$locationFactor = is_array($location) && isset($location['factor']) ? (float) $location['factor'] : 1.00;

// KORREKT:
$locationFactor = is_array($location) && isset($location['trend_factor']) ? (float) $location['trend_factor'] : 1.00;
```

---

## Standort-Trend-Faktoren (aus Locations.md)

| trend_factor | Bedeutung | Beispiele |
|---|---|---|
| 1.9 | Städtische Gemeinde einer grossen Agglomeration | Chur (7000) |
| 1.6 | Städtische Gemeinde einer kleinen Agglomeration / Tourismusgebiet | Davos, Landquart, Zizers |
| 1.1 | Intermediäre Gemeinde / leicht begehrte Gebiete | San Vittore, Flims, Laax, Arosa, Klosters, Ilanz |
| 1.0 | Ländliche Gemeinde / Normalgebiet | Die meisten Orte |

---

## JavaScript Berechnungsanzeige

**Datei:** `public/js/modules/reviewTool.js` → `displayResult()`

Das JavaScript zeigt die vom Server berechneten Werte an:

```javascript
// Faktor-Anzeige mit Schweizer Formatierung (Komma als Dezimaltrenner)
resultLocFactor.textContent = data.location_factor.toFixed(3).replace('.', ',');
resultCondFactor.textContent = data.condition_factor.toFixed(3).replace('.', ',');
resultEquipFactor.textContent = data.equipment_factor.toFixed(3).replace('.', ',');
resultResidenceFactor.textContent = (data.residence_status_factor || 1.0).toFixed(3).replace('.', ',');
```

---

## Zusammenfassung

| Faktor | Funktion | Default | Problem |
|---|---|---|---|
| base_price | getBasePrice() | 5000 | Keines |
| **location_factor** | DB locations.trend_factor | **1.00** | **BUG: Liest 'factor' statt 'trend_factor'** |
| condition_factor | getConditionFactor() | 1.00 | Keines |
| equipment_factor | getEquipmentFactor() | 1.00 | Keines |
| residence_factor | getResidenceStatusFactor() | 0.8/1.0 | Keines |

**Der Standortfaktor ist immer 1.00 weil die Spalte `factor` in der `locations` Tabelle nicht existiert. Die korrekte Spalte heisst `trend_factor`.**

Beim Vergleich der Quadratmeter- und Objektpreise für Wohneigentum (Einfamilienhäuser und Eigentumswohnungen) in der Schweiz lassen sich die relativen Preisunterschiede anhand der Typologie des Bundesamtes für Statistik (BFS) und den Marktdaten führender Immobilienanalysten (Wüest Partner, IAZI, Raiffeisen) wie folgt zusammenfassen:

### Relative Preisunterschiede nach Gemeindetypologie

*(Ländliche Gemeinden dienen hierbei als Basiswert = 100 %)*

* **Ländliche Gemeinden (Basis = 100 %):**
* **Preisniveau:** Das günstigste Segment für Wohneigentum.
* **Prozentualer Abstand:** Referenzwert (0 % Aufpreis).


* **Intermediäre Gemeinden (Agglomerationsgürtel & periurban):**
* **Preisniveau:** Mittleres Preissegment mit hoher Nachfrage durch Familien.
* **Prozentualer Abstand:** **+25 % bis +45 %** gegenüber ländlichen Gemeinden.


* **Städtische Gemeinden (Zentren & Kernstädte):**
* **Preisniveau:** Sehr hohes Preisniveau aufgrund von Landknappheit und hoher Dichte.
* **Prozentualer Abstand:** **+60 % bis +110 %** gegenüber ländlichen Gemeinden (in Grosszentren wie Zürich oder Genf teils noch höher).


* **Tourismusgemeinden (Hotspots & Bergdestinationen):**
* **Preisniveau:** Stark durch Zweitwohnungsnachfrage und internationales Publikum getrieben.
* **Prozentualer Abstand:** **+50 % bis +120 %** gegenüber ländlichen Gemeinden (Top-Destinationen wie St. Moritz, Zermatt oder Gstaad liegen oft auf oder über dem Niveau städtischer Grosszentren).



---

### Übersicht: Durchschnittliche Richtpreise & Abweichungen

| Gemeindetyp | Relativer Preisabstand | Richtwert $/m^2$ (EWG)* |
| --- | --- | --- |
| **Ländliche Gemeinden** | **Basis (100 %)** | CHF 5'500 – CHF 7'500 |
| **Intermediäre Gemeinden** | **+25 % bis +45 %** | CHF 7'500 – CHF 10'000 |
| **Städtische Gemeinden** | **+60 % bis +110 %** | CHF 10'500 – CHF 15'000+ |
| **Tourismusgemeinden** | **+50 % bis +120 %+** | CHF 9'500 – CHF 18'000+ |

**Hinweis: Richtwerte für Eigentumswohnungen (EWG) im mittleren Segment.*

---

### Wesentliche Treiber der Preisunterschiede

1. **Städtische Gemeinden:** Hohe Erwerbsdichte, beste ÖV-Anbindung, erstklassiges Infrastrukturangebot und streng begrenzte Baulandreserven treiben die Quadratmeterpreise massiv in die Höhe.
2. **Tourismusgemeinden:** Hoher Druck durch Zweitwohnungsinitiative und Auslandsnachfrage. Das Angebot ist stark limitiert, was zu überdurchschnittlichen Spitzenpreisen führt.
3. **Intermediäre Gemeinden:** Profitieren vom Ausweichdruck aus den Städten ("Pendlergürtel"). Sie bieten Kompromisse aus Erreichbarkeit und tragbaren Quadratmeterpreisen.
4. **Ländliche Gemeinden:** Grössere Grundstücksflächen zum vergleichsweise niedrigsten Quadratmeterpreis, jedoch eingeschränktere Erschliessung und längere Pendelzeiten.


In der Schweizer Immobilienbewertung (insbesondere bei den marktbeherrschenden **hedonischen Modellen** von Anbietern wie Wüest Partner, IAZI oder Fahrländer Partner) wird der Quadratmeterpreis bzw. Gesamtwert nicht durch eine starre Formel mit festen Prozent-Gewichten für Jedermann berechnet. Stattdessen nutzen die Algorithmen **multivariate Regressionsanalysen**, die anhand von zehntausenden realen Handänderungen (Kaufverkäufen) laufend kalibriert werden.

Die Gewichtung der einzelnen Faktoren variiert je nach Objektart (Einfamilienhaus vs. Eigentumswohnung) und Region. Dennoch lässt sich die mathematische Struktur und die relative Gewichtung der von Ihnen genannten Faktoren aufschlüsseln.

---

### 1. Mathematische Grundstruktur (Hedonischer Ansatz)

Hedonische Modelle basieren meist auf einem **logarithmischen oder semi-logarithmischen Ansatz**, weil Preise nicht linear mit der Fläche wachsen (eine doppelt so grosse Wohnung kostet selten exakt das Doppelte pro m²).

Vereinfacht dargestellt sieht die Regressionsgleichung so aus:

$$\ln(\text{Preis}) = \beta_0 + \sum (\beta_i \cdot \text{Faktor}_i) + \varepsilon$$

Oder als marktübliches Multiplikatoren-Modell für den Quadratmeterpreis ($m^2$-Preis):

$$\text{Preis per m}^2 = \text{Basispreis}_\text{Region} \times f(\text{Makro}) \times f(\text{Mikro}) \times f(\text{Fläche}) \times f(\text{Zimmer}) \times f(\text{Zustand}) \times f(\text{Ausstattung}) \times f(\text{Wohnsitz})$$

---

### 2. Relative Gewichtung der Faktoren (Ranking nach Einfluss)

In der Praxis gewichten die Schweizer Algorithmen die Preis Treiber in etwa wie folgt (absteigend nach ihrer statistischen Varianzaufklärung):

| Rang | Faktor | Geschätzte relative Gewichtung | Einfluss & Wirkungsweise |
| --- | --- | --- | --- |
| **1** | **Mikrolage** | **ca. 30 – 40 %** | Der absolute Hauptpreistreiber (Besonnung, Aussicht, Lärmimmissionen, Steuerfuss der Gemeinde, ÖV-Güteklasse). |
| **2** | **Trendfaktor (Makrolage)** | **ca. 15 – 20 %** | Regionale Markt- und Wirtschaftsregion (z.B. Wirtschaftsraum Zürich/Genfersee vs. ländliches Berggebiet), Dynamik von Angebot und Nachfrage. |
| **3** | **Wohnfläche & Zimmeranzahl** | **ca. 15 – 20 %** | Die Grösse skaliert den Gesamtpreis massiv. Die Zimmeranzahl wirkt als Strukturfaktor (Aufteilung der m²). |
| **4** | **Ausstattung & Zustand** | **ca. 10 – 15 %** | Materialisierung (einfach, gehoben, Luxus), Sanierungsstand, Alter der Küche/Bäder, MINERGIE-Standard etc. |
| **5** | **Objektart & Wohnsitzart** | **ca. 5 – 10 %** | Unterschied EFH vs. ETW; bei Zweitwohnungen greifen oft regulatorische Effekte (Zweitwohnungsgesetz im Berggebiet, Lex Koller). |

---

### 3. Detailanalyse der einzelnen Faktoren und ihrer Funktionsweise

#### A. Makrolage & Trendfaktor ($f_\text{Makro}$)

* **Wirkung:** Bezieht sich auf die Gemeinde und Region. Die Steuerbelastung der Gemeinde fliesst hier direkt als monetärer Barwert in die Bewertung ein (tiefe Steuern = höhere Zahlungsbereitschaft der Käufer = höherer m²-Preis).
* **Trend:** Bildet die historische und aktuelle Preisentwicklung der Region ab.

#### B. Mikrolage ($f_\text{Mikro}$)

* **Wirkung:** Parzellenspezifisch. Eine Liegenschaft in derselben Gemeinde kann allein durch unverbaubare Seesicht oder absolute Ruhelage (keine Lärmbelastung durch Strasse/Bahn) einen Aufschlag von **20 % bis über 50 %** gegenüber einer Nachbarliegenschaft an einer Hauptstrasse erzielen.

#### C. Wohnfläche & Anzahl Zimmer ($f_\text{Fläche}, f_\text{Zimmer}$)

* **Fläche:** Wirkt degressiv. Verdoppelt sich die Wohnfläche, steigt der Gesamtpreis meist nur um Faktor 1.6 bis 1.8, da Fixkosten (Küche, Bäder, Erschliessung) prozentual sinken.
* **Zimmeranzahl:** Korreliert stark mit der Fläche, steuert aber die **Grundriss-Effizienz**. Bei gleicher Wohnfläche von 120 m² erzielt eine gut geschnittene 4.5-Zimmer-Wohnung oft einen anderen m²-Preis als eine loftartige 2.5-Zimmer-Wohnung. Zusätzliche halbe Zimmer (Reduit, Büro) geben einen moderaten Zusatzwert (ca. 3–5 % Aufschlag im hedonischen Modell).

#### D. Zustand & Ausstattung ($f_\text{Zustand}, f_\text{Ausstattung}$)

* **Skalierung:**
* *Einfach/Renovierungsbedürftig:* Abschlag von oft 15–25 % gegenüber dem Standard (Notwendigkeit von Investitionen).
* *Gehoben (Standard heute):* Basis des Modells (0 % Korrektur).
* *Luxus:* Individuelle Aufschläge (20 % bis 50%+), wobei hedonische Modelle im absoluten Luxussegment oft an ihre Grenzen stoßen, da dort der "Liebhaberwert" dominiert.



#### E. Wohnsitzart (Erst- vs. Zweitwohnung)

* **Wirkung:** In der Schweiz (speziell in Tourismuskantonen wie Graubünden, Wallis, Bern/Oberland) hat die Zweitwohnungseigenschaft ("Ferienimmobilie") eine starke steuernde Wirkung. Seit dem Zweitwohnungsgesetz (ZWG) sind Zweitwohnungen in vielen Gemeinden kontingentiert. Das treibt den Wert von bestehenden Zweitwohnungen (Bestandsschutz) massiv nach oben, während reine Erstwohnungs-Pflichtobjekte für einheimische Käufer preislich anders abgestützt sind.

---

### Zusammenfassung für die Praxis

Wenn Sie den Quadratmeterpreis einer Schweizer Immobilie modellieren möchten, starten Sie immer mit dem **Median-Quadratmeterpreis der Gemeinde (Makro/Mikro-Basis)** und multiplizieren diesen sequenziell mit den prozentualen Zu- oder Abschlägen der Objektdetails (Grösse, Zimmerstruktur, Baujahr, Zustand und Sonderstatus wie Zweitwohnung).

