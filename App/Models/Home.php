<?php

namespace App\Models;

use PDO;
use Core\View;

/**
 * Home Model
 * Handles valuation and location database operations.
 *
 * PHP version 8.0.22
 */
class Home extends \Core\Model
{
    // ---- Valuation properties ----
    public ?int $id = null;
    public ?string $plz = null;
    public ?string $location_name = null;
    public ?string $country = null;
    public ?string $property_type = null;
    public ?float $area = null;
    public ?string $condition = null;
    public ?string $equipment = null;
    public ?float $price_per_sqm = null;
    public ?float $total_value = null;
    public ?float $location_factor = null;
    public ?float $condition_factor = null;
    public ?float $equipment_factor = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

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
                (plz, location_name, country, property_type, area, condition,
                 equipment, price_per_sqm, total_value,
                 location_factor, condition_factor, equipment_factor,
                 created_at, updated_at)
                VALUES
                (:plz, :location_name, :country, :property_type, :area, :condition,
                 :equipment, :price_per_sqm, :total_value,
                 :location_factor, :condition_factor, :equipment_factor,
                 NOW(), NOW())';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':plz', $this->plz, PDO::PARAM_STR);
        $stmt->bindValue(':location_name', $this->location_name, PDO::PARAM_STR);
        $stmt->bindValue(':country', $this->country, PDO::PARAM_STR);
        $stmt->bindValue(':property_type', $this->property_type, PDO::PARAM_STR);
        $stmt->bindValue(':area', $this->area, PDO::PARAM_STR);
        $stmt->bindValue(':condition', $this->condition, PDO::PARAM_STR);
        $stmt->bindValue(':equipment', $this->equipment, PDO::PARAM_STR);
        $stmt->bindValue(':price_per_sqm', $this->price_per_sqm, PDO::PARAM_STR);
        $stmt->bindValue(':total_value', $this->total_value, PDO::PARAM_STR);
        $stmt->bindValue(':location_factor', $this->location_factor, PDO::PARAM_STR);
        $stmt->bindValue(':condition_factor', $this->condition_factor, PDO::PARAM_STR);
        $stmt->bindValue(':equipment_factor', $this->equipment_factor, PDO::PARAM_STR);

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
                    condition = :condition,
                    equipment = :equipment,
                    price_per_sqm = :price_per_sqm,
                    total_value = :total_value,
                    location_factor = :location_factor,
                    condition_factor = :condition_factor,
                    equipment_factor = :equipment_factor,
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
        $stmt->bindValue(':condition', $this->condition, PDO::PARAM_STR);
        $stmt->bindValue(':equipment', $this->equipment, PDO::PARAM_STR);
        $stmt->bindValue(':price_per_sqm', $this->price_per_sqm, PDO::PARAM_STR);
        $stmt->bindValue(':total_value', $this->total_value, PDO::PARAM_STR);
        $stmt->bindValue(':location_factor', $this->location_factor, PDO::PARAM_STR);
        $stmt->bindValue(':condition_factor', $this->condition_factor, PDO::PARAM_STR);
        $stmt->bindValue(':equipment_factor', $this->equipment_factor, PDO::PARAM_STR);

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
        if (empty($this->condition)) {
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
     * @param string $propertyType
     * @return float
     */
    public static function getBasePrice(string $propertyType): float
    {
        $basePrices = [
            'Einfamilienhaus'  => 8188.00,
            'Mehrfamilienhaus' => 6772.00,
            'Wohnung'          => 9026.00,
            'Reihenhaus'       => 7400.00,
            'Doppelhaus'       => 7900.00,
            'Grundstück'       => 1000.00,
        ];
        return $basePrices[$propertyType] ?? 5000.00; // default fallback
    }

    /**
     * Get condition factor
     *
     * @param string $condition
     * @return float
     */
    public static function getConditionFactor(string $condition): float
    {
        $factors = [
            'neuwertig'            => 1.20,
            'renoviert'            => 1.10,
            'gepflegt'             => 1.00,
            'sanierungsbedürftig'  => 0.85,
            'renierungsbedürftig'  => 0.70,
        ];
        return $factors[strtolower($condition)] ?? 1.00;
    }

    /**
     * Get equipment factor
     *
     * @param string $equipment
     * @return float
     */
    public static function getEquipmentFactor(string $equipment): float
    {
        $factors = [
            'luxus'    => 1.30,
            'gehoben'  => 1.15,
            'standard' => 1.00,
            'einfach'  => 0.85,
        ];
        return $factors[strtolower($equipment)] ?? 1.00;
    }

    /**
     * Calculate a full valuation
     *
     * @param string $plz
     * @param string $propertyType
     * @param float  $area
     * @param string $condition
     * @param string $equipment
     * @return array
     */
    public static function calculateValuation(string $plz, string $propertyType, float $area, string $condition, string $equipment): array
    {
        $location = static::getLocationByPLZ($plz);

        $basePrice        = static::getBasePrice($propertyType);
        $locationFactor   = is_array($location) && isset($location['factor']) ? (float) $location['factor'] : 1.00;
        $conditionFactor  = static::getConditionFactor($condition);
        $equipmentFactor  = static::getEquipmentFactor($equipment);

        $pricePerSqm = $basePrice * $locationFactor * $conditionFactor * $equipmentFactor;
        $totalValue  = $pricePerSqm * $area;

        $locationName = is_array($location) ? ($location['city'] ?? 'Unbekannt') : 'Unbekannt';
        $country      = is_array($location) ? ($location['country'] ?? 'CH') : 'CH';

        return [
            'plz'                => $plz,
            'location_name'      => $locationName,
            'country'            => $country,
            'property_type'      => $propertyType,
            'area'               => $area,
            'condition'          => $condition,
            'equipment'          => $equipment,
            'price_per_sqm'      => round($pricePerSqm, 2),
            'total_value'        => round($totalValue, 2),
            'location_factor'    => $locationFactor,
            'condition_factor'   => $conditionFactor,
            'equipment_factor'   => $equipmentFactor,
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
}
