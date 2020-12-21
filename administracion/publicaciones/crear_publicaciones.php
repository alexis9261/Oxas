<?php
session_start();
$variaciones=count($_SESSION['array_variaciones']);$atributos=count($_SESSION['array_atributos']);
require '../common/conexion.php';require '../common/take_at.php';require '../common/account-off.php';
$title=$_POST['title'];$price=$_POST['price'];$category=$_POST['category'];$quantity=$_POST['quantity'];
$description=$_POST['description'];$description=str_replace(array("\n","\r","\n\r","\r\n"),array('\\n','\\r','\\n\\r','\\r\\n'),$description);
$listing_type=$_POST['listing_type'];$item_condition=$_POST['condicion'];$currency="VES";$buying_mode=$_POST['buying_modes'];
$garantia=$_POST['garantia'];if($garantia=='si'){$garantia=$_POST['texto_garantia'];}
$pictures=array();$imagenes=array();$img=$_FILES['images'];
if($variaciones>0){
$string_variacion='';$string_atributo='';
foreach($_SESSION['array_variaciones'] as $value){
$variacion=$_POST["$value"];$value=str_replace("|"," ",$value);
if($string_variacion==''){$string_variacion="{\"name\":\"".$value."\", \"value_name\":\"".$variacion."\"}";
}else{$string_variacion.=",{\"name\":\"".$value."\", \"value_name\":\"".$variacion."\"}";}
}
foreach($_SESSION['array_atributos'] as $value){
$atributo=$_POST["$value"];$value=str_replace("|"," ",$value);
if($string_atributo==''){$string_atributo="{\"id\":\"".$value."\", \"value_name\":\"".$atributo."\"}";
}else{$string_atributo.=",{\"id\":\"".$value."\", \"value_name\":\"".$atributo."\"}";}
}
}
if(!empty($img)){
$k=0;
$picture_ids='';
foreach($img["error"] as $key => $error){
if($error==UPLOAD_ERR_OK){
$tmp_name=$img["tmp_name"][$key];$name=basename($img["name"][$key]);$info=new SplFileInfo($name);$extension=$info->getExtension();
if($extension=='jpg' || $extension=='jpge' || $extension=='png'){
$nombre_new=$id_user.$k.'.'.$extension;
$mover=move_uploaded_file($tmp_name,"imagenes/$nombre_new");
if($mover){array_push($pictures,$nombre_new);array_push($imagenes,"imagenes/$nombre_new");
if($variaciones>0){
$image="https://oxas.tech/administracion/publicaciones/imagenes/$nombre_new";
$post=array('source'=>$image);$post=json_encode($post);$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/pictures?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_POST,1);curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
$result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);
$id_imagen=$consulta->id;
if($picture_ids==''){$picture_ids="\"$id_imagen\"";}else{$picture_ids.=",\"$id_imagen\"";}
}
}
}
}
++$k;
}
}
if($variaciones==0){
$string_img='';
foreach($pictures as $value){
if($string_img==''){$string_img="{\"source\":\"https://oxas.tech/administracion/publicaciones/imagenes/$value\"}";
}else{$string_img.=",{\"source\":\"https://oxas.tech/administracion/publicaciones/imagenes/$value\"}";}
}
}
$string_img="[".$string_img."]";
$ch=curl_init();curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
if($variaciones>0){
curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"title\":\"$title\",\"category_id\":\"$category\",\"price\":$price,
\"currency_id\":\"$currency\",\"available_quantity\":$quantity,\"buying_mode\":\"$buying_mode\",
\"listing_type_id\":\"$listing_type\",\"description\":{\"plain_text\":\"$description\"},\"condition\":\"$item_condition\",
\"attributes\":[$string_atributo],\"variations\":[{\"available_quantity\":$quantity,\"price\":$price,\"attribute_combinations\":[$string_variacion],\"picture_ids\":[$picture_ids]}]}");
}else{
curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"title\":\"$title\",\"category_id\":\"$category\",\"price\":$price,
\"currency_id\":\"$currency\",\"available_quantity\":$quantity,\"buying_mode\":\"$buying_mode\",
\"listing_type_id\":\"$listing_type\",\"warranty\":\"$garantia\",\"description\":{\"plain_text\":\"$description\"},
\"pictures\":$string_img,\"condition\":\"$item_condition\"}");
}
curl_setopt($ch,CURLOPT_POST,1);$headers=array();$headers[]='Content-Type: application/json';curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
$result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);
foreach($imagenes as $value){unlink("$value");}
if(isset($consulta->id)){header('Location: index.php?create=1');}else{header('Location: index.php?create=2');}
?>
