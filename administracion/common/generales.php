<?php
session_start();
require '../common/meli.php';require '../common/conexion.php';require '../common/take_at.php';
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/me?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$var=json_decode($result);
$nickname=$var->nickname;$type=$var->identification->type;
$number=$var->identification->number;$dir=$var->address->address;
$city=$var->address->city;$direccion=$dir.', '.$city;
switch($var->address->state){
  case 'VE-A':$state='Distrito Capital';break;case 'VE-C':$state='Apure';break;case 'VE-D':$state='Aragua';break;case 'VE-B':$state='Anzoátegui';
  break;case 'VE-E':$state='Barinas';break;case 'VE-F':$state='Bolívar';break;case "VE-G":$state="Carabobo";break;case "VE-H":$state="Cojedes";
  break;case "VE-I":$state="Falcón";break;case "VE-J":$state="Guárico";break;case "VE-K":$state="Lara";break;case "VE-L":$state="Mérida";
  break;case "VE-M":$state="Miranda";break;case "VE-N":$state="Monagas";break;case "VE-O":$state="Nueva Esparta";break;case "VE-P":
  $state="Portuguesa";break;case "VE-R":$state="Sucre";break;case "VE-S":$state="Táchira";break;case "VE-T":$state="Trujillo";break;
  case "VE-V":$state="Zulia";break;case "VE-W":$state="Dependencias Federales";break;case "VE-X":$state="Vargas";break;case "VE-Y":
  $state="Delta Amacuro";break;case "VE-Z":$state="Amazonas";break;case "VE-U":$state="Yaracuy";break;default:$state=" ";break;
}
$google_map=$direccion.', Estado '.$state;$tel=$var->phone->number;
$mail=$var->email;$fecha=$var->registration_date;
$anio_r=substr($fecha,0,4);$mes_r=substr($fecha,5,2);$dia_r=substr($fecha,8,2);
$date=$dia_r."/".$mes_r."/".$anio_r;
if(isset($var->thumbnail->picture_url)){$img=$var->thumbnail->picture_url;};
//Buscar saludo y firma en bbdd
$sql="SELECT SALUDO,DESPEDIDA,IDUSUARIO FROM usuario WHERE CORREO='$mail'";
$result=$conn->query($sql);
if($result->num_rows>0){while($row=$result->fetch_assoc()){$saludo=$row['SALUDO'];$firma=$row['DESPEDIDA'];$id_usuario=$row['IDUSUARIO'];}}
//Busqueda de fechas de suscripcion
$sql="SELECT DATE_FORMAT(FECHAREGISTRO, '%d/%m/%Y') as FECHAR FROM suscripcion WHERE IDUSUARIO='$id_usuario'";
$result=$conn->query($sql);
if($result->num_rows>0){while($row=$result->fetch_assoc()){$fechaR=$row['FECHAR'];}}
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
      <title>Oxas - Configuración</title>
      <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
      <link href="../css/sb-admin.min.css" rel="stylesheet">
      <link href="../css/style.css" rel="stylesheet">
      <script src="../vendor/jquery/jquery.min.js"></script>
    </head>
    <body class="fixed-nav sticky-footer bg-dark" id="page-top">
      <?php include '../common/navbar.php';?>
      <div class="content-wrapper">
        <div class="container-fluid">
          <?php include "../common/breadcrumb.php";?>
          <section class="container-fluid breadcrumb">
            <div class="container-fluid">
              <div class="row justify-content-center">
                <div class="col-sm-2 align-self-center text-center"><img class="col-4 principal" src="<?php echo $img; ?>" alt="" title="Imagen principal" data-toggle="tooltip"></div>
                <div class="col-sm-10">
                  <div class="row my-4">
                    <div class="input-group input-group-sm mb-3 col-sm-5 my-1">
                      <div class="input-group-prepend"><span class="input-group-text bg-dark text-white" id="inputGroup-sizing-sm">Usuario</span></div>
                      <input type="text" class="form-control" style="background-color: #e3f2fd;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" value="<?php echo $nickname;?>" disabled>
                    </div>
                    <div class="input-group input-group-sm mb-3 col-sm-4 my-1">
                      <div class="input-group-prepend"><span class="input-group-text bg-dark text-white" id="inputGroup-sizing-sm"><?php echo $type;?></span></div>
                      <input type="text" class="form-control" style="background-color: #e3f2fd;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" value="<?php echo $number;?>" disabled>
                    </div>
                    <div class="input-group input-group-sm mb-3 col-sm-5 my-1">
                      <div class="input-group-prepend">
                        <span class="input-group-text bg-dark text-white" id="inputGroup-sizing-sm">Correo</span>
                      </div>
                      <input type="text" class="form-control" style="background-color: #e3f2fd;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" value="<?php echo $mail;?>" disabled id="correo">
                    </div>
                    <div class="input-group input-group-sm mb-3 col-sm-4 my-1">
                      <div class="input-group-prepend">
                        <span class="input-group-text bg-dark text-white" id="inputGroup-sizing-sm" title="Principal de Mercado Libre" data-toggle="tooltip">Teléfono</span>
                      </div>
                      <input type="text" class="form-control" style="background-color: #e3f2fd;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" value="<?php echo $tel;?>" disabled>
                    </div>
                    <div class="input-group input-group-sm mb-3 col-sm-5 my-1">
                      <div class="input-group-prepend">
                        <span class="input-group-text bg-dark text-white" id="inputGroup-sizing-sm" title="En Mercado Libre" data-toggle="tooltip">Fecha de Registro en ML</span>
                      </div>
                      <input type="text" class="form-control" style="background-color: #e3f2fd;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" value="<?php echo $date;?>" disabled>
                    </div>
                    <div class="input-group input-group-sm mb-3 col-sm-4 my-1">
                      <div class="input-group-prepend">
                        <span class="input-group-text bg-dark text-white" id="inputGroup-sizing-sm" title="En Oxas" data-toggle="tooltip">Fecha de Registro<span class="d-none d-sm-block"></span></span>
                      </div>
                      <input type="text" class="form-control" style="background-color: #e3f2fd;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" value="<?php echo $fechaR;?>" disabled>
                    </div>
                    <div class="input-group input-group-sm mb-3 col-sm-8 my-1">
                      <div class="input-group-prepend">
                        <span class="input-group-text bg-dark text-white" id="inputGroup-sizing-sm" title="Con la cual apareces en Mercado Libre" data-toggle="tooltip">Dirección</span>
                      </div>
                      <input type="text" class="form-control" style="background-color: #e3f2fd;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm" value="<?php echo $direccion;?>" disabled>
                      <div class="input-group-append">
                        <a class="btn btn-outline-secondary" href="http://maps.google.es/maps?q=<?php echo $google_map;?>&output=embed" target="_blank">Google Maps</a>
                      </div>
                    </div>
                    <!--div class="col-auto">
                    <button class="btn btn-success btn-sm mt-2" type="button">Agregar Sucursal</button>
                  </div-->
                </div>
              </div>
            </div>
          </div>
            <div class="container">
              <!--Formato para enviar saludo & Firma-->
              <input type="hidden" name="_token" value="5k43h5v3vg2cv23g5g23jhk23k4h23jg4kj32gh23fhh23kl4j23kf">
              <div class="row justify-content-center">
                <div class="col-sm-8">
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-info text-white" id="inputGroup-sizing-default">Saludo</span>
                    </div>
                    <?php if($saludo==NULL){?>
                      <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="Aparecerá al incio en todas tus respuestas." id="saludo">
                    <?php }else{?>
                      <input type="text" class="form-control" value="<?php echo $saludo;?>" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="Aparecerá al incio en todas tus respuestas." id="saludo">
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="row justify-content-center">
                <div class="col-sm-8">
                  <div class="input-group mb-2">
                    <div class="input-group-prepend"><span class="input-group-text bg-success text-white" id="inputGroup-sizing-default">Firma</span></div>
                    <?php if($firma==NULL){ ?>
                      <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="Aparecerá al final en todas tus respuestas." id="firma">
                    <?php }else{ ?>
                      <input type="text" class="form-control" value="<?php echo $firma;?>" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="Aparecerá al final en todas tus respuestas." id="firma">
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </section>
          <div class="container text-center my-3"><button  class="btn btn-sm btn-outline-primary col-sm-2" type="submit" title="Guardar Cambios" data-toggle="tooltip" id="enviar">Guardar</button></div>
        </div>
      <script>
        $("#enviar").click(function(){
          var saludo=$("#saludo").val();
          var firma=$("#firma").val();
          var correo=$("#correo").val();
          $.get("ajax_saludo.php",{saludo:saludo,firma:firma,correo:correo},verificar,'text');
          function verificar(text){
            if(text==1){
              const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
              toast({type:'success',title:'¡fue guardado exitosamente!'});
            }else{
              const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
              toast({type:'error',title:'¡Hubo un pequeño problema! \n Inténtalo de nuevo'});
            }
            }
        });
      </script>
      <?php  include '../common/footer.php';?>
      <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
      <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
      <script src="../js/sb-admin.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.0/dist/sweetalert2.all.min.js"></script>
    </div>
  </body>
  </html>
