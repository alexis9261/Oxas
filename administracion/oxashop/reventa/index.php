<?php
set_time_limit(0);
session_start();
require '../../common/meli.php';require '../../common/conexion.php';require '../../common/take_at.php';
if(isset($_GET['nickname'])){
  $nickname=$_GET['nickname'];
}else if(isset($_GET['publicacion'])){
  $publicacion=$_GET['publicacion'];
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items?ids=$publicacion");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  $result=curl_exec($ch);curl_close($ch);$var=json_decode($result);
  $var=$var[0]->body;
  $seller_id=$var->seller_id;
  $ch=curl_init();
  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/users/$seller_id");
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  $result=curl_exec($ch);curl_close($ch);$var=json_decode($result);
  $nickname=$var->nickname;
}
if(isset($nickname)){
  $nickname=urlencode(str_replace("+"," ",$nickname));
  $ch=curl_init();
  if(isset($_GET['page']) & !empty($_GET['page'])){
    $pagina=$_GET['page'];
    $page_offset=$pagina-1;
    if($page_offset>=21){$offset=0;}else{$offset=$page_offset*50;}
    curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$siteId/search?nickname=$nickname&offset=$offset");
  }else{
    curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$siteId/search?nickname=$nickname");
  }
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  $result=curl_exec($ch);
  curl_reset($ch);
  $var=json_decode($result);
  $id_seller=$var->seller->id;
  $tienda_oficial=$var->seller->eshop;
  $total_publicaciones=$var->paging->total;
  $array_publicaciones=$var->results;
  $array_filtros_disponibles=$var->available_filters;
  foreach($array_filtros_disponibles as $filtro){
    $id_filtro=$filtro->id;
    $nombre_filtro=$filtro->name;
    if($id_filtro=="category"){
      $array_categorias=$filtro->values;
    }elseif($id_filtro=="price"){
      $array_precios=$filtro->values;
    }elseif($id_filtro=="condition"){
      $array_condiciones=$filtro->values;
    }
  }
  curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/'.$id_seller);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  $result=curl_exec($ch);
  curl_close($ch);
  $var=json_decode($result);
  $fecha_inicio_ML=$var->registration_date;
  $meses=array('','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic');
  $año_inicio_ML=substr($fecha_inicio_ML,0,4);
  $mes_inicio_ML=substr($fecha_inicio_ML,5,2);
  $dia_inicio_ML=substr($fecha_inicio_ML,8,2);
  $año_actual=date("Y");
  $años_en_ML=intval($año_actual)-intval($año_inicio_ML);
  $ciudad=$var->address->city;
  $estado=$var->address->state;
  switch($estado){
    case 'VE-A':$state='Distrito Capital';break;case 'VE-C':$state='Apure';break;case 'VE-D':$state='Aragua';break;case 'VE-B':$state='Anzoátegui';
    break;case 'VE-E':$state='Barinas';break;case 'VE-F':$state='Bolívar';break;case "VE-G":$state="Carabobo";break;case "VE-H":$state="Cojedes";
    break;case "VE-I":$state="Falcón";break;case "VE-J":$state="Guárico";break;case "VE-K":$state="Lara";break;case "VE-L":$state="Mérida";
    break;case "VE-M":$state="Miranda";break;case "VE-N":$state="Monagas";break;case "VE-O":$state="Nueva Esparta";break;case "VE-P":
    $state="Portuguesa";break;case "VE-R":$state="Sucre";break;case "VE-S":$state="Táchira";break;case "VE-T":$state="Trujillo";break;
    case "VE-V":$state="Zulia";break;case "VE-W":$state="Dependencias Federales";break;case "VE-X":$state="Vargas";break;case "VE-Y":
    $state="Delta Amacuro";break;case "VE-Z":$state="Amazonas";break;case "VE-U":$state="Yaracuy";break;default:$state=" ";break;
  }
  $reputation_level_id=$var->seller_reputation->level_id;
  $seller_reputation=$var->seller_reputation->power_seller_status;
  $total_ventas=$var->seller_reputation->transactions->total;
  $ventas_concretadas=$var->seller_reputation->transactions->completed;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="shortcut icon" href="../../../img/favicon.ico">
  <title>Oxas - Re-Venta</title>
  <link href="../../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <script src="../../vendor/jquery/jquery.min.js"></script>
  <link href="../../css/sb-admin.min.css" rel="stylesheet">
  <link href="../../css/style.css" rel="stylesheet">
  <link href="../../vendor/datatables/datatables.min.css" rel="stylesheet">
</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">
  <?php include '../../common/navbar.php';?>
  <div class="content-wrapper">
    <div class="container-fluid">
      <form class="container-fluid px-0" action="" method="get">
        <div class="row mb-3">
          <div class="input-group input-group-sm col-sm-8">
            <div class="input-group-prepend"><span class="input-group-text bg-dark text-white" id="inputGroup-sizing-sm">Nombre del Vendedor</span></div>
            <?php if(isset($nickname)){ ?>
              <input type="text" class="form-control" name="nickname" aria-describedby="inputGroup-sizing-sm" value="<?php echo $nickname;?>">
            <?php }else{ ?>
              <input type="text" class="form-control" name="nickname" aria-describedby="inputGroup-sizing-sm">
            <?php } ?>
          </div>
          <div class="input-group input-group-sm col-4">
            <button type="submit" class="btn btn-sm btn-primary px-5">Buscar datos del vendedor</button>
          </div>
        </div>
      </form>
      <form class="container-fluid px-0" action="" method="get">
        <div class="row mb-3">
          <div class="input-group input-group-sm col-sm-8">
            <div class="input-group-prepend"><span class="input-group-text bg-dark text-white" id="inputGroup-sizing-sm">Id de la Publicación</span></div>
            <?php if(isset($publicacion)){ ?>
              <input type="text" class="form-control" name="publicacion" aria-describedby="inputGroup-sizing-sm" value="<?php echo $publicacion;?>">
            <?php }else{ ?>
              <input type="text" class="form-control" name="publicacion" aria-describedby="inputGroup-sizing-sm">
            <?php } ?>
          </div>
          <div class="input-group input-group-sm col-4">
            <button type="submit" class="btn btn-sm btn-primary px-5">Buscar la publiación</button>
          </div>
        </div>
      </form>
      <section class="container-fluid p-3">
        <?php if(isset($id_seller)){ ?>
          <div class="row align-items-center mb-3">
            <div class="col-auto">
              <?php if($seller_reputation=="platinum"){ ?>
                <svg xmlns="http://www.w3.org/2000/svg" width='20px' viewBox="0 0 512 512"><path fill='#38ff3e' d="M223.75 130.75L154.62 15.54A31.997 31.997 0 0 0 127.18 0H16.03C3.08 0-4.5 14.57 2.92 25.18l111.27 158.96c29.72-27.77 67.52-46.83 109.56-53.39zM495.97 0H384.82c-11.24 0-21.66 5.9-27.44 15.54l-69.13 115.21c42.04 6.56 79.84 25.62 109.56 53.38L509.08 25.18C516.5 14.57 508.92 0 495.97 0zM256 160c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm92.52 157.26l-37.93 36.96 8.97 52.22c1.6 9.36-8.26 16.51-16.65 12.09L256 393.88l-46.9 24.65c-8.4 4.45-18.25-2.74-16.65-12.09l8.97-52.22-37.93-36.96c-6.82-6.64-3.05-18.23 6.35-19.59l52.43-7.64 23.43-47.52c2.11-4.28 6.19-6.39 10.28-6.39 4.11 0 8.22 2.14 10.33 6.39l23.43 47.52 52.43 7.64c9.4 1.36 13.17 12.95 6.35 19.59z"/></svg>
                <span class="">Mercado Lider Platinum</span>
              <?php }elseif($seller_reputation=="gold"){ ?>
                <svg xmlns="http://www.w3.org/2000/svg" width='20px' viewBox="0 0 512 512"><path fill='#ffc107' d="M223.75 130.75L154.62 15.54A31.997 31.997 0 0 0 127.18 0H16.03C3.08 0-4.5 14.57 2.92 25.18l111.27 158.96c29.72-27.77 67.52-46.83 109.56-53.39zM495.97 0H384.82c-11.24 0-21.66 5.9-27.44 15.54l-69.13 115.21c42.04 6.56 79.84 25.62 109.56 53.38L509.08 25.18C516.5 14.57 508.92 0 495.97 0zM256 160c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm92.52 157.26l-37.93 36.96 8.97 52.22c1.6 9.36-8.26 16.51-16.65 12.09L256 393.88l-46.9 24.65c-8.4 4.45-18.25-2.74-16.65-12.09l8.97-52.22-37.93-36.96c-6.82-6.64-3.05-18.23 6.35-19.59l52.43-7.64 23.43-47.52c2.11-4.28 6.19-6.39 10.28-6.39 4.11 0 8.22 2.14 10.33 6.39l23.43 47.52 52.43 7.64c9.4 1.36 13.17 12.95 6.35 19.59z"/></svg>
                <span class="">Mercado Lider Gold</span>
              <?php }elseif($seller_reputation=="silver"){ ?>
                <svg xmlns="http://www.w3.org/2000/svg" width='20px' viewBox="0 0 512 512"><path fill='#6c757d' d="M223.75 130.75L154.62 15.54A31.997 31.997 0 0 0 127.18 0H16.03C3.08 0-4.5 14.57 2.92 25.18l111.27 158.96c29.72-27.77 67.52-46.83 109.56-53.39zM495.97 0H384.82c-11.24 0-21.66 5.9-27.44 15.54l-69.13 115.21c42.04 6.56 79.84 25.62 109.56 53.38L509.08 25.18C516.5 14.57 508.92 0 495.97 0zM256 160c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm92.52 157.26l-37.93 36.96 8.97 52.22c1.6 9.36-8.26 16.51-16.65 12.09L256 393.88l-46.9 24.65c-8.4 4.45-18.25-2.74-16.65-12.09l8.97-52.22-37.93-36.96c-6.82-6.64-3.05-18.23 6.35-19.59l52.43-7.64 23.43-47.52c2.11-4.28 6.19-6.39 10.28-6.39 4.11 0 8.22 2.14 10.33 6.39l23.43 47.52 52.43 7.64c9.4 1.36 13.17 12.95 6.35 19.59z"/></svg>
                <small class="">Mercado Lider</small>
              <?php }else{ ?>
                <svg xmlns="http://www.w3.org/2000/svg" width='20px' viewBox="0 0 512 512"><path fill='#000' d="M223.75 130.75L154.62 15.54A31.997 31.997 0 0 0 127.18 0H16.03C3.08 0-4.5 14.57 2.92 25.18l111.27 158.96c29.72-27.77 67.52-46.83 109.56-53.39zM495.97 0H384.82c-11.24 0-21.66 5.9-27.44 15.54l-69.13 115.21c42.04 6.56 79.84 25.62 109.56 53.38L509.08 25.18C516.5 14.57 508.92 0 495.97 0zM256 160c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm92.52 157.26l-37.93 36.96 8.97 52.22c1.6 9.36-8.26 16.51-16.65 12.09L256 393.88l-46.9 24.65c-8.4 4.45-18.25-2.74-16.65-12.09l8.97-52.22-37.93-36.96c-6.82-6.64-3.05-18.23 6.35-19.59l52.43-7.64 23.43-47.52c2.11-4.28 6.19-6.39 10.28-6.39 4.11 0 8.22 2.14 10.33 6.39l23.43 47.52 52.43 7.64c9.4 1.36 13.17 12.95 6.35 19.59z"/></svg>
                <small class="">No es Mercado Lider</small>
              <?php } ?>
            </div>
            <div class='col-auto' title='Reputación en Mercado Libre' data-toggle='tooltip'>
              <?php
              switch($reputation_level_id){
                case '1_red': ?>
                <span class='mal bord' style='background-color: #ff191d;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span>
                <?php break;case '2_orange':?>
                <span class='mal' style='background-color: #ffc6a5;'></span><span class='medio bord' style='background-color: #ff8419;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span>
                <?php break;case '3_yellow': ?>
                <span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med bord' style='background-color: #ffff36;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span>
                <?php break;case '4_light_green': ?>
                <span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien bord' style='background-color: #58ff3f;'></span><span class='exc' style='background-color: #d2ffb0;'></span>
                <?php break;case '5_green': ?>
                <span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc bord' style='background-color: #00ca00;'></span>
                <?php break;default:?>
                <span class='mal' style='background-color: #beccc1;'></span><span class='medio' style='background-color: #beccc1;'></span><span class='med' style='background-color: #beccc1;'></span><span class='bien' style='background-color: #beccc1;'></span><span class='exc' style='background-color: #beccc1;'></span>
                <?php break;} ?>
            </div>
          </div>
          <div class="row mt-2 align-items-center">
            <div class="input-group input-group-sm mb-3 col-sm-2 my-1">
              <div class="input-group-prepend">
                <span class="input-group-text bg-dark text-white" id="inputGroup-sizing-sm">Estado</span>
              </div>
              <input type="text" class="form-control" aria-describedby="inputGroup-sizing-sm" value="<?php echo $state;?>" disabled>
            </div>
            <div class="input-group input-group-sm mb-3 col-sm-3 my-1">
              <div class="input-group-prepend">
                <span class="input-group-text bg-dark text-white" id="inputGroup-sizing-sm">Ciudad</span>
              </div>
              <input type="text" class="form-control" aria-describedby="inputGroup-sizing-sm" value="<?php echo $ciudad;?>" disabled>
            </div>
            <div class="input-group input-group-sm mb-3 col-sm-3 my-1">
              <div class="input-group-prepend">
                <span class="input-group-text bg-dark text-white" id="inputGroup-sizing-sm"><?php echo $años_en_ML;?> años en ML</span>
              </div>
              <input type="text" class="form-control" aria-describedby="inputGroup-sizing-sm" value="<?php echo $dia_inicio_ML." de ".$meses[intval($mes_inicio_ML)]." del ".$año_inicio_ML;?>" disabled>
            </div>
            <div class="input-group input-group-sm mb-3 col-sm-2 my-1">
              <div class="input-group-prepend">
                <span class="input-group-text bg-dark text-white" id="inputGroup-sizing-sm">Total de Ventas</span>
              </div>
              <input type="text" class="form-control" aria-describedby="inputGroup-sizing-sm" value="<?php echo number_format($total_ventas,0,',','.');?>" disabled>
            </div>
            <div class="input-group input-group-sm mb-3 col-sm-2 my-1">
              <div class="input-group-prepend">
                <span class="input-group-text bg-dark text-white" id="inputGroup-sizing-sm" title="Concretadas" data-toggle="tooltip">Ventas Conc.</span>
              </div>
              <input type="text" class="form-control" aria-describedby="inputGroup-sizing-sm" value="<?php echo number_format($ventas_concretadas,0,',','.');?>" disabled>
            </div>
          </div>
          <div class="row">
            <div class="col-12 mb-3 text-muted">
              <span>Este vendedor tiene <strong class="text-success"><?php echo $total_publicaciones;?></strong> publicaiones activas</span>
            </div>
            <?php if($total_publicaciones!=0){ ?>
              <?php foreach($array_categorias as $catgoria){ ?>
              <div class="col-auto mb-2">
                <label class="categories"><?php echo "<b>".$catgoria->name."</b>(".$catgoria->results.")";?><input type="checkbox" value="<?php echo $catgoria->id;?>" name='categorias[]'><span class="checkmark"></span>
                </label>
              </div>
              <?php } ?>
            <?php } ?>
          </div>
          <?php if($total_publicaciones!=0){ ?>
            <div class="row">
            <div class="col-12 mb-2 text-muted">
              <span>Condiciones de las publicaciones</span>
            </div>
            <?php foreach($array_condiciones as $condicion){ ?>
                <div class="col-auto">
                  <label class="categories"><?php echo "<b>".$condicion->name."</b>(".$condicion->results.")";?><input type="checkbox" value="<?php echo $condicion->id;?>" name='condiciones[]'><span class="checkmark"></span>
                  </label>
                </div>
              <?php } ?>
            <div class="col-12 mb-2 text-muted">
              <span>Precios de las publicaciones</span>
            </div>
            <?php foreach($array_precios as $precio){ ?>
              <div class="col-auto mb-2">
                <label class="categories"><?php echo "<b>".$precio->name."</b>(".$precio->results.")";?><input type="checkbox" value="<?php echo $precio->id;?>" name='precios[]'><span class="checkmark"></span>
                </label>
              </div>
            <?php } ?>
          </div>
          <?php } ?>
        <?php } ?>
      </section>
    </div>
    <section class="container-fluid">
      <?php if(isset($id_seller)){ ?>
      <table id="example" class="display" style="width:100%">
        <thead>
          <tr>
            <th>#</th>
            <th></th>
            <th>Titulo</th>
            <th>Categoria</th>
            <th>Precio</th>
            <th>Nivel</th>
            <th title="Condición" data-toggle="tooltip">Cond.</th>
            <th>...</th>
            <th title="Coloca la ganancia esperada en %" data-toggle="tooltip">Ganancia</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $array_publicaciones_originales=array();
          $sql="SELECT CODIGOORIGINAL,GANANCIA FROM publicacion WHERE IDUSUARIO=$id_user";
          $result=$conn->query($sql);
          if($result->num_rows>0){
            while($row=$result->fetch_assoc()){
              $item=$row['CODIGOORIGINAL'];$ganancia=$row['GANANCIA'];array_push($array_publicaciones_originales,$item);
            }
          }
          if(isset($pagina)){$cont=($pagina-1)*50+1;}else{$cont=1;}
          foreach($array_publicaciones as $item){
            $id_item=$item->id;
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items?ids=$id_item&attributes=seller_id,pictures");
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            $result=curl_exec($ch);curl_close($ch);$result=json_decode($result);
            $seller_id=$result[0]->body->seller_id;
            $array_images=$result[0]->body->pictures;
            $title=$item->title;
            $imagen=$item->thumbnail;
            $precio=number_format($item->price,0,',','.');
            $level=$item->listing_type_id;
            $permalink=$item->permalink;
            if($level=='gold_special'){$level='Premium';}elseif($level=='bronze'){$level='Clásico';}else{$level='Gratis';}
            $buying_mode=$item->buying_mode;
            $condicion=$item->condition;
            $categoria=$item->category_id;
            $envios=$item->shipping->free_shipping;
            $ch=curl_init();
            curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/categories/$categoria");
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$var=json_decode($result);
            $raiz_cat=$var->path_from_root;
            $categoria=$raiz_cat[0]->name;
            ?>
            <tr>
              <td class="text-center"><b><?php echo $cont;?></b></td>
              <td class="text-center">
                <a href="#" data-toggle='modal' data-target='.image<?php echo $id_item?>'>
                  <img src="<?php echo $imagen;?>" width="30vw" id="imagen<?php echo $id_item;?>">
                </a>
              </td>
              <td><a href="<?php echo $permalink;?>" target="_blank"><small><?php echo $title;?></small></a></td>
              <td><small><?php echo $categoria;?></small></td>
              <td><strong><small id="precio<?php echo $id_item;?>"><?php echo $precio;?></small></strong></td>
              <td><small><?php echo $level;?></small></td>
              <td class="text-center"><small><?php echo $condicion;?></small></td>
              <td>
                <button class='btn btn-link p-0 px-1' data-toggle='modal' id="descrip<?php echo $id_item?>" data-target='.description<?php echo $id_item?>'>
                  <svg xmlns='http://www.w3.org/2000/svg' title='Descripción' data-toggle='tooltip' width='12px' class='svg-primary' viewBox='0 0 448 512'><path d='M288 44v40c0 8.837-7.163 16-16 16H16c-8.837 0-16-7.163-16-16V44c0-8.837 7.163-16 16-16h256c8.837 0 16 7.163 16 16zM0 172v40c0 8.837 7.163 16 16 16h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16zm16 312h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm256-200H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16h256c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16z'/></svg>
                </button>
                <?php
                if($buying_mode!='classified'){
                if($envios==true){
                ?>
                <svg xmlns="http://www.w3.org/2000/svg" title='Envío Gratis' data-toggle='tooltip' width='14px' viewBox="0 0 640 512"><path fill='#38ff3e' d="M624 352h-16V243.9c0-12.7-5.1-24.9-14.1-33.9L494 110.1c-9-9-21.2-14.1-33.9-14.1H416V48c0-26.5-21.5-48-48-48H48C21.5 0 0 21.5 0 48v320c0 26.5 21.5 48 48 48h16c0 53 43 96 96 96s96-43 96-96h128c0 53 43 96 96 96s96-43 96-96h48c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zM160 464c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm320 0c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm80-208H416V144h44.1l99.9 99.9V256z"/></svg>
                <?php }else{ ?>
                <svg xmlns="http://www.w3.org/2000/svg" title='Sin Envío Gratis' data-toggle='tooltip' class="svg-secondary" width='14px' viewBox="0 0 640 512"><path d="M624 352h-16V243.9c0-12.7-5.1-24.9-14.1-33.9L494 110.1c-9-9-21.2-14.1-33.9-14.1H416V48c0-26.5-21.5-48-48-48H48C21.5 0 0 21.5 0 48v320c0 26.5 21.5 48 48 48h16c0 53 43 96 96 96s96-43 96-96h128c0 53 43 96 96 96s96-43 96-96h48c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zM160 464c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm320 0c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm80-208H416V144h44.1l99.9 99.9V256z"/></svg>
                <?php } } ?>
              </td>
              <td>
                <div class="input-group input-group-sm">
                  <?php if(in_array($id_item,$array_publicaciones_originales)){ ?>
                    <input type="number" class="form-control" aria-describedby="inputGroup-sizing-sm" value="<?php echo $ganancia;?>" disabled>
                  <?php }else{ ?>
                    <input type="number" class="form-control" aria-describedby="inputGroup-sizing-sm" id="ganancia<?php echo $id_item;?>">
                  <?php } ?>
                </div>
              </td>
              <td>
                <?php if(in_array($id_item,$array_publicaciones_originales)){ ?>
                  <button class="btn btn-warning btn-sm px-2" type="submit" disabled title="Este item ya fue republicado" data-toggle="tooltip">Publicado</button>
                <?php }else{ ?>
                  <button class="btn btn-success btn-sm px-2" type="submit" id="publicar<?php echo $id_item;?>">Publicar</button>
                <?php } ?>
              </td>
            </tr>
            <!-- Ganacia Esperada -->
            <script>
            var ganancia = document.querySelector('#ganancia<?php echo $id_item;?>');
            ganancia.addEventListener('keypress',function(e){
              //no permito que escriban letras
              if(!soloNumeros(event)){
                e.preventDefault();
              }
            });
            ganancia.addEventListener('blur',function(e){
              var ganancia_porcent=document.querySelector('#ganancia<?php echo $id_item;?>').value;
              if(ganancia_porcent.length>0){
                //Obtengo el precio real
                var precio_real=document.querySelector('#precio<?php echo $id_item;?>').innerHTML;
                //debo cambiar los puntos por nada
                precio_real=reemplazarCadena(".","",precio_real);
                precio_real=parseInt(precio_real);
                var ganancia_real=precio_real*(ganancia_porcent/100);
                var aux=new Intl.NumberFormat().format(ganancia_real);
                const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:5000});
                toast({type:'info',title:"Obtendrás una ganancia de: \n "+aux+" Bs."})
              }
            });
            function reemplazarCadena(cadenaVieja,cadenaNueva,cadenaCompleta){
              for (var i = 0; i < cadenaCompleta.length; i++) {
                if (cadenaCompleta.substring(i, i + cadenaVieja.length) == cadenaVieja) {
                  cadenaCompleta= cadenaCompleta.substring(0, i) + cadenaNueva + cadenaCompleta.substring(i + cadenaVieja.length, cadenaCompleta.length);
                }
              }
              return cadenaCompleta;
            }
              //Solo permite introducir numeros.
              function soloNumeros(e){
                var key = e.charCode;
                console.log(key);
                return key >= 48 && key <= 57;
              }
            </script>
            <!-- Envio de datos -->
            <script>
              $('#publicar<?php echo $id_item;?>').on('click',function(){
                  checkboxes=document.getElementsByName('imagenes<?php echo $id_item;?>');
                    var aray_imagenes=[];
                    var k=0;
                    for(var i=0,n=checkboxes.length;i<n;i++){
                      if(checkboxes[i].checked){
                        aray_imagenes.push(checkboxes[i].value);
                        ++k;
                      }
                    }
                    var string_imagenes=aray_imagenes.join();
                    var id_item="<?php echo $id_item;?>";
                    var ganancia=$("#ganancia<?php echo $id_item;?>").val();
                    if(k==0){
                      const toast=swal.mixin({toast:true,position:'top',showConfirmButton:false,timer:4000});
                      toast({type:'info',title:"¡Te falta seleccionar la(s) imagen(es) de la publicación!"});
                    }else if(ganancia==0){
                      const toast=swal.mixin({toast:true,position:'top',showConfirmButton:false,timer:4000});
                      toast({type:'info',title:"Debes colocar un valor de ganancia"})
                    }else{
                      $("#loader_now").click();
                      $.get('republicar.php',{id_item:id_item,ganancia:ganancia,string_imagenes:string_imagenes},verificar,'text');
                      function verificar(respuesta){
                        console.log(respuesta);
                        if(respuesta==1){
                          const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:4500});
                          toast({type:'success',title:'¡Se creo exitosamente la publicación!'});
                          $("#publicar<?php echo $id_item;?>").attr("disabled",true);
                          $("#ganancia<?php echo $id_item;?>").attr("disabled",true);
                        }else if(respuesta==2){
                          const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:4000});
                          toast({type:'info',title:"¡Se creo exitosamente la publicación! \n ¡PERO! No se pudo afiliar la publicación original.\n Afiliala en Publicaciones!"});
                          $("#publicar<?php echo $id_item;?>").attr("disabled",true);
                          $("#ganancia<?php echo $id_item;?>").attr("disabled",true);
                        }else if(respuesta==3){
                          const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:4000});
                          toast({type:'error',title:"No se pudo crear la publicación.\n Comunicate con el Dpto. de Desarrollo"})
                        }else if(respuesta==4){
                          const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:4000});
                          toast({type:'error',title:"Hubo un error con la imagen.\n Intentalo de nuevo"})
                        }else{
                          const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:4000});
                          toast({type:'error',title:"Hubo un error\n Intentalo de nuevo"})
                        }
                        setTimeout ("$('button#close_loader').click();",500);
                      }
                    }
                });
            </script>
            <!-- Descripcion -->
            <div class="modal fade description<?php echo $id_item?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content container">
                  <div class="modal-header">
                    <h5 class="modal-title"><?php echo $title;?></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                  </div>
                  <div class="modal-body text-muted"><div id="text<?php echo $id_item?>"></div></div>
                </div>
              </div>
            </div>
            <!-- Descripcion de la publicacion -->
            <script>
              $("#descrip<?php echo $id_item?>").click(function(){
                var item="<?php echo $id_item?>", url='https://api.mercadolibre.com/items/'+item+'/description';
                $.getJSON(url,function(datos){
                  var description=datos.plain_text, auxiliar=description.split('\n').length+3;
                  description= "<textarea class='textarea_description' rows='"+auxiliar+"' disabled id='textarea_description<?php echo $id_item?>'>"+description+"</textarea>";
                  document.getElementById("text<?php echo $id_item?>").innerHTML=description;
                });
              });
            </script>
            <!-- Imagenes -->
            <div class="modal fade image<?php echo $id_item?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content container">
                  <div class="modal-header">
                    <h5 class="modal-title"><?php echo $title;?></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                  </div>
                  <div class="modal-body container text-muted">
                    <div class="row">
                      <?php foreach ($array_images as $imagen): ?>
                        <div class="col-auto">
                          <img class='mr-2 mb-2 col-auto images-publicaciones img-fluid img-thumbnail' src='<?php echo $imagen->url;?>'>
                        </div>
                        <label class="categories">
                          <input type="checkbox" value="<?php echo $imagen->id;?>" name='imagenes<?php echo $id_item;?>'>
                          <span class="checkmark"></span>
                        </label>
                      <?php endforeach; ?>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-success px-4" type="button" data-dismiss="modal">Listo</button>
                  </div>
                </div>
              </div>
            </div>
            <?php
            ++$cont;
          }
          ?>
        </tbody>
      </table>
      <?php } ?>
    </section>
    <section>
      <div class="container mt-2">
        <div class="row justify-content-center">
          <?php if($total_publicaciones>50){$NroPag=ceil($total_publicaciones/50); ?>
            <nav aria-label="Page navigation example">
              <ul class="pagination">
                <?php
                if(isset($_GET['page']) & !empty($_GET['page'])){
                  if($pagina>6 && $NroPag>6){
                    ?>
                    <li class="page-item">
                      <a class="page-link" href="index.php?nickname=<?php echo $nickname;?>&page=<?php echo ($pagina-6);?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                      </a>
                    </li>
                    <?php
                  }}else{$pagina=1;}
                  if($pagina>5){
                    for($i=($pagina-5);$i<($pagina+5);$i++){
                      if(($i-1)==$NroPag){break;}else{if($i==$pagina){
                        ?>
                        <li class="page-item active"><a class="page-link" href="index.php?nickname=<?php echo $nickname;?>&page=<?php echo $i;?>"><?php echo $i;?></a></li>
                      <?php }else{ ?>
                        <li class="page-item"><a class="page-link" href="index.php?nickname=<?php echo $nickname;?>&page=<?php echo $i;?>"><?php echo $i;?></a></li>
                      <?php }}}}else{for($i=1;$i<11;$i++){if(($i-1)==$NroPag){break;}else{if($i==$pagina){ ?>
                        <li class="page-item active"><a class="page-link" href="index.php?nickname=<?php echo $nickname;?>&page=<?php echo $i;?>"><?php echo $i;?></a></li>
                      <?php }else{?>
                        <li class="page-item"><a class="page-link" href="index.php?nickname=<?php echo $nickname;?>&page=<?php echo $i;?>"><?php echo $i;?></a></li>
                      <?php }}}} ?>
                      <?php if($NroPag>10 & $pagina<($NroPag-4)){?>
                        <li class="page-item">
                          <a class="page-link" href="index.php?nickname=<?php echo $nickname;?>&page=<?php echo (5+$pagina);?>" aria-label="Next">
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
    </section>
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
  <script>
    window.onload=function(){
      $("#sidenavToggler").click();
      $("#example_paginate").hide();
      $("#example_info").hide();
      $('#example').addClass('nowrap').dataTable({
        responsive:true,
        pageLength:50,
        columnDefs:[{
          "targets":[-1],
          "orderable":false
        }]
      });
    }
  </script>
  <?php  include '../../common/footer.php';?>
  <script src="../../vendor/jquery/jquery.min.js"></script>
  <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="../../js/sb-admin.min.js"></script>
  <script src="../../vendor/datatables/datatables.min.js"></script>
  <script src='https://cdn.jsdelivr.net/npm/sweetalert2@7.29.0/dist/sweetalert2.all.min.js'></script>
</body>
</html>
