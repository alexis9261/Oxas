<?php
set_time_limit(0);session_start();
$_SESSION['masivas_exito']=array();$_SESSION['masivas_fallo']=array();
require '../common/conexion.php';require '../common/take_at.php'; require '../common/account-off.php';require '../common/callback.php';require '../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php';
$columnas=array('','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
$archivo=$_SESSION['archivo'];
$objPHPExcel=PHPEXCEL_IOFactory::load($archivo);
$numHojas=$objPHPExcel->getSheetCount();
$condition='new';$buying_mode='buy_it_now';$exposicion='gold_special';$currency='VES';
$image='https://oxas.tech/img/image.png';$url='https://api.mercadolibre.com/pictures?access_token='.$AccessToken;
$post=array('source'=>$image);$post=json_encode($post);
$ch=curl_init();curl_setopt($ch,CURLOPT_URL,$url);curl_setopt($ch,CURLOPT_POST,1);curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,$post);$result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);
$id_imagen=$consulta->id;
for($i=0;$i<$numHojas;$i++){
$objPHPExcel->setActiveSheetIndex($i);
$numRows=$objPHPExcel->setActiveSheetIndex($i)->getHighestRow();
$lastCol=$objPHPExcel->setActiveSheetIndex($i)->getHighestColumn();
$numCols=array_search($lastCol,$columnas);
for($f=2;$f<=$numRows;$f++){
$variaciones=array();$atributtes=array();
$title=$objPHPExcel->getActiveSheet()->getCell('A'.$f)->getCalculatedValue();
if(!empty($title)){
$title_aux=urlencode($title);
$categoria=$objPHPExcel->getActiveSheet()->getTitle($i);
switch($categoria){case 'Accesorios para Vehículos':$category=$siteId."1747";break;
case 'Animales y Mascotas':$category=$siteId."1071";break;case 'Bebés':$category=$siteId."1384";break;
case 'Cámaras y Accesorios':$category=$siteId.'1039';break;case 'Celulares y Teléfonos':$category=$siteId.'1051';break;
case 'Coleccionables y Hobbies':$category=$siteId.'1798';break;case 'Computación':$category=$siteId.'1648';break;
case 'Consolas y Videojuegos':$category=$siteId.'1144';break;case 'Deportes y Fitness':$category=$siteId.'1276';break;
case 'Electrodomésticos':$category=$siteId.'5726';break;case 'Electrónica, Audio y Video':$category=$siteId.'1000';break;
case 'Estética y Belleza':$category=$siteId.'1246';break;case 'Hogar y Muebles':$category=$siteId.'1574';break;
case 'Industrias':$category=$siteId.'1499';break;case 'Instrumentos Musicales':$category=$siteId.'1182';break;
case 'Juegos y Juguetes':$category=$siteId.'1132';break;case 'Libros, Música y Películas':$category=$siteId.'3025';break;
case 'Relojes, Joyas y Bisutería':$category=$siteId.'3937';break;case 'Ropa, Zapatos y Accesorios':$category=$siteId.'1430';break;
case 'Otras Categorías':$category=$siteId.'1953';break;
}
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$siteId/category_predictor/predict?title=$title_aux&seller_id=$id_user&category_from=$category");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);$result=json_decode($result);
$category_root=$result->id;
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/categories/'.$category_root.'/attributes');
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$atributos=json_decode($result);
foreach($atributos as $valor){
$atrib=$valor->name;$id_atrib=$valor->id;$tags=$valor->tags;
if(isset($tags->catalog_required) && $tags->catalog_required==true){
for($x=2;$x<($numCols-2);$x++){
$name=$objPHPExcel->getActiveSheet()->getCell($columnas[$x].'1')->getCalculatedValue();
if(strpos($name,'(atributo)')==true && substr($name,0,-11)==$atrib){
$atributo=$objPHPExcel->getActiveSheet()->getCell($columnas[$x].$f)->getCalculatedValue();
$string="{\"id\":\"".$id_atrib."\", \"value_name\":\"".$atributo."\"}";
array_push($atributtes,$string);break;
}
}
}elseif(isset($tags->allow_variations) && $tags->allow_variations==true){
for($x=2;$x<($numCols-2);$x++){
$name=$objPHPExcel->getActiveSheet()->getCell($columnas[$x].'1')->getCalculatedValue();
if(strpos($name,'(variante)')==true && substr($name,0,-11)==$atrib){
$variacion=$objPHPExcel->getActiveSheet()->getCell($columnas[$x].$f)->getCalculatedValue();
$string="{\"name\":\"".$atrib."\", \"value_name\":\"".$variacion."\"}";
array_push($variaciones,$string);break;
}
}
}
}
for($c=2;$c<=$numCols;$c++){
$name=$objPHPExcel->getActiveSheet()->getCell($columnas[$c].'1')->getCalculatedValue();
if($name=='Descripción'){$description=$objPHPExcel->getActiveSheet()->getCell($columnas[$c].$f)->getCalculatedValue();
}elseif($name=='Precio'){$price=$objPHPExcel->getActiveSheet()->getCell($columnas[$c].$f)->getCalculatedValue();
}elseif($name=='Cantidad'){$cantidad=$objPHPExcel->getActiveSheet()->getCell($columnas[$c].$f)->getCalculatedValue();}
}
$description=str_replace(array("\n","\r","\n\r","\r\n"),array('\\n','\\r','\\n\\r','\\r\\n'),$description);
$atributos_totales=implode(",",$atributtes);$variaciones_totales=implode(",",$variaciones);
$ch=curl_init();curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
if(count($variaciones)>0){
curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"title\":\"$title\",\"category_id\":\"$category_root\",\"price\":$price,\"currency_id\":\"$currency\",\"available_quantity\":$cantidad,\"buying_mode\":\"$buying_mode\",\"listing_type_id\":\"$exposicion\",\"description\":{\"plain_text\":\"$description\"},\"condition\":\"$condition\",
\"attributes\":[$atributos_totales],\"variations\":[{\"available_quantity\":$cantidad,\"price\":$price,\"attribute_combinations\":[$variaciones_totales],\"picture_ids\":[\"$id_imagen\"]}]}");
}else{curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"title\":\"$title\",\"category_id\":\"$category_root\",\"price\":$price,\"currency_id\":\"$currency\",\"available_quantity\":$cantidad,\"buying_mode\":\"$buying_mode\",\"listing_type_id\":\"$exposicion\",\"description\":{\"plain_text\":\"$description\"},\"condition\":\"$condition\",\"pictures\":[{\"source\":\"$image\"}]}");}
curl_setopt($ch,CURLOPT_POST,1);$headers=array();$headers[]='Content-Type: application/json';
curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);$result=curl_exec($ch);curl_close($ch);$result=json_decode($result);
if(isset($result->id)){array_push($_SESSION['masivas_exito'],$categoria.'@'.$title.'@'.$cantidad.'@'.$price);
}else{array_push($_SESSION['masivas_fallo'],$categoria.'@'.$title.'@'.$cantidad.'@'.$price);}
}
}
}
unlink("$archivo");
if(count($_SESSION['masivas_exito'])>0 || count($_SESSION['masivas_fallo'])>0){header ('Location: index.php?masive=1');
}else{header ('Location: index.php?masive=2');}
