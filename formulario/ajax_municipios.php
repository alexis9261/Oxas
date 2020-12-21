<?php
require '../administracion/common/conexion.php';
$respuesta=0;
$municipios=array();
if(isset($_GET['state'])){
  $estado=$_GET['state'];
  $sql="SELECT * FROM municipios WHERE ESTADO='$estado'";
  $res=$conn->query($sql);
  if($res->num_rows>0){
    while($row=$res->fetch_assoc()){
      array_push($municipios,$row['MUNICIPIO']);
    }
    $respuesta=implode(",",$municipios);
  }
}
echo "$respuesta";
