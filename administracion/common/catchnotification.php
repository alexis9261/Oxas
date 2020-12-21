<?php
header('HTTP/1.1 200 OK');
include 'conexion.php';
require 'Funciones.php';
$notifications=file_get_contents("php://input");
  $notifications=json_decode($notifications,true);
  $resource=$notifications['resource'];
  $topic=$notifications['topic'];
  $idUsuarioNotif=$notifications['user_id'];
  $api_raiz="https://api.mercadolibre.com";
  //busco el AccessToken y la bandera que indica si el chat esta o no activo(ChatRobot)
  $sql="SELECT ACCESSTOKEN,CHATROBOT,PAIS FROM usuario where IDUSUARIO=$idUsuarioNotif";
  $res=$conn->query($sql);
  if($res->num_rows>0){while($row=$res->fetch_assoc()){$robot=$row['CHATROBOT'];$access_token=$row['ACCESSTOKEN'];$siteId=$row['PAIS'];}}
  if($siteId="MLV"){
    date_default_timezone_set('America/Caracas');
    $appId='1153047962046613';
    $secretKey='i3RGdgCvJXrKT1ceMNOHs4YLNHdgZ9Mj';
    $redirectURI="https://app.oxas.tech/administracion/common/redirect.php";
  }elseif($siteId=="MLC"){
    date_default_timezone_set('America/Santiago');
    $appId='884214179114152';
    $secretKey='NpVPyfC6vtrFdS5EZ9Sr2DQe5sAOrXAK';
    $redirectURI="https://app.oxas.tech/administracion/common/redirect2.php";
  }
  //Funcion que actualiza el AccessToken
  function refreshToken($idUsuarioNotif,$appId,$secretKey,$ch,$conn,$api_raiz){
    $sql="SELECT REFRESTOKEN FROM usuario WHERE IDUSUARIO=$idUsuarioNotif;";
    $result_f=$conn->query($sql);
    if($result_f->num_rows>0){while($row=$result_f->fetch_assoc()){$refresh_token=$row['REFRESTOKEN'];}}
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,"$api_raiz/oauth/token?grant_type=refresh_token&client_id=$appId&client_secret=$secretKey&refresh_token=$refresh_token");
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_POST,1);
    $headers=array();$headers[]='Content-Type: application/json';
    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
    $result_ch=curl_exec($ch);curl_close($ch);$r=json_decode($result_ch);
    $access_token=$r->access_token;$refresh_token=$r->refresh_token;
    updateTokens($idUsuarioNotif,$access_token,$refresh_token);
    return $access_token;
  }
  //Funcion que responde la pregunta del comprador
  function responderPregunta($consulta,$idUsuarioNotif,$access_token,$ch,$band,$conn,$api_raiz){
    $id_p=$consulta->id;$pregunta=$consulta->text;$idpublic=$consulta->item_id;
    $sql="SELECT IDPUBLICACION FROM publicacion WHERE CODIGO='$idpublic';";
    $result=$conn->query($sql);
    if($result->num_rows>0){while($row=$result->fetch_assoc()){$idpublic_BBDD=$row['IDPUBLICACION'];}}
    //proceso la pregunta
    $ps=decodificador($pregunta);
    $ids=razonador($ps,$idUsuarioNotif,$idpublic_BBDD);
    $msn=codificador($ids);
    //Pregunto a ver si existe respuesta
    if($msn!=NULL){
      //Busco Saludo y Firma
      $sql="SELECT SALUDO,DESPEDIDA FROM usuario WHERE IDUSUARIO=$idUsuarioNotif";
      $result=$conn->query($sql);
      if($result->num_rows>0){while($row=$result->fetch_assoc()){$saludo=$row['SALUDO'];$firma=$row['DESPEDIDA'];}}
      //Confirmo que el comprador no me ha escrito antes, en caso de si, no le saludo, ni me despido. Solo le respondo puntualmente
      if($consulta->from->answered_questions>0){$saludo='';$firma='';}
      //agregar modulo, para no responder dos veces lo mismo
      //debo verificar si el comprador ya pregunto antes, en caso de si
      // debo verificar que le respondi antes, y no coincidir la misma respuesta.
      $respuesta=$saludo.' '.$msn.' '.$firma;
      $ch=curl_init();
      curl_setopt($ch,CURLOPT_URL,"$api_raiz/answers?access_token=$access_token");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"question_id\":$id_p,\"text\":\"$respuesta\"}");
      curl_setopt($ch,CURLOPT_POST,1);
      $headers=array();$headers[]="Content-Type: application/json";
      curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
      $result=curl_exec($ch);curl_close($ch);
      //Bandera que indica si se tuvo q actualizar el AccessToken o no.
      if($band==0){echo "OxasApp - OK";}else{echo "OxasApp - RefreshToken - OK";}
    }else{
      //En caso de no tener respuesta programada por Oxas, se le procede a preguntar al Vendedor Original
      realizarPregunta($pregunta,$idUsuarioNotif,$access_token,$ch,$idpublic,$conn,$api_raiz);
    }
  }
  //Funcion Pregunta al Vendedor Original en caso de Oxas No responder
  function realizarPregunta($pregunta,$idUsuarioNotif,$access_token,$ch,$idpublic,$conn,$api_raiz){
    //Busco el codigo de la publicaicon original, el codigo de la publicacion del vendedor(proveedor)
    $sql="SELECT CODIGOORIGINAL FROM publicacion WHERE CODIGO=$idpublic;";
    $result=$conn->query($sql);
    if($result->num_rows>0){while($row=$result->fetch_assoc()){$publicacionOriginal=$row['CODIGOORIGINAL'];}}
    //en caso de existir una publicacion original entonces es una publicacion que esta en Reventa
    if(isset($publicacionOriginal) && !empty($publicacionOriginal)){
      $ch=curl_init();
      curl_setopt($ch,CURLOPT_URL,"$api_raiz/questions/$publicacionOriginal?access_token=$access_token");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"text\":\"$pregunta\",\"item_id\":\"$publicacionOriginal\"}");
      curl_setopt($ch,CURLOPT_POST,1);
      $headers=array();$headers[]="Content-Type: application/json";
      curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
      $result=curl_exec($ch);curl_close($ch);
      echo "Pregunta Realizada - Oxas";
    }
  }
  //Funcion que elimina acentos
  function eliminar_acentos($cadena){
    $cadena=str_replace(array('Á','À','Â','Ä','á','à','ä','â','ª'),array('A','A','A','A','a','a','a','a','a'),$cadena);
    $cadena=str_replace(array('É','È','Ê','Ë','é','è','ë','ê'),array('E','E','E','E','e','e','e','e'),$cadena);
    $cadena=str_replace(array('Í','Ì','Ï','Î','í','ì','ï','î'),array('I','I','I','I','i','i','i','i'),$cadena);
    $cadena=str_replace(array('Ó','Ò','Ö','Ô','ó','ò','ö','ô'),array('O','O','O','O','o','o','o','o'),$cadena);
    $cadena=str_replace(array('Ú','Ù','Û','Ü','ú','ù','ü','û'),array('U','U','U','U','u','u','u','u'),$cadena);
    return $cadena;
  }
  //Funcion que filtra las respuestas del vendedor Original
  function filtroRespuesta($respuesta){
    $respuesta=strtolower($respuesta);
    $respuesta=eliminar_acentos($respuesta);
    $filterWords=array("eutuxia","retire en tienda","retirar en tienda","somos tienda fisica","caracas","estacion del metro","punto de venta","estamos en","estamos cerca de","barquisimeto");
    $wordsToChange=array(" "," "," "," "," "," "," "," "," "," ");
    $respuesta=str_replace($filterWords,$wordsToChange,$respuesta);
    $respuesta=ucfirst($respuesta);
    return $respuesta;
  }
  //Funcion que Responde la pregunta hecha por el comprador tomando la respouesta dada por el vendedor
  function responderVendedorOriginal($idUsuarioNotif,$consulta,$access_token,$ch,$conn,$api_raiz){
    $seller_id=$consulta->seller_id;
    //busco todos los revendedores
    $array_id_vendedores=array();
    $sqlReventa="SELECT USUARIOID FROM revendedores";
    $r=$conn->query($sqlReventa);
    if($r->num_rows>0){
      while($rw=$r->fetch_assoc()){array_push($array_id_vendedores,$rw['USUARIOID']);}
    }
    //Confirmo que el vendedor sea ReVendedor
    if(in_array($idUsuarioNotif,$array_id_vendedores)){
      $idpublic=$consulta->item_id;
      $respuesta=$consulta->answer->text;
      // la respuesta se debe fitrar
      $respuesta=filtroRespuesta($respuesta);
      $pregunta=$consulta->text;
      //Busco el codigo de la publicacion nuestra
      $sql="SELECT CODIGO FROM publicacion WHERE IDUSUARIO=$idUsuarioNotif AND CODIGOORIGINAL='$idpublic'";
      $result=$conn->query($sql);
      if($result->num_rows>0){while($row=$result->fetch_assoc()){$codigoPublic_BBDD=$row['CODIGO'];}}
      //busco las preguntas sin responder de nuestra publicacion
      $ch=curl_init();
      curl_setopt($ch,CURLOPT_URL,"$api_raiz/questions/search?item=$codigoPublic_BBDD&status=UNANSWERED&access_token=$access_token");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      $resultado_q=curl_exec($ch);
      curl_close($ch);
      $consulta=json_decode($resultado_q);
      $arrayPreguntasSinResp=$consulta->questions;
      // Recorro todas las preguntas sin responder de nuestra publicacion
      foreach($arrayPreguntasSinResp as $value){
        $preguntaSinResp=$value->text;
        if($preguntaSinResp==$pregunta){
          $idPreguntaSinResp=$value->id;
          break;
        }
      }
      //Respondo la pregunta de nuestra publiacion con la respuesta dada por el vendedor original
      $ch=curl_init();
      curl_setopt($ch,CURLOPT_URL,"$api_raiz/answers?access_token=$access_token");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"question_id\":$idPreguntaSinResp,\"text\":\"$respuesta\"}");
      curl_setopt($ch,CURLOPT_POST,1);
      $headers=array();$headers[]="Content-Type: application/json";
      curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
      $result=curl_exec($ch);curl_close($ch);
      echo "OxasApp - Respondida por Vendedor Original";
    }
  }
  //Funcion para enviar mensaje al crearse una Venta
  function enviarPrimerMensaje($idUsuarioNotif,$consulta,$access_token,$ch,$conn,$appId,$api_raiz){
    $order_id=$consulta->id;
    //Busco el mensaje que se enviara, estatus=1 indica que el mensaje debe enviarse al crearse la venta
    $sql="SELECT MENSAJE,HORA,DIAS FROM mensajes WHERE USUARIOID=$idUsuarioNotif AND ESTATUS=1;";
    $result=$conn->query($sql);
    if($result->num_rows>0){
      while($row=$result->fetch_assoc()){
        date_default_timezone_set ("America/Caracas");
        $mensaje=$row['MENSAJE'];
        $mensaje=str_replace(array("\n","\r","\n\r","\r\n"),array('\\n','\\r','\\n\\r','\\r\\n'),$mensaje);
        $filterWords=array("@formulario");
        //confirmo que el texto @formulario este en el texto
        if(strpos($mensaje,"@formulario")){
          //busco el id del revendedor en la tabla revendedores
          $sqlReventa="SELECT IDREVENDEDOR FROM revendedores WHERE USUARIOID=$idUsuarioNotif LIMIT 1";
          $r=$conn->query($sqlReventa);
          if($r->num_rows>0){
            while($rw=$r->fetch_assoc()){
              $idReSeller=$rw['IDREVENDEDOR'];
            }
          }
          $wordsToChange=array("https://app.oxas.tech/formulario/index.php?o=$order_id&r=$idReSeller");
        }else{
          $wordsToChange=array("https://app.oxas.tech/formulario/index.php?o=$order_id");
        }
        $mensaje=str_replace($filterWords,$wordsToChange,$mensaje);
        $horas=$row['HORA'];$dias=$row['DIAS'];
        $array_dias=['L','M','X','J','V','S','D'];
        $array_days=['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        $array_dias_BD=explode(",",$dias);
        $hoy=getdate();$dia=$hoy['weekday'];
        $dia_aux=$array_dias[array_search($dia,$array_days)];
        if(in_array($dia_aux,$array_dias_BD)){
          // Verificar que sea la hora
          $hora=$hoy['hours'];
          $band=1;
          if(strlen($horas)<8){
            $hora_minima=substr($hora,0,1);
            $hora_maxima=substr($hora,5,2);
            if($hora>$hora_minima && $hora<$hora_maxima){$band=1;}
          }else{
            $hora_minima=substr($hora,0,2);
            $hora_maxima=substr($hora,6,2);
            if($hora>$hora_minima && $hora<$hora_maxima){$band=1;}
          }
          if($band==1){
            $id_buyer=$consulta->buyer->id;
            if(is_null($consulta->pack_id)){$pack_id=$order_id;}else{$pack_id=$consulta->pack_id;}
            //pregunto si ya hay mensajes de mi parte en la orden
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,"$api_raiz/messages/packs/$pack_id/sellers/$idUsuarioNotif?access_token=$access_token");
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            $result=curl_exec($ch);curl_close($ch);$consult=json_decode($result);
            if($consult->paging->total==0){
              // Envio el mensaje al comprador
              $ch=curl_init();
              curl_setopt($ch,CURLOPT_URL,"$api_raiz/messages/packs/$pack_id/sellers/$idUsuarioNotif?access_token=$access_token&application_id=$appId");
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"from\":{\"user_id\":\"$idUsuarioNotif\"},\"to\":{\"user_id\":\"$id_buyer\"},\"text\":\"$mensaje\"}");
              curl_setopt($ch,CURLOPT_POST,1);
              $headers=array();$headers[]="Content-Type: application/json";
              curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
              $result=curl_exec($ch);curl_close($ch);
              echo "Primer Mensaje Enviado - Oxas";
            }
          }
        }
      }
    }
  }
  if($robot){
    if($topic=="questions"){
      $ch=curl_init();
      curl_setopt($ch,CURLOPT_URL,$api_raiz."$resource?access_token=$access_token");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      $resultado=curl_exec($ch);curl_close($ch);$consulta=json_decode($resultado);
      if(isset($consulta->error)){
        $access_token=refreshToken($idUsuarioNotif,$appId,$secretKey,$ch,$conn,$api_raiz);
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$api_raiz."$resource?access_token=$access_token");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $resultado_q=curl_exec($ch);curl_close($ch);$consulta=json_decode($resultado_q);
        $band=1;
        $status=$consulta->status;
        //Veo si la pregunta ya fue o no respondida
        if($status=="UNANSWERED"){
          responderPregunta($consulta,$idUsuarioNotif,$access_token,$ch,$band,$conn,$api_raiz);
        }elseif($status=="ANSWERED"){
          //si ya fue respondida, es porq el vendedor orignal repondio
          responderVendedorOriginal($idUsuarioNotif,$consulta,$access_token,$ch,$conn,$api_raiz);
        }
      }else{
        $band=0;
        $status=$consulta->status;
        //Veo si la pregunta ya fue o no respondida
        if($status=="UNANSWERED"){
          responderPregunta($consulta,$idUsuarioNotif,$access_token,$ch,$band,$conn,$api_raiz);
        }elseif($status=="ANSWERED"){
          //si ya fue respondida, es porq el vendedor orignal repondio
          responderVendedorOriginal($idUsuarioNotif,$consulta,$access_token,$ch,$conn,$api_raiz);
        }
      }
    }elseif($topic=="orders_v2"){
      $ch=curl_init();
      curl_setopt($ch,CURLOPT_URL,$api_raiz."$resource?access_token=$access_token");
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      $resultado=curl_exec($ch);curl_close($ch);$consulta=json_decode($resultado);
      if(isset($consulta->error)){
        $access_token=refreshToken($idUsuarioNotif,$appId,$secretKey,$ch,$conn,$api_raiz);
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$api_raiz."$resource?access_token=$access_token");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $resultado_q=curl_exec($ch);curl_close($ch);$consulta=json_decode($resultado_q);
        enviarPrimerMensaje($idUsuarioNotif,$consulta,$access_token,$ch,$conn,$appId,$api_raiz);
      }else{
        enviarPrimerMensaje($idUsuarioNotif,$consulta,$access_token,$ch,$conn,$appId,$api_raiz);
      }
    }
  }
//$topic=="messages" para mensajeria postVenta
