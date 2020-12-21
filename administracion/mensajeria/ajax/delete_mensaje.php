<?php
require '../../common/conexion.php';
$return=0;
if(isset($_GET['id_mensaje'])){
  $id_mensaje=$_GET['id_mensaje'];
  $sql="DELETE FROM mensajes WHERE IDMENSAJE=$id_mensaje;";
  if($conn->query($sql)===TRUE){$return=1;}
}
echo $return;
?>
