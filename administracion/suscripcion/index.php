<?php
session_start();
require '../common/meli.php';
require '../common/conexion.php';
require '../Oxa/Funciones.php';
require '../common/take_at.php';
require "../precios/index.php";
$array_meses=array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
$sql="SELECT * FROM servicios WHERE IDUSUARIO='$id_user' LIMIT 1";
$r=$conn->query($sql);
if($r->num_rows>0){
  $row=$r->fetch_assoc();
  $idplan=$row['IDPLAN'];
}
$sql="SELECT * FROM suscripcion WHERE IDUSUARIO='$id_user' LIMIT 1";
$r=$conn->query($sql);
if($r->num_rows>0){
  $row=$r->fetch_assoc();
  $capital=$row['CAPITAL'];
  $estatus=$row['ESTATUS'];
  $vencimiento=$row['FECHAVENCIMIENTO'];
  $stingVencimiento=substr($vencimiento,8,2)." de ".$array_meses[intval(substr($vencimiento,5,2))]." del ".substr($vencimiento,0,4);
}
?>
<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
  <meta name='description' content=''>
  <meta name='author' content=''>
  <link rel='shortcut icon' href='../../img/favicon.ico'>
  <title>Oxas - Respuestas Automáticas</title>
  <link href='../vendor/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
  <link href='../css/sb-admin.min.css' rel='stylesheet'>
  <link href='../css/style.css' rel='stylesheet'>
  <script src='../vendor/jquery/jquery.min.js'></script>
