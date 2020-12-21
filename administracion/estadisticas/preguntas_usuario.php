<?php
set_time_limit(0);session_start();
require '../common/conexion.php';require '../common/take_at.php'; require '../common/account-off.php';;
$mes=$_GET['mes'];$anio=$_GET['anio'];
$meses=array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
$diasMeses=array(31,28,31,30,31,30,31,31,30,31,30,31);
$numMeses=array('01','02','03','04','05','06','07','08','09','10','11','12');
$dayMonth=$diasMeses[array_search($mes,$meses)];//dias del mes
$Numero_de_mes=$numMeses[(array_search($mes,$meses))];
$fecha_inicial=$anio.'-'.$Numero_de_mes.'-01T00:00:00.000-04:00';//2019-03-25T00... año-mes-diaThora
$fecha_final=$anio.'-'.$Numero_de_mes.'-'.$dayMonth.'T00:00:00.000-04:00';
$url='https://api.mercadolibre.com/my/received_questions/search?dateCreateFrom='.$fecha_inicial."&dateCreateTo=".$fecha_final."&sort_fields=date_created&sort_types=ASC&access_token=".$AccessToken;
$ch=curl_init();curl_setopt($ch,CURLOPT_URL,$url);curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);
$consulta=json_decode($result);$totales=$consulta->total;$pages=ceil($totales/50);$resultados=$consulta->questions;
$maximo=0;$band=0;$dia_anterior=0;$cantidad_preg=0;
$fechas=array();$cantidades=array();
foreach($resultados as $value){
if($band==0){
$band=1;$dia_anterior=substr($value->date_created,8,2);//dia
++$cantidad_preg;$maximo=$cantidad_preg;
}else{
$dia_actual=substr($value->date_created,8,2);//dia actual
if($dia_actual==$dia_anterior){++$cantidad_preg;}elseif($dia_actual>$dia_anterior){$aux=$dia_actual-$dia_anterior;
if($aux>1){
array_push($fechas,$dia_anterior);array_push($cantidades,$cantidad_preg);
for($i=($dia_anterior+1);$i<($aux+$dia_anterior);$i++){if($i<10){array_push($fechas,'0'.$i);}else{array_push($fechas,$i);}array_push($cantidades,0);}
if($cantidad_preg>$maximo){$maximo=$cantidad_preg;}
$cantidad_preg=1;$dia_anterior=$dia_actual;//actualizo la fecha
}else{
array_push($fechas,$dia_anterior);array_push($cantidades,$cantidad_preg);
if($cantidad_preg>$maximo){$maximo=$cantidad_preg;}
$cantidad_preg=1;$dia_anterior=$dia_actual;//actualizo la fecha
} } } }
if($pages>1){
--$pages;
for($p=1;$p<=$pages;$p++){
$offset=($p)*50;
$url='https://api.mercadolibre.com/my/received_questions/search?dateCreateFrom='.$fecha_inicial.'&dateCreateTo='.$fecha_final.'&sort_fields=date_created&sort_types=ASC&access_token='.$AccessToken.'&offset='.$offset;
$ch=curl_init();curl_setopt($ch,CURLOPT_URL,$url);curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);
$consulta=json_decode($result);$resultados=$consulta->questions;
foreach($resultados as $value){
$dia_actual=substr($value->date_created,8,2);//dia actual
if($dia_actual==$dia_anterior){++$cantidad_preg;
}elseif($dia_actual>$dia_anterior){
$aux=$dia_actual-$dia_anterior;
if($aux>1){
array_push($fechas,$dia_anterior);array_push($cantidades,$cantidad_preg);
for($i=($dia_anterior+1);$i<($aux+$dia_anterior);$i++){
if($i<10){array_push($fechas,'0'.$i);}else{array_push($fechas,$i);}
array_push($cantidades,0);
}
if($cantidad_preg>$maximo){$maximo=$cantidad_preg;}
$cantidad_preg=1;$dia_anterior=$dia_actual;//actualizo la fecha
}else{
array_push($fechas,$dia_anterior);array_push($cantidades,$cantidad_preg);
if($cantidad_preg>$maximo){$maximo=$cantidad_preg;}
$cantidad_preg=1;
$dia_anterior=$dia_actual;//actualizo la fecha
} } } } }
//agregar el ultimo valor
$aux=$dia_actual-$dia_anterior;
if($aux>1){
array_push($fechas,$dia_anterior);array_push($cantidades,$cantidad_preg);
for($i=($dia_anterior+1);$i<($aux+$dia_anterior);$i++){
if($i<10){array_push($fechas,'0'.$i);}else{array_push($fechas,$i);}
array_push($cantidades,0);
} }else{ array_push($fechas,$dia_anterior);array_push($cantidades,$cantidad_preg);}
if($dia_anterior!=$dayMonth){for($i=($dia_anterior+1);$i<=$dayMonth;$i++){array_push($fechas,$i);array_push($cantidades,0);}}
$datos_fechas=implode('|',$fechas);$datos_cantidades=implode('|',$cantidades);
echo $dayMonth."%".$maximo."%".$datos_fechas.'%'.$datos_cantidades.'%'.$totales;
?>
