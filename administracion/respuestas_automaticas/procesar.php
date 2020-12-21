<?php
session_start();
require '../Oxa/Funciones.php';require '../common/conexion.php';require '../common/take_at.php';require '../common/account-off.php';
$idUsuario=$id_user;
//Estatus - Good (0)
$estatus=0;
if(isset($_GET['respuestaauto'],$_GET['palabrasclave'])){
      $respuesta=$_GET['respuestaauto'];
      $keywords=$_GET['palabrasclave'];
      //Palabras clave - Keyswords
      if($keywords!=NULL){
        $keywords=strtolower($keywords);
        $keyword_array=explode(",",$keywords);//genera un array $key con los elementos que estan en $keywords, separados por coma
      }else{
        #Estatus Error - Sin palabras claves asignadas (4)
        $estatus=4;
      }
      //Respuesta
      #Estatus Error - Sin respuesta asignada (6)
      if($respuesta==NULL){$estatus=6;}
/** Conseguir publicaciones de la session **/
if(isset($_SESSION['array_publicaciones'])){
      $publicaciones=$_SESSION['array_publicaciones'];
      if($publicaciones!=NULL){
          if(@$publicaciones[0]==1){
            //Seleccionar todos - ON
            $codigos=array();
            $nombres=array();
            /**
            1era Seleccionar todas las publicaciones y almacenarlas en un arreglo
            **/
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/users/$id_user/items/search?status=active&access_token=$AccessToken");
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            $result=curl_exec($ch);
            curl_close($ch);
            $consulta=json_decode($result);
            $items=$consulta->paging->total;
            //Solo para usuarios de 20 publicaciones
            if($items>0 && $items<=50){
              $array=$consulta->results; //arreglo de codigos de publicaciones, Maximo 50 Publicacione
              $total_items=count($array);
              $contador=0;
              while($contador!=$total_items){
                $array_publicaciones_veinte=array();
                $aux=$contador+19;
                for($u=$contador;$u<=$aux;$u++){
                  if(isset($array[$u])){
                    ++$contador;
                    array_push($array_publicaciones_veinte,$array[$u]);
                  }
                }
                $str_public=implode(",",$array_publicaciones_veinte);
                $array_publicaciones_veinte=array();
                $ch=curl_init();
                curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items?ids=$str_public&attributes=id,title&access_token=$AccessToken");
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                $result=curl_exec($ch);
                curl_close($ch);
                $result=json_decode($result);
                foreach($result as $valor){
                  $codigo_p=$valor->body->id;
                  $nombre_p=$valor->body->title;
                  array_push($codigos,$codigo_p);
                  array_push($nombres,$nombre_p);
                }
              }
            }else if($items<=50000){
              $paginas=ceil($items/50);
              $offset=0;
              for($i=0;$i<$paginas;$i++){
                $offset=$i*50;
                $ch=curl_init();
                curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/users/$id_user/items/search?status=active&access_token=$AccessToken&offset=$offset");
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                $result=curl_exec($ch);
                curl_close($ch);
                $consulta=json_decode($result);
                $items=$consulta->paging->total;
                $array=$consulta->results; //arreglo de codigos de publicaciones, LIMIT 50
                $total_items=count($array);
                $contador=0;
                while($contador!=$total_items){
                  $array_publicaciones_veinte=array();
                  $aux=$contador+19;
                  for($u=$contador;$u<=$aux;$u++){
                    if(isset($array[$u])){
                      ++$contador;
                      array_push($array_publicaciones_veinte,$array[$u]);
                    }
                  }
                  $str_public=implode(",",$array_publicaciones_veinte);
                  $array_publicaciones_veinte=array();
                  $ch = curl_init();
                  curl_setopt($ch, CURLOPT_URL, "https://api.mercadolibre.com/items?ids=".$str_public."&attributes=id,title&access_token=".$AccessToken);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                  $result = curl_exec($ch);
                  curl_close ($ch);
                  $result=json_decode($result);
                  foreach($result as $clave => $valor){
                    $codigo_p=$valor->body->id;
                    $nombre_p=$valor->body->title;
                    array_push($codigos, $codigo_p);
                    array_push($nombres, $nombre_p);
                  }
                }
              }
            }
            /**
            2do Deseleccionar las publicaciones deseleccinadas
            **/
            foreach($publicaciones as $valor){
              if($valor!=1){
                $p=explode('~¬',$valor);
                $codigo_p =$p[0];
                $nombre_p=$p[1];
                $indice=array_search($codigo_p,$codigos);//busco el id en el array, aqui obtendre el indice donde se encuentra el item
                unset($codigos[$indice]);//elimino el item del array
                unset($nombres[$indice]);//elimino el item del array
              }
            }
          }else{
            //Seleccionar todos - OFF
            //echo $respuesta .'<br>'. $publicaciones .'<br>'. $keywords;
            $codigos=array();
            $nombres=array();
            foreach($publicaciones as $p){
              $p=explode('~¬',$p);
              $codigo_p=$p[0];
              $nombre_p=$p[1];
              array_push($codigos,$codigo_p);
              array_push($nombres,$nombre_p);
            }
          }
      }else{
        //Estatus de Advertencia - No hay publicaciones asignadas (2)
        if($estatus<2){$estatus=1;}
      }
  }
      //Verificar que no existe error
      if($estatus<=1){
        //idRespuesta si es una actualizacion.
        if(isset($_GET['idrespuesta'])){
          /**
            Actualizacion de Auto Respuestas
           **/
            $idRespuesta=$_GET['idrespuesta'];
            $id_public_array=array();
            $n=0;
            #(1) Crear publicaciones
            if($publicaciones!=NULL){
               for($i=0;$i<count($codigos);$i++){
                $n++;
                  #if la publicacion no existe, creala
                  if (!publicacionExist($idUsuario, $codigos[$i]) ){
                      $idp=createPublicacion($idUsuario, $codigos[$i], $nombres[$i]);
                  }else{
                      $idp=getIdPublicacion($idUsuario,$codigos[$i]);
                  }
                  array_push($id_public_array, $idp);
                }
            }
            #(2.0) Elimina Respuesta
            $idInfo =$idRespuesta;
            deleteInfo($idInfo, $idUsuario);
            #(2) Modificar Respuestas
            if(!($respuesta==NULL or $keywords==NULL) ){
                //informacion
                $info=$respuesta;
                //numero de publicaciones
                $numPublicaciones=$n;
                createInfoId($idInfo,$idUsuario, $info, $keywords, $numPublicaciones);
                #echo $idInfo;
            }
        # Estatus Error - problemas al enlazar (5)
          $error=0;
          if (!($respuesta==NULL or $keywords==NULL or $publicaciones==NULL))  {
            foreach($id_public_array as $idpublicacion){
              $out=enlazar( $idInfo,$idpublicacion);
              $error=$error+$out;
            }
            if ($error>0){
              $estatus=5;
            }
          }
        }else{
        /**
         Creacion de Auto Respuestas
         **/
        $n=0;
        $id_public_array=array();
        #(1) Crear publicaciones
        if(!($publicaciones==NULL)){

          foreach ($codigos as $value){
              $n++;
              if (!publicacionExist($idUsuario,$value)){
                $idp=createPublicacion($idUsuario,$value,$nombres[$key]);
              }else{
                $idp=getIdPublicacion($idUsuario,$value);
              }
              array_push($id_public_array,$idp);
          }
        /*  for($i=0; $i<count($codigos);$i++){
            $n++;
            # foreach($codigos as $codigo){
            #if la publicacion no existe, creala
            if (!publicacionExist($idUsuario, $codigos[$i]) ){
              $idp=createPublicacion($idUsuario, $codigos[$i], $nombres[$i]);
            }else{
              $idp= getIdPublicacion($idUsuario, $codigos[$i]);
            }
            array_push($id_public_array, $idp);
          }
        */
        }
        #(2) Crear Respuestas
        if(!($respuesta==NULL or $keywords==NULL) ){
                #informacion
            $info=$respuesta;
            #numero de publicaciones
            $numPublicaciones=$n;
            $idInfo=createInfo($idUsuario,$info,$keywords,$numPublicaciones);
      #      echo $idInfo;
        }
        #(3) Crear Enlaces
        $error=0;
        if(!($respuesta==NULL or $keywords==NULL or $publicaciones==NULL)){
          foreach($id_public_array as $idpublicacion){
            $out=enlazar($idInfo,$idpublicacion);
            $error=$error+$out;
          }
          if($error>0){$estatus=5;}
        }
      }
    }

}else{$estatus=2;}
/*Estatus Error - Bad
0 - Good
1 - Estatus de Advertencia - No hay publicaciones asignadas
2 - Estatus Error - Bad
3 -
4 - Estatus Error - Sin palabras claves asignadas (4)
5 - Estatus Error - problemas al enlazar (5)
6 - Estatus Error - Sin respuesta asignada (6)
*/
if($estatus<=1){$_SESSION['array_publicaciones']=array();}
echo $estatus;
 ?>
