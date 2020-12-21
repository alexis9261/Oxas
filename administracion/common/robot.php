<?php
include 'conexion.php';

//obtener el estatus del robot mediante GET
if(isset($_GET['robot'], $_GET['id'])) {
$robot=$_GET['robot'];
$id_user=$_GET['id'];

//consultar suscripción
$sql="SELECT * FROM suscripcion WHERE IDUSUARIO='$id_user' LIMIT 1";
$r=$conn->query($sql);
if($r->num_rows>0){
  $row=$r->fetch_assoc();
  if ($row['ESTATUS']!=1 ){
    //puede cambiar el robot
    if (!$robot){
    //Desactiva robot
    $sql="UPDATE `usuario` SET `CHATROBOT`=0 where  `IDUSUARIO`=$id_user";
    if($conn->query($sql) === TRUE){
    echo '1';
    }else{
    echo '0';
    }
    }else{
    //Activa robot
    $sql="UPDATE `usuario` SET `CHATROBOT`=1 where  `IDUSUARIO`=$id_user";
    if($conn->query($sql) === TRUE){
    echo '1';
    }else{
    echo '0';
    }
    }
  }
}
}
?>
