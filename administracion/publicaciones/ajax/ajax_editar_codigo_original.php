<?php
require '../../common/conexion.php';
$return='';
if(isset($_GET['codigo'],$_GET['codigoOrig'])){
  $codigo=$_GET['codigo'];$codigoOrig=$_GET['codigoOrig'];
  $sql="UPDATE publicacion SET CODIGOORIGINAL='$codigoOrig' WHERE CODIGO='$codigo';";
  if($conn->query($sql)===TRUE){
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items/$codigoOrig");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  $result=curl_exec($ch);
  curl_close($ch);
  $var=json_decode($result);
  if(!isset($var->error)){
    $url=$var->permalink;
    $return=$url;
  }else{$return="NoExistPub";}
  }else{$return="No";}
}
echo $return;
?>
