```sql
UPDATE locations
SET macro_location_factor = CASE plz

    -- Referenzregion Churwalden / Lenzerheide
    WHEN '7062' THEN 1.00  -- Passugg
    WHEN '7063' THEN 0.95  -- Praden
    WHEN '7064' THEN 0.95  -- Tschiertschen
    WHEN '7074' THEN 0.95  -- Malix
    WHEN '7075' THEN 1.08  -- Churwalden
    WHEN '7076' THEN 1.15  -- Parpan
    WHEN '7077' THEN 1.65  -- Valbella
    WHEN '7078' THEN 1.82  -- Lenzerheide/Lai
    WHEN '7082' THEN 1.45  -- Vaz/Obervaz

    -- Chur und Umgebung
    WHEN '7000' THEN 1.12  -- Chur
    WHEN '7023' THEN 0.98  -- Haldenstein
    WHEN '7026' THEN 0.92  -- Maladers

    -- Arosa
    WHEN '7050' THEN 1.47  -- Arosa
    WHEN '7056' THEN 0.95  -- Molinis
    WHEN '7057' THEN 0.95  -- Langwies
    WHEN '7058' THEN 1.05  -- Litzirüti

    -- Flims / Umgebung
    WHEN '7012' THEN 1.05  -- Felsberg
    WHEN '7013' THEN 1.02  -- Domat/Ems
    WHEN '7014' THEN 1.08  -- Trin
    WHEN '7015' THEN 1.11  -- Tamins
    WHEN '7016' THEN 1.08  -- Trin Mulin
    WHEN '7017' THEN 1.42  -- Flims Dorf
    WHEN '7018' THEN 1.30  -- Flims Waldhaus
    WHEN '7019' THEN 1.08  -- Fidaz

    -- Prättigau / Davos
    WHEN '7250' THEN 1.55  -- Klosters
    WHEN '7252' THEN 1.45  -- Klosters Dorf
    WHEN '7260' THEN 1.60  -- Davos Dorf
    WHEN '7265' THEN 1.45  -- Davos Wolfgang
    WHEN '7270' THEN 1.55  -- Davos Platz
    WHEN '7272' THEN 1.40  -- Davos Clavadel
    WHEN '7276' THEN 1.38  -- Davos Frauenkirch
    WHEN '7277' THEN 1.30  -- Davos Glaris
    WHEN '7278' THEN 1.05  -- Davos Monstein

    -- Landquart / Bündner Rheintal
    WHEN '7205' THEN 1.02  -- Zizers
    WHEN '7206' THEN 1.00  -- Igis
    WHEN '7208' THEN 1.12  -- Malans
    WHEN '7302' THEN 0.91  -- Landquart
    WHEN '7304' THEN 1.12  -- Maienfeld
    WHEN '7306' THEN 1.18  -- Fläsch
    WHEN '7307' THEN 1.12  -- Jenins

    -- Surselva
    WHEN '7031' THEN 1.08  -- Laax
    WHEN '7032' THEN 1.08
    WHEN '7153' THEN 1.25  -- Falera
    WHEN '7165' THEN 1.00  -- Brigels
    WHEN '7132' THEN 1.02  -- Vals
    WHEN '7134' THEN 0.92  -- Obersaxen
    WHEN '7144' THEN 0.90  -- Vella
    WHEN '7130' THEN 0.95  -- Ilanz
    WHEN '7180' THEN 0.92  -- Disentis
    WHEN '7188' THEN 0.95  -- Sedrun

    -- Engadin
    WHEN '7500' THEN 2.35  -- St. Moritz
    WHEN '7503' THEN 1.77  -- Samedan
    WHEN '7504' THEN 1.72  -- Pontresina
    WHEN '7505' THEN 1.70  -- Celerina
    WHEN '7512' THEN 1.50  -- Champfèr
    WHEN '7513' THEN 1.85  -- Silvaplana
    WHEN '7514' THEN 1.72  -- Sils Maria
    WHEN '7515' THEN 1.65  -- Sils Baselgia
    WHEN '7516' THEN 1.55  -- Maloja
    WHEN '7522' THEN 1.35  -- La Punt
    WHEN '7523' THEN 1.35  -- Madulain
    WHEN '7524' THEN 1.45  -- Zuoz
    WHEN '7525' THEN 1.20  -- S-chanf

    -- Unterengadin
    WHEN '7530' THEN 1.05  -- Zernez
    WHEN '7545' THEN 1.08  -- Guarda
    WHEN '7546' THEN 1.08  -- Ardez
    WHEN '7550' THEN 1.25  -- Scuol
    WHEN '7551' THEN 1.25  -- Ftan
    WHEN '7552' THEN 1.25  -- Vulpera
    WHEN '7553' THEN 1.20  -- Tarasp
    WHEN '7554' THEN 1.18  -- Sent
    WHEN '7562' THEN 1.05  -- Samnaun-Compatsch
    WHEN '7563' THEN 1.08  -- Samnaun Dorf

    ELSE macro_location_factor

END
WHERE plz IN (
    '7062','7063','7064',
    '7074','7075','7076','7077','7078','7082',
    '7000','7023','7026',
    '7050','7056','7057','7058',
    '7012','7013','7014','7015','7016','7017','7018','7019',
    '7250','7252','7260','7265','7270','7272','7276','7277','7278',
    '7205','7206','7208','7302','7304','7306','7307',
    '7031','7032','7153','7165','7132','7134','7144','7130','7180','7188',
    '7500','7503','7504','7505','7512','7513','7514','7515','7516',
    '7522','7523','7524','7525',
    '7530','7545','7546','7550','7551','7552','7553','7554','7562','7563'
);
```
