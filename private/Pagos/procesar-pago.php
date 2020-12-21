<?php
require '../../administracion/common/conexion.php';
if (isset($_GET['estado'], $_GET['idpago'])){
  $estado=$_GET['estado'];
  $idpago=$_GET['idpago'];
  $pagado=false;
  if ($estado=='falla'){
    // LLamado SQL
    $sql="UPDATE `pagos` SET `ESTATUS`='2' WHERE `IDPAGO`=$idpago";
    if ($conn->query($sql)===TRUE){
      #enviar correo de pago fallido
      $sql="SELECT u.CORREO, p.REFERENCIA FROM pagos p INNER JOIN usuario u ON u.IDUSUARIO=p.IDUSUARIO where p.IDPAGO='$idpago' LIMIT 1";
      $r=$conn->query($sql);
      if ($r->num_rows>0){
      while($row=$r->fetch_assoc()){
      $destinatario=$row['CORREO'];
      $ref = $row['REFERENCIA'];
      include('../../administracion/correos/pagofallido.php');
      }
      }
        echo 'good';
    }else{echo 'bad';}
  }else{
        //BUSCAR MONTO DEL PAGO
        $sql1="SELECT MONTO,IDUSUARIO FROM pagos WHERE IDPAGO=$idpago  AND ESTATUS=0 LIMIT 1";
        $r1=$conn->query($sql1);
        if($r1->num_rows>0){
          $row1=$r1->fetch_assoc();
          $monto=$row1['MONTO'];
          $id_user=$row1['IDUSUARIO'];
          //cAMBIAR DE ESTATUS EL PAGO
          $sql="UPDATE `pagos` SET `ESTATUS`='1' WHERE `IDPAGO`=$idpago";
          if ($conn->query($sql)===TRUE){
            #enviar correo de pago exitoso
            $sql="SELECT u.CORREO, p.REFERENCIA FROM pagos p INNER JOIN usuario u ON u.IDUSUARIO=p.IDUSUARIO where p.IDPAGO='$idpago' LIMIT 1";
            $r=$conn->query($sql);
            if ($r->num_rows>0){
            while($row=$r->fetch_assoc()){
            $destinatario=$row['CORREO'];
            $ref = $row['REFERENCIA'];
            include('../../administracion/correos/pagoexitoso.php');
            }
            }
            echo 'Pago Procesado <br>';
          }
          //Manejo de CAPITAL
          $sql="SELECT CAPITAL,DATE_FORMAT(FECHAVENCIMIENTO, '%d-%m-%Y') AS FECHAVENCIMIENTO FROM suscripcion WHERE IDUSUARIO=$id_user";
          $r2=$conn->query($sql);
          if($r2->num_rows>0){
              $row2=$r2->fetch_assoc();
              $capital=$row2['CAPITAL'];
              $fechav=$row2['FECHAVENCIMIENTO'];
              //sABER EL pLAN
              $sql="SELECT MONTO,NOMBRE,TIEMPO FROM servicios se INNER JOIN plan p ON p.IDPLAN=se.IDPLAN WHERE IDUSUARIO='$id_user'";
              $r3=$conn->query($sql);
              if($r3->num_rows>0){
                  $row3=$r3->fetch_assoc();
                  $costo_plan=$row3['MONTO'];
                  $descripcion= $row3['NOMBRE'].' - '. $row3['TIEMPO'].' Mes(es)' ;
                  $tiempo= $row3['TIEMPO'];
                }
              //PAGO DE servicios
              if ($monto>=$costo_plan){
                //Emite factutara
                $sql="INSERT INTO `facturas` (`IDFACTURA`, `IDUSUARIO`, `FECHAEMISION`, `MONTO`, `DESCRIPCION`) VALUES (NULL, '$id_user', CURRENT_TIMESTAMP(), '$costo_plan', '$descripcion');";
                if($conn->query($sql)===TRUE){
                  //Modifica el capital
                  $total=$capital+$monto-$costo_plan;
                  #importante considerar si la fecha de vencimiento ya paso o no
                  $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                  $fecha_entrada = strtotime($fechav.' '.'21:00:00');

                  if($fecha_actual > $fecha_entrada){
                        #echo "La fecha entrada ya ha pasado";
                        $sql="UPDATE `suscripcion` SET `CAPITAL`=$total,`ESTATUS`=0, `FECHAVENCIMIENTO`=ADDDATE( CURRENT_DATE(), INTERVAL $tiempo MONTH) WHERE `IDUSUARIO`=$id_user";
                  }else{
                        #echo "Aun falta algun tiempo";
                        $sql="UPDATE `suscripcion` SET `CAPITAL`=$total,`ESTATUS`=0, `FECHAVENCIMIENTO`=ADDDATE(FECHAVENCIMIENTO, INTERVAL $tiempo MONTH) WHERE `IDUSUARIO`=$id_user";
                  }

                  if ($conn->query($sql)===TRUE){
                    //Encender Robot
                    $sql="UPDATE `usuario` SET `CHATROBOT`=1 where `IDUSUARIO`=$id_user";
                    if($conn->query($sql) === TRUE){
                    echo 'Robot-On<br>';
                    }else{
                    echo '0';
                    }
                    echo 'Capital debitado<br>';
                  }
                }
              }else{
                if($costo_plan<=$monto+$capital){
                  $sql="INSERT INTO `facturas` (`IDFACTURA`, `IDUSUARIO`, `FECHAEMISION`, `MONTO`, `DESCRIPCION`) VALUES (NULL, '$id_user', CURRENT_TIMESTAMP(), '$costo_plan', '$descripcion');";
                  if($conn->query($sql)===TRUE){
                    //modifica el capital
                    $total=$capital+$monto-$costo_plan;
                    #importante considerar si la fecha de vencimiento ya paso o no
                    $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
                    $fecha_entrada = strtotime($fechav.' '.'21:00:00');

                    if($fecha_actual > $fecha_entrada){
                          #echo "La fecha entrada ya ha pasado";
                          $sql="UPDATE `suscripcion` SET `CAPITAL`=$total,`ESTATUS`=0, `FECHAVENCIMIENTO`=ADDDATE( CURRENT_DATE(), INTERVAL $tiempo MONTH) WHERE `IDUSUARIO`=$id_user";
                    }else{
                          #echo "Aun falta algun tiempo";
                          $sql="UPDATE `suscripcion` SET `CAPITAL`=$total,`ESTATUS`=0, `FECHAVENCIMIENTO`=ADDDATE(FECHAVENCIMIENTO, INTERVAL $tiempo MONTH) WHERE `IDUSUARIO`=$id_user";
                    }
                    if ($conn->query($sql)===TRUE){
                      //Encender Robot
                      $sql="UPDATE `usuario` SET `CHATROBOT`=1 where `IDUSUARIO`=$id_user";
                      if($conn->query($sql) === TRUE){
                      echo 'Robot-On<br>';
                      }else{
                      echo '0';
                      }
                      echo 'Capital debitado<br>';
                    }
                  }
                }else{
                  //SUMAR CAPITAL
                  $sql="UPDATE `suscripcion` SET `CAPITAL`=CAPITAL+$monto WHERE `IDUSUARIO`=$id_user";
                  if ($conn->query($sql)===TRUE){
                    echo 'Capital incrementado<br>';
                  }
                }
              }
            }
        }
        echo 'Good';
  }
}
header('Location: ../Pagos');
?>
