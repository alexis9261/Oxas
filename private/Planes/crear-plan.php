<?php
require '../../administracion/common/conexion.php';
if(isset($_GET['nombre'], $_GET['descripcion'], $_GET['monto'], $_GET['duracion'])){
$nombre =$_GET['nombre'];
$descripcion=$_GET['descripcion'];
$monto= $_GET['monto'];
$duracion=$_GET['duracion'];
// LLamado SQL
$sql="INSERT INTO `plan`(`NOMBRE`, `DESCRIPCION`, `TIEMPO`, `MONTO`) VALUES ('$nombre','$descripcion','$duracion','$monto')";
if ($conn->query($sql)===TRUE){
    echo 'good';
}
header('Location: ../Planes');
}
 ?>
