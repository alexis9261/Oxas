<?php
require '../common/conexion.php';
require '../Oxa/Funciones.php';
if(isset($_GET['id_sinapsis'])){
  $id_user=$_GET['user'];
  $id_sinapsis=$_GET['id_sinapsis'];
  deleteInfo($id_sinapsis,$id_user);
}
echo "1";
 ?>
