<?php

$dia = $_GET['dia'] ?? 1;
$user = "Pullman";
$pasw = "123";

include __DIR__."/conexion.php";

// Usar consultas preparadas para evitar inyecciones SQL
$stmt = $mysqli->prepare("SELECT hash FROM masgps.hash WHERE user=? AND pasw=?");
$stmt->bind_param("ss", $user, $pasw);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$hash = $data['hash'];

$stmt->close();

date_default_timezone_set("America/Santiago");

$hoy = date("Y-m-d");
$ayer = date('Y-m-d', strtotime("-$dia days"));

include __DIR__."/listado.php";

$curl = curl_init();

$curlOpts = [
  CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/report/tracker/generate',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => http_build_query([
    'hash' => $hash,
    'title' => 'Informe de evento',
    'trackers' => $ids,
    'from' => $ayer . ' 00:00:00',
    'to' => $ayer . ' 23:59:59',
    'time_filter' => json_encode([
      'from' => '00:00',
      'to' => '23:59',
      'weekdays' => [1, 2, 3, 4, 5, 6, 7]
    ]),
    'plugin' => json_encode([
      'hide_empty_tabs' => true,
      'plugin_id' => 11,
      'show_seconds' => false,
      'group_by_type' => false,
      'event_types' => ['output_change']
    ])
  ]),
  CURLOPT_HTTPHEADER => [
    'Accept: */*',
    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36'
  ],
];

curl_setopt_array($curl, $curlOpts);

$response = curl_exec($curl);
curl_close($curl);

$arreglo = json_decode($response);
$reporte = $arreglo->id;

do {
  sleep(10);

  $curl = curl_init();
  curl_setopt_array($curl, [
    CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/report/tracker/retrieve',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => http_build_query([
      'hash' => $hash,
      'report_id' => $reporte
    ]),
    CURLOPT_HTTPHEADER => [
      'Accept: */*',
      'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
      'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36'
    ],
  ]);

  $response = curl_exec($curl);
  curl_close($curl);

  $datos = json_decode($response);

  if (isset($datos->report->sheets) && $datos->report->sheets[0]->header !== "No hay datos") {
    $insert_values = [];
    foreach ($datos->report->sheets as $tracker) {
      $pat = $tracker->header;
      $id = $tracker->entity_ids[0];

      foreach ($tracker->sections[0]->data[0]->rows as $item) {
        if ($item->event->v === 'Inicio Detección de Jamming') {
          $evento = $item->event->v;
          $fecha = $item->time->v;

          $objeto_fecha_hora = DateTime::createFromFormat('d/m/Y H:i', $fecha);
          $fecha_hora_formateada = $objeto_fecha_hora->format('Y-m-d H:i');

          $lat = $item->address->location->lat;
          $lng = $item->address->location->lng;

          $insert_values[] = "('$id', '$pat', '$evento', '$fecha_hora_formateada', '$lat', '$lng')";

          // Ejecutar los inserts en lotes de 50
          if (count($insert_values) >= 50) {
            $Q_insert = "INSERT INTO `masgps`.`jammingPullman` 
                                     (`id_tracker`, `patente`, `evento`, `fecha`, `lat`, `lng`) 
                                     VALUES " . implode(", ", $insert_values);
            mysqli_query($mysqli, $Q_insert);
            $insert_values = []; // Limpiar el arreglo después de ejecutar el insert
          }
        }
      }
    }

    // Ejecutar cualquier lote restante
    if (count($insert_values) > 0) {
      $Q_insert = "INSERT INTO `masgps`.`jammingPullman` 
                         (`id_tracker`, `patente`, `evento`, `fecha`, `lat`, `lng`) 
                         VALUES " . implode(", ", $insert_values);
      mysqli_query($mysqli, $Q_insert);
    }
  }
} while (!isset($datos->report->sheets));

fin:
