<?php
//Sensar las fechas de suscripcion con el dia de readline_add_history
include 'conexion.php';
//$sql="SELECT * FROM suscripcion WHERE FECHAVENCIMIENTO BETWEEN ADDDATE(NOW(), INTERVAL -8 DAY) AND NOW()";
//suscripciones vencidas.
$sql="SELECT IDSUSCRIPCION,IDUSUARIO FROM suscripcion WHERE FECHAVENCIMIENTO<=NOW() AND ESTATUS<>1";
$r=$conn->query($sql);
if($r->num_rows>0){
  while($row=$r->fetch_assoc()){
      //Cambiar suscripcion
      $id=$row['IDSUSCRIPCION'];
      $sql2="UPDATE suscripcion SET ESTATUS=1 WHERE IDSUSCRIPCION='$id'";
      if($conn->query($sql2)===TRUE){
        #enviar correo de cambio de Plan
        $sql="SELECT u.CORREO,u.IDUSUARIO FROM suscripcion s INNER JOIN usuario u ON u.IDUSUARIO=s.IDUSUARIO  WHERE s.IDSUSCRIPCION='$id' LIMIT 1";
        $r=$conn->query($sql);
        if ($r->num_rows>0){
        while($row=$r->fetch_assoc()){
        $id_user=$row['IDUSUARIO'];
        $destinatario=$row['CORREO'];
        //Apagar Robot
        $sql="UPDATE `usuario` SET `CHATROBOT`=0 where `IDUSUARIO`=$id_user";
        if($conn->query($sql) === TRUE){
        echo 'Robot-Off<br>';
        }else{
        echo '0';
        }
        include('../correos/vencimiento.php');
        }
        }
        echo 'correo enviado<br>';
      }

  }
}

//suscripciónes por vencer
$sql="SELECT IDSUSCRIPCION FROM suscripcion WHERE ESTATUS<>2 AND FECHAVENCIMIENTO BETWEEN  ADDDATE(NOW(), INTERVAL 1 DAY) AND ADDDATE(NOW(), INTERVAL 8 DAY) ";
$r=$conn->query($sql);
if($r->num_rows>0){
  while($row=$r->fetch_assoc()){
      $id=$row['IDSUSCRIPCION'];
      $sql2="UPDATE suscripcion SET ESTATUS=2 WHERE IDSUSCRIPCION='$id'";
      if($conn->query($sql2)===TRUE){
        #enviar correo de cambio de Plan
        $sql="SELECT u.CORREO, DATE_FORMAT(s.FECHAVENCIMIENTO, '%d/%m/%Y') AS FECHAVENCIMIENTO FROM suscripcion s INNER JOIN usuario u ON u.IDUSUARIO=s.IDUSUARIO  WHERE s.IDSUSCRIPCION='$id' LIMIT 1";
        $r=$conn->query($sql);
        if ($r->num_rows>0){
        while($row=$r->fetch_assoc()){
        $destinatario=$row['CORREO'];
        $fechaV=$row['FECHAVENCIMIENTO'];
        include('../correos/aviso1semana.php');
        }
        }
        echo 'Good';
      }
  }
}

//suscripciónes por vencer - 3 DIAS
$sql="SELECT IDSUSCRIPCION FROM suscripcion WHERE ESTATUS=2 AND FECHAVENCIMIENTO BETWEEN  ADDDATE(NOW(), INTERVAL 2 DAY) AND ADDDATE(NOW(), INTERVAL 3 DAY) ";
$r=$conn->query($sql);
if($r->num_rows>0){
  while($row=$r->fetch_assoc()){
      $id=$row['IDSUSCRIPCION'];
        #enviar correo de cambio de Plan
        $sql="SELECT u.CORREO, DATE_FORMAT(s.FECHAVENCIMIENTO, '%d/%m/%Y') AS FECHAVENCIMIENTO FROM suscripcion s INNER JOIN usuario u ON u.IDUSUARIO=s.IDUSUARIO  WHERE s.IDSUSCRIPCION='$id' LIMIT 1";
        $r=$conn->query($sql);
        if ($r->num_rows>0){
        while($row=$r->fetch_assoc()){
        $destinatario=$row['CORREO'];
        $fechaV=$row['FECHAVENCIMIENTO'];
        include('../correos/aviso3dias.php');
        }
        }
        echo 'Mail 3 dias';
  }
}
#si el usuario cuenta con capital para Apagar

 ?>
