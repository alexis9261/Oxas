<?php
set_time_limit(0);
session_start();
require '../common/conexion.php';require '../common/take_at.php'; require '../common/account-off.php';
if(isset($_SESSION['array_publicaciones'])){
}
$fulfilled=$_GET['fulfilled'];
$rating=$_GET['rating'];
$message=$_GET['message'];
$reason=$_GET['reason'];
$order=$_GET['order_id'];
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/orders/'.$order.'/feedback?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
if($fulfilled=="true"){
curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"fulfilled\":$fulfilled,\"rating\":\"$rating\",\"message\":\"$message\",}");
}elseif($fulfilled=="false"){
curl_setopt($ch, CURLOPT_POSTFIELDS,"{\"fulfilled\":$fulfilled,\"rating\":\"$rating\",\"message\":\"$message\",\"reason\":\"$reason\",\"restock_item\":false}");
}
curl_setopt($ch,CURLOPT_POST,1);
$headers=array();
$headers[]='Content-Type: application/json';
curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
$result=curl_exec($ch);
curl_close($ch);
echo $result;
?>
