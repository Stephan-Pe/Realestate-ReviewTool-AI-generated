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
