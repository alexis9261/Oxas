<?php
session_start();
require '../common/meli.php';require '../common/conexion.php';
require '../Oxa/Funciones.php';require '../common/take_at.php'; require '../common/account-off.php';
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
  <title>Oxas - OxaShop</title>
  <link href='/administracion/vendor/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
  <link href='/administracion/css/sb-admin.min.css' rel='stylesheet'>
  <link href='/administracion/css/style.css' rel='stylesheet'>
  <script src='/administracion/vendor/jquery/jquery.min.js'></script>
</head>
<body class='fixed-nav sticky-footer bg-dark' id='page-top'>
  <?php include '../common/navbar.php';?>
  <div class='content-wrapper'>
    <div class='container-fluid'>
      <section class='breadcrumb'>
        <div class='container-fluid'>
          <div class='row'>
            <h5 class='col-sm-8 m-1'>Seccion OxaShop</h5>
          </div>
        </div>
      </section>
      <section class="container mt-3">
        <div class="row">
          <div class="col-auto">
            <h5 class='mb-4'>
              Crear publicaciones <br> <small class="text-muted">Desde la cuenta de otro vendedor</small>
            </h5>
          </div>
          <div class="col-auto ml-auto">
            <a class="btn btn-success px-4" href="reventa/index.php">Revender</a>
          </div>
        </div>
      </section>
      <section class="container mt-3">
        <div class="row">
          <div class="col-auto">
            <h5>Gestionar Tus Vendedores del Modelo OxaShop  </h5>
            <small class="text-muted">Estos vendedores se les enviara el formulario de tus compradores</small>
          </div>
          <div class="col-auto ml-auto">
            <a class="btn btn-primary px-4" href="vendedores/index.php">Gestionar Vendedores</a>
          </div>
        </div>
      </section>
    </div>
  </div>
  <?php include '../common/footer.php';?>
  <script src='/administracion/vendor/jquery/jquery.min.js'></script>
  <script src='/administracion/vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
  <script src='/administracion/vendor/jquery-easing/jquery.easing.min.js'></script>
  <script src='/administracion/js/sb-admin.min.js'></script>
</div>
</body>
</html>
