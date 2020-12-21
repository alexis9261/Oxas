<?php
require '../../common/conexion.php';require '../../common/take_at.php'; require '../../common/account-off.php';
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$_GET['item'].'?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch); echo $result;
 ?>
