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
        <h2>Clientes Oxas</h2>
        <div class="table-responsive">
          <table class="table table-striped table-sm">
            <thead>
              <tr>
                <th>#</th>
                <th>Nickname</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>En Oxas</th>
                <th>Vencimiento</th>
                <th>Ultima Vez en Oxas</th>
                <th title="Publicaciones Activas">Pub</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              $c=0;
              $sql="SELECT u.IDUSUARIO,u.CORREO,u.NAME,u.LASTNAME,u.TELPPAL,u.TELSECOND,u.ACCESSTOKEN,u.REFRESTOKEN,u.ULTIMAFECHA,s.FECHAREGISTRO,s.ESTATUS,s.FECHAVENCIMIENTO FROM usuario u
              INNER JOIN suscripcion s ON s.IDUSUARIO=u.IDUSUARIO ORDER BY s.FECHAREGISTRO DESC;";
              $result=$conn->query($sql);
              if($result->num_rows>0){
                while($row=$result->fetch_assoc()){
                  $id_user=$row['IDUSUARIO'];
                  $correo=$row['CORREO'];
                  $name=ucfirst(strtolower($row['NAME']));
                  $lastname=ucfirst(strtolower($row['LASTNAME']));
                  $telppal=$row['TELPPAL'];
                  $telsecond=$row['TELSECOND'];
                  $accessToken=$row['ACCESSTOKEN'];
                  $refreshToken=$row['REFRESTOKEN'];
                  $fechaRegistro=$row['FECHAREGISTRO'];
                  $ultimaFecha=$row['ULTIMAFECHA'];
                  $estatus=$row['ESTATUS'];
                  $dateVencimiento=$row['FECHAVENCIMIENTO'];
                  //Ver cual es el ESTATUS
                  if($estatus==0){
                    $estatus_color="00ca00";
                    $text_estatus="Al dia";
                  }elseif($estatus==1){
                    $estatus_color="ff3b0a";
                    $text_estatus="Vencido";
                  }elseif($estatus==2){
                    $estatus_color="ffe733";
                    $text_estatus="Por vencer (max 3 dias)";
                  }
                  $c++;
                  $ch=curl_init();
                  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/users/$id_user");
                  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                  $w=curl_exec($ch);
                  curl_reset($ch);
                  $consulta=json_decode($w);
                  $permalink=$consulta->permalink;
                  $nickname=$consulta->nickname;
                  $nicknameURL=urlencode($nickname);
                  $ciudad=$consulta->address->city;
                  $estado=$consulta->address->state;
                  $reputacion=$consulta->seller_reputation->level_id;
                  $mercadoLider=$consulta->seller_reputation->power_seller_status;
                  $ventasTotales=$consulta->seller_reputation->transactions->total;
                  $concretadas=$consulta->seller_reputation->transactions->completed;
                  if($mercadoLider=="silver"){
                    $mercadoLider="Plata";
                  }elseif($mercadoLider=="gold"){
                    $mercadoLider="Oro";
                  }elseif($mercadoLider=="platinum"){
                    $mercadoLider="Platinum";
                  }else{
                    $mercadoLider="No es Mercado Libre";
                  }
                  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/MLV/search?nickname=$nicknameURL");
                  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                  $w=curl_exec($ch);
                  curl_close($ch);
                  $consulta=json_decode($w);
                  $publicaciones=$consulta->paging->total;
                    ?>
                    <tr>
                      <td><b><?=$c?></b></td>
                      <td><a href="<?=$permalink?>" target="_blank"><?=$nickname?></a></td>
                      <td><?php echo $name." ".$lastname;?></td>
                      <td><?=$correo?></td>
                      <td><?=$fechaRegistro?></td>
                      <td><?=$dateVencimiento?></td>
                      <td><?=$ultimaFecha?></td>
                      <td title="Publicaciones Activas" data-toogle="tooltip"><a href="https://listado.mercadolibre.com.ve/_CustId_<?php echo $id_user;?>" target="_blank"><?=$publicaciones?></a></td>
                      <td>
                        <span title="<?php echo $text_estatus;?>"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="15px"><path fill="#<?php echo $estatus_color; ?>" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z"/></svg></span>
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#+<?php echo $id_user;?>">+</button>
                      </td>
                    </tr>
                    <!-- Modal -->
                    <div class="modal fade" id="+<?php echo $id_user;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title text-muted" id="exampleModalLabel"><?php echo $nickname;?> || Id: <?php echo $id_user;?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-auto">
                                <h6>Telefonos:</h6>
                                <strong><?php echo $telppal;?></strong>
                                <br>
                                <strong><?php echo $telsecond;?></strong>
                              </div>
                            </div>
                            <hr>
                            <div class="row justify-content-center"><h6>Reputación</h6></div>
                            <div class="row justify-content-center mb-3">
                              <?php if ($reputacion=="1_red") { ?>
                                <span class='mal bord' style='background-color: #ff191d;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span>
                              <?php }else if ($reputacion=="2_orange") { ?>
                                <span class='mal' style='background-color: #ffc6a5;'></span><span class='medio bord' style='background-color: #ff8419;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span>
                              <?php }else if ($reputacion=="3_yellow") { ?>
                                <span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med bord' style='background-color: #ffff36;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span>
                              <?php }else if ($reputacion=="4_light_green") { ?>
                                <span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien bord' style='background-color: #58ff3f;'></span><span class='exc' style='background-color: #d2ffb0;'></span>
                              <?php }elseif ($reputacion=="5_green") { ?>
                                <span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc bord' style='background-color: #00ca00;'></span>
                              <?php }else { ?>
                                <span class='mal' style='background-color: #beccc1;'></span><span class='medio' style='background-color: #beccc1;'></span><span class='med' style='background-color: #beccc1;'></span><span class='bien' style='background-color: #beccc1;'></span><span class='exc' style='background-color: #beccc1;'></span>
                                <?php } ?>
                            </div>
                            <div class="row justify-content-center">
                              <b class="text-muted">Mercado Lider:&nbsp; </b>
                              <strong> <?php echo $mercadoLider;?></strong>
                            </div>
                            <div class="row justify-content-center">
                              <strong><span class="text-muted">Ventas totales:</span> <?php echo $ventasTotales;?></strong>
                            </div>
                            <div class="row justify-content-center">
                              <strong><span class="text-muted">Ventas Concretadas:</span> <?php echo $concretadas;?></strong>
                            </div>
                            <hr>
                            <!--div class="row">
                              <span class="col-auto mr-auto">Ver Ventas</span>
                              <span class="col-auto ml-auto mr-3 text-success cursor-pointer" id="ventas<?=$c?>">Ver</span>
                            </div-->
                            <div class="row">
                              <span class="col-auto mr-auto">Ver Respuestas Creadas</span>
                              <span class="col-auto ml-auto mr-3 text-success cursor-pointer" data-toggle="modal" data-target="#respuestas_creadas<?php echo $id_user;?>" id="respuestas<?php echo $id_user;?>">Ver</span>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- Modal Cantidad de Respuesta creadas -->
                    <div class="modal fade" id="respuestas_creadas<?php echo $id_user;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModal" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title text-muted" id="exampleModal"><?php echo $correo;?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="respuestas-close<?php echo $id_user;?>">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <div class="container">
                              <div class="row">
                                <strong class="col-1">
                                  #
                                </strong>
                                <strong class="col-7" title="Respuesta Programada" data-toggle="tooltip">
                                  Respuesta
                                </strong>
                                <strong class="col-3">
                                  Palabras Claves
                                </strong>
                                <strong class="col-1" title="Numero de Publicaciones" data-toggle="tooltip">
                                  Cant. Pub.
                                </strong>
                              </div>
                              <hr>
                            </div>
                            <div class="container" id="body_respuestas<?php echo $id_user;?>">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <script>
                      $("#datos-close<?php echo $id_user;?>").click(function(){$("#body_datos<?php echo $id_user;?>").empty();});
                      //Cantidad de respuestas
                      $(document).on("click","#respuestas<?php echo $id_user;?>",function(){
                        var user_id="<?php echo $id_user;?>";
                        $.get("cant_respuestas.php",{user_id:user_id},verificar,'text');
                        function verificar(respuesta){
                          if (respuesta==0 || respuesta=="") {
                            $("#body_respuestas<?php echo $id_user;?>").append("<div class='row'><strong class='col-auto'>No ha creado respuestas</strong></div><hr>")
                          }else{
                            var array_respuestas=respuesta.split("%");
                            for(var i=0;i<array_respuestas.length;i++){
                              var aux2=array_respuestas[i].split("|");
                              var wordsKeys=aux2[1].replace(/,/g, ", ");
                              $("#body_respuestas<?php echo $id_user;?>").append("<div class='row'><strong class='col-1'>"+(i+1)+"</strong><div class='col-7'>"+aux2[0]+"</div><div class='col-3'>"+wordsKeys+"</div><div class='col-1'>"+aux2[2]+"</div></div><hr>");
                            }
                          }
                        }
                      });
                      $("#respuestas-close<?php echo $id_user;?>").click(function(){$("#body_respuestas<?php echo $id_user;?>").empty();});
                    </script>
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
