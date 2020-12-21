<?php
require '../../administracion/common/conexion.php';
$id_user=$_GET['user_id'];
$respuestas=0;
$sql="SELECT INFO,ESTIMULOS,NUMPUBLICACIONES FROM sinapsis WHERE IDUSUARIO=$id_user";
//$sql="SELECT count(IDSINAPSIS) as cantidad FROM sinapsis WHERE IDUSUARIO=$id_user";
$result=$conn->query($sql);
if($result->num_rows>0){
  $respuestas="";
while($row=$result->fetch_assoc()){
  if($respuestas==""){
    $respuestas=$row['INFO']."|".$row['ESTIMULOS']."|".$row['NUMPUBLICACIONES'];
  }else{
    $respuestas.="%".$row['INFO']."|".$row['ESTIMULOS']."|".$row['NUMPUBLICACIONES'];
  }
}
}
echo $respuestas;
 ?>