</head>
<body class='fixed-nav sticky-footer bg-dark' id='page-top'>
  <?php include '../common/navbar.php';?>
  <div class='content-wrapper'>
    <div class='container-fluid'>
      <section class='breadcrumb'>
        <div class="container-fluid">
          <div class='row'>
            <?php if ($idplan=="-1"){ ?>
              <h5 class='col-auto'>Te encuentras en el plazo gratiuto. <small class="text-muted"> ¡Disfurtalo al máximo!</small> </h5>
            <?php }else { ?>
              <h5 class='col-auto text-muted'>¡Disfruta de todos los beneficios de <strong>Oxas</strong> a los mejores precios!</h5>
            <?php } ?>
            <span class="col-auto ml-auto">
              <a class='btn btn-sm btn-primary px-5 mr-3' href='planes.php'>Planes</a>
              <?php if ($estatus!="0"): ?>
                <a class='btn btn-sm btn-danger px-5' href='pagar.php' title='Pagar Suscripción' data-toggle='tooltip'>Pagar</a>
              <?php endif; ?>
            </span>
          </div>
        </div>
      </section>
      <?php $sql="SELECT *, DATE_FORMAT( FECHAVENCIMIENTO, '%d/%m/%Y') as FECHAV, p.MONTO as MONTO FROM servicios s INNER JOIN plan p ON p.IDPLAN=s.IDPLAN INNER JOIN suscripcion su ON su.IDUSUARIO=s.IDUSUARIO WHERE s.IDUSUARIO='$id_user' LIMIT 1";
      $r=$conn->query($sql);
      if($r->num_rows>0){
        $row=$r->fetch_assoc();
        ?>
        <section class="breadcrumb border-success-plan borde_plan">
          <div class="container-fluid">
            <div class="row justify-content-center">
              <div class="col-8">
                <h5 class="text-secondary mb-3"><?php echo $row['NOMBRE'];?>&nbsp;<strong><?php if($row['ESTATUS']==0){$text='<i class="text-success">(Activo)</i>';}else if($row['ESTATUS']==1) {$text= '<i class="text-danger">(Vencido)</i>';}else{$text='<i class="text-warning">(Por Vencer)</i>';} ; echo $text;?></strong></h5>
                <p class="text-secondary"><?php echo nl2br($row['DESCRIPCION']);?></p>
              </div>
              <div class="col-4">
                <p><b>Fecha Vencimiento: </b> <?php echo $row['FECHAV'];?></p>
                <h6><b>Precio:</b> <?php echo round(($row['MONTO']*$dolar)/1000)*1000;?>,00 Bs</h6>
                <p><b>Duración:</b> <?php echo $row['TIEMPO'];$tiempo=$row['IDPLAN']>0 ? 'Mes(es)':'Dias';echo $tiempo;?></p>
              </div>
            </div>
          </div>
        </section>
      <?php } ?>
      <?php if($idplan=='-1'): ?>
        <div class="alert alert-success">
          El peridodo gratuito es de 30 dias, en los cuales podrás disfrutar de todos los beneficios de <strong>Oxas</strong>. <br>
          <small class="text-muted">Tu periodo de prueba vence el <strong><?php echo $stingVencimiento;?>. <span class="text-dark">¡Aprovéchalo!</span> </strong></small>
        </div>
      <?php endif; ?>
      <?php $sql2="SELECT *,DATE_FORMAT(FECHAEMISION, '%d/%m/%y') AS FECHAP FROM facturas WHERE IDUSUARIO='$id_user' ORDER BY FECHAEMISION DESC LIMIT 6 ";
      $r=$conn->query($sql2);
      if($r->num_rows>0){
        ?>
        <hr>
        <div class="container-fluid mb-5">
          <div class="row">
            <div class="col-auto"><button class="btn btn-primary btn-sm px-5" id="_facturas">Mis Facturas</button></div>
            <div class="col-auto"><button class="btn btn-primary btn-sm px-5" id="_pagos">Mis Pagos</button></div>
          </div>
          <hr>
          <div class="row bg-light py-1 facturas" style="display:none">
            <div class="col-3"><b>Fecha</b></div>
            <div class="col-5"><b>Descripcion</b></div>
            <div class="col-3"><b>Monto(Bolivares)</b></div>
          </div>
          <?php while($row=$r->fetch_assoc()){ ?>
            <div class="row facturas" style="display:none">
              <div class="col-3 text-secondary"><?=$row['FECHAP']?></div>
              <div class="col-5 text-secondary">Servicio de <?=$row['DESCRIPCION']?></div>
              <div class="col-3 text-secondary"><?php echo round(($row['MONTO']*$dolar)/1000)*1000;?>,00 Bs</div>
            </div>
          <?php
        }
      }else{
          ?>
          <div class="text-center text-muted facturas">
            <small>Sin facturas Registradas</small>
          </div>
        <?php } ?>
        <?php $sql="SELECT *,DATE_FORMAT(FECHA, '%d/%m/%y') AS FECHAP FROM pagos WHERE IDUSUARIO='$id_user' ORDER BY FECHA DESC LIMIT 6";
        $r=$conn->query($sql);
        if($r->num_rows>0){
          ?>
          <div class="row bg-light py-1 pagos" style="display:none">
            <div class="col-2"><b>Fecha</b></div>
            <div class="col-3"><b>Referencia</b></div>
            <div class="col-2"><b>Monto</b></div>
            <div class="col-3"><b>Estatus</b></div>
          </div>
          <?php while($row=$r->fetch_assoc()){ ?>
            <div class="row pagos" style="display:none">
              <div class="col-2 text-muted"><?=$row['FECHAP']?></div>
              <div class="col-3 text-muted"><?=$row['REFERENCIA']?></div>
              <div class="col-2 text-muted"><?php echo round(($row['MONTO']*$dolar)/1000)*1000;?>,00 Bs</div>
              <div class="col-3 text-muted">
                <?php if($row['ESTATUS']=='1'){echo "Exitoso";}else if($row['ESTATUS']=='0'){echo 'En Revisión';}else{echo'Fallido';} ?>
              </div>
            </div>
          <?php
          }
        }else{
          ?>
          <div class="text-center  text-muted pagos" style="display:none">
            <small>Sin Pagos Registrados</small>
          </div>
        <?php } ?>
      </div>
    </div>
    <script>
      $("#_pagos").click(function(){
        $(".facturas").hide();
        $(".pagos").show();
      });
      $("#_facturas").click(function(){
        $(".facturas").show();
        $(".pagos").hide();
      });
    </script>
    <?php include '../common/footer.php';?>
    <script src='../vendor/jquery/jquery.min.js'></script>
    <script src='../vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
    <script src='../vendor/jquery-easing/jquery.easing.min.js'></script>
    <script src='../js/sb-admin.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.0/dist/sweetalert2.all.min.js"></script>
  </div>
</body>
</html>
