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
array("\\","¨","º","-","~",
"#","@","|","!","\"",
"·","$","%","&","/",
"(",")","?","'","¡",
"¿","[","^","<code>","]",
"+","}","{","¨","´",
">","<",";",",",":",
"."," "),
'',
$string
);
return $string ;}
/******************************************************/
/*****Funciones para el manejo en base de datos*******/
/******************************************************/
#Funcion para añadir un usuario - checked
function insertUser($id,$correo,$accesstoken,$refresstoken,$first_name,$last_name,$telppal,$telsecond,$site_id){
include 'conexion.php';
$sql="INSERT INTO `usuario`(`IDUSUARIO`,`CORREO`,`NAME`,`LASTNAME`,`TELPPAL`,`TELSECOND`,`ACCESSTOKEN`,`REFRESTOKEN`,`ULTIMAFECHA`,`PAIS`) VALUES ('$id','$correo','$first_name','$last_name','$telppal','$telsecond','$accesstoken','$refresstoken','2020','$site_id')";
if($conn->query($sql)===TRUE){
#  echo "New record created successfully";
}else{
#  echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
}
function userExist($idUsuario, $correo){
#configuracion de BD
include 'conexion.php';
$sql="select * from `usuario` where `IDUSUARIO`='$idUsuario' and `CORREO`='$correo' limit 1";
$result=$conn->query($sql);
if($result->num_rows>0){$band=1;}else{$band=0;}
$conn->close();
return $band;
}
#funcion para eliminar un usuario - checked
function deleteUser($idusuario){
#configuracion de BD
include 'conexion.php';
$sql="delete from `usuario` where `IDUSUARIO`='$idusuario'";
if($conn->query($sql)===TRUE){
//echo "Record deleted successfully";
}else{
# echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
}
#funcion los datos de Usuario - checked
/*
Devuelve un array(IDUSUARIO=>, CORREO=>, SALUDO=>, DESPEDIDA=>, ACCESSTOKEN=>, REFRESTOKEN=>) si el id del usuario es valido, sino retorna 0
*/
function getUsuario($idUsuario){
include 'conexion.php';
$sql="select * from `usuario` where `IDUSUARIO`='$idUsuario' limit 1";
$result=$conn->query($sql);
if($result->num_rows>0){
$row=$result->fetch_assoc();
}else{
#echo "0 results";
$row=0;
}
$conn->close();
return $row;
}
#conseguir correo  - checked
/*del usuario retorna un string con el correo si el id insertado es correcto, sino retorna 0.*/
function getCorreo($idUsuario){
include 'conexion.php';
$sql="select `CORREO` from `usuario` where `IDUSUARIO`='$idUsuario' limit 1";
$result=$conn->query($sql);
if($result->num_rows>0){
$row=$result->fetch_assoc();
$row=$row['CORREO'];
}else{
# echo "0 results";
$row=0;
}
$conn->close();
return $row;
}
#actualizar tokens
function updateTokens($idusuario,$accesstoken,$refresstoken){
include 'conexion.php';
$sql="update `usuario` set `ACCESSTOKEN`='$accesstoken',`REFRESTOKEN`='$refresstoken' where `IDUSUARIO`=$idusuario";
if($conn->query($sql)===TRUE){
//echo "Record updated successfully";
}else{
//echo "Error updating record: " . $conn->error;
}
$conn->close();
}
#consigue el Token -checked
/*
Retorna un arreglo con accessToken y RefressToken si el Id es correcto, sino retorna 0.
Salidad del Arreglo:
Array(ACCESSTOKEN,REFRESTOKEN)
Ejemplo:
$tokes=getTokens('2');
if ($tokes!=0){
$at=$tokes['ACCESSTOKEN'];
$rt=$tokes['REFRESTOKEN'];
}
*/
function getTokens($idUsuario){
include 'conexion.php';
$sql = "select `ACCESSTOKEN`, `REFRESTOKEN` from `usuario` where `IDUSUARIO`='$idUsuario' limit 1";
$result = $conn->query($sql);
if($result->num_rows > 0){
$row = $result->fetch_assoc();
$accesstoken=$row['ACCESSTOKEN'];
$refresstoken=$row['REFRESTOKEN'];
$out=array('ACCESSTOKEN'=>$accesstoken,'REFRESTOKEN'=>$refresstoken );
}else{
#  echo "0 results";
$out=0;
}
$conn->close();
return $out;
}
#Leer Saludo y Despedida - checked
/*Return: Array('SALUDO', 'DESPEDIDA')
ejemplo:
$RES= getsaludos('2');
foreach($RES as $r){
echo $r.'<br>';
}
*/
function getSaludos($idusuario){
include 'conexion.php';
$sql="select SALUDO,DESPEDIDA from `usuario` where `IDUSUARIO`='$idusuario' limit 1";
$result = $conn->query($sql);
if($result->num_rows > 0){
$row = $result->fetch_assoc();
$saludo=$row['SALUDO'];
$despedida=$row['DESPEDIDA'];
}else{
#  echo "0 results";
}
$conn->close();
return array('SALUDO'=>$saludo,'DESPEDIDA'=>$despedida);
}
#Guardar publicacion en base de datos  -checked
function createPublicacion($idUsuario,$codigo,$nombre){
include 'conexion.php';
$sql="insert into `publicacion`(`IDUSUARIO`,`CODIGO`,`NOMBRE`,`ESTATUS`) values ('$idUsuario','$codigo','$nombre',1)";
if($conn->query($sql)===TRUE){
$last_id=mysqli_insert_id($conn);
#  echo "New record created successfully";
}else{
#  echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
return $last_id;
}
#existe publicacion
function publicacionExist($idUsuario,$codigo){
include 'conexion.php';
$sql = "select * from `publicacion` where `IDUSUARIO`='$idUsuario' and `CODIGO`='$codigo' limit 1";
$result = $conn->query($sql);
if($result->num_rows > 0){
$band =1;
}else{
$band=0;
}
$conn->close();
return $band;
}
#consigue el IDpublicacion
function getIdPublicacion($idUsuario,$codigo){
include 'conexion.php';
$sql = "select * from `publicacion` where `IDUSUARIO`='$idUsuario' and `CODIGO`='$codigo' limit 1";
$result = $conn->query($sql);
if($result->num_rows > 0){
$row = $result->fetch_assoc();
$idp=$row['IDPUBLICACION'];
}else{
$idp=-1;
#  echo "0 results";
}
$conn->close();
return $idp;
}
#eliminar publicacion - checked
function deletePublicacion($idpublicacion){
include 'conexion.php';
$sql="delete from `publicacion` where `IDPUBLICACION`='$idpublicacion'";
if($conn->query($sql) === TRUE){
//echo "Record deleted successfully";
}else{
#echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
}
#Crear informacion con id
#Registrar informacion  mediante las sinapsis -checked
function createInfoId($idInfo,$idUsuario,$info,$keywords,$numPublicaciones){
include 'conexion.php';
#insertar en sinapsis
$sql="insert into `sinapsis`(`IDSINAPSIS`,`IDUSUARIO`,`INFO`,`ESTIMULOS`,`NUMPUBLICACIONES`) values ('$idInfo','$idUsuario','$info','$keywords','$numPublicaciones');";
if(mysqli_query($conn, $sql)){
#echo "New record created successfully<br>";
}else{
#echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
}
#Registrar informacion  mediante las sinapsis -checked
function createInfo($idUsuario,$info,$keywords,$numPublicaciones){
include 'conexion.php';
#insertar en sinapsis
$sql="insert into `sinapsis`(`IDUSUARIO`,`INFO`,`ESTIMULOS`,`NUMPUBLICACIONES`) values ('$idUsuario','$info','$keywords', '$numPublicaciones');";
if(mysqli_query($conn, $sql)){
$last_id = mysqli_insert_id($conn);
//echo "New record created successfully<br>";
}else{
//echo "Error: " . $sql . "<br>" . $conn->error;
$last_id=-1;
}
$conn->close();
return $last_id;
}
#Modificar  informacion mediante sinapsis -checked
function updateInfo($idInfo, $newinfo, $newkeywords,$numPublicaciones){
include 'conexion.php';
#update tabla informacion
$sql = "update `sinapsis` set `INFO`='$newinfo',`ESTIMULOS`='$newkeywords',`NUMPUBLICACIONES`='$numPublicaciones' where `IDSINAPSIS`='$idInfo'";
if($conn->query($sql) === TRUE){
#  echo "Record updated successfully";
}else{
#  echo "Error updating record: " . $conn->error;
}
$conn->close();
}
#eliminar informacion mediante sinapsis - checked
function deleteInfo($idInfo,$idUsuario){
include 'conexion.php';
$sql="delete from `sinapsis` where `IDSINAPSIS`='$idInfo' and `IDUSUARIO`='$idUsuario'";
if($conn->query($sql) === TRUE){
//  echo "Record deleted successfully";
}else{
#  echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
}
#enlazar la informacion de respuesta con las publicaciones - checkeds
/*
0 -- successfully
1 -- Error
*/
function enlazar($idInfo,$idpublicacion){
include 'conexion.php';
#crear enlace
$sql="insert into `enlace`(`IDSINAPSIS`,`IDPUBLICACION`) values ('$idInfo','$idpublicacion' )";
if($conn->query($sql) === TRUE){
//echo "Linked successfully";
}else{
//echo "Error: " . $sql . "<br>" . $conn->error;
return 1;
}
$conn->close();
return 0;
}
#desenlazar la informacion de respuesta con las publicaciones - checked
function desenlazar($idInfo,$idpublicacion){
include 'conexion.php';
$sql="delete from `enlace` where `IDSINAPSIS`='$idInfo' and `IDPUBLICACION`='$idpublicacion'";
if($conn->query($sql) === TRUE){
#  echo "Unlinked successfully";
}else{
#  echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
}
#desenlazar todas las publicacion asociadas a una
/****************************************************************
*******************Suscripcion del usuario******************
*****************************************************************/
#suscripcion - checked
/*
Ex: suscribir('1', '2018-07-17', '1','1');
*/
function suscribir($idusuario,$fechaR,$fechaV,$tipo,$estatus){
include 'conexion.php';
#suscribir con un insertar
$sql="INSERT INTO `suscripcion` (`IDSUSCRIPCION`,`IDUSUARIO`,`CAPITAL`,`FECHAREGISTRO`,`FECHAVENCIMIENTO`,`TIPOSUSCRIPCION`,`ESTATUS`) VALUES (NULL,'$idusuario','0.0','$fechaR','$fechaV','$tipo','$estatus');";
if($conn->query($sql) === TRUE){}else{}
$sql="INSERT INTO `servicios` (`IDSERVICIOS`,`IDUSUARIO`,`IDPLAN`) VALUES (NULL,'$idusuario','-1');";
if($conn->query($sql)===TRUE){}
$conn->close();
}
#actualizar suscripcion - checked
function updateSuscripcion($idUsuario,$newFechaV, $newEstatus, $newTipo){
/**
Mejorar  - comando suscripcion fue modificado
**/
//  suscripcion($idUsuario,$newFechaV, $newEstatus, $newTipo, 0);
}
#cambiar de plan - checked
function cambioPlanSuscripcion($idUsuario,$newTipo){
suscripcion($idUsuario,'', '', $newTipo,1);
}
#cambiar de estatus la suscripcion - checked
function cambioEstadoSuscripcion($idUsuario, $newEstatus){
suscripcion($idUsuario, '', $newEstatus, '', 2);
}
#cambiar de fecha y Estatus de la susbricicon - checked
function CambioEstadoFechaVSuscripcion($idUsuario,$newFechaV,$newEstatus){
suscripcion($idUsuario, $newFechaV, $newEstatus, '', 3);
}
#Funcion de suscripcion generica -checked - No Usar
/*
Uso es una variable que permite usar el codigo para otras funciones. Si la vas a actualizar todos los parametro que alli se presentan colola: $uso=0
$uso=1: Modifica el tipo de suscripcion
$uso=2: Modifica el estatus
$uso=3: Modifica la fecha de vencimiento y el estatus
*/
function suscripcion($idUsuario,$newFechaV, $newEstatus, $newTipo, $uso){
include 'conexion.php';
switch($uso){
case 0:
#modificar toda las variables
$sql = "update `suscripcion` set `FECHAVENCIMIENTO`='$newFechaV',`TIPOSUSCRIPCION`='$newTipo',`ESTATUS`='$newEstatus' where `IDUSUARIO`='$idUsuario'";
break;
case 1:
#modifica solo tipo de suscripcion
$sql = "update `suscripcion` set `TIPOSUSCRIPCION`='$newTipo' where  `IDUSUARIO`='$idUsuario'";
break;
case 2:
#modifica el estatus
$sql = "update `suscripcion` set `ESTATUS`='$newEstatus' where `IDUSUARIO`='$idUsuario'";
break;
case 3:
#modifica fecha de vencimiento y estatus
$sql = "update `suscripcion` set `FECHAVENCIMIENTO`='$newFechaV',`ESTATUS`='$newEstatus' where `IDUSUARIO`='$idUsuario'";
break;
default:
#modificar toda las variables
$sql = "update `suscripcion` set `FECHAVENCIMIENTO`='$newFechaV',`TIPOSUSCRIPCION`='$newTipo',`ESTATUS`='$estatus' where `IDUSUARIO`='$idUsuario'";
break;
}
if($conn->query($sql) === TRUE){
echo "Record updated successfully";
}else{ echo "Error updating record: " . $conn->error;}
$conn->close();
}
?>
