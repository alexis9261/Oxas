<?php
session_start();require '../../common/conexion.php';require '../../common/take_at.php'; require '../../common/account-off.php';
$text=$_GET['text'];$text=str_replace(array("\n","\r","\n\r","\r\n"),array('\\n','\\r','\\n\\r','\\r\\n'), $text);
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$_GET['item'].'/description?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"plain_text\":\"$text\"}");
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');$headers=array();$headers[]='Content-Type: application/json';
$headers[]='Accept: application/json';curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);$result=curl_exec($ch);curl_close($ch);echo $result;
 ?>
