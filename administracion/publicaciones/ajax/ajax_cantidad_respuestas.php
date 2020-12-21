<?php
require '../../common/conexion.php';
$item_id=$_GET['item'];$return='';
$sql_sinapsis="SELECT * from sinapsis s INNER JOIN enlace e ON e.IDSINAPSIS=s.IDSINAPSIS INNER JOIN publicacion p ON p.IDPUBLICACION=e.IDPUBLICACION WHERE p.CODIGO='$item_id'";
$res=$conn->query($sql_sinapsis);
if($res->num_rows>0){while($row = $res->fetch_assoc()){$estimulos=$row['ESTIMULOS'];$respuesta=$row['INFO'];
if($return==''){$return.=$estimulos.'~¬'.$respuesta;}else{$return.='%&%'.$estimulos.'~¬'.$respuesta;}}}else{$return=0;}
echo $return;
?>
