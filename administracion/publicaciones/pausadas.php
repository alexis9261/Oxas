<?php
session_start();
require '../common/meli.php';require '../common/conexion.php';
require '../common/take_at.php';require '../common/account-off.php';
$array_revendedores=array();
$sql="SELECT USUARIOID FROM revendedores";
$r=$conn->query($sql);
if($r->num_rows>0){
  while($rw=$r->fetch_assoc()){
    array_push($array_revendedores,$rw['USUARIOID']);
  }
}
?>
<!DOCTYPE html>
<html lang='es'>
<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="shortcut icon" href="../../img/favicon.ico">
  <title>Oxas - Publicaciones</title>
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/sb-admin.min.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
  <link rel="icon" type="image/vnd.microsoft.icon" href="../../img/favicon.ico">
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/chartjs/Chart.js"></script>
</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <?php
  $ch=curl_init();
  if(isset($_GET['search']) & !empty($_GET['search'])){
    $query=urlencode($_GET['search']);
    curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/'.$id_user.'/items/search?status=paused&query='.$query.'&access_token='.$AccessToken);
  }else{
    if(isset($_GET['page']) & !empty($_GET['page'])){
      $page_offset=$_GET['page']-1;
      if($page_offset>=21){$offset=0;}else{$offset=$page_offset*50;}
      curl_setopt($ch, CURLOPT_URL,"https://api.mercadolibre.com/users/".$id_user."/items/search?status=paused&access_token=".$AccessToken."&offset=".$offset);
    }else{
      $_SESSION['array_publicaciones']=array();
      curl_setopt($ch, CURLOPT_URL,"https://api.mercadolibre.com/users/".$id_user."/items/search?status=paused&access_token=".$AccessToken);
    }
  }
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  $result=curl_exec($ch);
  curl_close($ch);
  $consulta=json_decode($result);
  $items_paginacion=$consulta->paging->total;
  $items=$consulta->results;
  $total_items=count($items);
  include '../common/navbar.php';?>
  <script>
  Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
  Chart.defaults.global.defaultFontColor = '#292b2c';
