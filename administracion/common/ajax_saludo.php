<?php
include 'conexion.php';
$response=0;
if(isset($_GET['correo'])){
  $mail=$_GET['correo'];
  if(isset($_GET['saludo'])){
    $saludo=$_GET['saludo'];
    if($saludo !=NULL){
      $sql="UPDATE `usuario` SET `SALUDO`='$saludo' WHERE `CORREO`='$mail'";
      if($conn->query($sql)===TRUE){$response=1;}
    }
  }
  if (isset($_GET['firma'])){
    $firma=$_GET['firma'];
    if($firma !=NULL){
      $sql="UPDATE `usuario` SET `DESPEDIDA`='$firma' WHERE `CORREO`='$mail'";
      if($conn->query($sql)===TRUE){$response=1;}
    }
  }
}
echo $response;
