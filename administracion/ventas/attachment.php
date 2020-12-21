<?php
session_start();
$title=$_GET['title'];$type=$_GET['type'];
require '../common/conexion.php';require '../common/take_at.php'; require '../common/account-off.php';
$url='https://api.mercadolibre.com/messages/attachments/'.$_GET['attachment'].'?access_token='.$AccessToken;
$ch=curl_init();curl_setopt($ch,CURLOPT_URL,$url);$result=curl_exec($ch);curl_close($ch);echo "jk";
 ?>
