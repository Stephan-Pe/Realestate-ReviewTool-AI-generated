```sql
UPDATE `locations`
SET 
  `macro_location_factor` = CASE `plz`
    -- 1. Sanierung Südtäler & Misox (Peripherie / Neutrale Lagen)
    WHEN '6534' THEN 1.00  -- San Vittore
    WHEN '6535' THEN 1.00  -- Roveredo (GR)
    WHEN '7741' THEN 1.00  -- S. Carlo (Poschiavo)
    WHEN '7742' THEN 1.00  -- Poschiavo
    WHEN '7743' THEN 0.95  -- Brusio
    WHEN '7744' THEN 0.95  -- Campocologno

    -- 2. Schanfigg-Passagedörfer (Korrektur von 1.10 auf Peripheriewert)
    WHEN '7027' THEN 0.95  -- Calfreisen
    WHEN '7028' THEN 0.95  -- Pagig
    WHEN '7029' THEN 0.95  -- Peist

    -- 3. Surselva Tourismus-Hotspots & Wintersportorte
    WHEN '7031' THEN 1.35  -- Laax
    WHEN '7032' THEN 1.35  -- Laax
    WHEN '7134' THEN 1.15  -- Obersaxen
    WHEN '7144' THEN 1.15  -- Vella
    WHEN '7180' THEN 1.12  -- Disentis/Mustér
    WHEN '7188' THEN 1.20  -- Sedrun

    -- 4. Val Surses / Oberhalbstein
    WHEN '7457' THEN 1.10  -- Bivio
    WHEN '7460' THEN 1.18  -- Savognin

    -- Alle übrigen PLZs behalten ihren Wert
    ELSE `macro_location_factor`
  END,
  `updated_at` = NOW()
WHERE `plz` IN (
  '6534', '6535', '7741', '7742', '7743', '7744',
  '7027', '7028', '7029', '7031', '7032', '7134',
  '7144', '7180', '7188', '7457', '7460'
);
```