</script>
<div class="content-wrapper p-0">
  <div class="container-fluid background_activas py-2">
    <div class="row align-items-center">
      <div class="col-auto ml-2">
        <?php if(isset($_SESSION['array_publicaciones'][0]) && $_SESSION['array_publicaciones'][0]==1 && count($_SESSION['array_publicaciones'])<2){ ?>
          <input class='pl-1' type='checkbox' id='selectall' checked>
          <?php
        }else{
          ?>
          <input class='pl-1' type='checkbox' id='selectall'>
          <?php
        } ?>
      </div>
      <div class='col-auto pt-1 px-0 dropdown cursor-pointer' id='dropdown'>
        <a class='dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
          <svg xmlns='http://www.w3.org/2000/svg' width='12px' class='svg-dark' viewBox='0 0 448 512'><path d='M16 132h416c8.837 0 16-7.163 16-16V76c0-8.837-7.163-16-16-16H16C7.163 60 0 67.163 0 76v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z'/></svg>
        </a>
        <div class='dropdown-menu'>
          <div class='ml-3 cursor-pointer hover-mouse' id='activar'>Activar</div>
          <div class="ml-3 cursor-pointer hover-mouse" id="edit_stock" data-toggle='modal' data-target='#modificar_stock'>Modificar stock</div>
        </div>
      </div>
      <div class='col-auto ml-4'>
        <a class='text-muted' href='index.php' data-toggle='tooltip' title='Publicaciones Pausadas'>Activas</a>
      </div>
      <div class='col-auto'>
        <span class='text-success'>Pausadas</span>
        <small class='font-weight-bold' title='Publicaciones pausadas' data-toggle='tooltip'> (<?php echo $items_paginacion;?>)</small>
      </div>
      <div class="col-auto">
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
          <input class="search" type="text" placeholder=" Busca tus publicaciones..." name='search'/>
          <button type="submit" class="button_search cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" class="svg-secondary" width="15px" viewBox="0 0 512 512"><path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"/></svg></button>
        </form>
      </div>
      <div class="col-auto ml-auto">
        <button class="btn btn-link btn-sm text-success" data-toggle='modal' data-target='#descargar_publicaciones'><span title='En un Formato Excel' data-toggle='tooltip'>Descargar Publicaciones</span></button>
      </div>
    </div>
  </div>
  <div class='container-fluid' id='publicaciones_pausadas'>
    <?php
    if($total_items==0){
      $items_paginacion=0;
      if(isset($_GET['search']) & !empty($_GET['search'])){
        ?>
        <div class='container mt-4 mb-5'>
          <strong class="mb-5">No tienes publicaciones pausadas con esta descripción: <span class="text-muted"><?php echo $_GET['search'];?></span></strong><br>
          <br><br><br>
        </div>
      <?php }else{ ?>
        <div class='container mt-4'>
          <strong>¡No tienes publicaciones pausadas!</strong><br>
          <small>Ir a <a href='https://myaccount.mercadolibre.com.ve/listings/#label=active' target='_blank'>Mercado Libre</a>.</small>
        </br></br></br>
      </div>
      <?php
    }
  }else{
    $contador=0;
    while($contador!=$total_items){
      $array_publicaciones_veinte=array();
      $aux=$contador+19;
      for($i=$contador;$i<=$aux;$i++){
        if(isset($items[$i])){++$contador;array_push($array_publicaciones_veinte,$items[$i]);}
      }
      $str_public=implode(',',$array_publicaciones_veinte);
      $array_publicaciones_veinte=array();
      $ch=curl_init();
      curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items?ids='.$str_public.'&attributes=id,title,permalink,thumbnail,available_quantity,price,category_id,listing_type_id&access_token='.$AccessToken);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      $result=curl_exec($ch);
      curl_close($ch);
      $result=json_decode($result);
      foreach($result as $valor){
        $id=$valor->body->id;
        $title=$valor->body->title;
        $precio=$valor->body->price;
        $categoria=$valor->body->category_id;
        $quantity=$valor->body->available_quantity;
        $nivel_public=$valor->body->listing_type_id;
        $thumbnail=$valor->body->thumbnail;
        $permalink=$valor->body->permalink;
        $sql_enlace="SELECT COUNT(IDENLACE) AS N FROM enlace e INNER JOIN publicacion p ON p.CODIGO='$id' WHERE e.IDPUBLICACION=p.IDPUBLICACION";
        $result_enlace=$conn->query($sql_enlace);
        if($result_enlace->num_rows>0){while($row_enlace=$result_enlace->fetch_assoc()){$cant_respuestas=$row_enlace['N'];}
      }else{$cant_respuestas=0;}
      ?>
      <div class='row border-botton align-items-center m-0 py-1'>
        <div>
          <?php if(isset($_SESSION['array_publicaciones'][0]) && $_SESSION['array_publicaciones'][0]==1 && !in_array($id,$_SESSION['array_publicaciones'])){ ?>
            <input type='checkbox' name='publicaciones' value='<?php echo $id;?>' checked>
          <?php }elseif(in_array($id,$_SESSION['array_publicaciones']) && $_SESSION['array_publicaciones'][0]!=1){ ?>
            <input type='checkbox' name='publicaciones' value='<?php echo $id;?>' checked>
          <?php }else{ ?>
            <input type='checkbox' name='publicaciones' value='<?php echo $id;?>'>
          <?php } ?>
        </div>
        <div class='col-auto cursor-pointer' data-toggle='modal' data-target='.images-<?php echo $id?>-modal-lg' id='images<?php echo $id?>'>
          <img class='img-fluid img-thumbnail' src='<?php echo $thumbnail;?>' width='65px' height='65px' title='Imagen publicación' data-toggle='tooltip'>
        </div>
        <div class="col-10 pr-0">
          <div class="row">
            <div class="col-auto p-0">
              <a href="<?php echo $permalink;?>" target="_blank" title="Publicación en ML" data-toggle="tooltip" id="codigo<?php echo $id;?>"><?php echo $id;?></a>
              <?php if(in_array($id_user,$array_revendedores)){ ?>
                <?php
                $sql_public="SELECT CODIGOORIGINAL,GANANCIA FROM publicacion WHERE CODIGO='$id';";
                $r=$conn->query($sql_public);
                if($r->num_rows>0){while($rw=$r->fetch_assoc()){
                  $codigoOriginal=$rw['CODIGOORIGINAL'];
                  $ganancia=$rw['GANANCIA'];
                  $ganancia=$ganancia/100;
                }}
                if(isset($codigoOriginal) && !empty($codigoOriginal)){
                  $ch=curl_init();
                  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items/$codigoOriginal");
                  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                  $result=curl_exec($ch);curl_close($ch);$var=json_decode($result);
                  $url=$var->permalink;
                  ?>
                  | <span id="CodigoOriginalReal<?php echo $id?>"><a class="text-info" href="<?php echo $url;?>" target="_blank" title="Publicación de Reventa en ML" data-toggle="tooltip"><?php echo $codigoOriginal;?></a></span>
                  <span title="Editar Publicación Original" data-toggle="tooltip" id="spanCodigoOriginal<?php echo $id?>">
                    <svg xmlns='http://www.w3.org/2000/svg' width='14px' class='svg-info cursor-pointer' data-toggle="modal" data-target="#modal-codigo<?php echo $id?>" viewBox='0 0 576 512'><path d='M402.6 83.2l90.2 90.2c3.8 3.8 3.8 10 0 13.8L274.4 405.6l-92.8 10.3c-12.4 1.4-22.9-9.1-21.5-21.5l10.3-92.8L388.8 83.2c3.8-3.8 10-3.8 13.8 0zm162-22.9l-48.8-48.8c-15.2-15.2-39.9-15.2-55.2 0l-35.4 35.4c-3.8 3.8-3.8 10 0 13.8l90.2 90.2c3.8 3.8 10 3.8 13.8 0l35.4-35.4c15.2-15.3 15.2-40 0-55.2zM384 346.2V448H64V128h229.8c3.2 0 6.2-1.3 8.5-3.5l40-40c7.6-7.6 2.2-20.5-8.5-20.5H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V306.2c0-10.7-12.9-16-20.5-8.5l-40 40c-2.2 2.3-3.5 5.3-3.5 8.5z'/></svg>
                  </span>
                <?php }else{ ?>
                  | <button type="button" class="btn btn-sm btn-link text-warning" data-toggle="modal" data-target="#modal-codigo<?php echo $id?>" id="ButtonCodigoOriginal<?php echo $id?>">Agregar Publicacion Original</button>
                <?php } ?>
              <?php } ?>
            </div>
            <div class='col-auto ml-auto text-center text-primary p-0' id="cont_precio<?php echo $id?>">
              <input class='cursor-pointer input_precio text-primary' type='number' min='1' value="<?php $decimales=number_format($precio,2,'.',''); echo $decimales;?>"  step='.01' title='Precio de Venta (Bs.)' data-toggle='tooltip' id="price<?php echo $id;?>"/>
              <span class="text-dark">Bs. | </span>
              <?php if(isset($ganancia) && !empty($ganancia)){
                $gananciaAux=$ganancia/($ganancia+1);
                 ?>
                <span class="text-success" title='<?php echo "Ganancia ".$decimales=number_format($precio*0.89*$gananciaAux,2,',','.')." Bs.";?>' data-toggle='tooltip'><?php echo $ganancia*100;?> %</span>
              <?php } ?>
            </div>
          </div>
          <div class="row">
            <div class='col-8 p-0' id="cont_title<?php echo $id?>">
              <span class='cursor-pointer' data-toggle='modal' data-target='#quantity<?php echo $id?>'>
                (<b class='text-success' title='Stock' data-toggle='tooltip' id='stock_real<?php echo $id?>'><?php echo $quantity;?> u.</b>)
              </span>
              <input class='cursor-pointer input_title text-primary' type="text" value="<?php echo $title;?>" id='titulo<?php echo $id?>' maxlength="60">
            </div>
            <div class='col-sm-1 text-center text-success cursor-pointer p-0' data-toggle='modal' data-target='.res-<?=$id?>-modal-lg' id='cant_respuestas<?=$id?>'>
              <span title='Número de respuestas automáticas asignadas' data-toggle='tooltip'><b><?php echo $cant_respuestas;?></b>  Resp.</span>
            </div>
            <div class='col-auto ml-auto p-0'>
              <button class='btn btn-link p-0 px-1' id='but<?php echo $id?>' data-toggle='modal' data-target='.description<?php echo $id?>'>
                <svg xmlns='http://www.w3.org/2000/svg' title='Descripción' data-toggle='tooltip' width='12px' class='svg-primary' viewBox='0 0 448 512'><path d='M288 44v40c0 8.837-7.163 16-16 16H16c-8.837 0-16-7.163-16-16V44c0-8.837 7.163-16 16-16h256c8.837 0 16 7.163 16 16zM0 172v40c0 8.837 7.163 16 16 16h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16zm16 312h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm256-200H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16h256c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16z'/></svg>
              </button>
              <button class='btn btn-link p-0 px-1' id='hist<?php echo $id?>' data-toggle='modal' data-target='.bd-<?php echo $id?>-modal-lg'>
                <svg xmlns='http://www.w3.org/2000/svg' title='Historial de preguntas' data-toggle='tooltip' width='12px' class='svg-primary' viewBox='0 0 512 512'><path d='M256 32C114.6 32 0 125.1 0 240c0 49.6 21.4 95 57 130.7C44.5 421.1 2.7 466 2.2 466.5c-2.2 2.3-2.8 5.7-1.5 8.7S4.8 480 8 480c66.3 0 116-31.8 140.6-51.4 32.7 12.3 69 19.4 107.4 19.4 141.4 0 256-93.1 256-208S397.4 32 256 32z'/></svg>
              </button>
              <button class='btn btn-link p-0 px-1' id='estadisticas<?php echo $id?>' data-toggle='modal' data-target='.modal-estadist-<?php echo $id?>'>
                <svg xmlns="http://www.w3.org/2000/svg" title='Estadísticas' data-toggle='tooltip' width='12px' class='svg-primary' viewBox="0 0 512 512"><path d="M332.8 320h38.4c6.4 0 12.8-6.4 12.8-12.8V172.8c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v134.4c0 6.4 6.4 12.8 12.8 12.8zm96 0h38.4c6.4 0 12.8-6.4 12.8-12.8V76.8c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v230.4c0 6.4 6.4 12.8 12.8 12.8zm-288 0h38.4c6.4 0 12.8-6.4 12.8-12.8v-70.4c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v70.4c0 6.4 6.4 12.8 12.8 12.8zm96 0h38.4c6.4 0 12.8-6.4 12.8-12.8V108.8c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v198.4c0 6.4 6.4 12.8 12.8 12.8zM496 384H64V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v336c0 17.67 14.33 32 32 32h464c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"/></svg>
              </button>
              <button class="btn btn-link p-0 px-1" id="opt<?php echo $id?>" data-toggle="modal" data-target=".op-<?php echo $id?>-modal-lg">
                <svg xmlns="http://www.w3.org/2000/svg" title="Opciones" data-toggle="tooltip" width="12px" class="svg-primary" viewBox="0 0 512 512"><path d="M328 256c0 39.8-32.2 72-72 72s-72-32.2-72-72 32.2-72 72-72 72 32.2 72 72zm104-72c-39.8 0-72 32.2-72 72s32.2 72 72 72 72-32.2 72-72-32.2-72-72-72zm-352 0c-39.8 0-72 32.2-72 72s32.2 72 72 72 72-32.2 72-72-32.2-72-72-72z"/></svg>
              </button>
              <span class="p-0 px-1">
                <?php
                switch($nivel_public){
                  case 'gold_special':$exposi='Premium';$color='svg-warning';$comision='11%';break;
                  case 'bronze':$exposi='Clásico &nbsp&nbsp';$color='svg-secondary';$comision='9%';break;
                  case 'free':$exposi="Gratuita &nbsp&nbsp";$color="svg-danger";$comision="0%";break;
                }
                ?>
                <svg xmlns='http://www.w3.org/2000/svg' width='12px' title='&nbsp&nbsp&nbsp Publicación <?php echo $exposi;?> &nbsp&nbsp&nbsp&nbsp <?php echo $comision;?> a pagar por cada venta' data-toggle='tooltip' class='<?php echo $color;?>' viewBox='0 0 384 512'><path d='M97.12 362.63c-8.69-8.69-4.16-6.24-25.12-11.85-9.51-2.55-17.87-7.45-25.43-13.32L1.2 448.7c-4.39 10.77 3.81 22.47 15.43 22.03l52.69-2.01L105.56 507c8 8.44 22.04 5.81 26.43-4.96l52.05-127.62c-10.84 6.04-22.87 9.58-35.31 9.58-19.5 0-37.82-7.59-51.61-21.37zM382.8 448.7l-45.37-111.24c-7.56 5.88-15.92 10.77-25.43 13.32-21.07 5.64-16.45 3.18-25.12 11.85-13.79 13.78-32.12 21.37-51.62 21.37-12.44 0-24.47-3.55-35.31-9.58L252 502.04c4.39 10.77 18.44 13.4 26.43 4.96l36.25-38.28 52.69 2.01c11.62.44 19.82-11.27 15.43-22.03zM263 340c15.28-15.55 17.03-14.21 38.79-20.14 13.89-3.79 24.75-14.84 28.47-28.98 7.48-28.4 5.54-24.97 25.95-45.75 10.17-10.35 14.14-25.44 10.42-39.58-7.47-28.38-7.48-24.42 0-52.83 3.72-14.14-.25-29.23-10.42-39.58-20.41-20.78-18.47-17.36-25.95-45.75-3.72-14.14-14.58-25.19-28.47-28.98-27.88-7.61-24.52-5.62-44.95-26.41-10.17-10.35-25-14.4-38.89-10.61-27.87 7.6-23.98 7.61-51.9 0-13.89-3.79-28.72.25-38.89 10.61-20.41 20.78-17.05 18.8-44.94 26.41-13.89 3.79-24.75 14.84-28.47 28.98-7.47 28.39-5.54 24.97-25.95 45.75-10.17 10.35-14.15 25.44-10.42 39.58 7.47 28.36 7.48 24.4 0 52.82-3.72 14.14.25 29.23 10.42 39.59 20.41 20.78 18.47 17.35 25.95 45.75 3.72 14.14 14.58 25.19 28.47 28.98C104.6 325.96 106.27 325 121 340c13.23 13.47 33.84 15.88 49.74 5.82a39.676 39.676 0 0 1 42.53 0c15.89 10.06 36.5 7.65 49.73-5.82zM97.66 175.96c0-53.03 42.24-96.02 94.34-96.02s94.34 42.99 94.34 96.02-42.24 96.02-94.34 96.02-94.34-42.99-94.34-96.02z'/></svg>
              </span>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal Codigo Original -->
      <div class="modal fade" id="modal-codigo<?php echo $id?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
          <div class="modal-content p-3">
            <span class="text-muted mb-2">Agregar Id de la Publicación Original de donde se tomo para Reventa</span>
            <input type="text" id="codigoOriginal<?php echo $id?>">
            <button type="button" class="btn btn-primary btn-sm px-5 mt-2" id="agregarCodigoOriginal<?php echo $id?>">Agregar</button>
          </div>
        </div>
      </div>
      <script>
        //Codigo Original
        $(document).on('click',"#agregarCodigoOriginal<?php echo $id;?>",function(){
          var codigoOrig=$("#codigoOriginal<?php echo $id;?>").val();
          var codigo=document.getElementById("codigo<?php echo $id;?>").innerHTML;
          $.ajax({url:'ajax/ajax_codigo_original.php',type:'GET',data:{codigo:codigo,codigoOrig:codigoOrig},async:false,dataType:'text',
          success: function(text){
            if(typeof text!=='undefined'){
              if(text=="No"){
                const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
                toast({type:'error',title:"¡Hubo un problema! \n No se pudo registrar en BD."})
              }else if(text=="NoExistPub"){
                const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
                toast({type:'error',title:"¡Hubo un problema! \n La Publicación No Existe."})
              }else if(text!=''){
                const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
                toast({type:'success',title:'¡Fue Agregado el codigo exitosamente!'});
                $("#ButtonCodigoOriginal<?php echo $id?>").after("<a href='"+text+"' class='text-success' target='_blank' title='Publicación de Reventa en ML' data-toggle='tooltip'>"+codigoOrig+"</a>");
                $('#ButtonCodigoOriginal<?php echo $id?>').remove();
              }else{
                const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
                toast({type:'error',title:"¡Hubo un problema! \n La Publicación No Existe."})
              }
            }
            $('#modal-codigo<?php echo $id?>').modal('hide');
          }
        });
      });
    </script>
    <!-- Modal Editar Codigo Original -->
    <div class="modal fade" id="modal-Editarcodigo<?php echo $id?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content p-3">
          <span class="text-muted mb-2">Agregar Id de la Publicación Original de donde se tomo para Reventa</span>
          <input type="text" id="EditarcodigoOriginal<?php echo $id?>">
          <button type="button" class="btn btn-primary btn-sm px-5 mt-2" id="EditarCodigoOriginal<?php echo $id?>">Editar</button>
        </div>
      </div>
    </div>
    <script>
      //editar Codigo Original
      $(document).on('click',"#EditarCodigoOriginal<?php echo $id;?>",function(){
        var codigoOrig=$("#EditarcodigoOriginal<?php echo $id;?>").val();
        var codigo=document.getElementById("codigo<?php echo $id;?>").innerHTML;
        $.ajax({url:'ajax/ajax_editar_codigo_original.php',type:'GET',data:{codigo:codigo,codigoOrig:codigoOrig},async:false,dataType:'text',
        success: function(text){
          if(typeof text!=='undefined'){
            if(text=="No"){
              const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
              toast({type:'error',title:"¡Hubo un problema! \n No se pudo registrar en BD."})
            }else if(text=="NoExistPub"){
              const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
              toast({type:'error',title:"¡Hubo un problema! \n La Publicación No Existe."})
            }else if(text!=''){
              const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
              toast({type:'success',title:'¡Fue Editado Exitosamente!'});
              //$("#spanCodigoOriginal<?php echo $id?>").before("<a class='text-success' href='"+text+"' target='_blank' title='Publicación de Reventa en ML' data-toggle='tooltip'>"+codigoOrig+"</a>");
              $('#CodigoOriginalReal<?php echo $id?>').remove();
            }else{
              const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
              toast({type:'error',title:"¡Hubo un problema! \n La Publicación No Existe."})
            }
          }
          $('#modal-Editarcodigo<?php echo $id?>').modal('hide');
        }
      });
    });
  </script>
      <!-- Imagenes -->
      <div class="modal fade images-<?php echo $id?>-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content container">
            <div class="modal-header">
              <div class="container">
                <div class="row">
                  <h5 class="modal-title" id="exampleModalLabel"><?php echo $title;?></h5>
                </div>
              </div>
              <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body" id="container-images<?php echo $id?>">
            </div>
            <div class="modal-footer text-muted">
              Imágenes de la publicación.
            </div>
          </div>
        </div>
      </div>
      <script>
        $('#images<?php echo $id?>').click(function(){
          $('#container-images<?php echo $id?>').empty();
          $.get('ajax/ajax_images.php',{item:'<?php echo $id?>'},verificar,'json');
          function verificar(respuesta){
            $.each(respuesta.pictures, function(i,resultado){
              $('#container-images<?php echo $id?>').append("<img class='mr-2 mb-2 col-auto images-publicaciones img-fluid img-thumbnail' src='"+resultado.secure_url+"'>");
            });
          }
        });
        //Title
        $(document).on('click',"#titulo<?php echo $id?>",function(){
          var aux1="<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' title='Actualizar' data-toggle='tooltip' width='13px' id='actualizartitle<?php echo $id?>' class='cursor-pointer mr-1 svg-info'><path d='M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z'/></svg>";
          var aux2="<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' title='Cancelar' data-toggle='tooltip' width='12px' id='cancelar_title<?php echo $id?>' class='cursor-pointer svg-danger'><path d='M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z'/></svg>";
          if(typeof $("#actualizartitle<?php echo $id?>").val()!=='undefined'){
            $("#actualizartitle<?php echo $id?>").remove();
            $("#cancelar_title<?php echo $id?>").remove();
            $("#cont_title<?php echo $id?>").append("<span class='auxiliar'>"+aux1+aux2+"</span>");
          }else{
            if(typeof $("span.auxiliar").val()!=='undefined'){
              $('span.auxiliar').each(function(){var aux=$(this).remove();});
              $("span.auxiliar").remove();
            }
            $("#cont_title<?php echo $id?>").append("<span class='auxiliar'>"+aux1+aux2+"</span>");
          }
        });
        $(document).on('click','#cancelar_title<?php echo $id?>',function(){
          $('#actualizartitle<?php echo $id?>').remove();
          $('#cancelar_title<?php echo $id?>').remove();
        });
        $(document).on('click','#actualizartitle<?php echo $id?>',function(){
          var id="<?php echo $id?>";
          var title=$("#titulo<?php echo $id?>").val();
          $.ajax({url:'ajax/ajax_title.php',type:'GET',data:{item:id,title:title},async:false,dataType:'json',
          success: function(json){
            if(typeof json.title!="undefined"){
              const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
              toast({type:'success',title:'¡Fue Actualizado Exitosamente a '+title+'!'})
              $('#actualizartitle<?php echo $id?>').remove();
              $('#cancelar_title<?php echo $id?>').remove();
              $("#titulo<?php echo $id?>").removeClass("text-primary").addClass("text-success");
            }else{
              const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
              toast({type:'error',title:"¡Hubo un problema! \n Inténtalo de nuevo."})
              $('#actualizartitle<?php echo $id?>').remove();
              $('#cancelar_title<?php echo $id?>').remove();
            }
          }
        });
      });
    </script>
    <!-- Quantity -->
    <div class="modal fade" id="quantity<?php echo $id?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><?php echo $title;?></h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close" id="close_quantity<?php echo $id;?>">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body text-muted">
            <b class="text-muted">Editar Cantidad</b>
            <div class="container">
              <div class="row">
                <input class="form-control col-5" type="number" value="<?php echo $quantity;?>" id="quantity_input<?php echo $id?>"/>
                <button class="col-auto ml-auto btn btn-success btn-sm mt-2" type="button" id="actualizarquantity<?php echo $id?>">Actualizar</button>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="container">
              <div class="row">
                <small class="text-muted col-12">Ten en cuenta que puede tardar un momento hasta ver la información actualizada.</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      //Quantity
      $("#actualizarquantity<?php echo $id?>").click(function(){
        var id="<?php echo $id?>";
        var quantity=$("#quantity_input<?php echo $id?>").val();
        var band=0;
        var origin='paused';
        $.ajax({url:'ajax/ajax_stock.php',type:'GET',data:{item:id,quantity:quantity,band:band,origin:origin},async:false,dataType:'json',
        success: function(json){
          if(typeof json.available_quantity!='undefined'){
            const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
            toast({type:'success',title:'¡Fue Actualizado Exitosamente a '+quantity+'!'})
            $("#close_quantity<?php echo $id;?>").click();
          }else{
            const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
            toast({type:'error',title:"¡Hubo un problema! \n Inténtalo de nuevo."})
          }
        }
      });
    });
  </script>
  <!-- numero de publicaciones -->
  <div class="modal fade res-<?=$id?>-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content container">
        <div class="modal-header">
          <div class="container">
            <div class="row">
              <h5 class="modal-title"><?php echo $title;?></h5>
            </div>
          </div>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body text-muted container-fluid" id='body_cant_resp<?php echo $id?>'>
        </div>
        <div class="modal-footer text-muted">
          <small class="text-muted text-center">Se mostrarán un máximo de 20 respuestas automáticas.</small>
        </div>
      </div>
    </div>
  </div>
  <script>
    //cantidad de respuestas
    $("#cant_respuestas<?=$id?>").click(function(){
      $("#body_cant_resp<?php echo $id?>").empty();
      var id="<?php echo $id?>";
      $.ajax({url:'ajax/ajax_cantidad_respuestas.php',type:'GET',data:{item:id},async:false,dataType:'text',
      success: function(texto){
        var respuestas='';
        if(texto!=0){
          respuestas=texto.split('%&%');
          $.each(respuestas,function(i,resultado){
            var auxiliar=resultado.split('~¬');
            var keywords=auxiliar[0].replace(/,/g,', ');
            $("#body_cant_resp<?php echo $id?>").append("<div class='row align-items-center justify-content-center'><div class='col-sm-8'><small title='Respuesta programada' data-toggle='tooltip'>"+auxiliar[1]+"</small></div><div class='col-sm-4 text-center' title='Frases Filtros' data-toggle='tooltip'><small>"+keywords+"</small></div></div><hr>");
          });
        }else{
          $("#body_cant_resp<?php echo $id?>").append("<div class='container-fluid text-success'>No tienes respuestas automáticas asociada a esta publicación.</div>")
        }
      }
    });
  });
  //Precio
  $(document).on('click',"#price<?php echo $id?>",function(){
    var aux1="<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 448 512' title='Actualizar' data-toggle='tooltip' width='12px' id='actualizarprice<?php echo $id?>' class='mr-1 cursor-pointer svg-info'><path d='M400 480H48c-26.51 0-48-21.49-48-48V80c0-26.51 21.49-48 48-48h352c26.51 0 48 21.49 48 48v352c0 26.51-21.49 48-48 48zm-204.686-98.059l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.248-16.379-6.249-22.628 0L184 302.745l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.25 16.379 6.25 22.628.001z'/></svg>";
    var aux2="<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' title='Cancelar' data-toggle='tooltip' width='12px' id='cancelar_price<?php echo $id?>' class='cursor-pointer svg-danger'><path d='M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z'/></svg>";
    if (typeof $("#actualizarprice<?php echo $id?>").val()!=='undefined'){
      $("#actualizarprice<?php echo $id?>").remove();
      $("#cancelar_price<?php echo $id?>").remove();
      $("#cont_precio<?php echo $id?>").append("<span class='auxiliar'>"+aux1+aux2+"</span>");
    }else{
      if(typeof $("span.auxiliar").val()!=='undefined'){
        $('span.auxiliar').each(function(){var aux=$(this).remove();});
        $("span.auxiliar").remove();
      }
      $("#cont_precio<?php echo $id?>").append("<span class='auxiliar'>"+aux1+aux2+"</span>");
    }
  });
  $(document).on('click','#cancelar_price<?php echo $id?>',function(){
    $('#actualizarprice<?php echo $id?>').remove();
    $('#cancelar_price<?php echo $id?>').remove();
  });
  $(document).on('click','#actualizarprice<?php echo $id?>',function(){
    var id="<?php echo $id?>";
    var price=$("#price<?php echo $id?>").val();
    $.ajax({url:'ajax/ajax_price.php',type:'GET',data:{item:id,price:price},async:false,dataType:'json',
    success: function(json){
      if(typeof json.price!='undefined'){
        if(json.price==price){
          const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
          toast({type:'success',title:'¡Fue Actualizado Exitosamente a '+price+' Bs!'})
          $('#actualizarprice<?php echo $id?>').remove();
          $('#cancelar_price<?php echo $id?>').remove();
          $("#price<?php echo $id?>").removeClass("text-primary").addClass("text-success");
        }else{
          const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
          toast({type:'error',title:"¡Hubo un problema! \n Inténtalo de nuevo."})
        }
      }else{
        const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
        toast({type:'error',title:"¡Hubo un problema! \n Inténtalo de nuevo."})
      }
      $('#actualizarprice<?php echo $id?>').remove();
      $('#cancelar_price<?php echo $id?>').remove();
    }
  });
});
</script>
<!-- Descripcion -->
<div class="modal fade description<?php echo $id?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content container">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo $title;?></h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body text-muted" >
        <div id="text<?php echo $id?>"></div>
      </div>
      <div class="modal-footer">
        <div class="container-fluid">
          <div class="row">
            <div class="col-auto">
              <button class="btn btn-sm px-4 btn-primary" type="button" id="edit_description<?php echo $id?>">Editar</button>
              <button class="btn btn-sm px-4 btn-success" type="button" id="edit_description_acept<?php echo $id?>">Aceptar</button>
            </div>
            <div class="col-auto ml-auto">
              <small class="text-muted">Última actualización.</small>
              <small class="text-muted"><b id="last<?php echo $id?>"></b></small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Preguntas -->
<div class="modal fade bd-<?php echo $id?>-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content container">
      <div class="modal-header">
        <div class="container">
          <div class="row">
            <h5 class="modal-title" id="exampleModalLabel"><?php echo $title;?></h5>
          </div>
          <div class="row text-muted">
            <small id="sub<?php echo $id?>"></small>
          </div>
        </div>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="cont<?php echo $id?>" class="container">
        </div>
      </div>
      <div class="modal-footer">
        <small class="text-muted">Se mostrarán máximo las últimas 50 preguntas que se han respondido en la actual publicación.</small>
      </div>
    </div>
  </div>
</div>
<!-- Estadisticas -->
<div class="modal fade modal-estadist-<?php echo $id?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content container">
      <div class="modal-header">
        <div class="container">
          <div class="row">
            <h5 class="modal-title" id="exampleModalLabel"><?php echo $title;?></h5>
          </div>
          <div class="row">
            <span class="d-block text-muted">Estadisticas de Visitas</span>
          </div>
          <div class="row text-muted">
            <small id="visitas-<?php echo $id?>"></small>
          </div>
        </div>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body container">
        <div class="row mb-3">
          <span class="col-auto text-muted">Selecciona el mes que deseas:</span>
          <div class="col-auto">
            <div class='input-group input-group-sm'>
              <div class='input-group-prepend'>
                <label class='input-group-text' for='"+id_aux+"'>Mes</label>
              </div>
              <select class='custom-select custom-select-sm' id='mes<?php echo $id?>'>
                <option value="Ene">Enero</option>
                <option value="Feb">Febrero</option>
                <option value="Mar">Marzo</option>
                <option value="Abr">Abril</option>
                <option value="May">Mayo</option>
                <option value="Jun">Junio</option>
                <option value="Jul">Julio</option>
                <option value="Ago">Agosto</option>
                <option value="Sep">Septiembre</option>
                <option value="Oct">Octubre</option>
                <option value="Nov">Noviembre</option>
                <option value="Dic">Diciembre</option>
              </select>
            </div>
          </div>
          <div class="col-auto">
            <div class='input-group input-group-sm'>
              <div class='input-group-prepend'>
                <label class='input-group-text' for='"+id_aux+"'>Año</label>
              </div>
              <select class="custom-select custom-select-sm" id='anio<?php echo $id?>'>
                <option value="2019">2019</option>
                <option value="2018">2018</option>
                <option value="2017">2017</option>
                <option value="2016">2016</option>
                <option value="2015">2015</option>
                <option value="2014">2014</option>
              </select>
            </div>
          </div>
          <div class="col-auto">
            <button class="btn btn-success btn-sm" type="button" id='search_estadist<?php echo $id?>'>Buscar</button>
          </div>
        </div>
        <div class="row">
          <canvas id="myAreaChart<?php echo $id?>" width="100%" height="30"></canvas>
        </div>
      </div>
      <div class="modal-footer" id='chart_foot<?php echo $id?>'>
      </div>
    </div>
  </div>
</div>
<script>
  $("#search_estadist<?php echo $id?>").click(function(){
    $('#chart_foot<?php echo $id?>').empty();
    var mes=$("#mes<?php echo $id?>").val();
    var anio=$("#anio<?php echo $id?>").val();
    var item='<?php echo $id?>';
    $.get("ajax/ajax_estadisticas.php",{anio:anio,mes:mes,item:item},verificar,'text');
    function verificar(respuesta){
      var array=respuesta.split('%');
      var totaly=parseInt(array[0]);//total de valores
      var mayor=parseInt(array[1]);
      var fechas=array[2];
      fechas=fechas.split('|');
      var cantidades=array[3];
      var totales=array[4];
      $('#chart_foot<?php echo $id?>').append("<small class='text-muted'>Tuviste "+totales+" visitas en este período.</small>")
      cantidades=cantidades.split('|');
      if(window.graficaVisitas){
        window.graficaVisitas.clear();
        window.graficaVisitas.destroy();
      }
      var ctx=document.getElementById("myAreaChart<?php echo $id?>");
      window.graficaVisitas=new Chart(ctx,{
        type: 'line',
        data: {
          labels: fechas,
          datasets: [{
            label: "Visitas",
            lineTension: 0.3,
            backgroundColor: "rgba(70,203,62,0.3)",
            borderColor: "rgba(70,203,62,1)",
            pointRadius: 5,
            pointBackgroundColor: "rgba(70,203,62,1)",
            pointBorderColor: "rgba(255,255,255,0.8)",
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(70,203,62,1)",
            pointHitRadius: 20,
            pointBorderWidth: 2,
            data: cantidades,
          }],
        },
        options:{
          scales:{
            xAxes:[{
              time:{unit:'Días'},
              gridLines:{display:false},
              ticks:{maxTicksLimit: totaly}
            }],
            yAxes:[{
              ticks:{
                min:0,
                max: mayor,
                maxTicksLimit:8
              },
              gridLines:{color:"rgba(0, 0, 0, .125)",}
            }],
          },
          legend:{display:false}
        }
      });
    }
  });//fin click boton visitas
</script>
<!-- Opciones -->
<div class="modal fade op-<?php echo $id?>-modal-lg" tabindex="-2" role="dialog" aria-labelledby="myLargeModalLabel2" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content container">
      <div class="modal-header">
        <div class="container">
          <div class="row">
            <h5 class="modal-title" id="exampleModalLabel2"><?php echo $title;?></h5>
          </div>
        </div>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container">
          Linea de categoria en la que se encuentra esta publicación.
          <div class="row">
            <div id="opti<?php echo $id?>">
            </div>
          </div>
          <div class="row ml-3">
            <small id="tendencia<?php echo $id?>" data-toggle="modal" data-target="#trends<?php echo $id?>"><a href="#">Tendencias</a></small>
          </div>
          <hr>
          <div class="row mr-2 mt-2">
            <b class="ml-2 col-auto">Activar la publicación.</b>
            <button type="button" class="btn btn-sm btn-warning" id="activar<?php echo $id?>">Activar</button>
          </div>
          <div class="row">
            <div class="col-auto ml-3">
              <small class="text-muted">Ten en cuenta que los cambios tardarán un tiempo en reflejarse</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Tendencias -->
<div class="modal fade" id="trends<?php echo $id?>" tabindex="-4" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tendencias de la categoría</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body text-muted" id="categ<?php echo $id?>">
      </div>
      <div class="modal-footer">
        <small class="text-muted">Estas son las frases con las cuales los compradores buscan en esta categoría. <br>
          Te recomendamos uses las palabras claves en los titulos de tus publicaciones, para que tengas mayor posicionamiento en las búsquedas.</small>
        </div>
      </div>
    </div>
  </div>
  <script>
    //Activar publicacion
    $(document).on('click','#activar<?php echo $id?>',function(){
      var items="<?php echo $id?>";
      var band=0;
      $.ajax({url:'ajax/ajax_activar.php',type:'GET',data:{item:items,band:band},async:false,dataType:'json',
      success: function(json){
        var status=json.status;
        if(typeof status!='undefined'){
          status=json.status;
          if(status=='active'){
            const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
            toast({type: 'success',title: '¡Fue activada Exitosamente!'})
            $('#close_<?php echo $id?>').click();
            $("#activar<?php echo $id?>").attr('disabled','disabled');
          }else{
            const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
            toast({type:'error',title:"¡Hubo un problema! \n Inténtalo de nuevo."})
          }
        }
      }
    });
  });
  //Descripcion de la publicacion
  $("#but<?php echo $id?>").click(function(){
    $("#edit_description<?php echo $id?>").show();
    $("#edit_description_acept<?php echo $id?>").hide();
    var item="<?php echo $id?>";
    var url='https://api.mercadolibre.com/items/'+item+'/description';
    $.getJSON(url,function(datos){
      var description=datos.plain_text;
      var auxiliar=description.split('\n').length+3;
      description= "<textarea class='textarea_description' rows='"+auxiliar+"' disabled id='textarea_description<?php echo $id?>'>"+description+"</textarea>";
      document.getElementById("text<?php echo $id?>").innerHTML=description;
      var update=datos.last_updated;
      document.getElementById("last<?php echo $id?>").innerHTML=update.substr(0,10);
    });
  });
  //Editar Descripcion
  $(document).on('click','#edit_description<?php echo $id?>',function(){
    $("#textarea_description<?php echo $id?>").removeAttr('disabled');
    $("#textarea_description<?php echo $id?>").css('background-color','#f7ffff');
    $("#textarea_description<?php echo $id?>").focus();
    $("#edit_description<?php echo $id?>").hide();
    $("#edit_description_acept<?php echo $id?>").show();
  });
  $(document).on("click","#edit_description_acept<?php echo $id?>",function(){
    var text=$("#textarea_description<?php echo $id?>").val();
    $.ajax({url:'ajax/ajax_edit_description.php',type:'GET',data:{item:"<?php echo $id?>",text:text},async:false,dataType:'json',
    success: function(json){
      if(typeof json.last_updated!="undefined"){
        const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
        toast({type:'success',title:'¡Fue Actualizada Exitosamente!'})
        $("#edit_description_acept<?php echo $id?>").hide();
        $("#edit_description<?php echo $id?>").show();
        $("#textarea_description<?php echo $id?>").attr('disabled','disabled');
        $("#textarea_description<?php echo $id?>").css("background-color","#eee");
      }else{
        const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
        toast({type:'error',title:"¡Hubo un problema! \n Inténtalo de nuevo."})
      }
    }
  });
});
//Preguntas historial
$("#hist<?php echo $id?>").click(function(){
  $('#cont<?php echo $id?>').empty();
  $.get("ajax/ajax_historial.php",{item:"<?php echo $id?>"},verificar,'json');
  function verificar(respuesta){
    var meses=[' ','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    document.getElementById("sub<?php echo $id?>").innerHTML=respuesta.total+' preguntas obtenidas en esta publicación.';
    if(respuesta.total==0){$("#cont<?php echo $id?>").append("<div class='text-success'>No han hecho preguntas en esta publicación.</div>");
  }else{
    $.each(respuesta.questions,function(i,resultado){
      var texto=resultado.text;
      var date_preg=resultado.date_created;
      var dia_preg=date_preg.substr(8,2);
      var hora_preg=date_preg.substr(11,5);
      if(date_preg.substr(5,2)>9){var mes_preg=date_preg.substr(5,2);}else{var mes_preg=date_preg.substr(6,1);}
      var fecha_preg=dia_preg+' de '+meses[mes_preg]+' a las '+hora_preg;
      var answer=resultado.answer.text;
      var date_resp=resultado.answer.date_created;
      var dia_resp=date_resp.substr(8,2);
      var hora_resp=date_resp.substr(11,5);
      if(date_resp.substr(5, 2)>9){var mes_resp=date_resp.substr(5,2);}else{var mes_resp=date_resp.substr(6,1);}
      var fecha_resp=dia_resp+' de '+meses[mes_resp]+' a las '+hora_resp;
      $("#cont<?php echo $id?>").append("<div class='row mb-3 border_respuesta'><div class='col-10 pregunta mb-2'><div class='row'><div class='col-12'>"+texto+"</div><div class='col-auto ml-auto text-muted'><small>"+fecha_preg+"</small></div></div></div><div class='col-10 ml-auto respuesta'><div class='row'><div class='col-12'>"+answer+"</div><div class='col-auto ml-auto text-muted'><small>"+fecha_resp+"</small></div></div></div></div>");
    });
  }
}
});
//Opciones
$("#opt<?php echo $id?>").click(function(){
  $("#opti<?php echo $id?>").empty();
  var cat='<?php echo $categoria;?>';
  var url='https://api.mercadolibre.com/categories/'+cat;
  $.getJSON(url,function(datos){
    var total=datos.total_items_in_this_category;
    var ramas=datos.path_from_root;
    var len=ramas.length;
    $.each(ramas,function(i,resultado){
      if(i == len - 1){
        $("#opti<?php echo $id?>").append("- <span title='Última rama de la categoria' data-toggle='tooltip' class='text-success'>"+resultado.name+" </span>");
      }else{$("#opti<?php echo $id?>").append('- <b>'+resultado.name+' </b>');}
    });
    $("#opti<?php echo $id?>").append("<span title='Cantidad de publicaciones en la categoría' data-toggle='tooltip' class='text-success'> ("+total+")</span>");
  });
});
//boton de tendencias
$("#tendencia<?php echo $id?>").click(function(){
  $("#categ<?php echo $id?>").empty();
  var cat='<?php echo $categoria;?>';
  var url='https://api.mercadolibre.com/sites/MLV/trends/search?category='+cat;
  $.getJSON(url,function(datos){
    if(typeof datos!=='undefined' && datos.length>0){
      $.each(datos,function(i,resultado){
        var keyword=resultado.keyword;
        var urlaux=resultado.url;
        var aux="<div class='row px-3 mb-2'><b>"+(i+1)+"- </b><a href='"+urlaux+"' target='_blank'>"+keyword+'</a></div>';
        $("#categ<?php echo $id?>").append(aux);
      });
    }else{
      var opcionalt="<div class='text-success'>¡Esta rama no tiene tendencias!</div>";
      $("#categ<?php echo $id?>").append(opcionalt);
    }
  });
});
</script>
<?php
}
}
} ?>
<div class="container mt-2">
  <div class="row justify-content-center">
    <?php
    if($items_paginacion>50){
      $NroPag=ceil($items_paginacion/50);
      ?>
      <nav aria-label="Page navigation example">
        <ul class="pagination">
          <?php
          if(isset($_GET['page']) & !empty($_GET['page'])){
            $pagina=$_GET['page'];
            if($pagina>6 && $NroPag>6){
              ?>
              <li class="page-item">
                <a class="page-link" href="pausadas.php?page=<?php echo ($pagina-6);?>" aria-label="Previous">
                  <span aria-hidden="true">&laquo;</span>
                  <span class="sr-only">Previous</span>
                </a>
              </li>
              <?php
            }
          }else{$pagina=1;}
          if($pagina>5){
            for($i=($pagina-5);$i<($pagina+5);$i++){
              if(($i-1)==$NroPag){
                break;
              }else{
                if($i==$pagina){
                  ?>
                  <li class="page-item active"><a class="page-link" href="pausadas.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
                  <?php }else{ ?>
                  <li class="page-item"><a class="page-link" href="pausadas.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
                  <?php
                }
              }
            }
          }else{
            for($i=1;$i<11;$i++){
              if(($i-1)==$NroPag){
                break;
              }else{
                if($i==$pagina){
                  ?>
                  <li class="page-item active"><a class="page-link" href="pausadas.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
                  <?php }else{ ?>
                  <li class="page-item"><a class="page-link" href="pausadas.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
                  <?php
                }
              }
            }
          }
          ?>
          <?php if($NroPag>10 & $pagina<($NroPag-4)){?>
            <li class="page-item">
              <a class="page-link" href="pausadas.php?page=<?php echo (5+$pagina);?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
              </a>
            </li>
          <?php } ?>
        </ul>
      </nav>
      <?php } ?>
  </div>
</div>
<div class="text-center mb-2">
  <a class="btn btn-outline-primary" href="https://myaccount.mercadolibre.com.ve/listings/#label=active" target="_blank">Ver publicaciones en Mercado Libre</a>
</div>
<input type="hidden" data-toggle="modal" data-target="#loader_modal" id="loader_now">
<div class="modal fade" id="loader_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" id="loader_real">
  <div class="modal-dialog" role="document">
    <div class="modal-content bg-transparent no_border mt-5 pt-5">
      <button type="button" class="close bg-transparent" data-dismiss="modal" aria-label="Close" id="close_loader">
      </button>
      <div class="container mt-5">
        <div class="row justify-content-center">
          <div class="col-auto background_loader">
            <div class="loader algin-self-middle"></div>
          </div>
        </div>
        <b class="row justify-content-center text-white">
          ¡¡Puede tardar unos segundos!!
        </b>
      </div>
    </div>
  </div>
</div>
<script>
  //Seleccionar todas las publicaciones
  $('#selectall').change(function(){
    var band=0;
    if($(this).is(':checked')){
      band=1;
      $('input[type=checkbox]').each(function(){if($(this).attr('disabled')){}else{$(this).prop('checked',true);}});
    }else{band=0;$('input[type=checkbox]').prop('checked',false);}
    $.get('../respuestas_automaticas/array_publicaciones.php',{all:true,band:band},verificar,'text');
    function verificar(respuesta){}
  });
  //apagar el checkbox general
  $(document).on('change','.public input[type=checkbox]',function(){
    if($(this).is(':checked')){}else{$("#selectall").prop('checked',false);}
  });
  //guardar en el array de sesion el item que se ha seleccionado
  $(document).on('click',"#publicaciones_pausadas input[name='publicaciones']",function(){
    var item=$(this).val();
    var band=0;
    if($(this).is(':checked')){band=1;}else{band=0;}
    $.get('../respuestas_automaticas/array_publicaciones.php',{item:item,band:band},verificar,'text');
    function verificar(respuesta){}
  });
  //activar masivamente
  $(document).on('click','#activar',function(){
    $("#loader_now").click();
    var band=1;
    $.get('ajax/ajax_activar.php',{band:band},verificar,'text');
    function verificar(respuesta){
      if(respuesta!=''){
        publicaciones=respuesta.split(',');
        $('input:checkbox:checked').each(function(){
          var aux=$(this).val();
          var a=publicaciones.indexOf(aux);
          if(a!=(-1)){
            $(this).prop('checked',false);
            $(this).attr('disabled','disabled');
            $("#pausar"+aux).attr('disabled','disabled');
            $("#selectall").prop('checked',false);
          }
        });
        const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
        toast({type:'success',title:'¡Fueron activadas exitosamente!'})
        setTimeout ("$('button#close_loader').click();",500);
      }else{
        setTimeout ("$('button#close_loader').click();",500);
        const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
        toast({type:'info',title:'¡No hay publicaciones para activar!'})
      }
    }
  });
</script>
<!-- Descargar Publicaciones -->
<div class="modal fade" id="descargar_publicaciones" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Descargar Publicaciones</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close" id='close_publicaciones'>
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body text-muted">
        <div class="container">
          <div class="row text-success ml-2 mb-2">
            Selecciona las publicaciones que deseas descargar.
          </div>
          <form action="download_publicaciones.php" method="get">
            <input type="checkbox" name="activas" value="1" id='activ'>
            <label for="activ">Activas</label>
            <br>
            <input type="checkbox" name="pausadas" value="1" id='pausad'>
            <label for="pausad">Pausadas</label>
            <br>
            <input type="checkbox" name="inactivas" value="1" id="finaliz">
            <label for="finliz">Inactivas (Finalizadas)</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success btn-sm mr-auto text-white" id='action_descargar'>Descargar Publicaciones</button>
        </form>
        <small class="text-muted my-1">Se descargarán en formato Excel.</small>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).on('click','#action_descargar',function(){
    $('#close_publicaciones').click();
    const toast=swal.mixin({toast:true,position:'top',showConfirmButton:false,timer:8000});
    toast({type:'info',title:'¡Se estan descargando las publicaciones! \n ¡Tardará unos segundos!'})
  });
