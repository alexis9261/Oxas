<?php
require 'administracion/Oxa/Funciones.php';
include 'administracion/common/conexion.php';
set_time_limit(0);
//obtengo todos los usuarios q hacen reventa en Oxas
$array_id_user=array();
$sql="SELECT USUARIOID FROM revendedores";
$r=$conn->query($sql);
if($r->num_rows>0){
  while($rw=$r->fetch_assoc()){
    array_push($array_id_user,$rw['USUARIOID']);
  }
}
//funcion de refrescar Token
function refreshToken($id_user,$appId,$secretKey,$ch,$conn){
  $sql="SELECT REFRESTOKEN FROM usuario WHERE IDUSUARIO=$id_user";
  $q=$conn->query($sql);
  if($q->num_rows>0){while($row=$q->fetch_assoc()){$refresh_token=$row['REFRESTOKEN'];}}
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/oauth/token?grant_type=refresh_token&client_id=$appId&client_secret=$secretKey&refresh_token=$refresh_token");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch,CURLOPT_POST,1);
  $headers=array();$headers[]='Content-Type: application/json';
  curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
  $r=curl_exec($ch);curl_close($ch);$c=json_decode($r);
  $AccessToken=$c->access_token;$refresh_token=$c->refresh_token;
  updateTokens($id_user,$access_token,$refresh_token);
  return $AccessToken;
}
//recorro todos los usuarios q estan haciendo reventas
foreach($array_id_user as $id_user){
  //busco el accessToken del usuario
  $sql="SELECT ACCESSTOKEN FROM usuario where IDUSUARIO=$id_user";
  $res=$conn->query($sql);
  if($res->num_rows>0){
    while($row=$res->fetch_assoc()){
      $AccessToken=$row['ACCESSTOKEN'];
    }
  }
  //Inicio los arreglos auxiliares para las publicaciones
  $array_publicaciones=array();
  $array_publicaciones_originales=array();
  $array_ganancias=array();
  //busco todas las publicaciones ACTIVAS del usuario
  $sql="SELECT CODIGO,CODIGOORIGINAL,GANANCIA FROM publicacion where IDUSUARIO=$id_user AND ESTATUS=1";
  $res=$conn->query($sql);
  if($res->num_rows>0){
    while($row=$res->fetch_assoc()){
      array_push($array_publicaciones,$row['CODIGO']);
      array_push($array_publicaciones_originales,$row['CODIGOORIGINAL']);
      array_push($array_ganancias,$row['GANANCIA']);
    }
  }
  //Recorro todos los items activos del Revendedor
  $t=0;
  foreach($array_publicaciones as $publicacion){
    //confirmo q el item tenga asociado el id de la publicacion original
    if(isset($array_publicaciones_originales[$t]) && !empty($array_publicaciones_originales[$t])){
      $publicacion_original=$array_publicaciones_originales[$t];
      $ganancia=$array_ganancias[$t];
      //busco el precio del item original via api
      $ch=curl_init();
      curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items?id=$publicacion_original");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$r=curl_exec($ch);curl_close($ch);$c=json_decode($r);
      //veo si la publicacion esta activa
      if($c->status=='active'){
        // tomo el precio de la publicacion
        $priceOriginal=$c->price;
        //proceso el precio para generar el precio final incluido ganancia y comisiones por venta
        $ganancia_aux=$ganancia/100;
        $precioNew=round($priceOriginal*($ganancia_aux+1)/(0.89),-3)-1;
        $precioNew=intval($precioNew);
        //busco mi publicacion a ver si tienes variaciones
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items/$publicacion?access_token=$AccessToken");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$r=curl_exec($ch);curl_close($ch);$c=json_decode($r);
        if(isset($c->error)){
          $AccessToken=refreshToken($id_user,$appId,$secretKey,$ch,$conn);
        }else{
          //veo si tiene o no variaciones
          if(empty($c->variations)){
            //actualizo el precio de mi publicacion mediante la api
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items/$publicacion?access_token=$AccessToken");
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,"{\n\"price\":$precioNew\n}");
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
            $headers=array();
            $headers[]='Content-Type: application/json';
            $headers[]='Accept: application/json';
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
            $r=curl_exec($ch);curl_close($ch);
          }else{
            $x=0;
            foreach($c->variations as $value){
              $id=$value->id;
              if($x==0){
                $x=1;$text="{\"id\":$id,\"price\":$precioNew}";
              }else{
                $text.=",{\"id\":$id,\"price\":$precioNew}";
              }
            }
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items/$publicacion?access_token=$AccessToken");
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"variations\":[\n$text\n]}");
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
            $headers=array();
            $headers[]='Content-Type: application/json';
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
            $r=curl_exec($ch);curl_close($ch);
          }
        }
      }else{
        // en caso de no estar activa la publicacion original, entonces debo pausar la mia
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items/$publicacion?access_token=$AccessToken");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"status\":\"paused\"}");
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
        $headers=array();
        $headers[]='Content-Type: application/json';
        $headers[]='Accept: application/json';
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        $r=curl_exec($ch);curl_close($ch);
      }
    }
    ++$t;
  }
}
