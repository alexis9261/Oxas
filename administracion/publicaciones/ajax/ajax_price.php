<?php
session_start();
require '../../common/conexion.php';require '../../common/take_at.php';require '../../common/account-off.php';
$price=$_GET['price'];$item_id=$_GET['item'];
$ch=curl_init();curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$item_id.'?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);$consulta=json_decode($result);
if(empty($consulta->variations)){
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$item_id.'?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);curl_setopt($ch,CURLOPT_POSTFIELDS,"{\n\"price\":$price\n}");
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');$headers=array();$headers[]='Content-Type: application/json';
$headers[]='Accept: application/json';curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);$result=curl_exec($ch);curl_close($ch);
}else{curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$item_id.'?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$x=0;
foreach($consulta->variations as $value){$id=$value->id;
if($x==0){$x=1;$text="{\"id\":$id,\"price\":$price}";}else{$text.=",{\"id\":$id,\"price\":$price}";}}
curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"variations\":[\n $text \n]}");
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
$headers=array();
$headers[]='Content-Type: application/json';
curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
$result=curl_exec($ch);curl_close($ch);
}echo $result;
?>
