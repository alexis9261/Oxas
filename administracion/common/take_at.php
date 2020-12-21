<?php
if(isset($_COOKIE['id_user'],$_COOKIE['_validate'],$_COOKIE['expires_in'])){
  $id_user=$_COOKIE['id_user'];
  $sql="SELECT ACCESSTOKEN,CORREO,PAIS FROM usuario where IDUSUARIO='$id_user';";
  $res=$conn->query($sql);
  if($res->num_rows>0){
    while($row=$res->fetch_assoc()){
      $AccessToken=$row['ACCESSTOKEN'];
      $correoUser=$row['CORREO'];
      $siteId=$row['PAIS'];
    }
  }
  if($siteId=="MLV"){
    date_default_timezone_set('America/Caracas');
    $appId='1153047962046613';
    $secretKey='i3RGdgCvJXrKT1ceMNOHs4YLNHdgZ9Mj';
    $redirectURI="https://app.oxas.tech/administracion/common/redirect.php";
  }else if($siteId=="MLC"){
    date_default_timezone_set('America/Santiago');
    $appId='884214179114152';
    $secretKey='NpVPyfC6vtrFdS5EZ9Sr2DQe5sAOrXAK';
    $redirectURI="https://app.oxas.tech/administracion/common/redirect2.php";
  }
}else{
  header('Location: https://app.oxas.tech/');
}
?>
