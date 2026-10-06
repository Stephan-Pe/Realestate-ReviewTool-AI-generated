Your instinct is spot on. Real estate valuation algorithms that rely solely on surface area ($m^2$) and basic regional factors miss two critical drivers of land/house value: **spatial micro-location** and **unrealized development potential (Zoning/FAR)**.

---

### Key Shortcomings in Your Current Logic

1. **`residence_factor = 0.80` for Erstwohnsitz (Primary Residence):**
In Swiss luxury and tourist municipalities (like Davos, St. Moritz, or Lenzerheide), the *Zweitwohnungsgesetz* (Lex Weber) restricts new second homes. This makes existing, legally non-restricted **Zweitwohnungen worth 15–20% more** due to scarcity. However, assigning a $0.80$ discount to Primary Residence lowers the base value artificially. It is clearer to treat **Erstwohnsitz as the baseline ($1.00$)** and apply a premium ($1.15$–$1.25$) for **Zweitwohnsitz**.
2. **Single `location_factor` (Macro vs. Micro):**
Davos (PLZ 7260) spans from prime locations near the Promenade/Parsennbahn to remote peripheral areas like Monstein or Sertig. A macro factor of $1.60$ for Davos is an accurate aggregate, but it requires a **micro-location modifier**.
3. **Ignoring Plot Area ($m^2$ Parzelle) and Utilization Rate (Ausnützungsziffer):**
For single-family homes (*Einfamilienhaus*), the plot of land carries significant inherent value—especially if the existing structure does not fully utilize the allowed building density (*Ausnützungsziffer / Geschossflächenzahl*).

---

### Recommended Expanded Model Architecture

To incorporate micro-location, plot size, and expansion/development potential cleanly, restructure your multi-factor equation into three distinct layers:

$$\text{Total Value} = (\text{Building Value}) + (\text{Excess Land / Development Potential Value})$$

#### Layer 1: Enhanced Location Factors

Split `location_factor` into Macro (Municipality/PLZ) and Micro (Neighborhood/Micro-Lage):

* **`macro_location_factor`** (PLZ level):
* Davos = $1.60$


* **`micro_location_factor`** (Lagequalität within municipality):
* *Top-Lage / Zentrum / Hanglage mit Panoramablick & Besonnung:* **$1.15$ – $1.25$**
* *Gute Wohnlage / Ruhig / Gute Erschliessung:* **$1.00$** (Baseline)
* *Peripherie / Schattig / Hauptstrasse / Immissionsbelastet:* **$0.85$ – $0.90$**



---

#### Layer 2: Revised Residence Factor (`residence_status`)

* **`erstwohnsitz`**: **$1.00$** (Standard primary home baseline)
* **`zweitwohnsitz_privilegiert`** (Status als Altrechtliche Zweitwohnung / Lex Weber pass): **$1.18$ – $1.25$**

---

#### Layer 3: Plot Proportion & Development Potential (*Grundstück & Ausnützungsreserven*)

For single-family homes, evaluate the land using a two-step calculation:

1. **Standard Land Component ($Land_{standard}$):**
A standard single-family house typically requires a land-to-living-area ratio (e.g., $1.5\times$ to $2.5\times$ the living area).

$$\text{Land Ratio} = \frac{\text{Grundstücksfläche } (m^2)}{\text{Wohnfläche } (m^2)}$$


2. **Development Reserve Factor (`development_potential_factor`):**
If the local zoning plan (*Bauzone / Ausnützungsziffer*) allows building significantly more living space on that plot, add a premium:
* *Ausnützung voll ausgeschöpft (No building reserve):* **$1.00$**
* *Moderate Reserve (+25% bis +50% anbau-/aufstockbar):* **$1.08$ – $1.12$**
* *Grosse Ausnützungsreserve / Verdichtungspotenzial (Zusatzbau möglich):* **$1.15$ – $1.25$**



---

### Refined Calculation Example (Davos)

Using your $150\text{ m}^2$ home in Davos with a **$600\text{ m}^2$ plot** (Ratio = $4.0\times$, indicating expansion potential) in a **preferred micro-location**:

#### Inputs:

* **Wohnfläche:** $150\text{ m}^2$
* **`base_price`:** $8,188\text{ CHF/m}^2$
* **`macro_location`:** $1.60$ (Davos)
* **`micro_location`:** $1.15$ (Bevorzugte Lage / Sonnig / Sicht)
* **`condition`:** $1.00$ (Gepflegt)
* **`equipment`:** $1.00$ (Standard)
* **`residence`:** $1.00$ (Erstwohnsitz)
* **`development_potential`:** $1.12$ (Grosses Grundstück $600\text{ m}^2$ mit Ausbaureserve)

#### Corrected Calculation:

$$\begin{aligned} \text{Effective Location Factor} &= 1.60 \times 1.15 = \mathbf{1.84} \\ \text{price\_per\_m}^2 &= 8,188 \times 1.84 \times 1.00 \times 1.00 \times 1.00 \times 1.12 = \mathbf{16,873.34\text{ CHF/m}^2} \\ \text{total\_value} &= 16,873.34 \times 150\text{ m}^2 = \mathbf{2,531,001.00\text{ CHF}} \end{aligned}$$

---

### Summary Checklist for Implementation

1. **Decouple Macro & Micro Location:** Keep PLZ trend factor as base, multiply by a user-selected Micro-Location rating (`top`, `good`, `standard`, `peripheral`).
2. **Fix `residence_factor` Baseline:** Set Erstwohnsitz to $1.00$ and apply a premium ($1.20$) for Zweitwohnung status in tourist zones.
3. **Add Land & Zoning Inputs for Houses (`Objektart == 'Einfamilienhaus'`):**
* `plot_area_m2` (*Grundstücksfläche*)
* `zoning_reserve` (*Ausnützungsreserve / Erweiterungspotenzial*: None, Minor, Major).