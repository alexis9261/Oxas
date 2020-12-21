<?php
require '../../common/conexion.php';
$return=0;
if(isset($_GET['user_id'],$_GET['mensaje'],$_GET['horas'],$_GET['dias'])){
  $user_id=$_GET['user_id'];$mensaje=$_GET['mensaje'];$horas=$_GET['horas'];$dias=$_GET['dias'];
  $sql="INSERT INTO `mensajes`(`USUARIOID`,`MENSAJE`,`HORA`,`DIAS`,`ESTATUS`) VALUES ($user_id,'$mensaje','$horas','$dias',1)";
  if($conn->query($sql)===TRUE){$return=1;}
}
echo $return;
?>
