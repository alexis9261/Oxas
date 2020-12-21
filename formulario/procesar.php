<?php
require '../administracion/common/conexion.php';
require '../administracion/common/configApp.php';
require '../administracion/Oxa/Funciones.php';
$id_seller=$_GET['user_id'];
$sql="SELECT ACCESSTOKEN FROM usuario where IDUSUARIO=$id_seller;";
$res=$conn->query($sql);
if($res->num_rows>0){while($row=$res->fetch_assoc()){$AccessToken=$row['ACCESSTOKEN'];}}
$respuesta=0;
function refreshToken($id_seller,$appId,$secretKey,$ch,$conn){
  $sql="SELECT REFRESTOKEN FROM usuario WHERE IDUSUARIO=$id_seller;";
  $result_f=$conn->query($sql);
  if($result_f->num_rows>0){while($row=$result_f->fetch_assoc()){$refresh_token=$row['REFRESTOKEN'];}}
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/oauth/token?grant_type=refresh_token&client_id=$appId&client_secret=$secretKey&refresh_token=$refresh_token");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch,CURLOPT_POST,1);
  $headers=array();$headers[]='Content-Type: application/json';
  curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
  $result_ch=curl_exec($ch);curl_close($ch);$r=json_decode($result_ch);
  $access_token=$r->access_token;$refresh_token=$r->refresh_token;
  updateTokens($id_seller,$access_token,$refresh_token);
  return $access_token;
}
function enviarMensaje($consulta,$id_seller,$AccessToken,$appId,$ch,$mensaje,$id_orden){
  $id_buyer=$consulta->buyer->id;
  if(is_null($consulta->pack_id)){$pack_id=$id_orden;}else{$pack_id=$consulta->pack_id;}
  // Envio el mensaje al comprador
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/messages/packs/$pack_id/sellers/$id_seller?access_token=$AccessToken&application_id=$appId");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"from\":{\"user_id\":\"$id_seller\"},\"to\":{\"user_id\":\"$id_buyer\"},\"text\":\"$mensaje\"}");
  curl_setopt($ch,CURLOPT_POST,1);
  $headers=array();$headers[]="Content-Type: application/json";
  curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
  $result=curl_exec($ch);curl_close($ch);
}
if(isset($_GET['orden_id'],$_GET['cedula'],$_GET['telefono'],$_GET['correo'],$_GET['b_emisor'],$_GET['b_receptor'],$_GET['pago'],$_GET['fecha_pago'],$_GET['referencia'],$_GET['agencia'],$_GET['estado'],$_GET['municipio'],$_GET['codigo_agencia'],$_GET['direccion'])){
  $id_orden=$_GET['orden_id'];$cedula=$_GET['cedula'];$extension=$_GET['extension_cel'];
  $telefono=$_GET['telefono'];$telefono=$extension.$telefono;$correo=$_GET['correo'];
  $banco_emisor=$_GET['b_emisor'];$banco_receptor=$_GET['b_receptor'];
  $pago=$_GET['pago'];$fecha_pago=$_GET['fecha_pago'];
  $fecha_pago=str_replace(array('-'),array(''),$fecha_pago);
  $referencia=$_GET['referencia'];$agencia=$_GET['agencia'];
  $estado=$_GET['estado'];$municipio=$_GET['municipio'];
  $codigo_agencia=$_GET['codigo_agencia'];$direccion=$_GET['direccion'];
  $sql="INSERT INTO formulario (`ORDENID`,`CIBUYER`,`TELEFONOBUYER`,`CORREOBUYER`,`BANKEMISOR`,`BANKRECEPTOR`,`PAGO`,`FECHAPAGO`,`REFERENCIA`,`AGENCIA`,`ESTADOENVIO`,`MUNICIPIOENVIO`,`CODIGOAGENCIA`,`DIRECCIONENVIO`)
  VALUES ('$id_orden','$cedula','$telefono','$correo','$banco_emisor','$banco_receptor','$pago','$fecha_pago','$referencia','$agencia','$estado','$municipio','$codigo_agencia','$direccion')";
  if($conn->query($sql)===TRUE){
    $respuesta=1;
    //enviar mensaje al cliente de q se recibio el formulario
    $mensaje="Recibimos tu formulario! \n\n Ya estamos en el proceso de confirmación de tu pago, si tienes alguna duda puedes escribirnos por acá, con mucho gusto te atenderemos. \n------------------------\n Feliz día!!! :)";
    $mensaje=str_replace(array("\n","\r","\n\r","\r\n"),array('\\n','\\r','\\n\\r','\\r\\n'),$mensaje);
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/orders/$id_orden?access_token=$AccessToken");
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $resultado=curl_exec($ch);curl_close($ch);$consulta=json_decode($resultado);
    if(isset($consulta->error)){
      $AccessToken=refreshToken($id_seller,$appId,$secretKey,$ch,$conn);
      $ch=curl_init();
      curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/orders/$id_orden?access_token=$AccessToken");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      $resultado=curl_exec($ch);curl_close($ch);$consulta=json_decode($resultado);
      enviarMensaje($consulta,$id_seller,$AccessToken,$appId,$ch,$mensaje,$id_orden);
    }else{
      enviarMensaje($consulta,$id_seller,$AccessToken,$appId,$ch,$mensaje,$id_orden);
    }
  }else{$respuesta=2;}
}
echo "$respuesta";
 ?>