</script>
<!-- Stock -->
<div class="modal fade" id="modificar_stock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container">
          <div class="row">
            <h5 class="modal-title">Modificar Stock</h5>
          </div>
          <div class="row"><small class="text-muted" id='stock_a_modificar'></small></div>
        </div>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close" id="close_stock">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body text-muted">
        <div class="container">
          <div class="row">
            <select class="form-control col-3" id="condicion">
              <option value="agregar">Agregar</option>
              <option value="restar">Restar</option>
            </select>
            <input class="form-control col-2" type="number" value="1" id="stock" step="1"/>
            <button class="col-auto ml-auto btn btn-success btn-sm mt-2" type="button" id="actualizar_stock">Actualizar</button>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <small class="text-muted">Ten en cuenta que puede tardar un momento hasta ver la información actualizada.</small>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).on('click','#edit_stock',function(){
    var items=<?php echo $items_paginacion;?>;
    $.get('cantidad_array.php',{items:items},verificar,'text');
    function verificar(respuesta){$("#stock_a_modificar").empty();$("#stock_a_modificar").append('Se modificarán '+respuesta+' publicaciones.');}
  });
  $(document).on('click','#actualizar_stock',function(){
    $("#close_stock").click();
    $("#loader_now").click();
    var stock=$("#stock").val();
    var condicion=$("#condicion").val();
    var band=1;
    var origin='paused';
    $.get('ajax/ajax_stock.php',{stock:stock,condicion:condicion,band:band,origin:origin},verificar,'text');
    function verificar(respuesta){
      var valores=respuesta.split(',');
      $.each(valores,function(i,resultado){
        var auxiliar=resultado.split('%');
        $('input:checkbox:checked').each(function(){
          var aux=$(this).val();
          if(aux==auxiliar[0]){
            $(this).prop('checked',false);
            $("#stock_real"+auxiliar[0]).empty();
            $("#stock_real"+auxiliar[0]).append(auxiliar[1]);
            if(auxiliar[1]==0){
              $("#stock_real"+auxiliar[0]).removeClass("text-success").addClass("text-danger");
            }else{
              $("#stock_real"+auxiliar[0]).removeClass("text-success").addClass("text-primary");
            }
          }
        });
      });
      const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
      toast({type:'success',title:'¡Fueron modificadas exitosamente!'})
      setTimeout ("$('button#close_loader').click();",500);
    }
  });
</script>
</div>
<?php include '../common/footer.php';?>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.0/dist/sweetalert2.all.min.js"></script>
</div>
</body>
</html>
