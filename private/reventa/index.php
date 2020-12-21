<?php
session_start();
set_time_limit(0);
require '../../administracion/common/conexion.php';
require '../../administracion/Oxa/Funciones.php';
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
        .sidebar .nav-link .feather {margin-right:2px;color:#999;}
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
      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 m-0 px-0">
        <h2>Usuarios de OxaShop</h2>
        <div class="table-responsive">
          <table class="table table-striped table-sm">
            <thead>
              <tr>
                <th>#</th>
                <th>Correo</th>
                <th>Nombre</th>
                <th>Pais</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $c=0;
              $sql="SELECT r.IDREVENDEDOR,u.CORREO,u.NAME,u.LASTNAME,u.PAIS FROM revendedores r INNER JOIN usuario u WHERE r.USUARIOID=u.IDUSUARIO";
              $result=$conn->query($sql);
              if($result->num_rows>0){
                while($row=$result->fetch_assoc()){
                  ++$c;
                  $id_user=$row['IDREVENDEDOR'];
                  $correo=$row['CORREO'];
                  $name=$row['NAME'];
                  $lastname=$row['LASTNAME'];
                  $pais=$row['PAIS'];
                    ?>
                    <tr>
                      <td><b><?=$c?></b></td>
                      <td><?=$correo?></td>
                      <td><?php echo $name." ".$lastname;?></td>
                      <td><?=$pais?></td>
                    </tr>
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
<?php
}else{header('Location: ../');}
}else{header('Location: ../');}
?>
