<?php
session_start();
require '../../administracion/common/conexion.php';
if(isset($_SESSION['admin'])){
if($_SESSION['admin']=='e20332c50c390a95a14b2820093a45fd'){
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="EutuxiaWeb">
<title>Oxas Analitic</title>
<link href="../../administracion/css/style.css" rel="stylesheet">
<link href="../../administracion/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<script src="../../administracion/vendor/jquery/jquery.min.js"></script>
<link rel="shortcut icon" href="../../img/favicon.ico">
<style>
body {font-size: .875rem;}
.feather {width: 16px;height: 16px;vertical-align: text-bottom;}
.sidebar {position: fixed;top: 0;bottom: 0;left: 0;z-index: 100;padding: 48px 0 0;box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);}
.sidebar-sticky {position: relative;top: 0;height: calc(100vh - 48px);padding-top: .5rem;overflow-x: hidden;overflow-y: auto;}
@supports ((position: -webkit-sticky) or (position: sticky)) {.sidebar-sticky {position: -webkit-sticky;position: sticky;}}
.sidebar .nav-link {font-weight:500;color:#333;}
.sidebar .nav-link .feather {margin-right:4px;color:#999;}
.sidebar .nav-link.active {color:#007bff;}
.sidebar .nav-link:hover .feather,
.sidebar .nav-link.active .feather {color:inherit;}
.sidebar-heading {font-size:.75rem;text-transform:uppercase;}
[role="main"] {padding-top:133px;}
@media(min-width:768px){[role="main"]{padding-top:48px;}}
.navbar-brand{padding-top:.75rem;padding-bottom:.75rem;font-size:1rem;background-color:rgba(0,0,0,.25);box-shadow:inset -1px 0 0 rgba(0,0,0,.25);}
.navbar .form-control{padding:.75rem 1rem;border-width:0;border-radius:0;}
.form-control-dark{color:#fff;background-color:rgba(255,255,255,.1);border-color:rgba(255,255,255,.1);}
.form-control-dark:focus {border-color: transparent;box-shadow: 0 0 0 3px rgba(255,255,255,.25);}
.bd-placeholder-img{font-size: 1.125rem;text-anchor: middle;}
@media (min-width:768px){.bd-placeholder-img-lg{font-size:3.5rem;}}
</style>
</head>
<body>
  <?php include('../common/navbar.php'); ?>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
<h2>Manejo de Pagos</h2>
<div class="table-responsive">
<table class="table table-striped table-sm">
<thead>
<tr>
<th>Fecha(Emisión)</th>
<th>Titular</th>
<th>RIF/CI</th>
<th>Banco Emisor</th>
<th>Banco Receptor</th>
<th>Referencia</th>
<th>Monto</th>
<th>...</th>
</tr>
</thead>
<tbody>
<?php $sql="SELECT *, DATE_FORMAT(FECHA,'%d/%m/%Y') AS FECHA FROM PAGOS";
$r=$conn->query($sql);
if($r->num_rows>0){
  while($row=$r->fetch_assoc()){
    ?>
<tr>
<td><?=$row['FECHA']?></td>
<td><?=$row['TITULAR']?></td>
<td><?=$row['DOCID']?></td>
<td><?=$row['BANCOE']?></td>
<td><?=$row['BANCOR']?></td>
<td><?=$row['REFERENCIA']?></td>
<td><?=$row['MONTO']?>.00 Bs</td>
<td>
  <?php if($row['ESTATUS']==0){
    ?>
    <button class="btn btn-link p-2" data-toggle="modal" data-target="#fallido<?=$row['IDPAGO']?>"  style="fill:#ff3030" ><svg xmlns="http://www.w3.org/2000/svg" width='11px' viewBox="0 0 352 512"><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"/></svg></button>
    <button class="btn btn-link p-2" data-toggle="modal" data-target="#exitoso<?=$row['IDPAGO']?>" style="fill:#00b506" ><svg xmlns="http://www.w3.org/2000/svg" width='12px' viewBox="0 0 512 512"><path d="M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z"/></svg></button>
    <?php
  } else if($row['ESTATUS']==1){
    echo 'Procesado';
  }else {
      echo 'Fallido';
  }?>
</td>
   
</tr>
<!-- Modal -->
<div class="modal fade" id="fallido<?=$row['IDPAGO']?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <!-- Modal conten t-->
      <form method="GET" action="procesar-pago.php">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Procesar Pago</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-row">
        <div class="text-center">
          <p>¿Estas seguro que deseas dar como <span class="text-danger">Fallido</span> este Pago?</p>
          <input type="hidden" name="estado" value="falla">
          <input type="hidden" name="idpago" value="<?=$row['IDPAGO']?>">
        </div>
        </div>
     </div>
     <div class="modal-footer">
       <input class="btn btn-success" type="submit" class="btn btn-muted" value="Aceptar">
       <button class="btn btn-muted" type="button"  data-dismiss="modal"  name="button">cancelar</button>
     </div>
    </div>
      </form>
    </div>
  </div>
  <!-- Modal -->
  <div class="modal fade" id="exitoso<?=$row['IDPAGO']?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <!-- Modal conten t-->
        <form method="GET" action="procesar-pago.php">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Procesar Pago</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-row">
          <div class="text-center">
            <p>¿Estas seguro que deseas dar como <span class="text-success">Exitoso</span> este Pago?</p>
            <input type="hidden" name="estado" value="exito">
            <input type="hidden" name="idpago" value="<?=$row['IDPAGO']?>">
          </div>
          </div>
       </div>
       <div class="modal-footer">
         <input class="btn btn-success" type="submit" class="btn btn-muted" value="Aceptar">
         <button class="btn btn-muted" type="button" data-dismiss="modal" name="button">cancelar</button>
       </div>
      </div>
        </form>
      </div>
    </div>
    <?php
  }
}
?>
</tbody>
</table>
</div>
</div>
</main>
</div>
</div>
<script src="../../administracion/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../administracion/vendor/jquery-easing/jquery.easing.min.js"></script>
</html>
<?php
}else{header('Location: ../');}
}else{header('Location: ../');}
?>
