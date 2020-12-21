<?php
include 'conexion.php';
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://s3.amazonaws.com/dolartoday/data.json');
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);
$dolar=$consulta->USD->transferencia;
if(!is_null($dolar) && isset($dolar) && $dolar!=0 && !empty($dolar)){
$sql="UPDATE divisas SET CAMBIODOLAR='$dolar' WHERE SITE='MLV'";
if($conn->query($sql)===TRUE){}
}
