<?php
set_time_limit(0);
session_start();
require '../../common/conexion.php';
require '../../common/take_at.php';require '../../common/account-off.php';
$status='';
if(isset($_GET['band']) && $_GET['band']==1){
  if(isset($_SESSION['array_publicaciones'])){
    $publicaciones=$_SESSION['array_publicaciones'];
    if(isset($publicaciones[0]) && $publicaciones[0]==1){
      $ch=curl_init();
      curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/'.$id_user.'/items/search?status=active&access_token='.$AccessToken);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      $result=curl_exec($ch);
      curl_close($ch);
      $consulta=json_decode($result);
      $items=$consulta->paging->total;
      if($items>0 && $items<=50){
        $array=$consulta->results;
        $array_aux=array();
        foreach($array as $value){
          $id_publicacion=$value;
          if(!in_array($id_publicacion,$publicaciones)){
            array_push($array_aux,$id_publicacion);
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$id_publicacion.'?access_token='.$AccessToken);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"status\":\"paused\"}");
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
            $headers=array();
            $headers[]='Content-Type: application/json';
            $headers[]='Accept: application/json';
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
            $result=curl_exec($ch);
            curl_close($ch);
            $sql="UPDATE publicacion SET ESTATUS=0 WHERE CODIGO='$id_publicacion';";
            if($conn->query($sql)===TRUE){}
          }
        }
        $status=implode(',',$array_aux);
      }elseif($items>50 && $items<=50000){
        $cant_no_pausar=count($publicaciones);
        $contador=($cant_no_pausar>1) ? $cant_no_pausar-1:0;
        $band=0;
        $array_aux=array();
        while($band==0){
          $ch=curl_init();
          curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/'.$id_user.'/items/search?status=active&access_token='.$AccessToken);
          curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
          $result=curl_exec($ch);
          curl_close($ch);
          $consulta=json_decode($result);
          $array=$consulta->results;
          foreach($array as $valor){
            $id_publicacion=$valor;
            if(!in_array($id_publicacion,$publicaciones)){
              array_push($array_aux,$id_publicacion);
              $ch=curl_init();
              curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$id_publicacion.'?access_token='.$AccessToken);
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"status\":\"paused\"}");
              curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
              $headers=array();
              $headers[]='Content-Type: application/json';
              $headers[]='Accept: application/json';
              curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
              $result=curl_exec($ch);
              curl_close($ch);
              $sql="UPDATE publicacion SET ESTATUS=0 WHERE CODIGO='$id_publicacion'";
              if($conn->query($sql)===TRUE){}
              ++$contador;
              if($contador==$items){$band=1;}
            }
          }
        }
        $status=implode(',',$array_aux);
      }
    }else{
      foreach($publicaciones as $value){
        $id_publicacion=$value;
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$id_publicacion.'?access_token='.$AccessToken);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"status\":\"paused\"}");
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
        $headers=array();
        $headers[]='Content-Type: application/json';
        $headers[]='Accept: application/json';
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        $result=curl_exec($ch);
        curl_close($ch);
        $sql="UPDATE publicacion SET ESTATUS=0 WHERE CODIGO='$id_publicacion';";
        if($conn->query($sql)===TRUE){}
      }
      $status=implode(',',$publicaciones);
    }
  }
}else{
  $id_publicacion=$_GET['item'];
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$id_publicacion.'?access_token='.$AccessToken);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"status\":\"paused\"}");
  curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
  $headers=array();
  $headers[]='Content-Type: application/json';
  $headers[]='Accept: application/json';
  curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
  $result=curl_exec($ch);
  curl_close($ch);
  $status=$result;
  $sql="UPDATE publicacion SET ESTATUS=0 WHERE CODIGO='$id_publicacion'";
  if($conn->query($sql)===TRUE){}
}
echo $status;
$_SESSION['array_publicaciones']=array();
?>
