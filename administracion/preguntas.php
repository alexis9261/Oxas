<?php
session_start();
require 'common/meli.php';require 'common/conexion.php';
require 'Oxa/Funciones.php';require 'common/take_at.php'; require 'common/account-off.php';
?>
<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
  <meta name='description' content=''>
  <meta name='author' content=''>
  <link rel='shortcut icon' href='../img/favicon.ico'>
  <title>Oxas - Respuestas Automáticas</title>
  <link href='vendor/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
  <link href='css/sb-admin.min.css' rel='stylesheet'>
  <link href='css/style.css' rel='stylesheet'>
  <script src='vendor/jquery/jquery.min.js'></script>
</head>
<body class='fixed-nav sticky-footer bg-dark' id='page-top'>
  <?php include 'common/navbar.php';?>
  <div class='content-wrapper'>
    <div class='container-fluid'>
      <section class='breadcrumb'>
        <div class='container-fluid'>
          <div class='row'>
            <h5 class='col-sm-8 m-1'>Seccion de Preguntas</h5>
          </div>
        </div>
      </section>
      <section class="container mt-3">
        <div class="row">
          <div class="col-auto">
            <?php
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/questions/search?seller_id='.$id_user.'&status=unanswered&access_token='.$AccessToken);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);$total_preguntas=$consulta->total;
            ?>
            <h5 class='mb-4'>
              Preguntas Pendientes <?php if($total_preguntas!=0){ ?><span class="align-self-center badge_preg"><?php echo $total_preguntas;?></span><?php } ?>
            </h5>
          </div>
          <div class="col-auto ml-auto">
            <a class="btn btn-success px-4" href="preguntas/index.php">Ver las preguntas pendientes</a>
          </div>
        </div>
      </section>
      <section class="container mt-3">
        <div class="row">
          <div class="col-auto">
            <h5 class='mb-4'>Respuestas Automáticas Creadas</h5>
          </div>
          <div class="col-auto ml-auto">
            <a class="btn btn-primary px-4" href="respuestas_automaticas/index.php">Ver las respuestas automáticas creadas</a>
          </div>
        </div>
      </section>
    </div>
  </div>
  <?php include 'common/footer.php';?>
  <script src='vendor/jquery/jquery.min.js'></script>
  <script src='vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
  <script src='vendor/jquery-easing/jquery.easing.min.js'></script>
  <script src='js/sb-admin.min.js'></script>
</div>
</body>
</html>
