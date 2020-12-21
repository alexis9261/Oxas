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
        <?php if($estatus!="0"){ ?>
          <div class='col-auto text-muted'>Sigue disfrutando de <strong> Oxas</strong>, realiza una transferencia a nuestra cuenta bancaria.</div>
        <?php }else{ ?>
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
  $row=$r->fetch_assoc();
  if($r->num_rows>0){
    $monto=$row['MONTO'];
    ?>
    <section class="breadcrumb border border-muted">
      <div class="container-fluid">
        <div class="row justify-content-around">
          <div class="col-auto mr-auto">
            <h5 class="text-secondary"><?php echo $row['NOMBRE'];?>&nbsp;<strong><?php if($row['ESTATUS']==0){$text='<i class="text-success">(Activo)</i>';}else if($row['ESTATUS']==1) {$text= '<i class="text-danger">(Vencido)</i>';}else{$text='<i class="text-warning">(Por Vencer)</i>';} ; echo $text;?></strong></h5>
          </div>
          <div class="col-auto">
            <b>Fecha Vencimiento: </b> <?php echo $row['FECHAV'];?>
          </div>
          <div class="col-auto">
            <b>Precio:</b> <?php echo number_format(round(($monto*$dolar)/1000)*1000,2,',','.');?> Bs
          </div>
          <div class="col-auto">
            <b>Duración:</b> <?php echo $row['TIEMPO'];$tiempo=$row['IDPLAN']>0 ? 'Mes(es)':'Dias';echo $tiempo;?>
          </div>
        </div>
      </div>
    </section>
  <?php } ?>
  <hr>
  <div class="container">
    <div class="row">
      <h5 class="text-muted col-auto">Banco Mercantil</h5>
      <h5 class="text-dark col-auto">01050 09749 10972 71781</h5>
      <h5 class="col-auto ml-auto"><b>Tipo Cta:</b> Corriente</h5>
    </div>
    <div class="row">
      <h5 class="col-auto"><b>Titular:</b> Alexis Montilla</h5>
      <h5 class="col-auto"><b>CI:</b> V-22744088</h5>
    </div>
  </div>
  <hr>
<?php if($row['IDPLAN']>0){ ?>
  <?php if($row['ESTATUS']!=0){ ?>
          <div class="container">
            <div class="row">
              <h6 class="col-auto">Reporta un Pago</h6>
              <strong class="col-auto ml-auto">Monto a transferir: <span class="text-muted"><?php echo number_format(round(($monto*$dolar)/1000)*1000,2,',','.');?> Bs</span></strong>
            </div>
            <div class="row my-3">
              <hr>
              <div class="input-group col-sm-6 mb-2">
                <input type="text" class="form-control pago" placeholder="Banco donde realizó la transferencia (Tu Banco)" id="bancoe" maxlength="32"/>
              </div>
              <!--div class="input-group col-sm-6 mb-2">
                <input type="text" class="form-control pago" value="Banco Mercantil" id="bancor" maxlength="32" readonly/>
              </div-->
              <div class="input-group col-sm-6 mb-2">
                <input type="number" step="0.01" class="form-control pago" placeholder="Inserte el Monto Transferido"  id="monto" maxlength="32"/>
                <select id="moneda" style="border: 1px solid #ddd; width:20%; border-radius: 0 4px  4px 0;">
                  <option value="Bs" selected>Bolivares</option>
                </select>
              </div>
              <div class="input-group col-sm-6 mb-2">
                <input type="text" class="form-control pago" placeholder="Titular de Cuenta" id="titular" maxlength="50"/>
              </div>
              <div class="input-group col-sm-6 mb-2">
                <input type="text" class="form-control pago" placeholder="CI/RIF (ej: j-498234983)" id="docid" maxlength="32"/>
              </div>
              <div class="input-group col-sm-6 mb-2">
                <input type="text" class="form-control pago" placeholder="Inserte la Referencia de la Transacción" id="referencia" maxlength="32"/>
              </div>
              <div class="input-group col-sm-6 mb-2">
                <label  class="input-group-text" for="fecha">Fecha de transacción</label>
                <input type="date" class="form-control pago" tooltip="Fecha de la transacción" id="fechapago"/>
              </div>
              <!--<input type="hidden" id="g-recaptcha-response">-->
              <div class="input-group-append col-12 justify-content-center">
                <button id="reporte" type="submit" class="btn btn-outline-secondary px-3">Reportar pago</button>
              </div>
            </div>
          </div>
        <script>
          $("#reporte").click(function(){
            $("#loader_now").click();
            var _titular= $("#titular").val();
            var _docid=$("#docid").val();
            var _bancoe=$("#bancoe").val();
            //var _bancor=$("#bancor").val();
            var _bancor="Banco Mercantil";
            var _monto=$("#monto").val();
            var _ref=$("#referencia").val();
            var _fecha=$("#fechapago").val();
            $.get("reportar-pago.php",{titular:_titular,docid:_docid,bancoe:_bancoe,bancor:_bancor,monto:_monto,referencia:_ref,fecha:_fecha},validar);
            function validar(value){
              if(value==1){
                $(".pago").val('');
                const toast=swal.mixin({toast:true,position:'top',showConfirmButton:false,timer:5500});
                toast({type:'success',titleText:'¡Has Registrado un Pago de Manera Exitosa!'});
              }else{
                const toast=swal.mixin({toast:true,position:'top',showConfirmButton:false,timer:5500});
                toast({type:'warning',titleText:'¡Has ocurido un error!. Recuerda rellenar todos los campos.'});
              }
              setTimeout ("$('button#close_loader').click();", 500);
            }
          });
        </script>
      <?php } ?>
  <!--Historia de pagos-->
  <!--section class="breadcrumb border border-muted">
    <div class="container row">
      <div class="col-10">
        <h3>Historial de pagos</h3>
      </div>
      <div class="col-2">
        <button class="btn btn-sm " id="enabler3" style="display:none" >Esconder</btn><button class="btn btn-sm " id="disabler3"  >Mostrar</btn>
      </div>
    </div>
    <div class="container-fluid" id="historial" style="display:none">
      <div class="row">
        <hr>
        <b class="col-1">Fecha</b>
        <b class="col-2">Titular</b>
        <b class="col-1">Monto(Bs)</b>
        <b class="col-2">Bancos(E/R)</b>
        <b class="col-2">CI/RIF</b>
        <b class="col-2">Referencia</b>
        <b class="col-1">Estatus</b>
        <b class="col-1">...</b>
        <hr>
      </div-->
    <?php /*
    $sql= "SELECT *, DATE_FORMAT(FECHA, '%d/%m/%y') AS FECHAP FROM pagos  WHERE IDUSUARIO='$id_user' ORDER BY FECHA DESC";
    $r=$conn->query($sql);
    if ($r->num_rows>0){
      while($row=$r->fetch_assoc()){*/
          ?>
          <!--div class="row" id="pago<?=$row['IDPAGO']?>">
            <div class="col-1"><small><?=$row['FECHAP']?></small></div>
            <div class="col-2"><small><?=$row['TITULAR']?></small></div>
            <div class="col-1"><small><?php echo round(($row['MONTO']*$dolar)/1000)*1000;?>,00</small></div>
            <div class="col-2"><small><?=$row['BANCOE']?>/<?=$row['BANCOR']?></small></div>
            <div class="col-2"><small><?=$row['DOCID']?></small></div>
            <div class="col-2"><small><?=$row['REFERENCIA']?></small></div>
            <?php //if($row['ESTATUS']==0){ ?>
              <div class="col-1">
                <small>Revisión</small>
              </div>
              <div class="col-1">
                <button class="btn-link border border-white text-danger" data-toggle="modal" data-target="#eliminar<?=$row['IDPAGO']?>">X</button>
              </div>
            <?php //}else if($row['ESTATUS']==1){ ?>
              <div class="col-1">
                <small><span class="text-success">Procesado</span></small>
              </div>
              <div class="col-1">
              </div>
            <?php //}else{ ?>
              <div class="col-1">
                <small><span class="text-danger">Fallido</span></small>
              </div>
              <div class="col-1">
              </div>
            <?php //} ?>
          </div-->
          <!--div class="modal fade" id="eliminar<?=$row['IDPAGO']?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-body">
                <h6 class="text-center">¿Estas seguro que deseas eliminar este Pago?<br> <small>Los Pagos solo podrán ser eliminados antes de ser Procesados.</small> </h6>
                </div>
                <div class="modal-footer">
                  <div class="row">
                    <div class="col-12 text-center">
                      <button id="elimina<?=$row['IDPAGO']?>" class="btn btn-sm btn-success" class="close" data-dismiss="modal">Aceptar</button>
                      <button class="btn btn-sm btn-muted" class="close" data-dismiss="modal" type="button">Cancelar</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div-->
          <script>
            /*$("#elimina<?=$row['IDPAGO']?>").click(function(){
                $.get("eliminar-pago.php", {id: <?=$row['IDPAGO']?>} , validar);
                function validar(value) {
                  if (value==1){
                    const toast=swal.mixin({toast: true,position: 'top',showConfirmButton: false,timer: 5500});
                    toast({type: 'success',titleText: '¡Has Eliminado un Pago de Manera Exitosa!'});
                    $("#pago<?=$row['IDPAGO']?>").remove();
                  }else{
                    const toast=swal.mixin({toast: true,position: 'top',showConfirmButton: false,timer: 5500});
                    toast({type: 'warning',titleText: '¡Has ocurido un error al Eliminar el pago!. Intentelo de Nuevo'});
                  }
                }
            });*/
          </script>
          <?php
      //}
    //}else{
      ?>
    <!--div class="text-center">
        <small class="text-muted">Sin Pagos Registrados</small>
      </div-->
      <?php //} ?>
      <hr>
    </div>
  </section>
<?php }else{ ?>
  <section  class="breadcrumb border border-muted">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-12">
          <small>
            <b>¡No tienes facturas a pagar!</b>
          </small>
        </div>
      </div>
    </div>
  </section>
<?php } ?>
</div>
<input type="hidden" data-toggle="modal" data-target="#loader_modal" id="loader_now">
<div class="modal fade" id="loader_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" id="loader_real">
<div class="modal-dialog" role="document">
<div class="modal-content bg-transparent no_border mt-5 pt-5">
<button type="button" class="close bg-transparent" data-dismiss="modal" aria-label="Close" id="close_loader"></button>
<div class="container mt-5">
<div class="row justify-content-center"><div class="col-auto background_loader"><div class="loader algin-self-middle"></div></div></div>
<b class="row justify-content-center text-white">¡¡Puede tardar unos segundos!!</b>
</div>
</div>
</div>
</div>
<?php include '../common/footer.php';?>
<script>
  $("#enabler3").click( function(){
    $("#historial").hide();
    $("#enabler3").hide();
    $("#disabler3").show();
  });
  $("#disabler3").click( function(){
    $("#historial").show();
    $("#enabler3").show();
    $("#disabler3").hide();
  });
</script>
<script src='../vendor/jquery/jquery.min.js'></script>
<script src='../vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
<script src='../vendor/jquery-easing/jquery.easing.min.js'></script>
<script src='../js/sb-admin.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.0/dist/sweetalert2.all.min.js"></script>
</div>
</body>
</html>
