<?php
session_start();
require '../common/conexion.php';require '../common/take_at.php'; require '../common/account-off.php';
$text=$_GET['text'];
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/orders/'.$_GET['order'].'/notes/'.$_GET['id_note'].'?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"note\":\"$text\"}");
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
$headers=array();
$headers[]='Content-Type: application/json';
$headers[]='Accept: application/json';
curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
$result=curl_exec($ch);
curl_close($ch);
echo $result;
 ?>
