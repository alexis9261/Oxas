<?php
session_start();
require '../common/conexion.php';require '../common/take_at.php';
if(isset($_GET['order_id'])){
  $order_id=$_GET['order_id'];
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/orders/$order_id?access_token=$AccessToken");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  $result=curl_exec($ch);
  curl_close($ch);
  $var=json_decode($result);
  if(is_null($var->pack_id)){$pack_id=$order_id;}else{$pack_id=$var->pack_id;}
  $seller_id=$var->seller->id;
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/messages/packs/$pack_id/sellers/$seller_id?access_token=$AccessToken");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  $result=curl_exec($ch);
  curl_close($ch);
}
echo $result;
?>
