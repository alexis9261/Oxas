<?php
session_start();require '../common/conexion.php';require '../common/take_at.php';
$id_item=$_GET['id_item'];$ganancia=$_GET['ganancia'];$string_imagenes=$_GET['string_imagenes'];
$rutaCarpeta="/administracion/reventa/img/";$rutaCarpeta=$_SERVER['DOCUMENT_ROOT'].$rutaCarpeta;
$array_imagenes=explode(",",$string_imagenes);$respuesta=0;
function redimensionar_imagen($new_name_image,$urlSellerImage,$xmax,$ymax){
$ext=explode(".",$new_name_image);$ext=$ext[count($ext)-1];if($ext=="jpg" || $ext=="jpeg"){
$imagen=imagecreatefromjpeg($urlSellerImage);}elseif($ext=="png"){
$imagen=imagecreatefrompng($urlSellerImage);}$x=imagesx($imagen);$y=imagesy($imagen);
if($x > 500){$nuevay=$ymax;$nuevax=$nuevay * $x / $y ;
}else{$nuevax=$xmax;$nuevay=$y * $nuevax / $x;}$img2=imagecreatetruecolor($nuevax,$nuevay);
imagecopyresized($img2,$imagen,0,0,0,0,floor($nuevax),floor($nuevay),$x,$y);return $img2;}
$ch=curl_init();curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items?id=$id_item");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);curl_close($ch);$var=json_decode($result);
$id_seller=$var->seller_id;$title=$var->title;
$ch=curl_init();curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/users/$id_seller");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);
$nickname=$consulta->nickname;$title=str_ireplace($nickname,"",$title);$price=$var->price;
$ganancia_aux=$ganancia/100;$precio=round($price*($ganancia_aux+1)/(0.89),-3)-1;$precio=intval($precio);$quantity=50;
$description="Hacemos ENVÍOS GRATIS POR MRW a todo el país.\n \n No hacemos entregas personales. \nNo somos tienda física.\n -------------------------------------- \n El envió se realiza de 24 a 48 horas a partir de la confirmación del pago en nuestras cuentas.";
$description=str_replace(array("\n","\r","\n\r","\r\n"),array('\\n','\\r','\\n\\r','\\r\\n'),$description);
$category=$var->category_id;$listing_type='gold_special';$item_condition=$var->condition;
$currency="VES";$buying_mode=$var->buying_mode;$garantia='no';$atributos=$var->attributes;
$envio="{\"mode\":\"not_specified\",\"free_shipping\":true}";$pictures=$var->pictures;
$array_definitivo_imagenes=array();$k=0;foreach($pictures as $imagen){$id_imagen=$imagen->id;
if(in_array($id_imagen,$array_imagenes)){$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/pictures/$id_imagen");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);
$max_size=$consulta->max_size;$pos=strpos($max_size,'x');
$ancho=intval(substr($max_size,0,$pos));$alto=intval(substr($max_size,(-$pos),strlen($max_size)));
$urlSellerImage=$consulta->variations[0]->url;
$extension=end(explode(".",$urlSellerImage));$new_name_image=$id_item.$k.".".$extension;
$ruta_final_img=$rutaCarpeta.$new_name_image;if(!empty($extension)){
if($ancho>500 && $alto>500){$ch=curl_init();curl_setopt($ch,CURLOPT_URL,$urlSellerImage);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$fileImg=curl_exec($ch);curl_close($ch);
}else{$fileImg=redimensionar_imagen($new_name_image,$urlSellerImage,550,550);}
if($extension=="jpg" || $extension=="jpeg"){header('Content-Type: image/jpeg');imagejpeg($fileImg,$ruta_final_img);
}elseif($extension=="png"){header('Content-Type: image/png');imagepng($fileImg,$ruta_final_img);}
imagedestroy($fileImg);$imagen_server_oxas="https://oxas.tech/administracion/reventa/img/$new_name_image";
array_push($array_definitivo_imagenes,"{\"source\":\"$imagen_server_oxas\"}");}}++$k;}
$string_img=implode(",",$array_definitivo_imagenes);$string_img="[$string_img]";$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"title\":\"$title\",\"category_id\":\"$category\",\"price\":$precio,
\"currency_id\":\"$currency\",\"available_quantity\":$quantity,\"buying_mode\":\"$buying_mode\",
\"listing_type_id\":\"$listing_type\",\"warranty\":\"$garantia\",\"description\":{\"plain_text\":\"$description\"},
\"shipping\":$envio,\"pictures\":$string_img,\"condition\":\"$item_condition\"}");
curl_setopt($ch,CURLOPT_POST,1);$headers=array();
$headers[]='Content-Type: application/json';curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
$result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);
if(isset($consulta->id)){$id=$consulta->id;
$sql="INSERT INTO publicacion (IDUSUARIO,CODIGO,NOMBRE,CODIGOORIGINAL,GANANCIA) VALUES ($id_user,'$id','$title','$id_item',$ganancia)";
if($conn->query($sql)===TRUE){$respuesta=1;}else{$respuesta=2;}
}else{$respuesta=3;}
echo $respuesta;
?>
