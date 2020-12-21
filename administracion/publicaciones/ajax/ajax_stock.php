<?php
set_time_limit(0);
session_start();
require '../../common/conexion.php';
require '../../common/take_at.php';require '../../common/account-off.php';;
$status='';
if(isset($_GET['band']) && $_GET['band']==1){
  if(isset($_SESSION['array_publicaciones'])){
    $array_exito=array();
    $publicaciones=$_SESSION['array_publicaciones'];
    if(isset($publicaciones[0]) && $publicaciones[0]==1){
      $ch=curl_init();
      if($_GET['origin']=='active'){
        curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/'.$id_user.'/items/search?status=active&access_token='.$AccessToken);
      }else{
        curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/'.$id_user.'/items/search?status=paused&access_token='.$AccessToken);
      }
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      $result=curl_exec($ch);
      curl_close($ch);
      $consulta=json_decode($result);
      $items=$consulta->paging->total;
      if($items>0 && $items<=50){//LISTO
        $array=$consulta->results;
        $array_aux=array();
        foreach($array as $value){
          $id_publicacion=$value;
          if(!in_array($id_publicacion,$publicaciones)){
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items?ids='.$id_publicacion.'&attributes=available_quantity&access_token='.$AccessToken);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            $result=curl_exec($ch);
            curl_close($ch);
            $result=json_decode($result);
            $quantity=$result[0]->body->available_quantity;
            if($_GET['condicion']=="restar"){$stock=($_GET['stock']>=$quantity) ? 0:$quantity-$_GET['stock'];}else{$stock=$quantity+$_GET['stock'];}
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$id_publicacion.'?access_token='.$AccessToken);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"available_quantity\":\"$stock\"}");
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
            $headers=array();
            $headers[]='Content-Type: application/json';
            $headers[]='Accept: application/json';
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
            $result=curl_exec($ch);
            curl_close($ch);
            $consulta=json_decode($result);
            if(isset($consulta->available_quantity)){
              array_push($array_exito,$id_publicacion.'%'.$consulta->available_quantity);
            }
          }
        }
        $status=implode(',',$array_exito);
      }elseif($items>50 && $items<=50000){//LISTO
        $cant_no_pausar=count($publicaciones);
        $contador=($cant_no_pausar>1) ? $cant_no_pausar-1:0;
        $band=0;
        while($band==0){
          $ch=curl_init();
          if($_GET['origin']=='active'){
            curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/'.$id_user.'/items/search?status=active&access_token='.$AccessToken);
          }else{
            curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/'.$id_user.'/items/search?status=paused&access_token='.$AccessToken);
          }
          curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
          $result=curl_exec($ch);
          curl_close($ch);
          $consulta=json_decode($result);
          $array=$consulta->results;
          foreach($array as $valor){
            $id_publicacion=$valor;
            if(!in_array($id_publicacion,$publicaciones)){
              $ch=curl_init();
              curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items?ids='.$id_publicacion.'&attributes=available_quantity&access_token='.$AccessToken);
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              $result=curl_exec($ch);
              curl_close($ch);
              $result=json_decode($result);
              $quantity=$result[0]->body->available_quantity;
              if($_GET['condicion']=="restar"){$stock=($_GET['stock']>=$quantity) ? 0:$quantity-$_GET['stock'];}else{$stock=$quantity+$_GET['stock'];}
              $ch=curl_init();
              curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$id_publicacion.'?access_token='.$AccessToken);
              curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
              curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"available_quantity\":\"$stock\"}");
              curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
              $headers=array();
              $headers[]='Content-Type: application/json';
              $headers[]='Accept: application/json';
              curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
              $result=curl_exec($ch);
              curl_close($ch);
              $consulta=json_decode($result);
              if(isset($consulta->available_quantity)){
                array_push($array_exito,$id_publicacion.'%'.$consulta->available_quantity);
              }
              ++$contador;
              if($contador==$items){$band=1;}
            }
          }
        }
        $status=implode(',',$array_exito);
      }
    }else{//LISTO
      foreach($publicaciones as $value){
        $id_publicacion=$value;
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items?ids='.$id_publicacion.'&attributes=available_quantity&access_token='.$AccessToken);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $result=curl_exec($ch);
        curl_close($ch);
        $result=json_decode($result);
        $quantity=$result[0]->body->available_quantity;
        if($_GET['condicion']=="restar"){$stock=($_GET['stock']>=$quantity) ? 0:$quantity-$_GET['stock'];}else{$stock=$quantity+$_GET['stock'];}
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$id_publicacion.'?access_token='.$AccessToken);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"available_quantity\":\"$stock\"}");
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
        $headers=array();
        $headers[]='Content-Type: application/json';
        $headers[]='Accept: application/json';
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        $result=curl_exec($ch);
        curl_close($ch);
        $consulta=json_decode($result);
        if(isset($consulta->available_quantity)){
          array_push($array_exito,$id_publicacion.'%'.$consulta->available_quantity);
        }
      }
      $status=implode(',',$array_exito);
    }
  }
}else{//LISTO
  $quantity=$_GET['quantity'];
  $item_id=$_GET['item'];
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$item_id.'?access_token='.$AccessToken);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch,CURLOPT_POSTFIELDS,"{\"available_quantity\":\"$quantity\"}");
  curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'PUT');
  $headers=array();
  $headers[]='Content-Type: application/json';
  $headers[]='Accept: application/json';
  curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
  $result=curl_exec($ch);
  curl_close($ch);
  $status=$result;
}
echo $status;
$_SESSION['array_publicaciones']=array();
?>
