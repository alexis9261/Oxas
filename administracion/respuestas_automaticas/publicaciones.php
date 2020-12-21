<?php
session_start();
require '../common/conexion.php';require '../common/take_at.php'; require '../common/account-off.php';require '../common/callback.php';
$publicaciones_enlace=array();$str_public='';
$sql_enlace="SELECT p.CODIGO FROM enlace e  RIGHT JOIN publicacion p ON p.IDPUBLICACION=e.IDPUBLICACION WHERE IDSINAPSIS=".$_GET['id_sinapsis']." LIMIT 20";
$result_enlace=$conn->query($sql_enlace);
if(!$result_enlace){trigger_error('Invalid query: '.$conn->error);}
if($result_enlace->num_rows>0){
$band=false;
while($row_enlace=$result_enlace->fetch_assoc()){
array_push($publicaciones_enlace, $row_enlace['CODIGO']);
if(!$band){$str_public=$str_public.$row_enlace['CODIGO'];$band=true;}else{$str_public=$str_public.','.$row_enlace['CODIGO'];}
}
}
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items?ids='.$str_public.'&attributes=title,permalink,thumbnail,price&access_token='.$AccessToken.'&callback='.$callback);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$length=strlen($result)-6;
$string=substr($result,4,$length);$var=json_decode($string);$consulta=$var[2];$auxiliar=json_encode($consulta);echo $auxiliar;
?>
