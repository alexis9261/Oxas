<?php
/*Decodificar - Modulo 1*/
/*Funcion que devuelve la pregunta sin palabras repetidas, ni cracteres especiales*/
function decodificador($string){
$p=strtolower($string);
$ps=explode(' ',$p);
foreach($ps as &$p){$p=eliminar_simbolos($p);}
$ps=array_unique($ps); #eliminar palabras repetidas
return $ps;
}
/*Razonador - Modulo 2*/
/*
Palabras que se generan de la codificacion de la prgunta
OJO: Se supone que las Keywords han sido organizadas mediante ',' con un implode
*/
function razonador($ps,$idUsuario,$idpublicacions){
  $ids=array();$keywords=array();$idRecord=array();
  include 'conexion.php';
  $sql='';
  $j=0;
  foreach($ps as $p){
    $j++;
    $sql.="SELECT s.`IDSINAPSIS`,s.`ESTIMULOS` FROM `sinapsis` s INNER JOIN `enlace` e ON e.`IDPUBLICACION`='$idpublicacions' WHERE s.`IDUSUARIO`='$idUsuario' and s.`IDSINAPSIS`=e.`IDSINAPSIS` AND s.`ESTIMULOS` LIKE '%$p%'";
    if($p!=end($ps) || $j!=count($ps)){
      $sql.=" UNION ";
    }
  }
  $result=$conn->query($sql);
  if(!$result){trigger_error('Invalid query: '.$conn->error);}
  if($result->num_rows>0){
    while($row=$result->fetch_assoc()){
      array_push($ids,$row['IDSINAPSIS']);
      array_push($keywords,$row['ESTIMULOS']);
    }
  }
  $conn->close();
  $z=0;
  foreach($keywords as $ms){
    $ms=explode(',',$ms);// genero un array con las palabras claves
    foreach($ms as $me){
      #comparar : Se compara los datos en memoria con las sinapsis de la pregunta
      $id=comparar($me,$ps,$ids[$z]);
      #validar : Valida si la respuestas fue valida o no
      if($id!=-1){
        #Recopilar informacion
        array_push($idRecord,$id);
        break;
      }
    }
    $z++;
  }
  return $idRecord;
}
/*comprar Submodulo 2.1*/
function comparar($ms,$ps,$idres){
$memorias=explode(' ',$ms);
//echo '<br>'.implode('|',$memorias);
$c=0;
foreach($ps as $p){
foreach($memorias as $m){
if($m===$p){$c++;}
}
}
//echo '<br>'.$c.'|'.count($memorias);
if($c===count($memorias)){$id=$idres;}
else{$id= -1;}
//echo ' - out:'.$id;
return $id;
}
/*COdificador - Modulo 3*/
function codificador($ids){
include 'conexion.php';
$mns='';
foreach($ids as $id){
$sql="select `INFO` from `sinapsis` where `IDSINAPSIS`='$id' limit 1";
$result=$conn->query($sql);
if($result->num_rows > 0){
while($row = $result->fetch_assoc()){
$mns.=$row['INFO'].' ';
}
}else{
//  echo "0 results";
}
}
$conn->close();
return $mns;
}
/*Eliminar simbolos: Realiza una limpieza de los simbolos de la palabra*/
function eliminar_simbolos($string){
$string = trim($string);
$string = str_replace(
array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
$string
);
$string = str_replace(
array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
$string
);
$string = str_replace(
array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
$string
);
$string = str_replace(
array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
$string
);
$string = str_replace(
array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
$string
);
$string = str_replace(
array('ñ', 'Ñ', 'ç', 'Ç'),
array('n', 'N', 'c', 'C',),
$string
);
$string = str_replace(
array("\\", "¨", "º", "-", "~",
"#", "@", "|", "!", "\"",
"·", "$", "%", "&", "/",
"(", ")", "?", "'", "¡",
"¿", "[", "^", "<code>", "]",
"+", "}", "{", "¨", "´",
">", "< ", ";", ",", ":",
".", " "),
'',$string);
return $string;}
function insertUser($id,$correo,$accesstoken,$refresstoken,$first_name,$last_name,$telppal,$telsecond,$site_id){
include 'conexion.php';
$sql="INSERT INTO `usuario`(`IDUSUARIO`,`CORREO`,`NAME`,`LASTNAME`,`TELPPAL`,`TELSECOND`,`ACCESSTOKEN`,`REFRESTOKEN`,`ULTIMAFECHA`,`PAIS`) VALUES ('$id','$correo','$first_name','$last_name','$telppal','$telsecond','$accesstoken','$refresstoken','2020','$site_id')";
if($conn->query($sql)===TRUE){}else{}$conn->close();}
function userExist($idUsuario, $correo){include 'conexion.php';
$sql="select * from `usuario` where `IDUSUARIO`='$idUsuario' and `CORREO`='$correo' limit 1";
$result=$conn->query($sql);if($result->num_rows>0){$band=1;}else{$band=0;}$conn->close();return $band;}
function deleteUser($idusuario){
include 'conexion.php';$sql="delete from `usuario` where `IDUSUARIO`='$idusuario'";
if($conn->query($sql)===TRUE){}else{}$conn->close();}
function getUsuario($idUsuario){include 'conexion.php';
$sql="select * from `usuario` where `IDUSUARIO`='$idUsuario' limit 1";$result=$conn->query($sql);
if($result->num_rows>0){$row=$result->fetch_assoc();}else{$row=0;}$conn->close();return $row;}
function getCorreo($idUsuario){include 'conexion.php';
$sql="select `CORREO` from `usuario` where `IDUSUARIO`='$idUsuario' limit 1";$result=$conn->query($sql);
if($result->num_rows>0){$row=$result->fetch_assoc();$row=$row['CORREO'];}else{$row=0;}$conn->close();return $row;}
function updateTokens($idusuario,$accesstoken,$refresstoken){include 'conexion.php';
$sql="update `usuario` set `ACCESSTOKEN`='$accesstoken',`REFRESTOKEN`='$refresstoken' where `IDUSUARIO`=$idusuario";
if($conn->query($sql)===TRUE){}else{}$conn->close();}
function getTokens($idUsuario){include 'conexion.php';
$sql="select `ACCESSTOKEN`, `REFRESTOKEN` from `usuario` where `IDUSUARIO`='$idUsuario' limit 1";
$result=$conn->query($sql);
if($result->num_rows>0){$row=$result->fetch_assoc();$accesstoken=$row['ACCESSTOKEN'];$refresstoken=$row['REFRESTOKEN'];
$out=array('ACCESSTOKEN'=>$accesstoken,'REFRESTOKEN'=>$refresstoken);
}else{$out=0;}$conn->close();return $out;}
function getSaludos($idusuario){include 'conexion.php';
$sql="select SALUDO,DESPEDIDA from `usuario` where `IDUSUARIO`='$idusuario' limit 1";$result=$conn->query($sql);
if($result->num_rows>0){$row=$result->fetch_assoc();$saludo=$row['SALUDO'];$despedida=$row['DESPEDIDA'];}else{}
$conn->close();return array('SALUDO'=>$saludo,'DESPEDIDA'=>$despedida);}
function createPublicacion($idUsuario,$codigo,$nombre){include 'conexion.php';
$sql="insert into `publicacion`(`IDUSUARIO`,`CODIGO`,`NOMBRE`,`ESTATUS`) values ('$idUsuario','$codigo','$nombre',1)";
if($conn->query($sql)===TRUE){$last_id=mysqli_insert_id($conn);}else{}$conn->close();return $last_id;}
function publicacionExist($idUsuario,$codigo){include 'conexion.php';
$sql="select * from `publicacion` where `IDUSUARIO`='$idUsuario' and `CODIGO`='$codigo' limit 1";
$result=$conn->query($sql);if($result->num_rows>0){$band=1;}else{$band=0;}$conn->close();return $band;}
function getIdPublicacion($idUsuario,$codigo){include 'conexion.php';
$sql="select * from `publicacion` where `IDUSUARIO`='$idUsuario' and `CODIGO`='$codigo' limit 1";
$result=$conn->query($sql);if($result->num_rows>0){$row = $result->fetch_assoc();$idp=$row['IDPUBLICACION'];
}else{$idp=-1;}$conn->close();return $idp;}
function deletePublicacion($idpublicacion){include 'conexion.php';
$sql="delete from `publicacion` where `IDPUBLICACION`='$idpublicacion'";
if($conn->query($sql) === TRUE){}else{}$conn->close();}
function createInfoId($idInfo,$idUsuario,$info,$keywords,$numPublicaciones){include 'conexion.php';
$sql="insert into `sinapsis`(`IDSINAPSIS`,`IDUSUARIO`,`INFO`,`ESTIMULOS`,`NUMPUBLICACIONES`) values ('$idInfo','$idUsuario','$info','$keywords','$numPublicaciones');";
if(mysqli_query($conn, $sql)){}else{}$conn->close();}
function createInfo($idUsuario,$info,$keywords,$numPublicaciones){include 'conexion.php';
$sql="insert into `sinapsis`(`IDUSUARIO`,`INFO`,`ESTIMULOS`,`NUMPUBLICACIONES`) values ('$idUsuario','$info','$keywords', '$numPublicaciones');";
if(mysqli_query($conn, $sql)){$last_id = mysqli_insert_id($conn);
}else{$last_id=-1;}$conn->close();return $last_id;}
function updateInfo($idInfo, $newinfo, $newkeywords,$numPublicaciones){
include 'conexion.php';
$sql="update `sinapsis` set `INFO`='$newinfo',`ESTIMULOS`='$newkeywords',`NUMPUBLICACIONES`='$numPublicaciones' where `IDSINAPSIS`='$idInfo'";
if($conn->query($sql)===TRUE){}else{}$conn->close();}
function deleteInfo($idInfo,$idUsuario){include 'conexion.php';
$sql="delete from `sinapsis` where `IDSINAPSIS`='$idInfo' and `IDUSUARIO`='$idUsuario'";
if($conn->query($sql)===TRUE){}else{}$conn->close();}
function enlazar($idInfo,$idpublicacion){include 'conexion.php';
$sql="insert into `enlace`(`IDSINAPSIS`,`IDPUBLICACION`) values ('$idInfo','$idpublicacion' )";
if($conn->query($sql)===TRUE){}else{return 1;}$conn->close();return 0;}
function desenlazar($idInfo,$idpublicacion){include 'conexion.php';
$sql="delete from `enlace` where `IDSINAPSIS`='$idInfo' and `IDPUBLICACION`='$idpublicacion'";
if($conn->query($sql)===TRUE){}else{}$conn->close();}
function suscribir($idusuario,$fechaR,$fechaV,$tipo,$estatus){include 'conexion.php';
$sql="INSERT INTO `suscripcion` (`IDSUSCRIPCION`,`IDUSUARIO`,`CAPITAL`,`FECHAREGISTRO`,`FECHAVENCIMIENTO`,`TIPOSUSCRIPCION`,`ESTATUS`) VALUES (NULL,'$idusuario','0.0','$fechaR','$fechaV','$tipo','$estatus');";
if($conn->query($sql)===TRUE){}else{}
$sql="INSERT INTO `servicios` (`IDSERVICIOS`,`IDUSUARIO`,`IDPLAN`) VALUES (NULL,'$idusuario','-1');";
if($conn->query($sql)===TRUE){}$conn->close();}
function updateSuscripcion($idUsuario,$newFechaV,$newEstatus,$newTipo){}
function cambioPlanSuscripcion($idUsuario,$newTipo){suscripcion($idUsuario,'','',$newTipo,1);}
function cambioEstadoSuscripcion($idUsuario,$newEstatus){suscripcion($idUsuario,'',$newEstatus,'',2);}
function CambioEstadoFechaVSuscripcion($idUsuario,$newFechaV,$newEstatus){suscripcion($idUsuario,$newFechaV,$newEstatus,'',3);}
function suscripcion($idUsuario,$newFechaV,$newEstatus,$newTipo,$uso){include 'conexion.php';switch($uso){case 0:
$sql="update `suscripcion` set `FECHAVENCIMIENTO`='$newFechaV',`TIPOSUSCRIPCION`='$newTipo',`ESTATUS`='$newEstatus' where `IDUSUARIO`='$idUsuario'";
break;case 1:
$sql="update `suscripcion` set `TIPOSUSCRIPCION`='$newTipo' where  `IDUSUARIO`='$idUsuario'";
break;case 2:
$sql="update `suscripcion` set `ESTATUS`='$newEstatus' where `IDUSUARIO`='$idUsuario'";
break;case 3:
$sql="update `suscripcion` set `FECHAVENCIMIENTO`='$newFechaV',`ESTATUS`='$newEstatus' where `IDUSUARIO`='$idUsuario'";
break;default:
$sql="update `suscripcion` set `FECHAVENCIMIENTO`='$newFechaV',`TIPOSUSCRIPCION`='$newTipo',`ESTATUS`='$estatus' where `IDUSUARIO`='$idUsuario'";
break;}
if($conn->query($sql) === TRUE){echo "Record updated successfully";}else{ echo "Error updating record: " . $conn->error;}
$conn->close();
}
?>
