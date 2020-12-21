<?php
session_start();
require 'meli.php';
include 'conexion.php';
include '../Oxa/Funciones.php';
$redirectURI="https://app.oxas.tech/administracion/common/redirect.php";
$appId='1153047962046613';
$secretKey='i3RGdgCvJXrKT1ceMNOHs4YLNHdgZ9Mj';
date_default_timezone_set('America/Caracas');
$meli=new Meli($appId,$secretKey);
if(@$_GET['code'] || @$_COOKIE['_validate']){// si existe un codigo(code), quiere decir que se esta registrando por 1 vez, si existe un validate quiere decir q ya se habia registrado antes
if($_GET['code']){// If code exist and session is empty
$user=$meli->authorize($_GET['code'],$redirectURI);
$AT=$user['body']->access_token;
$RT=$user['body']->refresh_token;
#Creacion de Cookies
$duracion=time()+5*60*60; #Duracion de la COOKIE 6Hr
$ruta="/";
#commando curl
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/users/me?access_token=$AT");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);
if(curl_errno($ch)){echo 'Error:'.curl_error($ch);}
curl_close($ch);
$reply=json_decode($result);
$telppal=@$reply->phone->area_code.$reply->phone->number;
$telsecond=@$reply->alternative_phone->area_code.$reply->alternative_phone->number;
$site_id=@$reply->site_id;
#existe el usuario?
if(userExist($reply->id,$reply->email)){
#actualizar tokens
setcookie("id_user",$reply->id,$duracion,$ruta);
updateTokens($reply->id,$AT,$RT);
//verifica la suscripcion
$sql="SELECT ESTATUS FROM suscripcion WHERE IDUSUARIO=$reply->id LIMIT 1;";
$r=$conn->query($sql);
if($r->num_rows>0){
$row=$r->fetch_assoc();
$estatus=$row['ESTATUS'];
setcookie("_validate",$estatus,$duracion,$ruta);
setcookie("expires_in", time() + $user['body']->expires_in,$duracion,$ruta);
}
}else{
#No existe el Usuario
#guardar en base de datos IDUser, Correo, AccessToken, refreshToken
setcookie("id_user", $reply->id,$duracion,$ruta);
insertUser($reply->id,$reply->email,$AT,$RT,$reply->first_name,$reply->last_name,$telppal,$telsecond,$site_id);
#registra la suscripcion
$idUsuario=$reply->id;
//Fecha Actual de Registro
$fechaR=date("Y-m-d");
//Fecha actual mas 21 día de membresia.
$fechaV=date("Y-m-d",strtotime($fechaR."+ 30 days"));//Modificar de 30 dias Luego.
$tipo=0;//tipo cero indica plan gratis
$estatus=0;
suscribir($idUsuario,$fechaR,$fechaV,$tipo,$estatus);
#crearmos cookies de acceso
setcookie("_validate",$estatus,$duracion,$ruta);
setcookie("expires_in", time() + $user['body']->expires_in,$duracion,$ruta);
//enviar correo a nuevo usuario
$destinatario=$reply->email;
include('../correos/bienvenida.php');
}
header('location:../');
}else{
// We can check if the access token in invalid checking the time
if($_COOKIE['expires_in'] < time()){
try {
#Leer de la base de datos refreshToken
$refresh=$meli->refreshAccessToken();
$AT=$refresh['body']->access_token;
$RT=$refresh['body']->refresh_token;
$duracion=time()+5*60*60;
$ruta="/";
setcookie("_validate",true,$duracion,$ruta);
setcookie("expires_in", time() + $refresh['body']->expires_in,$duracion,$ruta);
#guardar en base de datos  AccessToken, refreshToken mediante
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/users/me?access_token=$AT");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);
if(curl_errno($ch)){ echo 'Error:'.curl_error($ch);}
curl_close($ch);
$reply=json_decode($result);
if(userExist($reply->id,$reply->email)){
#actualizar tokens
setcookie("id_user",$reply->id,$duracion,$ruta);
updateTokens($reply->id, $AT,$RT);
}
}catch(Exception $e){ echo "Exception: ",$e->getMessage(),"\n";}
}
}
}else{ header('location:.../'); }
?>
