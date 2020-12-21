<?php
session_start();
require '../common/meli.php';require '../common/conexion.php';require '../common/take_at.php';
$id_p=$_GET['id_p'];$text=$_GET['respuesta'];$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/answers?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"question_id\":$id_p,\"text\":\"$text\"}");
curl_setopt($ch,CURLOPT_POST,1);$headers=array();$headers[]='Content-Type: application/json';
curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);$result=curl_exec($ch);curl_close($ch);echo $result;
 ?>
