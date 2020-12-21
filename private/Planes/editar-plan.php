<?php
require '../../administracion/common/conexion.php';
if(isset($_GET['nombre'], $_GET['descripcion'], $_GET['monto'], $_GET['duracion'],$_GET['idplan'])){
$nombre =$_GET['nombre'];
$descripcion=$_GET['descripcion'];
$monto= $_GET['monto'];
$duracion=$_GET['duracion'];
$id=$_GET['idplan'];
// LLamado SQL
$sql="UPDATE `plan` SET `NOMBRE`='$nombre', `DESCRIPCION`='$descripcion',`TIEMPO`='$duracion',`MONTO`='$monto' WHERE `IDPLAN`=$id";
if ($conn->query($sql)===TRUE){
    echo 'good';
}
header('Location: ../Planes');
}
 ?>
