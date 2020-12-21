<?php
require '../../administracion/common/conexion.php';
if(isset($_GET['idplan'])){
$id=$_GET['idplan'];
// LLamado SQL
$sql="DELETE FROM `plan` WHERE `IDPLAN`=$id";
if ($conn->query($sql)===TRUE){
    echo 'good';
}
header('Location: ../Planes');
}
 ?>
