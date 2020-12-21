<?php
require 'conexion.php';
require 'Funciones.php';
//Funcion que actualiza el AccessToken
function refreshToken($id_user,$appId,$secretKey,$ch,$conn){
  $sql="SELECT REFRESTOKEN FROM usuario WHERE IDUSUARIO=$id_user";
  $result_f=$conn->query($sql);
  if($result_f->num_rows>0){while($row=$result_f->fetch_assoc()){$refresh_token=$row['REFRESTOKEN'];}}
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/oauth/token?grant_type=refresh_token&client_id=$appId&client_secret=$secretKey&refresh_token=$refresh_token");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch,CURLOPT_POST,1);
  $headers=array();$headers[]='Content-Type: application/json';
  curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
  $result_ch=curl_exec($ch);curl_close($ch);$r=json_decode($result_ch);
  $access_token=$r->access_token;$refresh_token=$r->refresh_token;
  updateTokens($id_user,$access_token,$refresh_token);
  return $access_token;
}
if(isset($_GET['o'],$_GET['r'])){
  $id_order=$_GET['o'];
  $idReSeller=$_GET['r'];
  $sql="SELECT USUARIOID FROM revendedores WHERE IDREVENDEDOR=$idReSeller";
  $r=$conn->query($sql);
  if($r->num_rows>0){
    while($row=$r->fetch_assoc()){
      $id_user=$row['USUARIOID'];
    }
  }
  $sql="SELECT ACCESSTOKEN,PAIS FROM usuario WHERE IDUSUARIO=$id_user";
  $r=$conn->query($sql);
  if($r->num_rows>0){
    while($row=$r->fetch_assoc()){
      $AccessToken=$row['ACCESSTOKEN'];
      $siteId=$row['PAIS'];
    }
  }
  if($siteId="MLV"){
    $appId='1153047962046613';
    $secretKey='i3RGdgCvJXrKT1ceMNOHs4YLNHdgZ9Mj';
  }elseif($siteId=="MLC"){
    $appId='884214179114152';
    $secretKey='NpVPyfC6vtrFdS5EZ9Sr2DQe5sAOrXAK';
  }
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/orders/$id_order?access_token=$AccessToken");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  $result=curl_exec($ch);curl_close($ch);
  $consulta=json_decode($result);
  if(isset($consulta->error)){
    $AccessToken=refreshToken($id_user,$appId,$secretKey,$ch,$conn);
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/orders/$id_order?access_token=$AccessToken");
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $result=curl_exec($ch);
    curl_close($ch);
    $consulta=json_decode($result);
  }
  $id=$consulta->id;
  $buyer=$consulta->buyer->first_name." ".$consulta->buyer->last_name;
  $nickname=$consulta->buyer->nickname;
  $items=$consulta->order_items;
  foreach($items as $item){
    $id_item=$item->item->id;
    $title=$item->item->title;
    $cantidad=$item->quantity;
    $precio=$item->unit_price;
  }
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items?id=$id_item");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  $result=curl_exec($ch);
  curl_close($ch);
  $consulta=json_decode($result);
  $thumbnail=$consulta->thumbnail;
}else{//header("Location: https://oxasapp.com.ve/");}
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../img/favicon.ico">
    <title>Formulario de Compra - OxasApp</title>
    <link href="../administracion/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <link href='../administracion/css/sb-admin.min.css' rel='stylesheet'>
    <link href="../administracion/css/style.css" rel="stylesheet">
    <script src="../administracion/vendor/jquery/jquery.min.js"></script>
    <style media="screen">
      .cursor:hover{
        cursor: pointer;
      }
    </style>
  </head>
  <body>
    <div class="container pt-3">
      <div class="row mb-4 breadcrumb">
        <h1 class="lead display-4">Formulario de compra</h1>
      </div>
      <input type="hidden" name="user_id" value="<?php echo $id_user;?>">
      <div class="row">
        <div class="input-group col-sm-4 mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Nombre y Apellido</span>
          </div>
          <input type="text" class="form-control" value="<?php echo $buyer;?>" required disabled>
        </div>
        <div class="input-group col-sm-3 mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Cédula</span>
          </div>
          <input type="number" class="form-control" name="cedula" placeholder="Ej. 20123456" required autocomplete="off" id="cedula">
        </div>
        <div class="input-group col-sm-5 mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Nro télefono</span>
          </div>
          <select class="custom-select" name="extension_cel">
            <option value="0424">0424</option>
            <option value="0414">0414</option>
            <option value="0412">0412</option>
            <option value="0426">0426</option>
          </select>
          <input type="tel" class="form-control" id="celular" name="telefono" pattern="[0-9]{3}-[0-9]{2}-[0-9]{2}" required maxlength="7">
        </div>
      </div>
      <div class="row">
        <div class="input-group col-sm-6 mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text">Correo eléctronico</span>
          </div>
          <input type="email" class="form-control" name="correo" placeholder="Ej. usuario1234@gmail.com" required autocomplete="off">
          <small class="form-text text-muted">Este correo nos servirá para ayudarte en caso de algún inconveniente.</small>
        </div>
      </div>
    </div>
    <div class="container px-3 pt-1 pb-5" style="background-color:#f5f5f5;border-radius:10px;">
        <div class="row my-3">
          <h1 class="col-auto lead" title="Reporta el pago de tus productos" data-toggle="tooltip">Datos de pago</h1>
          <div class="col-auto mb-2">
            <a href="#" class="badge badge-primary px-3 py-2" data-toggle="modal" data-target="#mercantil">Ver Cta. Mercantil</a>
          </div>
          <div class="col-auto">
            <a href="#" class="badge badge-dark px-3 py-2" data-toggle="modal" data-target="#pago_movil">Ver Pago Móvil</a>
          </div>
          <div class="col-auto ml-auto">
            <a href="#" data-toggle="modal" data-target="#productos">Ver productos</a>
          </div>
        </div>
        <div class="row">
          <div class="input-group col-sm-4 mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Método de Pago</span>
            </div>
            <select class="form-control" name="transaccion">
              <option value="Transferencia">Transferencia</option>
              <option value="Pago Movil">Pago Móvil</option>
            </select>
          </div>
          <div class="input-group col-sm-4 mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" title="Tu Banco" data-toggle="tooltip">Banco Emisor</span>
            </div>
            <select class="form-control" name="b_emisor">
              <option value="Banesco">Banesco</option>
              <option value="Mercantil">Mercantil</option>
              <option value="Venezuela">Venezuela</option>
              <option value="Tesoro">Del Tesoro</option>
              <option value="Provincial">Provincial</option>
              <option value="100% Banco">100% Banco</option>
              <option value="Bancaribe">Bancaribe</option>
              <option value="Banco Activo">Banco Activo</option>
              <option value="Bicentenario">Bicentenario</option>
              <option value="BNC">Banco Nacional de Credito</option>
              <option value="Venezolano de Crédito">Venezolano de Crédito</option>
              <option value="BOD">BOD</option>
              <option value="Fondo Común">Fondo Común</option>
              <option value="Banplus">Banplus</option>
              <option value="Exterior">Banco Exterior</option>
              <option value="Caroní">Caroní</option>
              <option value="Banco Plaza">Banco Plaza</option>
              <option value="Del Sur">Del Sur</option>
              <option value="Bancrecer">Bancrecer</option>
            </select>
          </div>
          <div class="input-group col-sm-4 mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" title="Nuestro Banco" data-toggle="tooltip">Banco Receptor</span>
            </div>
            <select class="form-control" name="b_receptor">
              <option value="Mercantil">Mercantil</option>
            </select>
          </div>
          <div class="input-group col-sm-4 mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" title="Debes pagar <?php $decimales=number_format($precio,2,',','.'); echo "$decimales";?> Bs." data-toggle="tooltip">Monto</span>
            </div>
            <input type="number" class="form-control" name="pago" required autocomplete="off" id="monto">
          </div>
          <div class="input-group col-sm-4 mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Fecha de pago</span>
            </div>
            <input type="date" class="form-control" name="fecha_pago" required>
          </div>
          <div class="input-group col-sm-4 mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Nro. Referencia</span>
            </div>
            <input type="number" class="form-control" name="referencia" required autocomplete="off">
          </div>
        </div>
        <div class="row my-3">
          <h1 class="col-auto lead">Datos de envío</h1>
        </div>
        <div class="row">
          <div class="input-group col-sm-4 mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Agencia de encomiendas</span>
            </div>
            <select class="custom-select" name="agencia" id="agencias">
              <option style="color:#00cb00;" selected value="mrw">MRW</option>
              <option value="domesa">Domesa</option>
              <option value="tealca">Tealca</option>
              <option value="zoom">Zoom</option>
            </select>
            <small class="form-text text-muted">Recuerda que el envío te sale <strong title="Envío: De agencia MRW a MRW" data-toggle='tooltip' style="color:#00cb00;">gratis por MRW</strong>.</small>
          </div>
          <div class="input-group col-sm-8 mb-3">
            <span><strong>*</strong>Busca la agencia MRW mas cercana a ti <a href="http://agencias.com.ve/MRW-1" target="_blank">Agencias MRW</a> </span>
          </div>
        </div>
        <div class="row">
          <div class="input-group mb-3 col-sm-4">
            <div class="input-group-prepend">
              <span class="input-group-text">Estado</span>
            </div>
            <select class="custom-select input_datos" name="estado" id="estado" onchange="estados()">
              <option value="Amazonas">Amazonas</option>
              <option value="Anzoategui">Anzoátegui</option>
              <option value="Apure">Apure</option>
              <option value="Aragua">Aragua</option>
              <option value="Barinas">Barinas</option>
              <option value="Bolivar">Bolívar</option>
              <option value="Carabobo">Carabobo</option>
              <option value="Cojedes">Cojedes</option>
              <option value="Delta Amacuro">Delta Amacuro</option>
              <option value="Distrito Capital">Distrito Capital</option>
              <option value="Falcon">Falcón</option>
              <option value="Guarico">Guárico</option>
              <option value="Lara">Lara</option>
              <option value="Merida">Mérida</option>
              <option value="Miranda">Miranda</option>
              <option value="Monagas">Monagas</option>
              <option value="Nueva Esparta">Nueva Esparta</option>
              <option value="Portuguesa">Portuguesa</option>
              <option value="Sucre">Sucre</option>
              <option value="Tachira">Táchira</option>
              <option value="Trujillo">Trujillo</option>
              <option value="Vargas">Vargas</option>
              <option value="Yaracuy">Yaracuy</option>
              <option value="Zulia">Zulia</option>
            </select>
          </div>
          <div class="input-group mb-3 col-sm-4">
            <div class="input-group-prepend">
              <span class="input-group-text">Municipio</span>
            </div>
            <select class="custom-select input_datos" name="municipio" id="municipio">
              <option value="Atures">Atures</option>
              <option value="Alto Orinoco">Alto Orinoco</option>
              <option value="Atabapo">Atabapo</option>
              <option value="Autana">Autana</option>
              <option value="Manapiare">Manapiare</option>
              <option value="Maroa">Maroa</option>
              <option value="Río Negro">Río Negro</option>
            </select>
          </div>
          <div class="input-group col-sm-4 mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text" title="Es el código único de la agencia donde te vamos a hacer el envío" data-toggle="tooltip">Código de la agencia</span>
            </div>
            <input type="text" class="form-control" name="codigo_agencia" placeholder="Ej. 100221" required autocomplete="off">
          </div>
          <div class="input-group col-12 mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Dirección</span>
            </div>
            <input type="text" class="form-control" name="direccion" required autocomplete="off">
          </div>
        </div>
        <input type="hidden" name="orden_id" value="<?php echo $id_order;?>">
        <div class="row justify-content-center">
          <button type="submit" class="btn btn-info px-5" id="enviar">Enviar Formulario</button>
        </div>
      </div>
  <!-- Modal Productos -->
  <div class="modal fade" id="productos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Mis productos</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class='row border-botton align-items-center m-0 py-1'>
            <div class='col-auto cursor-pointer'>
              <img class='img-fluid img-thumbnail' src='<?php echo $thumbnail;?>' width='65px' height='65px' title='Imagen publicación' data-toggle='tooltip'>
            </div>
            <span class='col-8 p-0'>(<?php echo $cantidad;?>) <?php echo $title;?></span>
            <span class='col-auto ml-auto text-center text-primary p-0'><?php $decimales=number_format($precio,2,',','.'); echo "$decimales";?>Bs.</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal Mercantil -->
  <div class="modal fade" id="mercantil" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Datos Cuenta Mercantil</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row">
              <div class="col-12">
                Alpargata Skate, C.A.
              </div>
              <div class="col-12">
                J 405852089
              </div>
              <div class="col-12">
                <strong>0105-0283-7512-8314-8412</strong>
              </div>
              <div class="col-12">
                Cta Corriente
              </div>
              <div class="col-12">
                alpargataskate@gmail.com
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal Pago Movil -->
  <div class="modal fade" id="pago_movil" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Pago Móvil</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row">
              <div class="col-12">
                Mercantil(0105)
              </div>
              <div class="col-12">
                J 405852089
              </div>
              <div class="col-12">
                0412-403 86 48
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Loadeer -->
  <input type="hidden" data-toggle="modal" data-target="#loader_modal" id="loader_now">
  <div class="modal fade" id="loader_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" id="loader_real">
    <div class="modal-dialog" role="document">
      <div class="modal-content bg-transparent no_border mt-5 pt-5">
        <button type="button" class="close bg-transparent" data-dismiss="modal" aria-label="Close" id="close_loader"></button>
        <div class="container mt-5">
          <div class="row justify-content-center"><div class="col-auto background_loader"><div class="loader algin-self-middle"></div></div></div>
        </div>
      </div>
    </div>
  </div>
  <!-- Agencias de Encomiendas -->
  <script>
    $('#agencias').on('change',function(){
      var valor=$(this).val();
      valor=valor.charAt(0).toUpperCase() + valor.slice(1);
      if(valor!='Mrw'){
        Swal.fire({
          title: "Por "+valor+" el envío será Cobro Destino",
          text: 'Recuerda que los envíos por MRW te salen gratis',
          showClass: {popup: 'animated fadeInDown faster'},
          hideClass: {popup: 'animated fadeOutUp faster'}
        })
      }
    });
  </script>
  <!-- Municipios -->
  <script>
    function estados(){
      $('#municipio option').each(function(){$(this).remove();});
      var state=$('#estado').val();
      $.get('ajax_municipios.php',{state:state},verificar,'text');
      function verificar(respuesta){
        var municipios=respuesta.split(",");
        $.each(municipios,function(i,resultado){
          $('#municipio').append("<option value='"+resultado+"'>"+resultado+'</option>');
        });
      }
    };
  </script>
  <!-- Validaciones -->
  <script>
    var cedula = document.querySelector('#cedula');
    cedula.addEventListener('keypress',function(e){
      //no permito que escriban letras
      if(!soloNumeros(event)){
        e.preventDefault();
      }
      //No permito que hayan mas de 7 numeros
      if(cedula.value.toString().length>7){
        e.preventDefault();
      }
    });
    var celular = document.querySelector('#celular');
    celular.addEventListener('keypress',function(e){
      //no permito que escriban letras
      if(!soloNumeros(event)){
        e.preventDefault();
      }
    });
    var monto=document.querySelector('#monto');
    monto.addEventListener('keypress',function(e){
      //no permito que escriban letras
      if(!soloNumeros(event)){
        e.preventDefault();
      }
    });
    //Solo permite introducir numeros.
    function soloNumeros(e){
      var key = e.charCode;
      console.log(key);
      return key >= 48 && key <= 57;
    }
  </script>
  <!-- Envio -->
  <script>
    $('#enviar').on('click',function(){
      $("#enviar").prop('disabled',true);
      $("#loader_now").click();
      var user_id= document.querySelector("input[name='user_id']").value;
      var orden_id = document.querySelector("input[name='orden_id']").value;
      var cedula = document.querySelector("input[name='cedula']").value;
      var extension_cel=document.querySelector("select[name='extension_cel']").value;
      var telefono = document.querySelector("input[name='telefono']").value;
      var correo = document.querySelector("input[name='correo']").value;
      var b_emisor = document.querySelector("select[name='b_emisor']").value;
      var b_receptor = document.querySelector("select[name='b_receptor']").value;
      var pago = document.querySelector("input[name='pago']").value;
      var fecha_pago = document.querySelector("input[name='fecha_pago']").value;
      var referencia = document.querySelector("input[name='referencia']").value;
      var agencia = document.querySelector("select[name='agencia']").value;
      var estado = document.querySelector("select[name='estado']").value;
      var municipio = document.querySelector("select[name='municipio']").value;
      var codigo_agencia = document.querySelector("input[name='codigo_agencia']").value;
      var direccion = document.querySelector("input[name='direccion']").value;
      $.get('procesar.php',{user_id:user_id,orden_id:orden_id,cedula:cedula,extension_cel:extension_cel,telefono:telefono,correo:correo,b_emisor:b_emisor,b_receptor:b_receptor,pago:pago,fecha_pago:fecha_pago,referencia:referencia,agencia:agencia,estado:estado,municipio:municipio,codigo_agencia:codigo_agencia,direccion:direccion},verificar,'text');
      function verificar(respuesta){
        location.href="https://oxas.tech/index.php?r="+respuesta;
      }
    });
  </script>
  <script src="../administracion/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../administracion/vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src='../administracion/js/sb-admin.min.js'></script>
  <script src='https://cdn.jsdelivr.net/npm/sweetalert2@7.29.0/dist/sweetalert2.all.min.js'></script>
</body>
</html>
