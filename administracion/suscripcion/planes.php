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
        <?php if ($idplan=='-1'){ ?>
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
<div class="container">
  <div class="row">
    <?php if($idplan=='-1'): ?>
      <div class="col-sm-4">
        <section class="breadcrumb px-4 pb-5 border border-muted">
          <div class="container-fluid">
            <div class="row">
              <h5><strong class="text-muted">Plan Gratuito&nbsp;</strong></h5><strong class="text-success"><i>(Plan Actual)</i></strong>
              <p>30 dias gratuitos</p>
            </div>
            <div class="row mt-2">
              <b class="col-auto">Precio:</b>
              <span class="col-auto ml-auto">0,00 Bs</span>
            </div>
            <div class="row">
              <b class="col-auto">Duración:</b>
              <span class="col-auto ml-auto">30 Dias</span>
            </div>
            <hr>
            <div class="row">
              <b class="col-12">Fecha de Vencimiento:</b>
              <span class="col-12"><?php echo $stingVencimiento;?></span>
            </div>
          </div>
        </section>
      </div>
    <?php endif; ?>
    <?php
    $sql="SELECT se.IDSERVICIOS,se.IDPLAN FROM suscripcion s INNER JOIN servicios se ON s.IDUSUARIO=se.IDUSUARIO WHERE s.IDUSUARIO='$id_user' LIMIT 1";
    $r=$conn->query($sql);
    if($r->num_rows>0){
      $row=$r->fetch_assoc();
      $id_plan=$row['IDPLAN'];
      $id_servicios=$row['IDSERVICIOS'];
    }
    $sql="SELECT * FROM plan WHERE IDPLAN>0";
    $result=$conn->query($sql);
    if($result->num_rows>0){
      while($row=$result->fetch_assoc()){
        ?>
        <div class="col-sm-4">
          <section class="breadcrumb px-4 pb-5 <?php echo $row['NOMBRE']=="Plan Premium" ? 'border_premium' : 'border border-muted'; ?>">
            <div class="container-fluid">
              <div class="row">
                <h5><strong class="text-muted"><?=$row['NOMBRE']?>&nbsp;</strong></h5><strong class="text-success"><i class="text-success actual" <?php echo $row['IDPLAN']!=$id_plan ? 'style="display:none"' : ''; ?>>(Plan Actual)</i></strong>
              </div>
              <div class="row"><p><?=nl2br($row['DESCRIPCION'])?></p></div>
              <div class="row mt-2">
                <b class="col-auto">Precio:</b>
                <span class="col-auto ml-auto"><?php echo number_format(round(($row['MONTO']*$dolar)/1000)*1000,2,',','.');?>,00 Bs</span>
              </div>
              <div class="row">
                <b class="col-auto">Duración:</b>
                <span class="col-auto ml-auto"><?=$row['TIEMPO']?> Mes(es)</span>
              </div>
              <hr>
              <div class="row">
                <?php if($row['IDPLAN']!=$id_plan){ ?>
                  <button class="btn btn-primary btn-sm ok px-3" id="cam<?=$row['IDPLAN']?>" type="button" data-toggle="modal" data-target="#cambiar<?=$row['IDPLAN']?>">Adquirir Plan</button>
                <?php }else{  ?>
                  <button id="cam<?=$row['IDPLAN']?>" class="btn btn-primary btn-sm ok px-3" type="button"  data-toggle="modal" data-target="#cambiar<?=$row['IDPLAN']?>" disabled>Plan Actual</button>
                <?php } ?>
              </div>
            </div>
          </section>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="cambiar<?=$row['IDPLAN']?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Adquirir Plan</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <div class="form-row">
                  <div class="form-group col-md-12">
                    <label for="inputEmail4">Nombre del Plan</label>
                    <input type="text" class="form-control" id="inputEmail4" placeholder="Nombre de plan" value="<?=$row['NOMBRE']?>" disabled>
                  </div>
                </div>
                <div class="form-group">
                  <label for="exampleFormControlTextarea1">Descripción</label>
                  <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"  maxlength="300"  disabled><?=$row['DESCRIPCION']?></textarea>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="inputEmail4">Monto</label>
                    <input type="text" class="form-control" id="inputEmail4" placeholder="Monto" value="<?php echo number_format(round(($row['MONTO']*$dolar)/1000)*1000,2,',','.');?> Bs"  disabled>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="inputState">Duración</label>
                    <select id="inputState" class="form-control" disabled>
                      <option value="1" <?php if($row['TIEMPO']==1){ echo 'selected';} ?>>1 Mes</option>
                      <option value="3" <?php if($row['TIEMPO']==3){ echo 'selected';} ?>>3 Meses</option>
                      <option value="6" <?php if($row['TIEMPO']==6){ echo 'selected';} ?>>6 Meses</option>
                      <option value="12" <?php if($row['TIEMPO']==12){ echo 'selected';} ?>>12 Meses</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <div class="row">
                  <div class="col-12 text-center mb-3">
                    ¿Estas seguro que deseas cambiar el plan de suscripción? <br>
                    <small class="text-muted">Recuerda que este se activará, Luego de vencerse tu plan anterior.</small>
                  </div>
                  <div class="col-12 text-center">
                    <button id="ok<?=$row['IDPLAN']?>" type="submit" class="btn btn-sm btn-success px-5" class="close" data-dismiss="modal">Adquirir</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <script >
          $("button#ok<?=$row['IDPLAN']?>").click(function(){
            $.get("cambio-suscripcion.php", {idplan:<?=$row['IDPLAN']?>, idservicio:<?=$id_servicios?>}, verificar);
            function verificar (valor){
              if(valor==1){
                //good
                const toast=swal.mixin({ toast:true,position:'top',showConfirmButton:false,timer:5500});
                toast({type:'success',titleText:'¡Has cambiado de plan de manera exitosa!'});
                $(".ok").removeAttr('disabled');
                $("#cam<?=$row['IDPLAN']?>").attr('disabled','true');
                $(".actual").hide();
                $("#actual<?=$row['IDPLAN']?>").show();
              }else{
                //bad
                const toast=swal.mixin({ toast:true,position:'top',showConfirmButton:false,timer:5500});
                toast({type:'danger',titleText:'¡Has ocurido un error al cambiar de suscripción. \n Por favor, ¡intentelo nuevamente!'
              });
            }
          }
        });
      </script>
      <?php
    }
  }
  ?>
  </div>
 <hr>
 <div class="row justify-content-center mb-4">
    <a href="../suscripcion" class="btn btn-outline-secondary px-5">Volver</a>
 </div>
</div>
</div>
<?php include '../common/footer.php';?>
<script src='../vendor/jquery/jquery.min.js'></script>
<script src='../vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
<script src='../vendor/jquery-easing/jquery.easing.min.js'></script>
<script src='../js/sb-admin.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.0/dist/sweetalert2.all.min.js"></script>
</div>
</body>
</html>
