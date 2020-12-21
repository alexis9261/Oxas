<?php
require '../common/conexion.php';
if (isset($_GET['idplan'], $_GET['idservicio'] )){
$plan=$_GET['idplan'];
$id=$_GET['idservicio'];
$sql="UPDATE servicios SET IDPLAN='$plan' WHERE IDSERVICIOS='$id'";
if($conn->query($sql)===TRUE){
#enviar correo de cambio de Plan
$sql="SELECT u.CORREO, p.NOMBRE FROM servicios s INNER JOIN usuario u ON u.IDUSUARIO=s.IDUSUARIO INNER JOIN plan p ON p.IDPLAN=s.IDPLAN  WHERE s.IDSERVICIOS='$id' LIMIT 1";
$r=$conn->query($sql);
if ($r->num_rows>0){
while($row=$r->fetch_assoc()){
$destinatario=$row['CORREO'];
$plan= $row['NOMBRE'];
include('../correos/cambio_plan.php');
}
}
echo '1';}else{echo '0';}
}else{echo "0";}
?>
