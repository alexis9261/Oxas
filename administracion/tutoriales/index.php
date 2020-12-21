<?php
session_start();
require '../common/conexion.php';
require '../common/meli.php';
require '../common/configApp.php';
require '../common/take_at.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="shortcut icon" href="../../img/favicon.ico">
  <title>Oxas - Tutoriales</title>
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/sb-admin.min.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet"/>
  <link rel="icon" type="image/vnd.microsoft.icon" href="../../img/favicon.ico">
</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <?php include '../common/navbar.php';?>
  <div class="content-wrapper">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active">Tutoriales</li>
      </ol>
      <section class="container">
        <div class="row breadcrumb">
          <a class="col-1 text-center" data-toggle="modal" href="#" data-target="#tutorial4">
            <span title="Ver tutorial" data-toggle="tooltip">
              <svg xmlns="http://www.w3.org/2000/svg" width="15px" class="svg-secondary" viewBox="0 0 448 512"><path d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"/></svg>
            </span>
          </a>
          <span class="col-10 text-center">Estadísticas y Tendencias</span>
        </div>
        <div class="row breadcrumb">
          <a class="col-1 text-center" data-toggle="modal" href="#" data-target="#tutorial1">
            <span title="Ver tutorial" data-toggle="tooltip">
              <svg xmlns="http://www.w3.org/2000/svg" width="15px" class="svg-secondary" viewBox="0 0 448 512"><path d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"/></svg>
            </span>
          </a>
          <span class="col-10 text-center">¿Como Crear una Respuesta Automatica?</span>
        </div>
        <div class="row breadcrumb">
          <a class="col-1 text-center" data-toggle="modal" href="#" data-target="#tutorial5">
            <span title="Ver tutorial" data-toggle="tooltip">
              <svg xmlns="http://www.w3.org/2000/svg" width="15px" class="svg-secondary" viewBox="0 0 448 512"><path d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"/></svg>
            </span>
          </a>
          <span class="col-10 text-center">Preguntas Pendientes</span>
        </div>
        <div class="row breadcrumb">
          <a class="col-1 text-center" data-toggle="modal" href="#" data-target="#tutorial2">
            <span title="Ver tutorial" data-toggle="tooltip">
              <svg xmlns="http://www.w3.org/2000/svg" width="15px" class="svg-secondary" viewBox="0 0 448 512"><path d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"/></svg>
            </span>
          </a>
          <span class="col-10 text-center">Simulador de Respuestas Automaticas</span>
        </div>
        <div class="row breadcrumb">
          <a class="col-1 text-center" data-toggle="modal" href="#" data-target="#tutorial3">
            <span title="Ver tutorial" data-toggle="tooltip">
              <svg xmlns="http://www.w3.org/2000/svg" width="15px" class="svg-secondary" viewBox="0 0 448 512"><path d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"/></svg>
            </span>
          </a>
          <span class="col-10 text-center">Manejo de Ventas</span>
        </div>
        <div class="row breadcrumb">
          <a class="col-1 text-center" data-toggle="modal" href="#" data-target="#tutorial7">
            <span title="Ver tutorial" data-toggle="tooltip">
              <svg xmlns="http://www.w3.org/2000/svg" width="15px" class="svg-secondary" viewBox="0 0 448 512"><path d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"/></svg>
            </span>
          </a>
          <span class="col-10 text-center">¿Como Crear, Ediar y Eliminar Notas en Ventas?</span>
        </div>
        <div class="row breadcrumb">
          <a class="col-1 text-center" data-toggle="modal" href="#" data-target="#tutorial6">
            <span title="Ver tutorial" data-toggle="tooltip">
              <svg xmlns="http://www.w3.org/2000/svg" width="15px" class="svg-secondary" viewBox="0 0 448 512"><path d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"/></svg>
            </span>
          </a>
          <span class="col-10 text-center">¿Como Calificar Ventas Abiertas?</span>
        </div>
        <div class="row breadcrumb">
          <a class="col-1 text-center" data-toggle="modal" href="#" data-target="#tutorial8">
            <span title="Ver tutorial" data-toggle="tooltip">
              <svg xmlns="http://www.w3.org/2000/svg" width="15px" class="svg-secondary" viewBox="0 0 448 512"><path d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"/></svg>
            </span>
          </a>
          <span class="col-10 text-center">Pausar y Reactivar Publicaciones</span>
        </div>
    </section>
    </div>
    <?php include '../common/footer.php';?>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin.min.js"></script>
  </div>
</body>
</html>

<div class="modal fade" id="tutorial1" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="exampleModalCenterTitle2">Ver Tutorial</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body text-center">
      <iframe width="100%" height="315" src="https://www.youtube.com/embed/UzaJyiAgUoQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary" data-dismiss="modal">Listo</button>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="tutorial2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="exampleModalCenterTitle2">Ver Tutorial</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body text-center">
        <iframe width="100%" height="315" src="https://www.youtube.com/embed/cpNM4IajAIY" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary" data-dismiss="modal">Listo</button>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="tutorial3" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="exampleModalCenterTitle2">Ver Tutorial</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body text-center">
      <iframe width="100%" height="315" src="https://www.youtube.com/embed/eGKtaNR1NZE" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary" data-dismiss="modal">Listo</button>
    </div>
  </div>
</div>
</div>
<div class="modal fade" id="tutorial4" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="exampleModalCenterTitle2">Ver Tutorial</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body text-center">
      <iframe width="100%" height="315" src="https://www.youtube.com/embed/LwJDnDk8E5Y" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary" data-dismiss="modal">Listo</button>
    </div>
  </div>
</div>
</div>
<div class="modal fade" id="tutorial5" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="exampleModalCenterTitle2">Ver Tutorial</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body text-center">
      <iframe width="100%" height="315"  src="https://www.youtube.com/embed/9LII05KGtQ8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary" data-dismiss="modal">Listo</button>
    </div>
  </div>
</div>
</div>
<div class="modal fade" id="tutorial6" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="exampleModalCenterTitle2">Ver Tutorial</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body text-center">
      <iframe width="100%" height="315"  src="https://www.youtube.com/embed/lCHl8cZsbVU" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary" data-dismiss="modal">Listo</button>
    </div>
  </div>
</div>
</div>
<div class="modal fade" id="tutorial7" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="exampleModalCenterTitle2">Ver Tutorial</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body text-center">
      <iframe width="100%" height="315"  src="https://www.youtube.com/embed/fbIgsqmwNmA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary" data-dismiss="modal">Listo</button>
    </div>
  </div>
</div>
</div>
<div class="modal fade" id="tutorial8" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="exampleModalCenterTitle2">Ver Tutorial</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body text-center">
      <iframe width="100%" height="315"  src="https://www.youtube.com/embed/fzgxdSvAy1s" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-primary" data-dismiss="modal">Listo</button>
    </div>
  </div>
</div>
</div>
