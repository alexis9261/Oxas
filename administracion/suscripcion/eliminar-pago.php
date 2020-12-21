<?php
require '../common/conexion.php';
require '../common/take_at.php';
if(isset($_GET['id'])){
$id=$_GET['id'];
if(!empty($id)){
//Good Registrados
$sql="DELETE FROM `pagos` WHERE IDUSUARIO='$id_user' AND IDPAGO='$id'";
if($conn->query($sql)===TRUE){echo '1';}else{echo '2'; //error bbdd
}
}else{echo '0'; //error
}
}else{echo '-1'; //error
}
?>
