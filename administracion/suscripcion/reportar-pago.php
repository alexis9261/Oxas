<?php
require '../common/conexion.php';
require '../common/take_at.php';
if(isset($_GET['titular'],$_GET['docid'],$_GET['bancoe'],$_GET['bancor'],$_GET['monto'],$_GET['referencia'],$_GET['fecha'])){
$titular=$_GET['titular'];$docid=$_GET['docid'];$bancoe=$_GET['bancoe'];$bancor=$_GET['bancor'];$monto=$_GET['monto'];$referencia=$_GET['referencia'];$fecha=$_GET['fecha'];
if(!empty($titular) && !empty($docid) && !empty($bancoe) && !empty($bancor)&& !empty($monto) &&!empty($referencia) && !empty($fecha))  {
//Good Registrados
$sql="INSERT INTO `pagos` (`IDPAGO`,`IDUSUARIO`,`TITULAR`,`DOCID`,`BANCOE`,`BANCOR`,`FECHA`,`MONTO`,`REFERENCIA`,`ESTATUS`)
VALUES (NULL,'$id_user','$titular','$docid','$bancoe','$bancor','$fecha','$monto','$referencia','0')";
if($conn->query($sql)===TRUE){
#enviar correo de cambio de Plan
$sql="SELECT CORREO FROM usuario where IDUSUARIO='$id_user' LIMIT 1";
$r=$conn->query($sql);
if($r->num_rows>0){
while($row=$r->fetch_assoc()){
$destinatario=$row['CORREO'];
include('../correos/reportepago.php');
}
}
echo '1'; //good
}else{echo '2'; //error bbdd
}
}else{echo '0'; //error
}
}else{echo '-1'; //error
}
?>
