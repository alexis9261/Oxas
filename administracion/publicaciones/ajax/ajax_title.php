<?php
session_start();
require '../../common/conexion.php';require '../../common/take_at.php';require '../../common/account-off.php';
$title=$_GET['title'];$item_id=$_GET['item'];
$ch=curl_init();curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$item_id.'?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"title\":\"$title\"}");
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');$headers=array();$headers[]='Content-Type: application/json';
curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);$result=curl_exec($ch);curl_close($ch);echo $result;
 ?>
