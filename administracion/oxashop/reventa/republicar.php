<?php
session_start();
require '../../common/conexion.php';
require '../../common/take_at.php';
$id_item=$_GET['id_item'];
$ganancia=$_GET['ganancia'];
$string_imagenes=$_GET['string_imagenes'];
$rutaCarpeta=$_SERVER['DOCUMENT_ROOT']."/administracion/oxashop/reventa/img/";
$array_imagenes=explode(",",$string_imagenes);
$respuesta=0;
function redimensionar_imagen($new_name_image,$urlSellerImage,$xmax,$ymax){
  $ext=explode(".",$new_name_image);
  $ext=$ext[count($ext)-1];
  if($ext=="jpg" || $ext=="jpeg"){
    $imagen=imagecreatefromjpeg($urlSellerImage);
  }elseif($ext=="png"){
    $imagen=imagecreatefrompng($urlSellerImage);
  }
  $x=imagesx($imagen);
  $y=imagesy($imagen);
  if($x > 500){
    $nuevay=$ymax;$nuevax=$nuevay * $x / $y ;
  }else{
    $nuevax=$xmax;$nuevay=$y * $nuevax / $x;
  }
  $img2=imagecreatetruecolor($nuevax,$nuevay);
  imagecopyresized($img2,$imagen,0,0,0,0,floor($nuevax),floor($nuevay),$x,$y);
  return $img2;
}
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items?ids=$id_item");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);curl_close($ch);$var=json_decode($result);$var=$var[0]->body;
$id_seller=$var->seller_id;
$title=$var->title;
//Filtro del titulo de la publicacion
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/users/$id_seller");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);
$direccion=$consulta->address->city.",".$consulta->address->state;
$nickname=$consulta->nickname;
$perfil_ml=$consulta->permalink;
//elimino el nickname del vendedor del titulo, en caso de que lo haya
$title=str_ireplace($nickname,"",$title);
$price=$var->price;
//procesar el precio
$ganancia_aux=$ganancia/100;
$precio=round($price*($ganancia_aux+1)/(0.89),-3)-1;
$precio=intval($precio);
//incializo los otros parametros
$quantity=50;
$description="Hacemos ENVÍOS GRATIS POR MRW a todo el país.\n \n No hacemos entregas personales. \nNo somos tienda física.\n -------------------------------------- \n El envió se realiza de 24 a 48 horas a partir de la confirmación del pago en nuestras cuentas.";
$description=str_replace(array("\n","\r","\n\r","\r\n"),array('\\n','\\r','\\n\\r','\\r\\n'),$description);
$category=$var->category_id;
$listing_type='gold_special';
$item_condition=$var->condition;
$currency="VES";
$buying_mode=$var->buying_mode;
$garantia='null';
$atributos=$var->attributes;
//configurar el envio gratuito
$envio="{\"mode\":\"not_specified\",\"free_shipping\":true}";
//busco las imagenes que fueron seleccionadas
$pictures=$var->pictures;
//declaro el array que tendra las imagenes ya cargadas para el item
$array_definitivo_imagenes=array();
//recorro todas las imagenes de la publicacion
foreach($pictures as $imagen){
  $id_imagen=$imagen->id;
  //confirmo si el id de la imagen fue seleccionada
  if(in_array($id_imagen,$array_imagenes)){
    //genero un nombre ramdom para la imagenes
    $ramdom=rand();
    //busco la imagen con su id, para obtener la mayor resolucion disponible
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/pictures/$id_imagen");
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);
    //obtengo la mayor resolucion disponible
    $max_size=$consulta->max_size;
    $pos=strpos($max_size,'x');
    //obtengo el alto y ancho de la imagen
    $ancho=intval(substr($max_size,0,$pos));
    $alto=intval(substr($max_size,(-$pos),strlen($max_size)));
    //obtengo la url de la imagen del vendedor de mayor resolucion
    $urlSellerImage=$consulta->variations[0]->url;
    // obtengo la extension de la imagen
    $extension=end(explode(".",$urlSellerImage));
    //el nombre de la imgen con extension, que sera colocada en el server de oxas
    $new_name_image=$ramdom.".".$extension;
    // Declaramos la ruta definitiva para almacenar la imagen
    $ruta_final_img=$rutaCarpeta.$new_name_image;
    //verifico q se pudo obtener la extension de la imagen con mayor resolucion
    if(!empty($extension)){
      //confirmo que la imagen principal sea mayor que 500x500px, lo minimo requerido por ML
      if($ancho>500 && $alto>500){
        // Descargo la imagen del vendedor, se guardara en la variable $fileImg
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$urlSellerImage);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $fileImg=curl_exec($ch);
        curl_close($ch);
      }else{
        $fileImg=redimensionar_imagen($new_name_image,$urlSellerImage,550,550);
      }
      //confirmo la extension para guardar la imagen en el directorio de mi servidor
      $miarchivo=fopen($ruta_final_img,"w+");
      //inserto la imagen en el directorio creado
      fputs($miarchivo,$fileImg);
      //cierro el directorio
      fclose($miarchivo);
      //escribo la ruta de la imagen que subi en el servidor de oxas, esta imagen depues sera enlazada cin la nueva publicacion
      $imagen_server_oxas="https://app.oxas.tech/administracion/oxashop/reventa/img/$new_name_image";
      //string de la imagen
      array_push($array_definitivo_imagenes,"{\"source\":\"$imagen_server_oxas\"}");
    }
  }
}
$string_img=implode(",",$array_definitivo_imagenes);
$string_img="[$string_img]";
//creo la publicacion
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"title\":\"$title\",
  \"category_id\":\"$category\",\"price\":$precio,
  \"currency_id\":\"$currency\",\"available_quantity\":$quantity,
  \"buying_mode\":\"$buying_mode\",
  \"listing_type_id\":\"$listing_type\",\"warranty\":\"$garantia\",
  \"description\":{\"plain_text\":\"$description\"},
  \"shipping\":$envio,\"pictures\":$string_img,\"condition\":\"$item_condition\"}");
  curl_setopt($ch,CURLOPT_POST,1);$headers=array();
  $headers[]='Content-Type: application/json';curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
  $result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);
  if(isset($consulta->id)){
    $id=$consulta->id;
    //Cargar el id original en BD
    $sql="INSERT INTO publicacion (IDUSUARIO,CODIGO,NOMBRE,CODIGOORIGINAL,GANANCIA,ESTATUS) VALUES ($id_user,'$id','$title','$id_item',$ganancia,1)";
    if($conn->query($sql)===TRUE){
      $respuesta=1;
      //agrego al vendedor al modelo oxashop
      $sql="SELECT ESTATUS FROM originalsellers WHERE IDORIGSELL=$id_seller";
      $result=$conn->query($sql);
      if($result->num_rows>0){
      }else{
        $sql="INSERT INTO originalsellers (IDORIGSELL,NICKNAME,DIRECCION,PERFILML,ESTATUS) VALUES ($id_seller,'$nickname','$direccion','$perfil_ml',1)";
        if($conn->query($sql)===TRUE){$respuesta=1;}
      }
    }else{$respuesta=2;}
  }else{$respuesta=3;}
  //print_r($consulta);
echo $respuesta;
?>
