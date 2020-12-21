<?php
session_start();
require '../common/conexion.php';require '../common/take_at.php'; require '../common/account-off.php';
$text=$_GET['nota'];
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/orders/'.$_GET['order'].'/notes?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"note\":\"$text\"}");
curl_setopt($ch,CURLOPT_POST,1);
$headers=array();
$headers[]='Content-Type: application/json';
curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
$result=curl_exec($ch);
curl_close($ch);
echo $result;
 ?>
