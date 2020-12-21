<?php
set_time_limit(0);$tiempo_inicio=microtime(true);session_start();
require 'common/meli.php';require 'common/configApp.php';require 'common/callback.php';require 'common/conexion.php';require 'common/take_at.php';require 'Oxa/Funciones.php';
$id_sellers=array();
$id_categ_principal="MLV1430";
$nombre_categ_princ="Ropa, Zapatos y Accesorios";
//$ids_categorias_excluyentes=array("MLV3377","MLV1870","MLV1691","MLV1649","MLV9533","MLV2141","MLV1652","MLV1694","MLV1656","MLV1714","MLV1651","MLV5027","MLV1712","MLV1693","MLV1657","MLV1670","MLV1709","MLV1700","MLV1723","MLV117283","MLV1658","MLV1692","MLV1912");
//Ropa
//$ids_categorias_excluyentes=array("MLV8820","MLV116898","MLV116867","MLV26400","MLV116909","MLV116859","MLV3123","MLV24787","MLV116862","MLV116883","MLV116856","MLV116849","MLV116864","MLV24621","MLV116824","MLV116853","MLV116841","MLV116827","MLV5208","MLV9865","MLV1911");
$ids_categorias_excluyentes=array("MLV116867","MLV26400","MLV116909","MLV116859","MLV3123","MLV24787","MLV116862","MLV116883","MLV116856","MLV116849","MLV116864","MLV24621","MLV116824","MLV116853","MLV116841","MLV116827","MLV5208","MLV9865","MLV1911");
$ids_categorias_hijos_excluyentes=array();
$array_estados=array();
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/categories/$id_categ_principal");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);
curl_reset($ch);
$consulta=json_decode($result);
$categoriasHijos=$consulta->children_categories;
function imprimirDatos($conn,$id_categ,$cantidad_publicac,$id_estado,$id_condicion,$id_precio,$id_sellers,$id_book,$ch,$nombre_categ_princ,$AccessToken,$callback){
for($c=0;$c<$cantidad_publicac;$c++){
$offset=50*$c;
if($id_estado!=""){
if($id_condicion!=""){
if($id_precio!=""){
if($id_book!=""){
$string="state=$id_estado&condition=$id_condicion&price=$id_precio&BOOK_GENRE=$id_book&offset=$offset&callback=$callback";
}else{$string="state=$id_estado&condition=$id_condicion&price=$id_precio&offset=$offset&callback=$callback";}
}else{$string="state=$id_estado&condition=$id_condicion&offset=$offset&callback=$callback";}
}else{$string="state=$id_estado&offset=$offset&callback=$callback";}
}else{$string="offset=$offset&callback=$callback";}
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?category=$id_categ&access_token=$AccessToken&$string");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$publicaciones_result=$r->results;
foreach($publicaciones_result as $valor){
$id_seller=$valor->seller->id;
$estado=$valor->address->state_name;
$ciudad=$valor->address->city_name;
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/users/$id_seller?callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$ventas_totales=$r->seller_reputation->transactions->total;
$ventas_concretadas=$r->seller_reputation->transactions->completed;
$reputacion=$r->seller_reputation->level_id;
if(!in_array($id_seller,$id_sellers)){
array_push($id_sellers,$id_seller);
$mercadoLider=$r->seller_reputation->power_seller_status;
$fecharegistro=$r->registration_date;
$perfilML=$r->permalink;
$nickname=urlencode($r->nickname);
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?nickname=$nickname&callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$publicaciones=$r->paging->total;
$sql="INSERT INTO sellers (CATEGORIA,IDUSER,NICKNAME,FECHAINICIOML,TRANSACCIONES,CONCRETADAS,REPUTACION,MLIDER,PUBLICACIONES,ESTADO,CIUDAD,PERFILML) VALUES ('$nombre_categ_princ','$id_seller','$nickname','$fecharegistro','$ventas_totales','$ventas_concretadas','$reputacion','$mercadoLider','$publicaciones','$estado','$ciudad','$perfilML');";
if($conn->query($sql)===TRUE){}else{echo "$id_seller no se registro <br>";}
}
}
}
}
foreach($categoriasHijos as $key){
$id_categ=$key->id;
if(!in_array($id_categ,$ids_categorias_excluyentes)){
$nombre_categ=$key->name;
$total_items=$key->total_items_in_this_category;
if($total_items>10000){
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/categories/$id_categ?callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);$length=strlen($result)-6;
$string=substr($result,4,$length);$var=json_decode($string);$r=$var[2];
$categoriasHijosHijos=$r->children_categories;
if(count($categoriasHijosHijos)>0){
foreach($categoriasHijosHijos as $child){
$id_categ=$child->id;
if(!in_array($id_categ,$ids_categorias_hijos_excluyentes)){
$total_items_child=$child->total_items_in_this_category;
if($total_items_child>10000){
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/categories/$id_categ?callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$categoriasHijosHijosHijos=$r->children_categories;
if(count($categoriasHijosHijosHijos)>0){
foreach($categoriasHijosHijosHijos as $child2){
$id_categ=$child2->id;
$namechild2=$child2->name;
$total_items_child_child=$child2->total_items_in_this_category;
if($total_items>10000){
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?category=$id_categ?callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$array_filtros=$r->available_filters;
foreach($array_filtros as $filtros){
if($filtros->id=="state"){
$array_estados=$filtros->values;
}else if($filtros->id=="price"){
$array_precio=$filtros->values;
}else if($filtros->id=="condition"){$array_condicion=$filtros->values;}
}
foreach($array_estados as $estado){
$id_estado=$estado->id;
if(!in_array($id_estado,$array_estados)){
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?category=$id_categ&state=$id_estado&callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$total_items_estado=$r->paging->total;
if($total_items_estado>10000){
foreach($array_condicion as $condicion){
$id_condicion=$condicion->id;
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?category=$id_categ&state=$id_estado&condition=$id_condicion&callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$total_items_estado_condicion=$r->paging->total;
if($total_items_estado_condicion>10000){
foreach($array_precio as $precio){
$id_precio=$precio->id;
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?category=$id_categ&state=$id_estado&condition=$id_condicion&price=$id_precio&callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$total_items_estado_condicion_precio=$r->paging->total;
if($total_items_estado_condicion_precio>10000){
}else{
$cantidad_publicac=ceil($total_items_estado_condicion_precio/50);
imprimirDatos($conn,$id_categ,$cantidad_publicac,$id_estado,$id_condicion,$id_precio,$id_sellers,"",$ch,$nombre_categ_princ,$AccessToken,$callback);
}
}
}else{
$cantidad_publicac=ceil($total_items_estado_condicion/50);
if(!isset($id_precio)){$id_precio="";}
imprimirDatos($conn,$id_categ,$cantidad_publicac,$id_estado,$id_condicion,$id_precio,$id_sellers,"",$ch,$nombre_categ_princ,$AccessToken,$callback);
}
}
}else{
$cantidad_publicac=ceil($total_items_estado/50);
if(!isset($id_precio)){$id_precio="";}if(!isset($id_condicion)){$id_condicion="";}
imprimirDatos($conn,$id_categ,$cantidad_publicac,$id_estado,$id_condicion,$id_precio,$id_sellers,"",$ch,$nombre_categ_princ,$AccessToken,$callback);
}
}
}
}else{
$cantidad_publicac=ceil($total_items_child_child/50);
if(!isset($id_estado)){$id_estado="";}if(!isset($id_precio)){$id_precio="";}if(!isset($id_condicion)){$id_condicion="";}
imprimirDatos($conn,$id_categ,$cantidad_publicac,$id_estado,$id_condicion,$id_precio,$id_sellers,"",$ch,$nombre_categ_princ,$AccessToken,$callback);
}
}
}else{
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?category=$id_categ&callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$array_filtros=$r->available_filters;
foreach($array_filtros as $filtros){
if($filtros->id=="state"){
$array_estados=$filtros->values;
}else if($filtros->id=="price"){
$array_precio=$filtros->values;
}else if($filtros->id=="condition"){$array_condicion=$filtros->values;}
}
foreach($array_estados as $estado){
$id_estado=$estado->id;
if(!in_array($id_estado,$array_estados)){
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?category=$id_categ&state=$id_estado&callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$total_items_estado=$r->paging->total;
if($total_items_estado>10000){
foreach($array_condicion as $condicion){
$id_condicion=$condicion->id;
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?category=$id_categ&state=$id_estado&condition=$id_condicion&callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$total_items_estado_condicion=$r->paging->total;
if($total_items_estado_condicion>10000){
foreach($array_precio as $precio){
$id_precio=$precio->id;
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?category=$id_categ&state=$id_estado&condition=$id_condicion&price=$id_precio&callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$total_items_estado_condicion_precio=$r->paging->total;
if($total_items_estado_condicion_precio>10000){
}else{
$cantidad_publicac=ceil($total_items_estado_condicion_precio/50);
imprimirDatos($conn,$id_categ,$cantidad_publicac,$id_estado,$id_condicion,$id_precio,$id_sellers,"",$ch,$nombre_categ_princ,$AccessToken,$callback);
}
}
}else{
$cantidad_publicac=ceil($total_items_estado_condicion/50);
if(!isset($id_precio)){$id_precio="";}
imprimirDatos($conn,$id_categ,$cantidad_publicac,$id_estado,$id_condicion,$id_precio,$id_sellers,"",$ch,$nombre_categ_princ,$AccessToken,$callback);
}
}
}else{
$cantidad_publicac=ceil($total_items_estado/50);
if(!isset($id_precio)){$id_precio="";}if(!isset($id_condicion)){$id_condicion="";}
imprimirDatos($conn,$id_categ,$cantidad_publicac,$id_estado,$id_condicion,$id_precio,$id_sellers,"",$ch,$nombre_categ_princ,$AccessToken,$callback);
}
}
}
}
}else{
$cantidad_publicac=ceil($total_items_child/50);
imprimirDatos($conn,$id_categ,$cantidad_publicac,$id_estado,$id_condicion,$id_precio,$id_sellers,"",$ch,$nombre_categ_princ,$AccessToken,$callback);
}
}
}
}else{
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?category=$id_categ&callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$array_filtros=$r->available_filters;
foreach($array_filtros as $filtros){
if($filtros->id=="state"){
$array_estados=$filtros->values;
}else if($filtros->id=="price"){
$array_precio=$filtros->values;
}else if($filtros->id=="condition"){$array_condicion=$filtros->values;}
}
foreach($array_estados as $estado){
$id_estado=$estado->id;
if(!in_array($id_estado,$array_estados)){
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?category=$id_categ&state=$id_estado&callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$total_items_estado=$r->paging->total;
if($total_items_estado>10000){
foreach($array_condicion as $condicion){
$id_condicion=$condicion->id;
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?category=$id_categ&state=$id_estado&condition=$id_condicion&callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$total_items_estado_condicion=$r->paging->total;
if($total_items_estado_condicion>10000){
foreach($array_precio as $precio){
$id_precio=$precio->id;
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?category=$id_categ&state=$id_estado&condition=$id_condicion&price=$id_precio&callback=$callback");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$string=substr($result,4,strlen($result)-6);$var=json_decode($string);$r=$var[2];
$total_items_estado_condicion_precio=$r->paging->total;
if($total_items_estado_condicion_precio>10000){
}else{
$cantidad_publicac=ceil($total_items_estado_condicion_precio/50);
imprimirDatos($conn,$id_categ,$cantidad_publicac,$id_estado,$id_condicion,$id_precio,$id_sellers,"",$ch,$nombre_categ_princ,$AccessToken,$callback);
}
}
}else{
$cantidad_publicac=ceil($total_items_estado_condicion/50);
if(!isset($id_precio)){$id_precio="";}
imprimirDatos($conn,$id_categ,$cantidad_publicac,$id_estado,$id_condicion,$id_precio,$id_sellers,"",$ch,$nombre_categ_princ,$AccessToken,$callback);
}
}
}else{
$cantidad_publicac=ceil($total_items_estado/50);
if(!isset($id_precio)){$id_precio="";}if(!isset($id_condicion)){$id_condicion="";}
imprimirDatos($conn,$id_categ,$cantidad_publicac,$id_estado,$id_condicion,$id_precio,$id_sellers,"",$ch,$nombre_categ_princ,$AccessToken,$callback);
}
}
}
}
}else{
$cantidad_publicac=ceil($total_items/50);
imprimirDatos($conn,$id_categ,$cantidad_publicac,$id_estado,$id_condicion,$id_precio,$id_sellers,"",$ch,$nombre_categ_princ,$AccessToken,$callback);
}
}
}
$tiempo_fin=microtime(true);$segundos=$tiempo_fin - $tiempo_inicio;$minutos=$segundos/60;echo "$minutos minutos<br>";
curl_close($ch);
?>
