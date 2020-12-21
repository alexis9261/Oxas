<?php
session_start();
require '../../common/meli.php';require '../../common/conexion.php';
require '../../Oxa/Funciones.php';require '../../common/take_at.php'; require '../../common/account-off.php';
?>
<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
  <meta name='description' content=''>
  <meta name='author' content=''>
  <link rel='shortcut icon' href='../../../img/favicon.ico'>
  <title>Oxas - Respuestas Automáticas</title>
  <link href='/administracion/vendor/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
  <link href='/administracion/css/sb-admin.min.css' rel='stylesheet'>
  <link href='/administracion/css/style.css' rel='stylesheet'>
  <link href='/administracion/vendor/datatables/datatables.min.css' rel='stylesheet'>
  <script src='/administracion/vendor/jquery/jquery.min.js'></script>
</head>
<body class='fixed-nav sticky-footer bg-dark' id='page-top'>
  <?php include '../../common/navbar.php';?>
  <div class='content-wrapper'>
    <div class='container-fluid'>
      <section class='breadcrumb'>
        <div class='container-fluid'>
          <div class='row'>
            <h5 class='col-sm-8 m-1'>Agregar Vendedores</h5>
            <small>Los vendedores que agreges, se les estaran enviando los datos del comprador via correo</small>
          </div>
        </div>
      </section>
      <section class="container-fluid mt-3">
          <table id="vendedores" class="display" style="width:100%">
            <thead>
              <tr>
                <th>#</th>
                <th>Vendedor</th>
                <th>Dirección</th>
                <th>Telefono</th>
                <th>Correo</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
                <?php
                $cont=0;
                $sql="SELECT * FROM originalsellers";
                $result=$conn->query($sql);
                if($result->num_rows>0){
                  while($row=$result->fetch_assoc()){
                    $id_seller_original=$row['IDORINGSELL'];
                    $nickname=$row['NICKNAME'];
                    $correo=$row['CORREO'];
                    $telefono=$row['TELEFONO'];
                    $direccion=$row['DIRECCION'];
                    $perfil_ml=$row['PERFILML'];
                    $estatus=$row['ESTATUS'];
                    ++$cont;
                      ?>
                      <tr>
                        <td class="text-center"><?php echo $cont;?></td>
                        <td class="text-center"><a href="<?php echo $perfil_ml;?>" target="_blank"><?php echo $nickname;?></a> </td>
                        <td><?php echo $direccion;?></td>
                        <td><input class="form-control" type="text" name="telefono" width="100%" value="<?php echo $telefono ?>"></td>
                        <td><input class="form-control" type="text" name="correo" width="100%" value="<?php echo $correo ?>"></td>
                        <td>
                          <button class="btn btn-sm btn-success px-2" type="button">Actualizar datos</button>
                          <?php if ($estatus==1){ ?>
                            <button class="btn btn-sm btn-danger px-2" type="button" data-toggle="tooltip" title="Al pausarlo, no se le enviaran ningun tipo de datos automaticamente">Pausar</button>
                          <?php }else{ ?>
                            <button class="btn btn-sm btn-primary px-2" type="button">Activar</button>
                          <?php } ?>
                        </td>
                      </tr>
                      <?php
                    }
                  }else {
                    echo "No hay vendedores para OxaShop";
                  }
                 ?>
            </tbody>
          </table>
      </section>
    </div>
  </div>
  <script>
    $(document).ready(function(){
      $('#vendedores').addClass('nowrap').dataTable({
        responsive:true,
        paging: false,
        searching: false,
        columnDefs:[{
          "targets":[-1],
          "orderable":false
        }]
      });
    });
  </script>
  <?php include '../../common/footer.php';?>
  <script src='/administracion/vendor/jquery/jquery.min.js'></script>
  <script src='/administracion/vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
  <script src='/administracion/vendor/jquery-easing/jquery.easing.min.js'></script>
  <script src='/administracion/js/sb-admin.min.js'></script>
  <script src='/administracion/vendor/datatables/datatables.min.js'></script>
</div>
</body>
</html>
