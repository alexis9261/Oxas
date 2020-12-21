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
<h2>Planes de Oxas App</h2>
<div class="container-fluid-center p-2 m-1 bg-light">
  <div class="row">
    <div class="col-10">
      <h4>Nuevo Plan</h4>
    </div>
    <div class="col-2">
      <button class="form-control" type="button" name="button" data-toggle="modal" data-target="#modal-crear">Crear</button>
    </div>
  </div>
</div>

<div class="container table-responsive py-3">
<table class="table table-striped table-sm">
<thead>
<tr>
<th>Nombre</th>
<th>Duración</th>
<th>Monto</th>
<th>...</th>
</tr>
</thead>
<tbody>
<?php
$c=0;
$sql="SELECT * FROM plan";
$result=$conn->query($sql);
if($result->num_rows>0){
while($row=$result->fetch_assoc()){
$c++;
?>
<tr>
<td><?=$row['NOMBRE']?></td>
<td><?=$row['TIEMPO']?> <?php if ($row['IDPLAN']>0){ echo 'Mes(es)';}else{echo 'Dias';} ?></td>
<td><?=$row['MONTO']?> Bs</td>
<td>
   <?php if($row['IDPLAN']>0){ ?>
  <button type="button" class="btn btn-sm" data-toggle="modal" data-target="#editar<?=$row['IDPLAN']?>">Editar</button>
   <button class="btn btn-sm" type="button" name="button" data-toggle="modal" data-target="#eliminar<?=$row['IDPLAN']?>">Eliminar</button>
   <?php } ?>
</td>
</tr>
<!-- Modal -->
<div class="modal fade" id="editar<?=$row['IDPLAN']?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <!-- Modal conten t-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Editar Plan</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
        <form method="GET" action="editar-plan.php">
      <div class="modal-body">
  <div class="form-row">
    <div class="form-group col-md-12">
      <label for="inputEmail4">Nombre del Plan</label>
      <input type="text" class="form-control" id="inputEmail4" placeholder="Nombre de plan" name="nombre" value="<?=$row['NOMBRE']?>" required>
    </div>
  </div>
  <div class="form-group">
     <label for="exampleFormControlTextarea1">Descripción</label>
     <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="descripcion" maxlength="300"  required><?=$row['DESCRIPCION']?></textarea>
   </div>
   <div class="form-row">
       <div class="form-group col-md-6">
         <label for="inputEmail4">Monto</label>
         <input type="number" class="form-control" id="inputEmail4" placeholder="Monto"  name="monto"  value="<?=$row['MONTO']?>"  required>
       </div>
       <div class="form-group col-md-6">
       <label for="inputState">Duracion</label>
       <select id="inputState" name="duracion" class="form-control">
         <option value="1" <?php if($row['TIEMPO']==1){ echo 'selected';} ?>>1 mes</option>
         <option value="3" <?php if($row['TIEMPO']==3){ echo 'selected';} ?>>3 meses</option>
         <option value="6" <?php if($row['TIEMPO']==6){ echo 'selected';} ?>>6 meses</option>
         <option value="12" <?php if($row['TIEMPO']==12){ echo 'selected';} ?>>12 meses</option>
       </select>
     </div>
     <input type="hidden" name="idplan" value="<?=$row['IDPLAN']?>">
     </div>
      </div>
      <div class="modal-footer">
        <input type="submit" class="btn btn-muted" value="Editar">
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Datos -->
<div class="modal fade" id="eliminar<?=$row['IDPLAN']?>" tabindex="-1" role="dialog" aria-labelledby="exampleModal" aria-hidden="true">
  <div class="modal-dialog">
    <!-- Modal conten t-->
    <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
        <form method="GET" action="eliminar-plan.php">
      <div class="modal-body">
      <div class="form-row container-fluid">
        <h6> ¿Desea Eliminar este plan?</h6>
        <small>Ten Presente que el plan sera completamente supprimido del sistema.</small>
     </div>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="idplan" value="<?php echo $row['IDPLAN'] ?>">
        <input type="submit" class="btn btn-muted" value="Eliminar">
      </div>
      </form>
    </div>
  </div>
</div>
<!-- Cantidad de Respuesta creadas -->
<div class="modal fade" id="respuestas_creadas<?=$row['IDUSUARIO']?>" tabindex="-1" role="dialog" aria-labelledby="exampleModal" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title text-muted" id="exampleModal"><?=$row['CORREO']?></h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close" id="respuestas-close<?=$row['IDUSUARIO']?>">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body" id="body_respuestas<?=$row['IDUSUARIO']?>">
</div>
<div class="modal-footer">
</div>
</div>
</div>
</div>

<?php
}
}else{echo "Sin Resultados";}
$conn->close();
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
<!-- Modal -->
<div id="modal-crear" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal conten t-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Crear Plan</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
        <form method="GET" action="crear-plan.php">
      <div class="modal-body">
  <div class="form-row">
    <div class="form-group col-md-12">
      <label for="inputEmail4">Nombre del Plan</label>
      <input type="text" class="form-control" id="inputEmail4" placeholder="Nombre de plan" name="nombre" required>
    </div>

  </div>
  <div class="form-group">
     <label for="exampleFormControlTextarea1">Descripción</label>
     <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="descripcion" maxlength="300" required></textarea>
   </div>
   <div class="form-row">
       <div class="form-group col-md-6">
         <label for="inputEmail4">Monto</label>
         <input type="number" class="form-control" id="inputEmail4" placeholder="Monto"  name="monto" required>
       </div>
       <div class="form-group col-md-6">
       <label for="inputState">Duracion</label>
       <select id="inputState" name="duracion" class="form-control">
         <option value="1" selected>1 mes</option>
         <option value="3">3 meses</option>
         <option value="6">6 meses</option>
         <option value="12">12 meses</option>
       </select>
     </div>
     </div>
      </div>
      <div class="modal-footer">
        <input type="submit" class="btn btn-muted" value="Crear">
      </div>
      </form>
    </div>
  </div>
</div>
<?php
}else{header('Location: ../');}
}else{header('Location: ../');}
?>
