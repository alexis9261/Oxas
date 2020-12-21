<?php
session_start();
require '../common/conexion.php';require '../common/take_at.php';
$id_p=$_GET['id_p'];$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/questions/'.$id_p.'?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'DELETE');$result=curl_exec($ch);echo $result;
 ?>
