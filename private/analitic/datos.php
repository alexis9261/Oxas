<?php
include '../../administracion/common/conexion.php';
require '../../administracion/Oxa/Funciones.php';
require '../../administracion/common/configApp.php';
$user_id=$_GET['user_id'];
$accesst=$_GET['at'];
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/'.$user_id.'?access_token='.$accesst);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);
curl_close($ch);
$auxiliar=json_decode($result);
if(isset($auxiliar->error)){
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/oauth/token?grant_type=refresh_token&client_id='.$appId.'&client_secret='.$secretKey.'&refresh_token='.$_GET['rt']);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch,CURLOPT_POST,1);
  $headers=array();
  $headers[]='Content-Type: application/json';
  curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
  $result=curl_exec($ch);
  curl_close($ch);
  $r=json_decode($result);
  $access_token=$r->access_token;
  $refresh_token=$r->refresh_token;
  updateTokens($_GET['user_id'],$access_token,$refresh_token);
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/'.$user_id.'?access_token='.$access_token);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  $result=curl_exec($ch);
  curl_close($ch);
}
echo $result;
 ?>
