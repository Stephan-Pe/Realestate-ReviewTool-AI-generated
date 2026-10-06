<?php

namespace App\Models;

use PDO;
use Core\View;

/**
 * Home Model
 * Handles valuation and location database operations.
 *
 * PHP version 8.2.12
 */
class Home extends \Core\Model
{
    // ---- Valuation properties ----
    public ?int $id = null;
    public ?string $plz = null;
    public ?string $location_name = null;
    public ?string $country = null;
    public ?string $property_type = null;
    public ?string $residence_status = null;
    public ?float $residence_status_factor = null;
    public ?string $threshold = null;
    public ?string $csrf_token = null;

    /**
     * Static cache for residence status factors
     * @var array
     */
    private static array $residenceFactorCache = [];
    public ?float $area = null;
    public ?float $plot_area_m2 = null;
    public ?string $property_condition = null;
    public ?string $equipment = null;
    public ?string $micro_location = null;
    public ?string $development_potential = null;
    public ?float $price_per_sqm = null;
    public ?float $total_value = null;
    public ?float $location_factor = null;
    public ?float $micro_location_factor = null;
    public ?float $condition_factor = null;
    public ?float $equipment_factor = null;
    public ?float $development_potential_factor = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?float $building_value = null;
    public ?float $development_value = null;
    public ?float $effective_location_factor = null;
    public ?float $macro_location_factor = null;
    public ?float $trend_factor = null;
    public ?float $trend_factor_2019 = null;
    public ?float $trend_factor_2020 = null;
    /**
     * Validation error messages
     * @var array
     */
    public $errors = [];

    /**
     * Base path for file operations
     */
    protected const BASE_PATH = __DIR__ . '/../..';

