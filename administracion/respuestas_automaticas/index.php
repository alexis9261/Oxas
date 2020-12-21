<?php
session_start();
require '../common/meli.php';require '../common/conexion.php';
require '../Oxa/Funciones.php';require '../common/take_at.php'; require '../common/account-off.php';
$sql="SELECT CHATROBOT from usuario where IDUSUARIO='$id_user' LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  $row=$result->fetch_assoc();
  $isActive=$row['CHATROBOT'];
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
        <div class='container-fluid'>
          <div class='row justify-content-center'>
            <h5 class='col-sm-8 m-1'>Respuestas Automáticas</h5>
            <a class='btn btn-sm btn-primary col-sm-1 m-1' href='nueva.php' title='Crear respuesta automática' data-toggle='tooltip'>Crear</a>
            <a class='btn btn-sm btn-primary col-sm-1 m-1' href='../Oxa/' title='Para pruebas de respuestas' data-toggle='tooltip'>Simular</a>
            <?php if($isActive){
              ?>
              <button class='btn btn-sm btn-success col-sm-1 m-1' id="activar" name="activar" title='Activa el Respondedor' data-toggle='tooltip' style="display:none">Activar</a>
                <button class='btn btn-sm btn-danger col-sm-1 m-1' id="desactivar" name="desactivar" title='Desactiva el Respondedor' data-toggle='tooltip'>Desactivar</a>
                  <?php
                }else{
                  ?>
                  <button class='btn btn-sm btn-success col-sm-1 m-1' id="activar" name="activar" title='Activa el Chatbot' data-toggle='tooltip'>Activar</a>
                    <button class='btn btn-sm btn-danger col-sm-1 m-1' id="desactivar" name="desactivar" title='Desactiva el Chatbot' data-toggle='tooltip' style="display:none">Desactivar</a>
                      <?php
                    } ?>
                  </div>
                </div>
              </section>
              <div class='mt-2'>
                <h5 class='text-success mb-4'>Respuestas Automáticas Creadas</h5>
                <?php
                $sql="SELECT `IDSINAPSIS`,`INFO`,`NUMPUBLICACIONES`,`ESTIMULOS` FROM `sinapsis` WHERE `IDUSUARIO`='$id_user'";
                $result=$conn->query($sql);
                if($result->num_rows > 0){
                  while($row=$result->fetch_assoc()){
                    $estimulos=$row['ESTIMULOS'];$estimulos=str_replace(',', ', ', $estimulos);
                    ?>
                    <div class='container-fluid' id="respuesta<?=$row['IDSINAPSIS']?>">
                      <div class='row align-items-center'>
                        <?php if($row['NUMPUBLICACIONES']>0){ ?>
                          <a class='mr-1' href='' id="pub<?=$row['IDSINAPSIS']?>" data-toggle='modal' data-target=".bd-<?=$row['IDSINAPSIS']?>-modal-lg">
                            <strong title='Número de Publicaciones asociadas' data-toggle='tooltip'><?=$row['NUMPUBLICACIONES']?> Items</strong>
                          </a>
                        <?php }else{?><strong title='Número de Publicaciones asociadas' data-toggle='tooltip'><?=$row['NUMPUBLICACIONES']?> Items</strong><?php }?>
                        <div class='col-sm-6'><small title='Respuesta programada' data-toggle='tooltip'><?=$row['INFO']?></small></div>
                        <div class='col-sm-4'><small title='Palabras Filtro' data-toggle='tooltip'><?php echo $estimulos;?></small></div>
                        <div class='col-auto ml-auto'>
                          <a class='text-center text-primary' href="Modifica.php?modifica=<?=$row['IDSINAPSIS']?>" title='Modificar' data-toggle='tooltip'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='18px' class='svg-primary' viewBox='0 0 576 512'><path d='M402.6 83.2l90.2 90.2c3.8 3.8 3.8 10 0 13.8L274.4 405.6l-92.8 10.3c-12.4 1.4-22.9-9.1-21.5-21.5l10.3-92.8L388.8 83.2c3.8-3.8 10-3.8 13.8 0zm162-22.9l-48.8-48.8c-15.2-15.2-39.9-15.2-55.2 0l-35.4 35.4c-3.8 3.8-3.8 10 0 13.8l90.2 90.2c3.8 3.8 10 3.8 13.8 0l35.4-35.4c15.2-15.3 15.2-40 0-55.2zM384 346.2V448H64V128h229.8c3.2 0 6.2-1.3 8.5-3.5l40-40c7.6-7.6 2.2-20.5-8.5-20.5H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V306.2c0-10.7-12.9-16-20.5-8.5l-40 40c-2.2 2.3-3.5 5.3-3.5 8.5z'/></svg>
                          </a>
                          <a class='text-center text-danger ml-1' href='' data-toggle='modal' data-target="#borrar<?=$row['IDSINAPSIS']?>">
                            <span title='Eliminar' data-toggle='tooltip'>
                              <svg xmlns='http://www.w3.org/2000/svg' width='15px' class='svg-danger' viewBox='0 0 448 512'><path d='M0 84V56c0-13.3 10.7-24 24-24h112l9.4-18.7c4-8.2 12.3-13.3 21.4-13.3h114.3c9.1 0 17.4 5.1 21.5 13.3L312 32h112c13.3 0 24 10.7 24 24v28c0 6.6-5.4 12-12 12H12C5.4 96 0 90.6 0 84zm415.2 56.7L394.8 467c-1.6 25.3-22.6 45-47.9 45H101.1c-25.3 0-46.3-19.7-47.9-45L32.8 140.7c-.4-6.9 5.1-12.7 12-12.7h358.5c6.8 0 12.3 5.8 11.9 12.7z'/></svg>
                            </span>
                          </a>
                        </div>
                      </div>
                    </div>
                    <hr>
                    <!-- Modal Publicaciones -->
                    <div class="modal fade bd-<?=$row['IDSINAPSIS']?>-modal-lg" tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
                      <div class='modal-dialog modal-lg'>
                        <div class='modal-content container'>
                          <div class='modal-header'>
                            <div class='row'><small><?=$row['INFO']?></small></div>
                            <button class='close' type='button' data-dismiss='modal' aria-label='Close' id="close<?=$row['IDSINAPSIS']?>"><span aria-hidden='true'>×</span></button>
                          </div>
                          <div class='modal-body container-fluid' id="cont<?=$row['IDSINAPSIS']?>"></div>
                          <div class='modal-footer'>
                            <small class'text-muted mr-auto'>(<?=$row['NUMPUBLICACIONES']?>) Publicaciones relacionadas con la respuesta automática.</small>
                            <small class='text-warning'>Se mostrarán máximo 20 publicaciones.</small>
                          </div>
                        </div>
                      </div>
                    </div>
                    <script>
                      //Publicaciones asignadas
                      $("#pub<?=$row['IDSINAPSIS']?>").click(function(){
                        $("#cont<?=$row['IDSINAPSIS']?>").empty();
                        var id_sinapsis=<?=$row['IDSINAPSIS']?>;
                        $.get('publicaciones.php',{id_sinapsis:id_sinapsis},verificar,'json');
                        function verificar(respuesta){$.each(respuesta,function(i,resultado){$("#cont<?=$row['IDSINAPSIS']?>").append("<div class='row align-items-center justify-content-center'><img class='col-sm-2 img-fluid img-thumbnail' src='"+resultado.body.thumbnail+"' style='max-width: 80px; max-height: 80px;'><div class='col-sm-10'><a href='"+resultado.body.permalink+"' target='_blank'>"+resultado.body.title+"</a><br><small class='text-muted'>"+resultado.body.price+" Bs.</small></div></div><hr>");});}
                      });
                      $("#close<?=$row['IDSINAPSIS']?>").click(function(){$("#optipub<?=$row['IDSINAPSIS']?>").remove();});
                    </script>
                    <!-- Modal Eliminar respuesta -->
                    <div class='modal fade' id="borrar<?=$row['IDSINAPSIS']?>" tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                      <div class='modal-dialog' role='document'>
                        <div class='modal-content'>
                          <div class='modal-header'>
                            <h5 class'modal-title'>¿Seguro que desea eliminar la respuesta automática?</h5>
                            <button class='close' type='button' data-dismiss='modal' aria-label='Close' id="close_eliminar<?=$row['IDSINAPSIS']?>"><span aria-hidden='true'>×</span></button>
                          </div>
                          <div class='modal-body text-muted'><?=$row['INFO']?></div>
                          <div class='modal-footer'>
                            <button class='btn btn-outline-secondary' type='button' data-dismiss='modal'>Cancel</button>
                            <a class='btn btn-outline-danger' id="delete<?=$row['IDSINAPSIS']?>">Eliminar</a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <script>
                      //Eliminar respuesta
                      $("#delete<?=$row['IDSINAPSIS']?>").click(function(){
                        var id_sinapsis=<?=$row['IDSINAPSIS']?>;
                        var user=<?php echo $id_user;?>;
                        $.get('ajax_delete.php',{id_sinapsis:id_sinapsis,user:user},verificar,'text');
                        function verificar(text){
                          if (text=="1") {
                            const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
                            toast({type:'success',title:'¡Fue eliminada Exitosamente!'})
                            $("#respuesta<?=$row['IDSINAPSIS']?>").remove();
                            $("#close_eliminar<?=$row['IDSINAPSIS']?>").click();
                          }
                        }
                      });
                    </script>
                  <?php } }else{ ?>
                    <div class='container mt-4'><div class='alert alert-success'><strong>¡No tienes registrada ninguna respuesta automatica!</strong></br>
                      <small>Crear una nueva respuesta automatica en el boton <a href='nueva.php'>Crear.</a></small></div></div>
                    <?php } $conn->close(); ?>
                  </div>
                </div>
                <?php include '../common/footer.php';?>
                <script>
                  $("button#activar").click(function(){
                    $.get("../common/robot.php", {robot:1, id:<?=$id_user?>}, verificar);
                    function verificar(valor){
                      if(valor=="1"){
                        $("#activar").hide();
                        $("#desactivar").show();
                        const toast=swal.mixin({
                          toast: true,
                          position: 'top',
                          showConfirmButton: false,
                          timer: 5500
                        });
                        toast({
                          type: 'success',titleText: '¡Has Activado el Chatbot de Manera Exitosa, El responderá por tí!'
                        });
                      }
                    }
                  });
                  $("button#desactivar").click(function(){
                    $.get('../common/robot.php', {robot:0, id:<?=$id_user?>}, verificar);
                    function verificar(valor){
                      if(valor=="1"){
                        $("#desactivar").hide();
                        $("#activar").show();
                        const toast=swal.mixin({
                          toast: true,
                          position: 'top',
                          showConfirmButton: false,
                          timer: 5500
                        });
                        toast({
                          type: 'warning',
                          titleText: '¡Has Desactivado el Chatbot, No Respondera Automaticamente hasta que lo Actives!'
                        });
                      }
                    }
                  });
                </script>
                <script src='../vendor/jquery/jquery.min.js'></script>
                <script src='../vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
                <script src='../vendor/jquery-easing/jquery.easing.min.js'></script>
                <script src='../js/sb-admin.min.js'></script>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.0/dist/sweetalert2.all.min.js"></script>
              </div>
            </body>
            </html>
