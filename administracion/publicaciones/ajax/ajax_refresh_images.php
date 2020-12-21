<?php
require '../../common/conexion.php';require '../../common/take_at.php';
$id_item=$_GET['item'];
$sql="SELECT CODIGOORIGINAL FROM publicacion WHERE CODIGO='$id_item' LIMIT 1;";
$r=$conn->query($sql);
if($r->num_rows>0){
  while($rw=$r->fetch_assoc()){
    $codigoOriginal=$rw['CODIGOORIGINAL'];
  }
}
//imagenes propias q se quieren editar, estas se deben eliminar
$string_imagenes_propias=$_GET['array_imagenes_propias'];
$array_imagenes_mias_get=explode(",",$string_imagenes_propias);
//imagenes propias que se quieren agregar
$string_imagenes_originales=$_GET['array_imagenes_originales'];
$array_imagenes_originales_get=explode(",",$string_imagenes_originales);
$rutaCarpeta=$_SERVER['DOCUMENT_ROOT']."/administracion/oxashop/reventa/img/";
$respuesta=0;
//funcion para redimensionar imagen
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
//primero busco las imagenes de mi publicaicon que voy a mantener
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items?ids=$id_item");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);curl_close($ch);$var=json_decode($result);$var=$var[0]->body;
//obtengo las imagenes de mi publicacion
$array_imagenes_item_propio=$var->pictures;
//este sera el array de las imagenes a conservar de mi publicacion
$array_definitivo_imagenes_propias=array();
//recorro el array de imagenes q obtuve via GET, son las imagenes q voy a cambiar, estas las debo eliminar
foreach($array_imagenes_item_propio as $imagen){
  $id_imagen_propia=$imagen->id;
  //Veo si el id de la imagen del la mi publicaion fue seleccionado para ser cambiado, en caso de q si, se debe eliminar este id del array
  if(!in_array($id_imagen_propia,$array_imagenes_mias_get)){
    //este sera el array de las imagenes a conservar de mi publicacion
    array_push($array_definitivo_imagenes_propias,"{\"id\":\"$id_imagen_propia\"}");
  }
}
//ahora busco las imagenes de la publicacion Original
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items?ids=$codigoOriginal");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);curl_close($ch);$var=json_decode($result);$var=$var[0]->body;
//obtengo el array de imagenes del item original
$array_imagenes_item_original=$var->pictures;
//inicio el array q contendra las rutas de las imagenes ya procesadas del item original
$array_definitivo_imagenes=array();
//recorro el array de las imagenes de la publicaicon original para guardar las q fueron seleccionadas
foreach($array_imagenes_item_original as $imagen){
  $id_imagen_original=$imagen->id;
  //veo si el id de la imagen fue seleccionada, debe encontrarse en el array enviado via GET
  if(in_array($id_imagen_original,$array_imagenes_originales_get)){
    //genero un nombre ramdom para la imagenes
    $ramdom=rand();
    //busco la imagen con su id, para obtener la mayor resolucion disponible
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/pictures/$id_imagen_original");
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
    //Nuevo nombre de la imgen con extension, que sera colocada en el server de oxas
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
      $imagen_server_oxas="https://oxas.tech/administracion/oxashop/reventa/img/$new_name_image";
      //string de la imagen
      array_push($array_definitivo_imagenes,"{\"source\":\"$imagen_server_oxas\"}");
    }
  }
}
$string_img_orig=implode(",",$array_definitivo_imagenes);
if(count($array_definitivo_imagenes_propias)>0){
  $string_img_propias=implode(",",$array_definitivo_imagenes_propias);
  $string_img="[$string_img_propias, $string_img_orig]";
}else{
  $string_img="[$string_img_orig]";
}
//creo la publicacion
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"http://api.mercadolibre.com/items/$id_item?access_token=$AccessToken");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"pictures\":$string_img}");
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
$headers=array();
$headers[]='Content-Type: application/json';
$headers[]='Accept: application/json';
curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
$result=curl_exec($ch);
curl_close($ch);
?>
