<?php
session_start();
require '../common/conexion.php';
require '../common/take_at.php';require '../common/account-off.php';
$text=$_GET['text'];
$text=str_replace(array("\n","\r","\n\r","\r\n"),array('\\n','\\r','\\n\\r','\\r\\n'),$text);
$order=$_GET['order'];
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/orders/$order?access_token=$AccessToken");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);curl_close($ch);$var=json_decode($result);
if(is_null($var->pack_id)){$pack_id=$order;}else{$pack_id=$var->pack_id;}
$seller_id=$var->seller->id;
$buyer=$var->buyer->id;
// Envio el mensaje al comprador
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/messages/packs/$pack_id/sellers/$seller_id?access_token=$AccessToken&application_id=$appId");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"from\":{\"user_id\":\"$seller_id\"},\"to\":{\"user_id\":\"$buyer\"},\"text\":\"$text\"}");
curl_setopt($ch,CURLOPT_POST,1);
$headers=array();$headers[]="Content-Type: application/json";
curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
$result=curl_exec($ch);curl_close($ch);
if(isset($consulta->error)){$respuesta=0;}else{$respuesta=1;}
echo $respuesta;
?>
