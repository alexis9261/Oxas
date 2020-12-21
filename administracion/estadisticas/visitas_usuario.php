<?php
set_time_limit(0);session_start();
require '../common/conexion.php';require '../common/take_at.php'; require '../common/account-off.php';;
$maximo=0;$mes=$_GET['mes'];$anio=$_GET['anio'];
if($mes=='Dic'){$anio=(int)$anio+1;}
$meses=array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
$diasMeses=array(31,28,31,30,31,30,31,31,30,31,30,31);
$numMeses=array('01','02','03','04','05','06','07','08','09','10','11','12');
$last=$diasMeses[array_search($mes,$meses)];$unit='day';
if($mes=='Dic'){$ending=$anio.'-01-01';}else{$ending=$anio.'-'.$numMeses[(array_search($mes,$meses)+1)].'-01';}
$url="https://api.mercadolibre.com/users/$id_user/items_visits/time_window?last=$last&unit=$unit&ending=$ending";
$ch=curl_init();curl_setopt($ch,CURLOPT_URL,$url);curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);
$consulta=json_decode($result);$totales=$consulta->total_visits;$resultados=$consulta->results;
$array_dia=array();$array_cant=array();
foreach($resultados as $value){
$date=$value->date;$dia_aux=substr($date,8,2)." $mes";//2019-01-27T00:00:00Z
$cantidad=$value->total;if($cantidad>$maximo){$maximo=$cantidad;}
array_push($array_dia,$dia_aux);array_push($array_cant,$cantidad);
}
$datos_fechas=implode('|',$array_dia);$datos_cantidades=implode('|',$array_cant);
echo $last."%".$maximo."%".$datos_fechas.'%'.$datos_cantidades.'%'.$totales;
 ?>
