<?php
require 'administracion/common/conexion.php';
require "administracion/precios/index.php";
if(isset($_GET['r'])){$respuesta_form=$_GET['r'];}
?>
﻿<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Oxas, La Mejor Aplicación para Vendedores de Mercado Libre en Venezuela">
    <meta name="author" content="EutuxiaWeb Team">
    <meta name="keywords" content="Oxas, Oxas App, Oxasapp, Oxas Venezuela, Oxas Mercadolibre, Respuestas Automaticas, Respondedor automatico Mercadolibre, contestador automatico mercadolibre, Chatbot Mercadolibre">
    <title>Oxas App</title>
    <link href="administracion/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
    <script src="administracion/vendor/jquery/jquery.min.js"></script>
    <script src="administracion/vendor/chartjs/Chart.js"></script>
    <script src='vendor/animejs/anime.min.js'></script>
    <link href="vendor/magnific-popup/magnific-popup.css" rel="stylesheet">
    <link href="css/creative.min.css" rel="stylesheet">
    <link href="administracion/css/style.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="icon" type="image/vnd.microsoft.icon" href="img/favicon.ico">
    <style media="screen">
      .dropdown-item-icon {
        display: inline-block;
        text-align: center;
        font-size: 1rem;
        min-width: 1.2rem;
        -ms-min-width:1.2rem;
        max-width: 1.2rem;
        -ms-max-width:1.2rem;
        max-height:1rem;
        margin-right: 0.25rem;
        padding-bottom: 0.15rem;
      }
      .dropdown-item-icon:hover{
        cursor: pointer;
      }
      .dropbtn_menu {
        background-color: #inherit;
        color: #aaa;
        font-size: 16px;
        border: none;
        cursor: pointer;
        font-weight: bold;
      }
      .dropdown_menu {
        position: relative;
        display: inline-block;
        float:right;
        outline: none;
        padding: 8px 16px;
        font-size: 13px;
      }
      .dropdown-content_menu {
        display: none;
        position: absolute;
        right: 0;
        background-color: inherit;
        min-width: 100px;
        box-shadow: 0px 8px 8px 0px rgba(0,0,0,0.2);
        z-index: 1000;
      }
      .dropdown-content_menu a {
        background-color: #fff;
        color: #444;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
      }
      .dropdown-content_menu a:hover {background-color: #f1f1f1;}
      .dropdown_menu:hover .dropdown-content_menu {display: block;}
      .dropdown_menu:hover .dropbtn_menu {color: #000;}
    </style>
  </head>
  <body id="page-top">
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
      <a class="navbar-brand" href="#page-top">OXAS APP</a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#services">Beneficios</a>
            </li>
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#portfolio">Precio</a>
            </li>
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#contact">Contacto</a>
            </li>
            <li class="nav-item">
              <div class="dropdown_menu">
                <span class="dropbtn_menu">VENEZUELA</span>
                <div class="dropdown-content_menu">
                  <a href="index.php">Venezuela</a>
                  <a href="index2.php">Chile</a>
                </div>
              </div>
            </li>
          </ul>
        </div>
    </nav>
    <header class="masthead text-center text-white d-flex">
      <div class="container my-auto">
        <div class="row">
          <div class="col-lg-10 mx-auto">
            <h1 class="text-uppercase">
              <svg version="1.1" class="img-oxas" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
              	 width="300px" height="300px" viewBox="0 0 202.49 218.989" enable-background="new 0 0 202.49 218.989"
              	 xml:space="preserve">
              <path fill-rule="evenodd" clip-rule="evenodd" fill="#FFFFFF" d="M175.384,113.041c0.805,3.85,0.854,11.896,0,15.734
              	c-0.977,4.395-6.903,14.758-7.646,16.303c-0.6,1.233-4.522,10.378-3.373,13.717c0.403,1.17,3.823,3.148,3.823,3.148l-8.77,20.687
              	l-4.859,15.013l-0.358,0.21l-0.472,0.161l3.216-16.508c0,0,2.533-13.184,1.574-15.291c-1.021-2.242-6.041-4.306-7.758-6.071
              	c-2.039-2.096-5.246-7.099-6.408-9.781c-1.626-3.753-3.854-15.899-3.854-15.899s-14.563,3.703-19.644,4.544
              	c-4.806,0.796-19.675,2.08-19.675,2.08s-3.136,5.407-3.823,7.364c-1.195,3.41-2.692,10.565-2.807,14.176
              	c-0.083,2.634,0.781,5.604,0.545,7.459c-0.145,1.138-1.299,3.228-1.39,4.371c-0.067,0.84,0.5,3.333,0.5,3.333l0.25,18.377
              	l-0.792,0.707l-1.254-11.32l-1.824-8.307c0,0-1.092-1.402-1.297-1.956c-0.219-0.589-0.845-3.282-0.845-3.282l-11.774-19.283
              	c0,0-5.198-5.843-6.753-7.932c-1.204-1.616-3.296-5.086-4.152-6.911c-0.728-1.55-1.815-4.995-2.101-6.684
              	c-0.308-1.823-0.401-7.174-0.401-7.174l0.222-3.588l0.68-6.719l0.208-2.771v-1.08l-0.822-24.035c0,0-0.924-9.348-1.862-12.318
              	c-0.296-0.936-1.442-2.898-2.126-3.3c-0.718-0.422-2.449-0.576-3.282-0.576c-1.388,0-5.543,0.328-5.543,0.328l-6.513,0.5
              	l-6.344-0.095l-3.945-0.325c0,0-2.352-0.282-3.926-1.051c-0.855-0.417-2.399-2.955-2.399-2.955s-1.285-2.956-0.518-3.46
              	c3.975-2.611,11.971-4.802,11.971-4.802s3.355-1.005,4.329-1.629c0.541-0.346,1.826-1.806,1.826-1.806l2.179-2.59
              	c0,0,1.452-2.144,2.01-2.8c0.665-0.781,2.909-2.896,2.909-2.896l2.923-1.869l4.708-1.925c0,0,1.454-0.536,2.783-1.265
              	c0.601-0.331,1.99-2.384,2.192-2.951c0.344-0.958,1.295-2.154,2.023-3.289c1.07-1.666,1.968-4.638,1.968-4.638
              	s1.578,3.884,1.704,5.26c0.208,2.272-1.057,5.027-1.057,5.027s2.73,1.344,3.401,2.082c1.125,1.236,1.338,2.824,1.519,3.841
              	c0.297,1.673,1.021,4.995,1.31,6.67c0.289,1.677,0.72,5.054,1,6.732c0.376,2.253,1.733,8.974,1.733,8.974l1.507,6.118
              	c0,0,1.574,5.751,2.103,7.589c0.783,2.722,2.735,6.639,4.713,7.744c3.63,2.027,16.527,1.855,16.527,1.855s39.836-3.208,52.955-0.199
              	c4.628,1.062,15.795-0.813,15.795-0.813s-0.089,0.782-1.45,3.265c-1.006,1.836-2.953,1.929-2.487,2.422
              	C170.963,99.625,174.557,109.083,175.384,113.041z"/>
              </svg>
            </h1>
            <h1 class="lead text-white"><b>OXAS APP</b></h1>
            <h4><strong class="lead">¡Una aplicación creada por Vendedores para Vendedores!</strong> </h4>
            <hr>
            <a class="btn btn-outline-oxas text-white px-5" href="registro.php?site=MLV">INICIAR</a>
            <a class="btn btn-outline-primary text-white d-none d-sm-block d-md-none px-5" href="registro.php?site=MLV">INICIAR</a>
            <a class="btn btn-outline-primary text-white d-none d-md-block d-lg-none px-5" href="registro.php?site=MLV">INICIAR</a>
          </div>
        </div>
      </div>
    </header>
    <script>
      anime({
        targets: '.img-oxas',
        translateY: 8,
        direction: 'alternate',
        loop: true,
        easing: 'easeInOutSine'
      });
    </script>
    <section class="py-5" style="background-color:#f5f5f5;">
      <div class="container py-5">
        <div class="row justify-content-center">
          <h3 class="text-center">La mejor aplicación para vendedores de Mercado Libre</h3>
        </div>
        <div class="row justify-content-center mt-2">
          <span class="text-muted text-center">Ayudamos a los vendedores a incrementar sus ventas y a automatizar todos sus procesos</span>
        </div>
        <div class="row justify-content-center mt-3">
          <h3>¿Que esperas?</h3>
        </div>
        <div class="row justify-content-center">
          <strong>¡Únete a nosotros!</strong>
        </div>
      </div>
    </section>
    <section id="services">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 text-center">
            <h2 class="section-heading">Beneficios</h2>
            <hr class="my-4">
          </div>
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-md-6 text-center">
            <div class="service-box mt-5 mx-auto">
              <div data-aos="zoom-in-up" data-aos-duration="1500" data-aos-offset="50">
                <svg xmlns="http://www.w3.org/2000/svg" width="40px" class="mb-3" viewBox="0 0 384 512"><path fill="#521a60" d="M360 64c13.255 0 24-10.745 24-24V24c0-13.255-10.745-24-24-24H24C10.745 0 0 10.745 0 24v16c0 13.255 10.745 24 24 24 0 90.965 51.016 167.734 120.842 192C75.016 280.266 24 357.035 24 448c-13.255 0-24 10.745-24 24v16c0 13.255 10.745 24 24 24h336c13.255 0 24-10.745 24-24v-16c0-13.255-10.745-24-24-24 0-90.965-51.016-167.734-120.842-192C308.984 231.734 360 154.965 360 64zM192 208c-57.787 0-104-66.518-104-144h208c0 77.945-46.51 144-104 144z"/></svg>
              </div>
              <h3 class="mb-3" data-aos="fade-zoom-in" data-aos-easing="ease-in-back" data-aos-delay="130" data-aos-offset="200" data-aos-duration="1000">Tiempo</h3>
              <p class="text-muted mb-0" data-aos="fade-zoom-in" data-aos-easing="ease-in-back" data-aos-delay="300" data-aos-offset="150">Reducirás al mínimo el tiempo de respuesta en tus publicaciones.</p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 text-center">
            <div class="service-box mt-5 mx-auto">
              <div data-aos="zoom-in-up" data-aos-duration="1500" data-aos-offset="50">
                <svg xmlns="http://www.w3.org/2000/svg" width="70px" class=" mb-3" viewBox="0 0 640 512"><path fill="#521a60" d="M512.1 191l-8.2 14.3c-3 5.3-9.4 7.5-15.1 5.4-11.8-4.4-22.6-10.7-32.1-18.6-4.6-3.8-5.8-10.5-2.8-15.7l8.2-14.3c-6.9-8-12.3-17.3-15.9-27.4h-16.5c-6 0-11.2-4.3-12.2-10.3-2-12-2.1-24.6 0-37.1 1-6 6.2-10.4 12.2-10.4h16.5c3.6-10.1 9-19.4 15.9-27.4l-8.2-14.3c-3-5.2-1.9-11.9 2.8-15.7 9.5-7.9 20.4-14.2 32.1-18.6 5.7-2.1 12.1.1 15.1 5.4l8.2 14.3c10.5-1.9 21.2-1.9 31.7 0L552 6.3c3-5.3 9.4-7.5 15.1-5.4 11.8 4.4 22.6 10.7 32.1 18.6 4.6 3.8 5.8 10.5 2.8 15.7l-8.2 14.3c6.9 8 12.3 17.3 15.9 27.4h16.5c6 0 11.2 4.3 12.2 10.3 2 12 2.1 24.6 0 37.1-1 6-6.2 10.4-12.2 10.4h-16.5c-3.6 10.1-9 19.4-15.9 27.4l8.2 14.3c3 5.2 1.9 11.9-2.8 15.7-9.5 7.9-20.4 14.2-32.1 18.6-5.7 2.1-12.1-.1-15.1-5.4l-8.2-14.3c-10.4 1.9-21.2 1.9-31.7 0zm-10.5-58.8c38.5 29.6 82.4-14.3 52.8-52.8-38.5-29.7-82.4 14.3-52.8 52.8zM386.3 286.1l33.7 16.8c10.1 5.8 14.5 18.1 10.5 29.1-8.9 24.2-26.4 46.4-42.6 65.8-7.4 8.9-20.2 11.1-30.3 5.3l-29.1-16.8c-16 13.7-34.6 24.6-54.9 31.7v33.6c0 11.6-8.3 21.6-19.7 23.6-24.6 4.2-50.4 4.4-75.9 0-11.5-2-20-11.9-20-23.6V418c-20.3-7.2-38.9-18-54.9-31.7L74 403c-10 5.8-22.9 3.6-30.3-5.3-16.2-19.4-33.3-41.6-42.2-65.7-4-10.9.4-23.2 10.5-29.1l33.3-16.8c-3.9-20.9-3.9-42.4 0-63.4L12 205.8c-10.1-5.8-14.6-18.1-10.5-29 8.9-24.2 26-46.4 42.2-65.8 7.4-8.9 20.2-11.1 30.3-5.3l29.1 16.8c16-13.7 34.6-24.6 54.9-31.7V57.1c0-11.5 8.2-21.5 19.6-23.5 24.6-4.2 50.5-4.4 76-.1 11.5 2 20 11.9 20 23.6v33.6c20.3 7.2 38.9 18 54.9 31.7l29.1-16.8c10-5.8 22.9-3.6 30.3 5.3 16.2 19.4 33.2 41.6 42.1 65.8 4 10.9.1 23.2-10 29.1l-33.7 16.8c3.9 21 3.9 42.5 0 63.5zm-117.6 21.1c59.2-77-28.7-164.9-105.7-105.7-59.2 77 28.7 164.9 105.7 105.7zm243.4 182.7l-8.2 14.3c-3 5.3-9.4 7.5-15.1 5.4-11.8-4.4-22.6-10.7-32.1-18.6-4.6-3.8-5.8-10.5-2.8-15.7l8.2-14.3c-6.9-8-12.3-17.3-15.9-27.4h-16.5c-6 0-11.2-4.3-12.2-10.3-2-12-2.1-24.6 0-37.1 1-6 6.2-10.4 12.2-10.4h16.5c3.6-10.1 9-19.4 15.9-27.4l-8.2-14.3c-3-5.2-1.9-11.9 2.8-15.7 9.5-7.9 20.4-14.2 32.1-18.6 5.7-2.1 12.1.1 15.1 5.4l8.2 14.3c10.5-1.9 21.2-1.9 31.7 0l8.2-14.3c3-5.3 9.4-7.5 15.1-5.4 11.8 4.4 22.6 10.7 32.1 18.6 4.6 3.8 5.8 10.5 2.8 15.7l-8.2 14.3c6.9 8 12.3 17.3 15.9 27.4h16.5c6 0 11.2 4.3 12.2 10.3 2 12 2.1 24.6 0 37.1-1 6-6.2 10.4-12.2 10.4h-16.5c-3.6 10.1-9 19.4-15.9 27.4l8.2 14.3c3 5.2 1.9 11.9-2.8 15.7-9.5 7.9-20.4 14.2-32.1 18.6-5.7 2.1-12.1-.1-15.1-5.4l-8.2-14.3c-10.4 1.9-21.2 1.9-31.7 0zM501.6 431c38.5 29.6 82.4-14.3 52.8-52.8-38.5-29.6-82.4 14.3-52.8 52.8z"/></svg>
              </div>
              <h3 class="mb-3" data-aos="fade-zoom-in" data-aos-easing="ease-in-back" data-aos-delay="120" data-aos-offset="200" data-aos-duration="1000">Automatización</h3>
              <p class="text-muted mb-0" data-aos="fade-zoom-in" data-aos-easing="ease-in-back" data-aos-delay="300" data-aos-offset="150">Estandarizar la atención a tus posibles compradores.</p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 text-center">
            <div class="service-box mt-5 mx-auto">
              <div data-aos="zoom-in-up" data-aos-duration="1500" data-aos-offset="50">
                <svg xmlns="http://www.w3.org/2000/svg" width="56px" class=" mb-3" viewBox="0 0 512 512"><path fill="#521a60" d="M3.263 139.527c0-7.477 3.917-11.572 11.573-11.572h15.131V88.078c0-5.163.534-10.503.534-10.503h-.356s-1.779 2.67-2.848 3.738c-4.451 4.273-10.504 4.451-15.666-1.068l-5.518-6.231c-5.342-5.341-4.984-11.216.534-16.379l21.72-19.938C32.815 33.602 36.732 32 42.785 32H54.89c7.656 0 11.749 3.916 11.749 11.572v84.384h15.488c7.655 0 11.572 4.094 11.572 11.572v8.901c0 7.477-3.917 11.572-11.572 11.572H14.836c-7.656 0-11.573-4.095-11.573-11.572v-8.902zM2.211 304.591c0-47.278 50.955-56.383 50.955-69.165 0-7.18-5.954-8.755-9.28-8.755-3.153 0-6.479 1.051-9.455 3.852-5.079 4.903-10.507 7.004-16.111 2.451l-8.579-6.829c-5.779-4.553-7.18-9.805-2.803-15.409C13.592 201.981 26.025 192 47.387 192c19.437 0 44.476 10.506 44.476 39.573 0 38.347-46.753 46.402-48.679 56.909h39.049c7.529 0 11.557 4.027 11.557 11.382v8.755c0 7.354-4.028 11.382-11.557 11.382h-67.94c-7.005 0-12.083-4.028-12.083-11.382v-4.028zM5.654 454.61l5.603-9.28c3.853-6.654 9.105-7.004 15.584-3.152 4.903 2.101 9.63 3.152 14.359 3.152 10.155 0 14.358-3.502 14.358-8.23 0-6.654-5.604-9.106-15.934-9.106h-4.728c-5.954 0-9.28-2.101-12.258-7.88l-1.05-1.926c-2.451-4.728-1.226-9.806 2.801-14.884l5.604-7.004c6.829-8.405 12.257-13.483 12.257-13.483v-.35s-4.203 1.051-12.608 1.051H16.685c-7.53 0-11.383-4.028-11.383-11.382v-8.755c0-7.53 3.853-11.382 11.383-11.382h58.484c7.529 0 11.382 4.027 11.382 11.382v3.327c0 5.778-1.401 9.806-5.079 14.183l-17.509 20.137c19.611 5.078 28.716 20.487 28.716 34.845 0 21.363-14.358 44.126-48.503 44.126-16.636 0-28.192-4.728-35.896-9.455-5.779-4.202-6.304-9.805-2.626-15.934zM144 132h352c8.837 0 16-7.163 16-16V76c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h352c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h352c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z"/></svg>
              </div>
              <h3 class="mb-3" data-aos="fade-zoom-in" data-aos-easing="ease-in-back" data-aos-delay="300" data-aos-offset="200" data-aos-duration="1000">Posicionamiento</h3>
              <p class="text-muted mb-0" data-aos="fade-zoom-in" data-aos-easing="ease-in-back" data-aos-delay="300" data-aos-offset="150">Tener un mejor promedio de respuesta, aumenta tu posicionamiento en Mercado Libre.</p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-4 col-md-6 text-center">
            <div class="service-box mt-5 mx-auto">
              <div data-aos="zoom-in-up" data-aos-duration="1500" data-aos-offset="50">
                <svg xmlns="http://www.w3.org/2000/svg" width="70px" class="mb-3" viewBox="0 0 576 512"><path fill="#521a60" d="M504.717 320H211.572l6.545 32h268.418c15.401 0 26.816 14.301 23.403 29.319l-5.517 24.276C523.112 414.668 536 433.828 536 456c0 31.202-25.519 56.444-56.824 55.994-29.823-.429-54.35-24.631-55.155-54.447-.44-16.287 6.085-31.049 16.803-41.548H231.176C241.553 426.165 248 440.326 248 456c0 31.813-26.528 57.431-58.67 55.938-28.54-1.325-51.751-24.385-53.251-52.917-1.158-22.034 10.436-41.455 28.051-51.586L93.883 64H24C10.745 64 0 53.255 0 40V24C0 10.745 10.745 0 24 0h102.529c11.401 0 21.228 8.021 23.513 19.19L159.208 64H551.99c15.401 0 26.816 14.301 23.403 29.319l-47.273 208C525.637 312.246 515.923 320 504.717 320zM408 168h-48v-40c0-8.837-7.163-16-16-16h-16c-8.837 0-16 7.163-16 16v40h-48c-8.837 0-16 7.163-16 16v16c0 8.837 7.163 16 16 16h48v40c0 8.837 7.163 16 16 16h16c8.837 0 16-7.163 16-16v-40h48c8.837 0 16-7.163 16-16v-16c0-8.837-7.163-16-16-16z"/></svg>
              </div>
              <h3 class="mb-3" data-aos="fade-zoom-in" data-aos-easing="ease-in-back" data-aos-delay="300" data-aos-offset="200" data-aos-duration="1000">Ventas</h3>
              <p class="text-muted mb-0" data-aos="fade-zoom-in" data-aos-easing="ease-in-back" data-aos-delay="300" data-aos-offset="150">Reducir el tiempo de respuesta, aumenta exponencialmente la probabilidad de concretar ventas.</p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 text-center">
            <div class="service-box mt-5 mx-auto">
              <div data-aos="zoom-in-up" data-aos-duration="1500" data-aos-offset="50">
                <svg xmlns="http://www.w3.org/2000/svg" width="65px" class=" mb-3" viewBox="0 0 512 512"><path fill="#521a60" d="M332.8 320h38.4c6.4 0 12.8-6.4 12.8-12.8V172.8c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v134.4c0 6.4 6.4 12.8 12.8 12.8zm96 0h38.4c6.4 0 12.8-6.4 12.8-12.8V76.8c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v230.4c0 6.4 6.4 12.8 12.8 12.8zm-288 0h38.4c6.4 0 12.8-6.4 12.8-12.8v-70.4c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v70.4c0 6.4 6.4 12.8 12.8 12.8zm96 0h38.4c6.4 0 12.8-6.4 12.8-12.8V108.8c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v198.4c0 6.4 6.4 12.8 12.8 12.8zM496 384H64V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v336c0 17.67 14.33 32 32 32h464c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"/></svg>
              </div>
              <h3 class="mb-3" data-aos="fade-zoom-in" data-aos-easing="ease-in-back" data-aos-delay="120" data-aos-offset="200" data-aos-duration="1000">Estadísticas</h3>
              <p class="text-muted mb-0" data-aos="fade-zoom-in" data-aos-easing="ease-in-back" data-aos-delay="300" data-aos-offset="150">Podrás ver las cantidades de Visitas, Preguntas y Ventas que has obtenido en tus publicaciones.</p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 text-center">
            <div class="service-box mt-5 mx-auto">
              <div data-aos="zoom-in-up" data-aos-duration="1500" data-aos-offset="50">
                <svg xmlns="http://www.w3.org/2000/svg" width="65px" class="mb-3"viewBox="0 0 512 512"><path fill="#521a60" d="M496 384H64V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v336c0 17.67 14.33 32 32 32h464c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16zM464 96H345.94c-21.38 0-32.09 25.85-16.97 40.97l32.4 32.4L288 242.75l-73.37-73.37c-12.5-12.5-32.76-12.5-45.25 0l-68.69 68.69c-6.25 6.25-6.25 16.38 0 22.63l22.62 22.62c6.25 6.25 16.38 6.25 22.63 0L192 237.25l73.37 73.37c12.5 12.5 32.76 12.5 45.25 0l96-96 32.4 32.4c15.12 15.12 40.97 4.41 40.97-16.97V112c.01-8.84-7.15-16-15.99-16z"/></svg>
              </div>
              <h3 class="mb-3" data-aos="fade-zoom-in" data-aos-easing="ease-in-back" data-aos-delay="120" data-aos-offset="200" data-aos-duration="1000">Tendencias</h3>
              <p class="text-muted mb-0" data-aos="fade-zoom-in" data-aos-easing="ease-in-back" data-aos-delay="300" data-aos-offset="150">Podrás ajustar el título de tus publicaciones a las palabras más buscados por los compradores en Mercado Libre.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="bg-dark">
      <div class="container mt-4 pt-3">
        <div class="row justify-content-center align-items-center">
          <div class="col-sm-6 text-center" data-aos="fade-right" data-aos-duration="1000">
            <svg class="mb-5 img-oxas" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="150px" height="150px"><path fill="#ddd" d="M505.04 442.66l-99.71-99.69c-4.5-4.5-10.6-7-17-7h-16.3c27.6-35.3 44-79.69 44-127.99C416.03 93.09 322.92 0 208.02 0S0 93.09 0 207.98s93.11 207.98 208.02 207.98c48.3 0 92.71-16.4 128.01-44v16.3c0 6.4 2.5 12.5 7 17l99.71 99.69c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.59.1-33.99zm-297.02-90.7c-79.54 0-144-64.34-144-143.98 0-79.53 64.35-143.98 144-143.98 79.54 0 144 64.34 144 143.98 0 79.53-64.35 143.98-144 143.98zm27.11-152.54l-45.01-13.5c-5.16-1.55-8.77-6.78-8.77-12.73 0-7.27 5.3-13.19 11.8-13.19h28.11c4.56 0 8.96 1.29 12.82 3.72 3.24 2.03 7.36 1.91 10.13-.73l11.75-11.21c3.53-3.37 3.33-9.21-.57-12.14-9.1-6.83-20.08-10.77-31.37-11.35V112c0-4.42-3.58-8-8-8h-16c-4.42 0-8 3.58-8 8v16.12c-23.63.63-42.68 20.55-42.68 45.07 0 19.97 12.99 37.81 31.58 43.39l45.01 13.5c5.16 1.55 8.77 6.78 8.77 12.73 0 7.27-5.3 13.19-11.8 13.19h-28.1c-4.56 0-8.96-1.29-12.82-3.72-3.24-2.03-7.36-1.91-10.13.73l-11.75 11.21c-3.53 3.37-3.33 9.21.57 12.14 9.1 6.83 20.08 10.77 31.37 11.35V304c0 4.42 3.58 8 8 8h16c4.42 0 8-3.58 8-8v-16.12c23.63-.63 42.68-20.54 42.68-45.07 0-19.97-12.99-37.81-31.59-43.39z"/></svg>
            <h3 class="section-heading text-white px-5" data-aos="fade-left">¡Las palabras mas buscadas en Mercado Libre!</h3>
            <hr class="light my-4">
            <p class="text-faded mb-4">¿alguna vez has querido saber cuales son las frases y/o palabras con las cuales estan buscando los compradores?
            Podrás saber cuales son las mejores palabras y frases para obtener mayor posicionamiento de tus publicaciones en Mercado Libre</p>
          </div>
          <div class="col-sm-6 text-center" data-aos="fade-left" data-aos-duration="1000">
            <svg class="mb-5 img-oxas" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512" width="150px" height="150px"><path fill="#ddd" d="M0 256c0 137 111 248 248 248s248-111 248-248S385 8 248 8 0 119 0 256zm200-48c0 17.7-14.3 32-32 32s-32-14.3-32-32 14.3-32 32-32 32 14.3 32 32zm158.5 16.5c-14.8-13.2-46.2-13.2-61 0L288 233c-8.3 7.4-21.6.4-19.8-10.8 4-25.2 34.2-42.1 59.9-42.1S384 197 388 222.2c1.7 11.1-11.4 18.3-19.8 10.8l-9.7-8.5zM157.8 325.8C180.2 352.7 213 368 248 368s67.8-15.4 90.2-42.2c13.6-16.2 38.1 4.2 24.6 20.5C334.3 380.4 292.5 400 248 400s-86.3-19.6-114.8-53.8c-13.5-16.3 11.2-36.7 24.6-20.4z"/></svg>
            <h3 class="section-heading text-white px-5" data-aos="fade-right">¡Respondemos por ti a cualquier hora del día!</h3>
            <hr class="light my-4">
            <p class="text-faded mb-4">Sin importar que dia del año sea, o la hora en la que el cliente realice la pregunta, la aplicación responderá por ti.
              ¡Y podrás concretar una venta a cualquier hora del día!</p>
          </div>
        </div>
      </div>
    </section>
    <script>
      anime({
        targets: '.img-oxas',
        translateY: 8,
        direction: 'alternate',
        loop: true,
        easing: 'easeInOutSine'
      });
    </script>
    <div class="container py-5" id="portfolio">
    <div class="row justify-content-center my-5">
      <h2 class="text-center col-12 mt-4 mb-3">¡El Mejor Precio!</h2>
      <hr>
      <div class="col-md-5 justify-self-center">
        <hr>
        <div class="dflex">
          <div class="alert alert-secondary text-center" role="alert" data-aos="fade-right" data-aos-duration="3000" data-aos-offset="150">
            1 cuenta de Mercado Libre (¡Por Ahora!)
          </div>
          <div class="alert alert-secondary text-center" role="alert" data-aos="fade-left" data-aos-duration="3000" data-aos-offset="150">
            Publicaciones Ilimitadas
          </div>
          <div class="alert alert-secondary text-center" role="alert" data-aos="fade-right" data-aos-duration="3000" data-aos-offset="150">
            Seguimiento de Tendencias por Publicación y por Pais
          </div>
          <div class="alert alert-secondary text-center" role="alert" data-aos="fade-left" data-aos-duration="3000" data-aos-offset="150">
            Ilimitadas Respuestas programadas
          </div>
          <div class="alert alert-secondary text-center" role="alert" data-aos="fade-right" data-aos-duration="3000" data-aos-offset="150">
            Visualización de Ventas
          </div>
          <div class="alert alert-secondary text-center" role="alert" data-aos="fade-left" data-aos-duration="3000" data-aos-offset="150">
            Estadísticas de Visitas, Preguntas, Ventas y Tendencias
          </div>
          <?php $sql="SELECT * FROM plan";
          $r=$conn->query($sql);
          if($r->num_rows>0){
            while($row=$r->fetch_assoc()){
              if($row['NOMBRE']=="Plan Basico"){ ?>
                <div class="alert alert-primary text-center" role="alert" data-aos="zoom-out-down" data-aos-duration="3000" data-aos-offset="150">
                  <strong><?php echo number_format(round(($row['MONTO']*$dolar)/1000)*1000,2,',','.');?> Bs Mensual - ¡Luego de los 30 días Gratis!</strong>
                </div>
                <?php
              }
            }
          }
          ?>
        </div>
      </div>
      <div class="col-12 text-center mt-3 mb-5">
        <a class="btn-outline-oxas py-2 px-5" href="registro.php?site=MLV">
          Iniciar
        </a>
      </div>
    </div>
    </div>
    <section id="contact" class="bg-light">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 mx-auto text-center">
            <h2 class="section-heading">Comunícate con nosotros</h2>
            <hr class="my-4">
            <p class="mb-5">Estamos esperando por tí, deseosos de ayudarte a aumentar tus ventas.</p>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-4 ml-auto text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="40px" class="mb-3" viewBox="0 0 512 512"><path fill='#521a60' d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.5-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.5 464-464 0-11.2-7.7-20.9-18.6-23.4z"/></svg>
            <p>+58 424 498 29 51</p>
            <small>Lunes a Viernes. 8:00 AM - 5:00 PM</small>
          </div>
          <div class="col-lg-4 mr-auto text-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="40px" class="mb-3" viewBox="0 0 512 512"><path fill='#521a60' d="M512 464c0 26.51-21.49 48-48 48H48c-26.51 0-48-21.49-48-48V200.724a48 48 0 0 1 18.387-37.776c24.913-19.529 45.501-35.365 164.2-121.511C199.412 29.17 232.797-.347 256 .003c23.198-.354 56.596 29.172 73.413 41.433 118.687 86.137 139.303 101.995 164.2 121.512A48 48 0 0 1 512 200.724V464zm-65.666-196.605c-2.563-3.728-7.7-4.595-11.339-1.907-22.845 16.873-55.462 40.705-105.582 77.079-16.825 12.266-50.21 41.781-73.413 41.43-23.211.344-56.559-29.143-73.413-41.43-50.114-36.37-82.734-60.204-105.582-77.079-3.639-2.688-8.776-1.821-11.339 1.907l-9.072 13.196a7.998 7.998 0 0 0 1.839 10.967c22.887 16.899 55.454 40.69 105.303 76.868 20.274 14.781 56.524 47.813 92.264 47.573 35.724.242 71.961-32.771 92.263-47.573 49.85-36.179 82.418-59.97 105.303-76.868a7.998 7.998 0 0 0 1.839-10.967l-9.071-13.196z"/></svg>
            <p>
              <a href="mailto:oxasvzla@gmail.com">oxasvzla@gmail.com</a>
            </p>
          </div>
        </div>
      </div>
    </section>
    <footer class="container-fluid bg-dark">
      <div class="row justify-content-center py-2">
      <small class="text-white">Desarrollado por: <a href="http://eutuxia.org.ve" target='_blank'>Eutuxia Web</a> </samll>
      </div>
    </footer>
    <script>
      window.onload = function() {
        var respuesta=<?php echo $respuesta_form;?>;
        if (typeof respuesta !== 'undefined') {
          if (respuesta==1) {
            Swal.fire({
              title: "¡El formulario fue enviado exitosamente!",
              showClass: {popup: 'animated fadeInDown faster'},
              hideClass: {popup: 'animated fadeOutUp faster'}
            })
          }else if (respuesta==2) {
            Swal.fire({
              title: "Ya habias llenado el formulario",
              text: 'Si deseas cambiar algún valor del formulario, ponte en contacto con nosotros por el chat de Mercado Libre',
              showClass: {popup: 'animated fadeInDown faster'},
              hideClass: {popup: 'animated fadeOutUp faster'}
            })
          }else{
            Swal.fire({
              title: "Hubo un error! :(",
              text: 'Te pedimos disculpas!! y te invitamos a que vuelvas a llenar el formulario. ;)',
              showClass: {popup: 'animated fadeInDown faster'},
              hideClass: {popup: 'animated fadeOutUp faster'}
            })
          }
        }
      };
    </script>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fa fa-angle-up"></i></a>
    <script src="administracion/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="administracion/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/scrollreveal/scrollreveal.min.js"></script>
    <script src="vendor/magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="js/creative.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@7.29.0/dist/sweetalert2.all.min.js'></script>
    <script>
      AOS.init();
    </script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-135364997-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-135364997-1');
    </script>
  </body>
</html>