    /**
     * Constructor — accepts an array of initial values
     *
     * @param array $data Initial property values
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    // ====================================================================
    //  VALUATION METHODS
    // ====================================================================

    /**
     * Get all valuations with optional search
     *
     * @param string $search Optional search term (plz, location, property_type)
     * @return array
     */
    public static function getAll(string $search = ''): array
    {
        $db = static::getDB();

        if ($search !== '') {
            $sql = 'SELECT * FROM valuations
                    WHERE plz LIKE :search
                       OR location_name LIKE :search2
                       OR property_type LIKE :search3
                    ORDER BY created_at DESC';
            $like = '%' . $search . '%';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':search', $like, PDO::PARAM_STR);
            $stmt->bindValue(':search2', $like, PDO::PARAM_STR);
            $stmt->bindValue(':search3', $like, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $stmt = $db->query('SELECT * FROM valuations ORDER BY created_at DESC');
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a valuation by ID
     *
     * @param int|string $id
     * @return array|false
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM valuations WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: false;
    }

    /**
     * Save a new valuation
     *
     * @return int|false Last insert ID or false on failure
     */
    public function save()
    {
        $this->validate();
        if (!empty($this->errors)) {
            return false;
        }

        $sql = 'INSERT INTO valuations
                (plz, location_name, country, property_type, area, plot_area_m2, property_condition,
                 equipment, micro_location, development_potential, residence_status,
                 price_per_sqm, total_value, location_factor, micro_location_factor,
                 condition_factor, equipment_factor, residence_status_factor,
                 development_potential_factor, created_at, updated_at)
                VALUES
                (:plz, :location_name, :country, :property_type, :area, :plot_area_m2, :property_condition,
                 :equipment, :micro_location, :development_potential, :residence_status,
                 :price_per_sqm, :total_value, :location_factor, :micro_location_factor,
                 :condition_factor, :equipment_factor, :residence_status_factor,
                 :development_potential_factor, NOW(), NOW())';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':plz', $this->plz, PDO::PARAM_STR);
        $stmt->bindValue(':location_name', $this->location_name, PDO::PARAM_STR);
        $stmt->bindValue(':country', $this->country, PDO::PARAM_STR);
        $stmt->bindValue(':property_type', $this->property_type, PDO::PARAM_STR);
        $stmt->bindValue(':area', $this->area, PDO::PARAM_STR);
        $stmt->bindValue(':plot_area_m2', $this->plot_area_m2, PDO::PARAM_STR);
        $stmt->bindValue(':property_condition', $this->property_condition, PDO::PARAM_STR);
        $stmt->bindValue(':equipment', $this->equipment, PDO::PARAM_STR);
        $stmt->bindValue(':micro_location', $this->micro_location, PDO::PARAM_STR);
        $stmt->bindValue(':development_potential', $this->development_potential, PDO::PARAM_STR);
        $stmt->bindValue(':residence_status', $this->residence_status ?? 'erstwohnsitz', PDO::PARAM_STR);
        $stmt->bindValue(':price_per_sqm', $this->price_per_sqm, PDO::PARAM_STR);
        $stmt->bindValue(':total_value', $this->total_value, PDO::PARAM_STR);
        $stmt->bindValue(':location_factor', $this->location_factor, PDO::PARAM_STR);
        $stmt->bindValue(':micro_location_factor', $this->micro_location_factor, PDO::PARAM_STR);
        $stmt->bindValue(':condition_factor', $this->condition_factor, PDO::PARAM_STR);
        $stmt->bindValue(':equipment_factor', $this->equipment_factor, PDO::PARAM_STR);
        $stmt->bindValue(':residence_status_factor', $this->residence_status_factor ?? 1.0, PDO::PARAM_STR);
        $stmt->bindValue(':development_potential_factor', $this->development_potential_factor ?? 1.0, PDO::PARAM_STR);

        $stmt->execute();
        return (int) $db->lastInsertId();
    }

    /**
     * Update an existing valuation
     *
     * @return bool
     */
    public function update()
    {
        $this->validate();
        if (!empty($this->errors)) {
            return false;
        }

        $sql = 'UPDATE valuations
                SET plz = :plz,
                    location_name = :location_name,
                    country = :country,
                    property_type = :property_type,
                    area = :area,
                    plot_area_m2 = :plot_area_m2,
                    property_condition = :property_condition,
                    equipment = :equipment,
                    micro_location = :micro_location,
                    development_potential = :development_potential,
                    residence_status = :residence_status,
                    price_per_sqm = :price_per_sqm,
                    total_value = :total_value,
                    location_factor = :location_factor,
                    micro_location_factor = :micro_location_factor,
                    condition_factor = :condition_factor,
                    equipment_factor = :equipment_factor,
                    residence_status_factor = :residence_status_factor,
                    development_potential_factor = :development_potential_factor,
                    updated_at = NOW()
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':plz', $this->plz, PDO::PARAM_STR);
        $stmt->bindValue(':location_name', $this->location_name, PDO::PARAM_STR);
        $stmt->bindValue(':country', $this->country, PDO::PARAM_STR);
        $stmt->bindValue(':property_type', $this->property_type, PDO::PARAM_STR);
        $stmt->bindValue(':area', $this->area, PDO::PARAM_STR);
        $stmt->bindValue(':plot_area_m2', $this->plot_area_m2, PDO::PARAM_STR);
        $stmt->bindValue(':property_condition', $this->property_condition, PDO::PARAM_STR);
        $stmt->bindValue(':equipment', $this->equipment, PDO::PARAM_STR);
        $stmt->bindValue(':micro_location', $this->micro_location, PDO::PARAM_STR);
        $stmt->bindValue(':development_potential', $this->development_potential, PDO::PARAM_STR);
        $stmt->bindValue(':residence_status', $this->residence_status ?? 'erstwohnsitz', PDO::PARAM_STR);
        $stmt->bindValue(':price_per_sqm', $this->price_per_sqm, PDO::PARAM_STR);
        $stmt->bindValue(':total_value', $this->total_value, PDO::PARAM_STR);
        $stmt->bindValue(':location_factor', $this->location_factor, PDO::PARAM_STR);
        $stmt->bindValue(':micro_location_factor', $this->micro_location_factor, PDO::PARAM_STR);
        $stmt->bindValue(':condition_factor', $this->condition_factor, PDO::PARAM_STR);
        $stmt->bindValue(':equipment_factor', $this->equipment_factor, PDO::PARAM_STR);
        $stmt->bindValue(':residence_status_factor', $this->residence_status_factor ?? 1.0, PDO::PARAM_STR);
        $stmt->bindValue(':development_potential_factor', $this->development_potential_factor ?? 1.0, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Delete a valuation by ID
     *
     * @param int $id
     * @return bool
     */
    public static function deleteByID(int $id): bool
    {
        $sql = 'DELETE FROM valuations WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Validate valuation data
     */
    public function validate(): void
    {
        if (empty($this->plz)) {
            $this->errors[] = 'PLZ ist erforderlich.';
        }
        if (empty($this->property_type)) {
            $this->errors[] = 'Objektart ist erforderlich.';
        }
        if (empty($this->area) || $this->area <= 0) {
            $this->errors[] = 'Wohnfläche muss grösser als 0 sein.';
        }
        if (empty($this->property_condition)) {
            $this->errors[] = 'Zustand ist erforderlich.';
        }
        if (empty($this->equipment)) {
            $this->errors[] = 'Ausstattung ist erforderlich.';
        }
    }

    // ====================================================================
    //  LOCATION / PLZ METHODS
    // ====================================================================

    /**
     * Search locations by PLZ prefix (autocomplete)
     *
     * @param string $query PLZ prefix
     * @return array
     */
    public static function searchLocations(string $query): array
    {
        if ($query === '') {
            return [];
        }

        $db = static::getDB();
        $sql = 'SELECT plz, city AS name, country
                FROM locations
                WHERE plz LIKE :q
                ORDER BY plz ASC
                LIMIT 15';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':q', $query . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get location data by PLZ
     *
     * @param string $plz
     * @return array|false
     */
    public static function getLocationByPLZ(string $plz)
    {
        $sql = 'SELECT * FROM locations WHERE plz = :plz LIMIT 1';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':plz', $plz, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: false;
    }

    // ====================================================================
    //  VALUATION CALCULATION (static helpers)
    // ====================================================================

    /**
     * Get base price per m² by property type (Switzerland / Graubünden)
     *
     * Step 1: Determine the BASE PRICE per m² based on property type.
     *         These are reference values for a 'standard' property in a 'normal' location.
     *         The base price is the starting point before any adjustments.
     *
     * @param string $propertyType
     * @return float
     */
    public static function getBasePrice(string $propertyType): float
    {
        // Base price reference values for a standard property in a neutral location
        // These values represent the starting point before any multipliers are applied
        $basePrices = [
            'Einfamilienhaus'  => 8188.00,  // Single-family home: higher base due to land value
            'Mehrfamilienhaus' => 6772.00,  // Multi-family: lower per-unit due to economies of scale
            'Wohnung'          => 9026.00,  // Apartment: premium per m² (no land cost spread)
            'Reihenhaus'       => 7400.00,  // Townhouse: mid-range
            'Doppelhaus'       => 7900.00,  // Semi-detached: near single-family pricing
            'Grundstück'       => 1000.00,  // Land: much lower, priced per m² raw
        ];
        // If an unknown property type is passed, fall back to 5000 CHF/m² as a generic default
        return $basePrices[$propertyType] ?? 5000.00;
    }

    /**
     * Get condition factor (Step 3: Adjust price based on property condition)
     *
     * The condition factor is a MULTIPLIER applied to the base price.
     * It reflects how the physical state of the building affects its market value.
     *
     * Formula contribution: price_per_m² = base_price × location_factor × condition_factor × equipment_factor × residence_factor
     *
     * @param string $property_condition
     * @return float
     */
    public static function getConditionFactor(string $property_condition): float
    {
        // Condition factors: each represents a percentage adjustment from the standard (1.00)
        // 'gepflegt' (well-maintained) is the baseline = 1.00 (no adjustment)
        $factors = [
            'neuwertig'            => 1.20,   // New: +20% premium (like-new condition, minimal wear)
            'renoviert'            => 1.10,   // Renovated: +10% premium (recently updated)
            'gepflegt'             => 1.00,   // Well-maintained: baseline (100% of base value)
            'sanierungsbedürftig'  => 0.85,   // Needs renovation: -15% discount (major repairs needed)
            'renierungsbedürftig'  => 0.70,   // Needs major repair: -30% discount (significant deterioration)
        ];
        // Normalize input to lowercase for case-insensitive matching
        // Default to 1.00 (standard) if condition is unknown
        return $factors[strtolower($property_condition)] ?? 1.00;
    }

    /**
     * Get equipment factor (Step 4: Adjust price based on equipment/amenities level)
     *
     * The equipment factor is a MULTIPLIER applied to the base price.
     * It reflects how the quality of interior finishes and amenities affects value.
     *
     * Formula contribution: price_per_m² = base_price × location_factor × condition_factor × equipment_factor × residence_factor
     *
     * @param string $equipment
     * @return float
     */
    public static function getEquipmentFactor(string $equipment): float
    {
        // Equipment factors: each represents a percentage adjustment from the standard (1.00)
        // 'standard' is the baseline = 1.00 (no adjustment)
        $factors = [
            'luxus'    => 1.30,   // Luxury: +30% premium (high-end finishes, premium appliances)
            'gehoben'  => 1.15,   // Premium: +15% (above-standard finishes)
            'standard' => 1.00,   // Standard: baseline (100% of base value)
            'einfach'  => 0.85,   // Basic: -15% (simple, functional finishes)
        ];
        // Normalize input to lowercase for case-insensitive matching
        // Default to 1.00 (standard) if equipment level is unknown
        return $factors[strtolower($equipment)] ?? 1.00;
    }

    /**
     * Calculate a full valuation
     *
     * OVERALL FORMULA:
     *   price_per_m² = base_price × location_factor × condition_factor × equipment_factor × residence_factor
     *   total_value    = price_per_m² × area
     *
     * Step-by-step calculation flow:
     *   1. Get base_price from property type (e.g., Einfamilienhaus = 8188 CHF/m²)
     *   2. Get location_factor from locations table by PLZ (should come from 'trend_factor' column)
     *   3. Get condition_factor from condition (e.g., 'gepflegt' = 1.00)
     *   4. Get equipment_factor from equipment level (e.g., 'standard' = 1.00)
     *   5. Get residence_factor from residence status + trend (e.g., 'erstwohnsitz' = 0.80)
     *   6. Multiply all factors together with base_price to get adjusted price/m²
     *   7. Multiply by area to get total property value
     *
     * EXAMPLE:
     *   PLZ 7260 (Davos), Einfamilienhaus, 150m², 'gepflegt', 'standard', 'erstwohnsitz'
     *   Step 1: base_price = 8188.00
     *   Step 2: location_factor = 1.60 (Davos is a high-trend tourist area)
     *   Step 3: condition_factor = 1.00 (gepflegt = baseline)
     *   Step 4: equipment_factor = 1.00 (standard = baseline)
     *   Step 5: residence_factor = 0.80 (erstwohnsitz with trend_threshold=0.00)
     *   Step 6: price_per_m² = 8188 × 1.60 × 1.00 × 1.00 × 0.80 = 10480.64 CHF/m²
     *   Step 7: total_value = 10480.64 × 150 = 1,572,096.00 CHF
     *
     * @param string $plz
     * @param string $propertyType
     * @param float  $area
     * @param string $property_condition
     * @param string $equipment
     * @param string $residenceStatus
     * @return array
     */
    /**
     * Calculate a full valuation using the THREE-LAYER MODEL (per Refactoring.md).
     *
     * OVERALL FORMULA (per Refactoring.md example):
     *   effective_location_factor = macro_location_factor × micro_location_factor
     *   price_per_m² = base_price × effective_location_factor × condition_factor
     *                × equipment_factor × residence_status_factor × development_potential_factor
     *   total_value = price_per_m² × area
     *
     * EXAMPLE (from Refactoring.md):
     *   PLZ 7260 (Davos), Einfamilienhaus, 150m², 600m² plot
     *   base_price = 8188.00
     *   macro_location = 1.60
     *   micro_location = 1.15 (bevorzugte Lage)
     *   property_condition = 1.00 (gepflegt)
     *   equipment = 1.00 (standard)
     *   residence = 1.00 (erstwohnsitz)
     *   development = 1.12 (grosses Grundstück mit Ausbaureserve)
     *   effective_location = 1.60 × 1.15 = 1.84
     *   price_per_m² = 8188 × 1.84 × 1.00 × 1.00 × 1.00 × 1.12 = 16,873.34 CHF/m²
     *   total_value = 16,873.34 × 150 = 2,531,001.00 CHF
     *
     * @param string $plz
     * @param string $propertyType
     * @param float  $area
     * @param float  $plotAreaM2
     * @param string $property_condition
     * @param string $equipment
     * @param string $microLocation
     * @param string $developmentPotential
     * @param string $residenceStatus
     * @return array
     */
    public static function calculateValuation(
        string $plz,
        string $propertyType,
        float $area,
        float $plotAreaM2,
        string $property_condition,
        string $equipment,
        string $microLocation,
        string $developmentPotential,
        string $residenceStatus = 'erstwohnsitz'
    ): array {
        // Step 0: Look up location data from the 'locations' table by PLZ
        $location = static::getLocationByPLZ($plz);

        // ---- LAYER 1: Enhanced Location Factors ----
        // Get macro location factor (PLZ-level)
        $macroLocationFactor = is_array($location) && isset($location['macro_location_factor'])
            ? (float) $location['macro_location_factor']
            : 1.00;

        // Get micro location factor (quality of specific location within PLZ)
        $microLocationFactor = static::getMicroLocationFactor($microLocation);

        // Effective location factor = macro × micro
        $effectiveLocationFactor = $macroLocationFactor * $microLocationFactor;

        // Also fetch the trend_factor for residence status lookup
        $trendFactor = $macroLocationFactor; // macro_location_factor serves as trend_factor

        // ---- LAYER 2: Property Characteristics ----
        // Get base price per m² for the property type
        $basePrice = static::getBasePrice($propertyType);

        // Get condition factor
        $conditionFactor = static::getConditionFactor($property_condition);

        // Get equipment factor
        $equipmentFactor = static::getEquipmentFactor($equipment);

        // Get residence status factor
        $residenceFactor = static::getResidenceStatusFactor($residenceStatus, $trendFactor);

        // Get development potential factor
        $developmentFactor = static::getDevelopmentPotentialFactor($developmentPotential);

        // Calculate price per m² (includes ALL factors per Refactoring.md)
        $pricePerSqm = $basePrice
            * $effectiveLocationFactor
            * $conditionFactor
            * $equipmentFactor
            * $residenceFactor
            * $developmentFactor;

        // Calculate total property value
        $totalValue = $pricePerSqm * $area;

        // Calculate building value (without development potential)
        $buildingValuePerSqm = $basePrice * $effectiveLocationFactor * $conditionFactor * $equipmentFactor * $residenceFactor;
        $buildingValue = $buildingValuePerSqm * $area;
        $developmentValue = $totalValue - $buildingValue;

        // Extract location name and country from the database result
        $locationName = is_array($location) ? ($location['city'] ?? 'Unbekannt') : 'Unbekannt';
        $country      = is_array($location) ? ($location['country'] ?? 'CH') : 'CH';

        // Return all calculation results
        return [
            'plz'                          => $plz,
            'location_name'                => $locationName,
            'country'                      => $country,
            'property_type'                => $propertyType,
            'area'                         => $area,
            'plot_area_m2'                 => $plotAreaM2,
            'property_condition'           => $property_condition,
            'equipment'                    => $equipment,
            'micro_location'               => $microLocation,
            'development_potential'        => $developmentPotential,
            'residence_status'             => $residenceStatus,
            'price_per_sqm'                => round($pricePerSqm, 2),
            'total_value'                  => round($totalValue, 2),
            'building_value'               => round($buildingValue, 2),
            'development_value'            => round($developmentValue, 2),
            'location_factor'              => $macroLocationFactor,
            'micro_location_factor'        => $microLocationFactor,
            'effective_location_factor'    => $effectiveLocationFactor,
            'condition_factor'             => $conditionFactor,
            'equipment_factor'             => $equipmentFactor,
            'residence_status_factor'      => $residenceFactor,
            'development_potential_factor' => $developmentFactor,
        ];
    }

    // ====================================================================
    //  FORMULA OPTIONS (for UI)
    // ====================================================================

    /**
     * Get sales images for admin (stub — review tool only)
     *
     * @return array
     */
    public static function getsalesImagesForAdmin(): array
    {
        return [];
    }

    /**
     * Return available property types
     *
     * @return array
     */
    public static function getPropertyTypes(): array
    {
        return [
            'Einfamilienhaus',
            'Mehrfamilienhaus',
            'Wohnung',
            'Reihenhaus',
            'Doppelhaus',
            'Grundstück',
        ];
    }

    /**
     * Return available conditions
     *
     * @return array
     */
    public static function getConditions(): array
    {
        return [
            'neuwertig',
            'renoviert',
            'gepflegt',
            'sanierungsbedürftig',
            'renierungsbedürftig',
        ];
    }

    /**
     * Return available equipment levels
     *
     * @return array
     */
    public static function getEquipments(): array
    {
        return [
            'luxus',
            'gehoben',
            'standard',
            'einfach',
        ];
    }

    /**
     * Get residence status factor based on type and location trend.
     *
     * This factor accounts for Swiss housing law restrictions on primary vs. secondary residences.
     * Under Swiss law, properties built after 2012 in certain areas can only be used as primary
     * residences (Erstwohnsitz), which significantly reduces their market value due to limited
     * buyer pool.
     *
     * RESIDENCE STATUS FACTOR VALUES (from residence_status_factors table):
     *   erstwohnsitz, trend_threshold=0.00  → factor = 1.000  (Baseline)
     *   zweitwohnsitz, trend_threshold=0.00 → factor = 1.180  (Standardgebiet +18%)
     *   zweitwohnsitz, trend_threshold=1.01 → factor = 1.250  (Tourismusgebiet +25%)
     *   zweitwohnsitz_privilegiert, trend_threshold=0.00 → factor = 1.200  (Altrecht +20%)
     *   zweitwohnsitz_privilegiert, trend_threshold=1.01 → factor = 1.250  (Altrecht +25%)
     *   feriendomizil, trend_threshold=0.00 → factor = 1.000  (normal area: no adjustment)
     *   feriendomizil, trend_threshold=1.01 → factor = 1.150  (tourist area: +15%)
     *
     * FORMULA INTEGRATION:
     *   residence_factor is the LAST multiplier applied to the price per m².
     *   It adjusts the price based on whether the property can be used as:
     *     - Erstwohnsitz (primary residence): baseline (1.00)
     *     - Zweitwohnsitz (secondary residence): premium due to scarcity (1.18–1.25)
     *     - Feriendomizil (vacation home): depends on location trend → normal or premium (1.00 or 1.15)
     *
     * LOGIC FLOW:
     *   1. If Erstwohnsitz → always look up factor where trend_threshold = 0.00 → returns 1.00
     *   2. If Zweitwohnsitz → check location's trend_factor:
     *      a. If trend_factor > 1.0 (high-trend area like Davos/St.Moritz) → use threshold 1.01 → returns 1.25
     *      b. If trend_factor <= 1.0 (normal area) → use threshold 0.00 → returns 1.18
     *   3. If Zweitwohnsitz_privilegiert → same logic as above but with different baseline threshold
     *   4. If Feriendomizil → check location's trend_factor:
     *      a. If trend_factor > 1.0 → use threshold 1.01 → returns 1.15
     *      b. If trend_factor <= 1.0 → use threshold 0.00 → returns 1.00
     *
     * @param string $residenceStatus 'erstwohnsitz', 'zweitwohnsitz', 'zweitwohnsitz_privilegiert', or 'feriendomizil'
     * @param float  $trendFactor     from location (1.0 = normal, 1.6 = high-trend)
     * @return float
     */
    private static function getResidenceStatusFactor(string $residenceStatus, float $trendFactor): float
    {
        // Normalize input to lowercase for case-insensitive matching
        $status = strtolower($residenceStatus);

        // Build cache key to avoid repeated database queries for the same combination
        $cacheKey = $status . '_' . $trendFactor;

        // Check in-memory cache first (static property persists across calls in same request)
        if (isset(static::$residenceFactorCache[$cacheKey])) {
            return static::$residenceFactorCache[$cacheKey];
        }

        $db = static::getDB();

        if ($status === 'erstwohnsitz') {
            // ---- ERSTWOHNSITZ (Primary Residence) ----
            // Always uses trend_threshold = 0.00 regardless of location trend
            // This returns factor = 1.000 (baseline, no adjustment)
            // Erstwohnsitz is the standard baseline for valuation
            $sql = "SELECT `factor` FROM residence_status_factors
                    WHERE `status` = 'erstwohnsitz' AND `trend_threshold` = 0.00
                    LIMIT 1";
            $stmt = $db->prepare($sql);
        } elseif ($status === 'zweitwohnsitz' || $status === 'zweitwohnsitz_privilegiert') {
            // ---- ZWEITWOHNSITZ (Secondary Residence) ----
            // The factor depends on the location's trend_factor:
            //   - If trend_factor > 1.0 (high-trend tourist area): use threshold 1.01 → factor = 1.25
            //     This reflects the premium for secondary homes in desirable tourist destinations
            //     under Lex Weber (Zweitwohnungsgesetz) which restricts new second homes
            //   - If trend_factor <= 1.0 (normal area): use threshold 0.00 → factor = 1.18
            //     Standard secondary home pricing, moderate premium
            //   - For 'zweitwohnsitz_privilegiert' (Altrecht): uses threshold 0.00 for baseline
            //     Privilegierte Zweitwohnungen have existing rights and command a premium
            $threshold = $trendFactor > 1.0 ? 1.01 : 0.00;
            $sql = "SELECT `factor` FROM residence_status_factors
                    WHERE `status` = ? AND `trend_threshold` = ?
                    LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(1, $status, PDO::PARAM_STR);
            $stmt->bindValue(2, $threshold, PDO::PARAM_STR);
        } else {
            // ---- FERIENDOMIZIL (Vacation Home) ----
            // The factor depends on the location's trend_factor:
            //   - If trend_factor > 1.0 (high-trend tourist area): use threshold 1.01 → factor = 1.15
            //     Premium for vacation homes in desirable tourist destinations
            //   - If trend_factor <= 1.0 (normal area): use threshold 0.00 → factor = 1.00
            //     Normal vacation home pricing, no premium
            $threshold = $trendFactor > 1.0 ? 1.01 : 0.00;
            $sql = "SELECT `factor` FROM residence_status_factors
                    WHERE `status` = 'feriendomizil' AND `trend_threshold` = ?
                    LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(1, $threshold, PDO::PARAM_STR);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_COLUMN);

        // Use database value if found, otherwise fall back to hardcoded defaults
        $factor = $result !== false ? (float) $result : ($status === 'erstwohnsitz' ? 1.0 : 1.0);

        // Cache the result for subsequent calls
        static::$residenceFactorCache[$cacheKey] = $factor;
        return $factor;
    }

    /**
     * Get micro location factor (quality of specific location within PLZ)
     *
     * Micro location quality ranges from 'peripheral' to 'top'.
     * This factor multiplies the macro location factor to refine the location premium.
     *
     * Values:
     *   'top'       → 1.25 (prime location, e.g., lakefront, ski-in/ski-out)
     *   'good'      → 1.10 (desirable area, good accessibility)
     *   'standard'  → 1.00 (average location, baseline)
     *   'peripheral'→ 0.85 (less desirable, remote, or noisy area)
     *
     * @param string $microLocation
     * @return float
     */
    public static function getMicroLocationFactor(string $microLocation): float
    {
        $factors = [
            'top'       => 1.25,
            'good'      => 1.10,
            'standard'  => 1.00,
            'peripheral'=> 0.85,
        ];

        // Normalize input to lowercase for case-insensitive matching
        // Default to 1.00 (standard) if micro location is unknown
        return $factors[strtolower($microLocation)] ?? 1.00;
    }

    /**
     * Get development potential factor (Ausnützungsreserve)
     *
     * Development potential represents the value of unused building rights
     * (excess land that could be developed).
     *
     * Values per Refactoring.md:
     *   'none'      → 1.00 (Ausnützung voll ausgeschöpft)
     *   'minor'     → 1.10 (Moderate Reserve +10%, anbau-/aufstockbar)
     *   'major'     → 1.20 (Grosse Ausnützungsreserve / Verdichtungspotenzial +20%)
     *
     * @param string $developmentPotential
     * @return float
     */
    public static function getDevelopmentPotentialFactor(string $developmentPotential): float
    {
        $factors = [
            'none'  => 1.00,
            'minor' => 1.10,
            'major' => 1.20,
        ];

        // Normalize input to lowercase for case-insensitive matching
        // Default to 1.00 (none) if development potential is unknown
        return $factors[strtolower($developmentPotential)] ?? 1.00;
    }

    /**
     * Get all residence status options with their factors
     *
     * Returns an array of residence status options suitable for dropdown menus.
     * Each option includes the status key, display label, and default factor.
     *
     * @return array
     */
    public static function getResidenceStatusOptions(): array
    {
        return [
            ['value' => 'erstwohnsitz', 'label' => 'Erstwohnsitz', 'factor' => 1.00],
            ['value' => 'zweitwohnsitz', 'label' => 'Zweitwohnsitz (Standard)', 'factor' => 1.18],
            ['value' => 'zweitwohnsitz_privilegiert', 'label' => 'Zweitwohnsitz (Privilegiert / Altrecht)', 'factor' => 1.20],
            ['value' => 'feriendomizil', 'label' => 'Feriendomizil', 'factor' => 1.00],
        ];
    }

    /**
     * Get all micro location options
     *
     * Returns an array of micro location options suitable for dropdown menus.
     *
     * @return array
     */
    public static function getMicroLocationOptions(): array
    {
        return [
            ['value' => 'top', 'label' => 'Top-Lage (Panoramablick, Sonnig)', 'factor' => 1.25],
            ['value' => 'good', 'label' => 'Gute Wohnlage (Ruhig, Gute Erschliessung)', 'factor' => 1.10],
            ['value' => 'standard', 'label' => 'Standard (Durchschnitt)', 'factor' => 1.00],
            ['value' => 'peripheral', 'label' => 'Peripherie (Schattig, Immissionsbelastet)', 'factor' => 0.85],
        ];
    }

    /**
     * Get all development potential options
     *
     * Returns an array of development potential options suitable for dropdown menus.
     *
     * @return array
     */
    public static function getDevelopmentPotentialOptions(): array
    {
        return [
            ['value' => 'none', 'label' => 'Keine (Ausnützung voll ausgeschöpft)', 'factor' => 1.00],
            ['value' => 'minor', 'label' => 'Gering (Moderate Reserve +10%)', 'factor' => 1.10],
            ['value' => 'major', 'label' => 'Signifikant (Verdichtungspotenzial +20%)', 'factor' => 1.20],
        ];
    }

    /**
     * Get development potential factors from database
     *
     * @return array
     */
    public static function getDevelopmentPotentialFactors(): array
    {
        $db = static::getDB();
        $sql = 'SELECT reserve_type, factor, description FROM development_potential_factors ORDER BY factor DESC';
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
