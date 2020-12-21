<?php
require '../Meli/meli.php';//El archivo del SDK de ML en php
require 'configApp.php';
require 'conexion.php';//conecto con la base de datos
$sql="SELECT ID_USUARIO FROM usuarios";//selecciono todos los usuarios, para actualizar TODOS los access tokens
$resultados= $conexion->query($sql);
while ($row=$resultados->fetch_assoc()) { //recorro todos los usuarios
$user=$row['ID_USUARIO'];//guardo el id user de un usuario
$registros= $conexion->query("SELECT ACCESS_TOKEN, REFRESH_TOKEN FROM usuarios WHERE ID_USUARIO='$user'");//busco en la BD el A_token y el R_token del usuario
  while ($rows=$registros->fetch_assoc()) {
    $token=$rows['ACCESS_TOKEN'];
    $r_token=$rows['REFRESH_TOKEN'];
    $ml_session = new Meli($appId, $secretKey, $token, $r_token);
    $refresh_tokn = $ml_session->refreshAccessToken();//Ejecutalo para obtener un nuevo token
    $new_token = $refresh_tokn['body']->access_token; //este es el nuevo token
    $new_refresh_token = $refresh_tokn['body']->refresh_token; // este es el nuevo refresh token
  //actualizo los valores en la BBDD
    $update=$conexion->query("INSERT INTO `usuarios`(`ACCESS_TOKEN`, `REFRESH_TOKEN`) VALUES ('$new_token','$new_refresh_token') WHERE ID_USURAIO='$user'");
  }
}
 ?>
