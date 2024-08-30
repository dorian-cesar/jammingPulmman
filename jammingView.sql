CREATE OR REPLACE VIEW viewJammingPullman as
SELECT 
    s.id,
    s.id_tracker,
    s.patente,
    s.evento,
	concat(lat,',',lng) as coordenadas,
    left(fecha,10) as date,
    right(fecha,8) as time,
    i.descCentroCosto,
    i.descFlota    
   
FROM jammingPullman s 
LEFT JOIN infoVehiculos i
    ON LEFT(s.patente, 7) = LEFT(i.patente, 7)
WHERE s.fecha >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)