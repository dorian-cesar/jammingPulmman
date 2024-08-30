<?php

$dia=$_GET['dia'] ?? 1;

$user="Particulares";


$pasw="123";

include "conexion.php";

$consulta="SELECT hash FROM masgps.hash where user='$user' and pasw='$pasw'";

$resutaldo= mysqli_query($mysqli,$consulta);

$data=mysqli_fetch_array($resutaldo);

$hash=$data['hash'];
//$hash="5b141c217d843e4667e54085a57eac51";

date_default_timezone_set("America/Santiago");

$hoy = date("Y-m-d");

$ayer=date('Y-m-d',strtotime("-1 days"));


include "listado.php";

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/report/tracker/generate',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'hash='.$hash.'&title=Informe%20de%20evento&trackers='.$ids.'&from='.$ayer.'%2000%3A00%3A00&to='.$ayer.'%2023%3A59%3A59&time_filter=%7B%22from%22%3A%2200%3A00%22%2C%22to%22%3A%2223%3A59%22%2C%22weekdays%22%3A%5B1%2C2%2C3%2C4%2C5%2C6%2C7%5D%7D&plugin=%7B%22hide_empty_tabs%22%3Atrue%2C%22plugin_id%22%3A11%2C%22show_seconds%22%3Afalse%2C%22group_by_type%22%3Afalse%2C%22event_types%22%3A%5B%22output_change%22%5D%7D',
  CURLOPT_HTTPHEADER => array(
    'Accept: */*',
    'Accept-Language: es-419,es;q=0.9,en;q=0.8',
    'Connection: keep-alive',
    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
    'Cookie: _ga=GA1.2.728367267.1665672802; session_key=313bcf73d4cab8b8934bae1556b273e2; _gid=GA1.2.1549217858.1690386194; locale=es; check_audit=313bcf73d4cab8b8934bae1556b273e2; _ga_XXFQ02HEZ2=GS1.2.1690386195.11.1.1690386280.0.0.0',
    'Origin: http://www.trackermasgps.com',
    'Referer: http://www.trackermasgps.com/pro/applications/reports/index.html?newuiwrap=1',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;



$arreglo=json_decode($response);





echo 
$reporte=$arreglo->id;




Loop :

sleep(10);




$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/report/tracker/retrieve',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'hash='.$hash.'&report_id='.$reporte,
  CURLOPT_HTTPHEADER => array(
    'Accept: */*',
    'Accept-Language: es-419,es;q=0.9,en;q=0.8',
    'Connection: keep-alive',
    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
    'Cookie: _ga=GA1.2.728367267.1665672802; _gid=GA1.2.183718605.1679328823; locale=es; session_key=cf290712c61924284913e1af01cfaded; check_audit=cf290712c61924284913e1af01cfaded; date_format=m-d-Y; date_format_moment=MM-DD-YYYY',
    'Origin: http://www.trackermasgps.com',
    'Referer: http://www.trackermasgps.com/pro/applications/reports/index.html?newuiwrap=1',
    'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36'
  ),
));
echo 
$response = curl_exec($curl);

curl_close($curl);

$datos=json_decode($response);

//print_r($datos);


//$.report.sheets[0].sections[0].data[0].rows[0].address.location.lat
//$.report.sheets[0].sections[0].data[0].rows[0].address.location.lng
//$.report.sheets[0].sections[0].data[0].rows[0].address.location.address
//$.report.sheets[0].sections[0].data[0].rows[0].time.v
//$.report.sheets[0].sections[0].data[0].rows[0].event.v


if(isset($datos->report->sheets)){
 $trackers=$datos->report->sheets;
 

  if ($trackers[0]->header<>"No hay datos"){

 foreach ($trackers as $tracker){

          Echo
          $pat=$tracker->header ;
          $id=$tracker->entity_ids[0] ;
       

         $items=$tracker->sections[0]->data[0]->rows;


         foreach ($items as $item){
            
            if($item->event->v='Inicio Detección de Jamming'){

             $evento=$item->event->v ;
           
             $fecha=$item->time->v ;

             $objeto_fecha_hora = DateTime::createFromFormat('d/m/Y H:i', $fecha);
             $fecha_hora_formateada = $objeto_fecha_hora->format('Y-m-d H:i');

             $lat=$item->address->location->lat;
             $lng=$item->address->location->lng;
             
              echo
             $Q_insert="INSERT INTO `masgps`.`jammingPullman` (`id_tracker`, `patente`, `evento`, `fecha`, `lat`, `lng`) VALUES ('$id', '$pat', '$evento', '$fecha_hora_formateada', '$lat', '$lng')";

            
             


             }


            }
            
         }

        }else{
          echo $trackers[0]->header;
        }

        }else {
          
          goto Loop;
        }

 fin: