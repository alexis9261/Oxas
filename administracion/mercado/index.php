<?php
set_time_limit(0);session_start();
require '../common/meli.php';require '../common/conexion.php';require '../common/take_at.php'; require '../common/account-off.php';require '../common/callback.php';
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
<title>Oxas - Estudio de Mercado</title>
<link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
<link href="../css/sb-admin.min.css" rel="stylesheet"/>
<link href="../css/style.css" rel="stylesheet"/>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/chartjs/Chart.js"></script>
</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">
<?php
$ch=curl_init();
if(isset($_GET['search']) & !empty($_GET['search'])){
$query=urlencode($_GET['search']);
if(isset($_GET['page']) & !empty($_GET['page'])){
$page_offset=$_GET['page']-1;
if($page_offset>=21){$offset=0;}else{$offset=$page_offset*50;}
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$siteId/search?q=$query&access_token=$AccessToken&offset=$offset");
}else{curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$siteId/search?q=$query&access_token=$AccessToken");}
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);
$items=$consulta->results;
$total_items=count($items);
$items_paginacion=$consulta->paging->total;//total de publicaciones en la busqueda
}else{$total_items=0;}
include '../common/navbar.php';?>
<script>
Chart.defaults.global.defaultFontFamily='-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor='#292b2c';
</script>
<div class="content-wrapper p-0">
<div class="container-fluid background_activas py-2">
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
<div class="row align-items-center">
<input class="ml-4 col-6 search" type="text" placeholder=" Buscar..." name='search'/>
<div class="col-auto">
<button type="submit" class="button_search cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" class="svg-secondary" width="15px" viewBox="0 0 512 512"><path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"/></svg></button>
</form>
</div>
</div>
</div>
<div class='container-fluid' id='publicaciones_activas'>
<?php
if($total_items==0){
$items_paginacion=0;
?>
<div class='container mt-4'><strong class="mb-5">Realiza una busqueda de las publicaciones que deseas estudiar.</strong><br></br></br></br></div>
<?php
}else{
$hoy=getdate();$year=$hoy['year'];
$items_id=array();
foreach($items as $value){$item=$value->id;array_push($items_id,$item);}
$contador=0;$band=0;
while($contador!=$total_items){
$array_publicaciones_veinte=array();
foreach($items_id as $value){
++$contador;array_push($array_publicaciones_veinte,$value);
if(($contador==20 || $contador==$total_items) && $band==0){
$band=1;$str_public=implode(',',$array_publicaciones_veinte);
$array_publicaciones_veinte=array();break;
}elseif(($contador==40 || $contador==$total_items) && $band==1){
$band=2;$str_public=implode(',',$array_publicaciones_veinte);
$array_publicaciones_veinte=array();break;
}elseif(($contador==50 || $contador==$total_items) && $band==2){
$band=3;$str_public=implode(',',$array_publicaciones_veinte);
$array_publicaciones_veinte=array();break;
}
}
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items?ids=".$str_public."&attributes=id,available_quantity,sold_quantity,start_time,pictures,buying_mode&access_token=".$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$result=json_decode($result);
$ids=array();$cantidades=array();$vendidos_p=array();$fechas=array();$imagenes_p=array();$metodos_venta=array();
foreach($result as $valor){
$id=$valor->body->id;array_push($ids,$id);
$cantidad=$valor->body->available_quantity;array_push($cantidades,$cantidad);
$vendidos=$valor->body->sold_quantity;array_push($vendidos_p,$vendidos);
$fecha_inicio=$valor->body->start_time;array_push($fechas,$fecha_inicio);
$imagenes=$valor->body->pictures;array_push($imagenes_p,$imagenes);
$metodo_venta=$valor->body->buying_mode;array_push($metodos_venta,$metodo_venta);
}
}
foreach($items as $valor){
$id=$valor->id;$seller=$valor->seller->id;
$power_status=$valor->seller->power_seller_status;$title=$valor->title;
$precio=$valor->price;$categoria=$valor->category_id;
$nivel_public=$valor->listing_type_id;$thumbnail=$valor->thumbnail;
$permalink=$valor->permalink;$official_store=$valor->official_store_id;
$estado=$valor->address->state_name;$ciudad=$valor->address->city_name;
$envios=$valor->shipping->free_shipping;$clave=array_search($id,$ids);
$quantity=$cantidades[$clave];$vendidos=$vendidos_p[$clave];
$fecha_inicial_publicacion=$fechas[$clave];$imagenes=$imagenes_p[$clave];
$modo_venta=$metodos_venta[$clave];
$meses=array('',"Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
$mes=substr($fecha_inicial_publicacion,5,2);
if($mes<10){$mes=$meses[substr($fecha_inicial_publicacion,6,1)];}else{$mes=$meses[substr($fecha_inicial_publicacion,5,2)];}
if($id_user==$seller){ ?><div class='row border-botton pl-2 align-items-center m-0 py-1 self_public'>
<?php }else{ ?><div class='row border-botton pl-2 align-items-center m-0 py-1'><?php } ?>
<div class='col-7 mr-auto m-0 p-0'>
<span class="cursor-pointer" data-toggle='modal' data-target='.images-<?php echo $id?>-modal-lg'>
<img class='img-fluid img-thumbnail' src='<?php echo $thumbnail;?>' width='40px' height='40px' title='Publicada desde el <?php echo substr($fecha_inicial_publicacion,8,2)." de $mes del ".substr($fecha_inicial_publicacion,0,4);?>' data-toggle='tooltip'>
</span>
<?php if($modo_venta!='classified'): ?>(<b class='text-success' title='Stock' data-toggle='tooltip'><?php echo $quantity;?></b>)<?php endif; ?>
<a class='text-primary' href="<?php echo $permalink;?>" target="_blank"><?php echo $title;?></a>
</div>
<span class="col-2 mr-auto text-muted">
<?php if ($modo_venta!='classified'): ?><span class="text-success" title='Vendidos' data-toggle='tooltip'>(<?php echo $vendidos;?>)</span><?php endif;?>
<?php if ($modo_venta=='classified'){ ?>
<?php if ($precio==null){ ?><span title='Precio' data-toggle='tooltip'>Precio a convenir</span>
<?php }else{ ?><span title='Precio' data-toggle='tooltip'><?php $decimales=number_format($precio,2,',','.'); echo "$decimales";?> Bs.</span><?php } ?>
<?php }else{ ?><span title='Precio' data-toggle='tooltip'><?php $precio; $decimales=number_format($precio,2,',','.'); echo "$decimales";?> Bs.</span><?php } ?>
</span>
<div class='col-sm-3 ml-auto'>
<button class="btn btn-link p-0 px-1" id='user<?php echo $id;?>' data-toggle='modal' data-target='.user<?php echo $id?>'>
<svg xmlns="http://www.w3.org/2000/svg" title='Usuario' data-toggle='tooltip' width='14px' class='svg-primary' viewBox="0 0 640 512"><path d="M610.5 373.3c2.6-14.1 2.6-28.5 0-42.6l25.8-14.9c3-1.7 4.3-5.2 3.3-8.5-6.7-21.6-18.2-41.2-33.2-57.4-2.3-2.5-6-3.1-9-1.4l-25.8 14.9c-10.9-9.3-23.4-16.5-36.9-21.3v-29.8c0-3.4-2.4-6.4-5.7-7.1-22.3-5-45-4.8-66.2 0-3.3.7-5.7 3.7-5.7 7.1v29.8c-13.5 4.8-26 12-36.9 21.3l-25.8-14.9c-2.9-1.7-6.7-1.1-9 1.4-15 16.2-26.5 35.8-33.2 57.4-1 3.3.4 6.8 3.3 8.5l25.8 14.9c-2.6 14.1-2.6 28.5 0 42.6l-25.8 14.9c-3 1.7-4.3 5.2-3.3 8.5 6.7 21.6 18.2 41.1 33.2 57.4 2.3 2.5 6 3.1 9 1.4l25.8-14.9c10.9 9.3 23.4 16.5 36.9 21.3v29.8c0 3.4 2.4 6.4 5.7 7.1 22.3 5 45 4.8 66.2 0 3.3-.7 5.7-3.7 5.7-7.1v-29.8c13.5-4.8 26-12 36.9-21.3l25.8 14.9c2.9 1.7 6.7 1.1 9-1.4 15-16.2 26.5-35.8 33.2-57.4 1-3.3-.4-6.8-3.3-8.5l-25.8-14.9zM496 400.5c-26.8 0-48.5-21.8-48.5-48.5s21.8-48.5 48.5-48.5 48.5 21.8 48.5 48.5-21.7 48.5-48.5 48.5zM224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm201.2 226.5c-2.3-1.2-4.6-2.6-6.8-3.9l-7.9 4.6c-6 3.4-12.8 5.3-19.6 5.3-10.9 0-21.4-4.6-28.9-12.6-18.3-19.8-32.3-43.9-40.2-69.6-5.5-17.7 1.9-36.4 17.9-45.7l7.9-4.6c-.1-2.6-.1-5.2 0-7.8l-7.9-4.6c-16-9.2-23.4-28-17.9-45.7.9-2.9 2.2-5.8 3.2-8.7-3.8-.3-7.5-1.2-11.4-1.2h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c10.1 0 19.5-3.2 27.2-8.5-1.2-3.8-2-7.7-2-11.8v-9.2z"/></svg>
</button>
<button class='btn btn-link p-0 px-1' id='but<?php echo $id?>' data-toggle='modal' data-target='.description<?php echo $id?>'>
<svg xmlns='http://www.w3.org/2000/svg' title='Descripción' data-toggle='tooltip' width='12px' class='svg-primary' viewBox='0 0 448 512'><path d='M288 44v40c0 8.837-7.163 16-16 16H16c-8.837 0-16-7.163-16-16V44c0-8.837 7.163-16 16-16h256c8.837 0 16 7.163 16 16zM0 172v40c0 8.837 7.163 16 16 16h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16zm16 312h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm256-200H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16h256c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16z'/></svg>
</button>
<button class='btn btn-link p-0 px-1' id='estadisticas<?php echo $id?>' data-toggle='modal' data-target='.modal-estadist-<?php echo $id?>'>
<svg xmlns="http://www.w3.org/2000/svg" title='Estadísticas' data-toggle='tooltip' width='12px' class='svg-primary' viewBox="0 0 512 512"><path d="M332.8 320h38.4c6.4 0 12.8-6.4 12.8-12.8V172.8c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v134.4c0 6.4 6.4 12.8 12.8 12.8zm96 0h38.4c6.4 0 12.8-6.4 12.8-12.8V76.8c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v230.4c0 6.4 6.4 12.8 12.8 12.8zm-288 0h38.4c6.4 0 12.8-6.4 12.8-12.8v-70.4c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v70.4c0 6.4 6.4 12.8 12.8 12.8zm96 0h38.4c6.4 0 12.8-6.4 12.8-12.8V108.8c0-6.4-6.4-12.8-12.8-12.8h-38.4c-6.4 0-12.8 6.4-12.8 12.8v198.4c0 6.4 6.4 12.8 12.8 12.8zM496 384H64V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v336c0 17.67 14.33 32 32 32h464c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"/></svg>
</button>
<span class="p-0 px-1">
<?php
if($modo_venta=='classified'){
switch($nivel_public){
case 'gold_premium':$tooltip='Publicación Premium';$color='svg-success';break;
case 'gold':$tooltip='Publicación Oro';$color='svg-warning';break;
default: $tooltip='Sin Información.';$color='svg-dark' ;break;
}
}else{
switch($nivel_public){
case 'gold_special':$tooltip='Publicación Premium';$color='svg-warning';break;
case 'bronze':$tooltip='Publicación Clásica';$color='svg-secondary';break;
case 'free':$tooltip='Publicación Gratuita';$color="svg-danger";break;
default: $tooltip='Sin Información.';$color='svg-dark';break;
}
}
?>
<svg xmlns='http://www.w3.org/2000/svg' width='12px' title='<?php echo $tooltip;?>' data-toggle='tooltip' class='<?php echo $color;?>' viewBox='0 0 384 512'><path d='M97.12 362.63c-8.69-8.69-4.16-6.24-25.12-11.85-9.51-2.55-17.87-7.45-25.43-13.32L1.2 448.7c-4.39 10.77 3.81 22.47 15.43 22.03l52.69-2.01L105.56 507c8 8.44 22.04 5.81 26.43-4.96l52.05-127.62c-10.84 6.04-22.87 9.58-35.31 9.58-19.5 0-37.82-7.59-51.61-21.37zM382.8 448.7l-45.37-111.24c-7.56 5.88-15.92 10.77-25.43 13.32-21.07 5.64-16.45 3.18-25.12 11.85-13.79 13.78-32.12 21.37-51.62 21.37-12.44 0-24.47-3.55-35.31-9.58L252 502.04c4.39 10.77 18.44 13.4 26.43 4.96l36.25-38.28 52.69 2.01c11.62.44 19.82-11.27 15.43-22.03zM263 340c15.28-15.55 17.03-14.21 38.79-20.14 13.89-3.79 24.75-14.84 28.47-28.98 7.48-28.4 5.54-24.97 25.95-45.75 10.17-10.35 14.14-25.44 10.42-39.58-7.47-28.38-7.48-24.42 0-52.83 3.72-14.14-.25-29.23-10.42-39.58-20.41-20.78-18.47-17.36-25.95-45.75-3.72-14.14-14.58-25.19-28.47-28.98-27.88-7.61-24.52-5.62-44.95-26.41-10.17-10.35-25-14.4-38.89-10.61-27.87 7.6-23.98 7.61-51.9 0-13.89-3.79-28.72.25-38.89 10.61-20.41 20.78-17.05 18.8-44.94 26.41-13.89 3.79-24.75 14.84-28.47 28.98-7.47 28.39-5.54 24.97-25.95 45.75-10.17 10.35-14.15 25.44-10.42 39.58 7.47 28.36 7.48 24.4 0 52.82-3.72 14.14.25 29.23 10.42 39.59 20.41 20.78 18.47 17.35 25.95 45.75 3.72 14.14 14.58 25.19 28.47 28.98C104.6 325.96 106.27 325 121 340c13.23 13.47 33.84 15.88 49.74 5.82a39.676 39.676 0 0 1 42.53 0c15.89 10.06 36.5 7.65 49.73-5.82zM97.66 175.96c0-53.03 42.24-96.02 94.34-96.02s94.34 42.99 94.34 96.02-42.24 96.02-94.34 96.02-94.34-42.99-94.34-96.02z'/></svg>
</span>
<span class="p-0 px-1" data-toggle="modal" data-target=".mercadolider<?php echo $id;?>" id="mercadolider<?php echo $id;?>">
<?php
switch($power_status){case 'silver':$estatus='MercadoLíder';$fill='#6c757d';break;
case 'gold':$estatus='MercadoLíder Gold';$fill='#ffc107';break;
case 'platinum':$estatus='MercadoLíder Platinum';$fill='#38ff3e';break;
default:$estatus='No es MercadoLíder';$fill='#000';break;
}
?>
<svg class="cursor-pointer" xmlns="http://www.w3.org/2000/svg" width='14px' title='<?php echo $estatus;?>' data-toggle='tooltip' viewBox="0 0 512 512"><path fill='<?php echo $fill;?>' d="M223.75 130.75L154.62 15.54A31.997 31.997 0 0 0 127.18 0H16.03C3.08 0-4.5 14.57 2.92 25.18l111.27 158.96c29.72-27.77 67.52-46.83 109.56-53.39zM495.97 0H384.82c-11.24 0-21.66 5.9-27.44 15.54l-69.13 115.21c42.04 6.56 79.84 25.62 109.56 53.38L509.08 25.18C516.5 14.57 508.92 0 495.97 0zM256 160c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm92.52 157.26l-37.93 36.96 8.97 52.22c1.6 9.36-8.26 16.51-16.65 12.09L256 393.88l-46.9 24.65c-8.4 4.45-18.25-2.74-16.65-12.09l8.97-52.22-37.93-36.96c-6.82-6.64-3.05-18.23 6.35-19.59l52.43-7.64 23.43-47.52c2.11-4.28 6.19-6.39 10.28-6.39 4.11 0 8.22 2.14 10.33 6.39l23.43 47.52 52.43 7.64c9.4 1.36 13.17 12.95 6.35 19.59z"/></svg>
</span>
<?php
if($modo_venta!='classified'){
if($official_store!=null){
?>
<button class="btn btn-link p-0 px-1" id="to<?php echo $id?>" data-toggle="modal" data-target=".tienda_off<?php echo $id?>">
<svg xmlns="http://www.w3.org/2000/svg" title="Tienda Oficial" data-toggle="tooltip" width="16px" viewBox="0 0 616 512"><path fill='#38ff3e' d="M602 118.6L537.1 15C531.3 5.7 521 0 510 0H106C95 0 84.7 5.7 78.9 15L14 118.6c-33.5 53.5-3.8 127.9 58.8 136.4 4.5.6 9.1.9 13.7.9 29.6 0 55.8-13 73.8-33.1 18 20.1 44.3 33.1 73.8 33.1 29.6 0 55.8-13 73.8-33.1 18 20.1 44.3 33.1 73.8 33.1 29.6 0 55.8-13 73.8-33.1 18.1 20.1 44.3 33.1 73.8 33.1 4.7 0 9.2-.3 13.7-.9 62.8-8.4 92.6-82.8 59-136.4zM529.5 288c-10 0-19.9-1.5-29.5-3.8V384H116v-99.8c-9.6 2.2-19.5 3.8-29.5 3.8-6 0-12.1-.4-18-1.2-5.6-.8-11.1-2.1-16.4-3.6V480c0 17.7 14.3 32 32 32h448c17.7 0 32-14.3 32-32V283.2c-5.4 1.6-10.8 2.9-16.4 3.6-6.1.8-12.1 1.2-18.2 1.2z"/></svg>
</button>
<?php }else{ ?>
<span class="p-0 px-1">
<svg xmlns="http://www.w3.org/2000/svg" title="No es tienda Oficial" data-toggle="tooltip" width="16px" class="svg-secondary" viewBox="0 0 616 512"><path d="M602 118.6L537.1 15C531.3 5.7 521 0 510 0H106C95 0 84.7 5.7 78.9 15L14 118.6c-33.5 53.5-3.8 127.9 58.8 136.4 4.5.6 9.1.9 13.7.9 29.6 0 55.8-13 73.8-33.1 18 20.1 44.3 33.1 73.8 33.1 29.6 0 55.8-13 73.8-33.1 18 20.1 44.3 33.1 73.8 33.1 29.6 0 55.8-13 73.8-33.1 18.1 20.1 44.3 33.1 73.8 33.1 4.7 0 9.2-.3 13.7-.9 62.8-8.4 92.6-82.8 59-136.4zM529.5 288c-10 0-19.9-1.5-29.5-3.8V384H116v-99.8c-9.6 2.2-19.5 3.8-29.5 3.8-6 0-12.1-.4-18-1.2-5.6-.8-11.1-2.1-16.4-3.6V480c0 17.7 14.3 32 32 32h448c17.7 0 32-14.3 32-32V283.2c-5.4 1.6-10.8 2.9-16.4 3.6-6.1.8-12.1 1.2-18.2 1.2z"/></svg>
</span>
<?php
} }
if($modo_venta!='classified'){
if($envios==true){
?>
<span class="p-0 px-1">
<svg xmlns="http://www.w3.org/2000/svg" title='Envío Gratis' data-toggle='tooltip' width='14px' viewBox="0 0 640 512"><path fill='#38ff3e' d="M624 352h-16V243.9c0-12.7-5.1-24.9-14.1-33.9L494 110.1c-9-9-21.2-14.1-33.9-14.1H416V48c0-26.5-21.5-48-48-48H48C21.5 0 0 21.5 0 48v320c0 26.5 21.5 48 48 48h16c0 53 43 96 96 96s96-43 96-96h128c0 53 43 96 96 96s96-43 96-96h48c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zM160 464c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm320 0c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm80-208H416V144h44.1l99.9 99.9V256z"/></svg>
</span>
<?php }else{ ?>
<span class="p-0 px-1">
<svg xmlns="http://www.w3.org/2000/svg" title='Sin Envío Gratis' data-toggle='tooltip' class="svg-secondary" width='14px' viewBox="0 0 640 512"><path d="M624 352h-16V243.9c0-12.7-5.1-24.9-14.1-33.9L494 110.1c-9-9-21.2-14.1-33.9-14.1H416V48c0-26.5-21.5-48-48-48H48C21.5 0 0 21.5 0 48v320c0 26.5 21.5 48 48 48h16c0 53 43 96 96 96s96-43 96-96h128c0 53 43 96 96 96s96-43 96-96h48c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zM160 464c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm320 0c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm80-208H416V144h44.1l99.9 99.9V256z"/></svg>
</span>
<?php } } ?>
</div>
</div>
<!-- Vendedor -->
<div class="modal fade user<?php echo $id;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<div id="title_head<?php echo $id;?>"></div>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div class="container-fluid">
<div class='row'>
<div class='col-auto'><b>Dirección:</b></div>
<div class='col-auto ml-auto'>
<span class='text-muted'>
<?php if(isset($estado)){ ?>
Estado <?php echo $estado;?>, <?php echo $ciudad;?>
<?php }else{ ?>
Sin Información.
<?php } ?>
</span>
</div>
</div>
<div class='row' id='contenido<?php echo $id;?>'></div>
<hr>
<div class="row mb-2">
<div class="container">
<h6>Datos como vendedor</h6>
<div class="row justify-content-center">Reputación</div>
<div class="row justify-content-center" id="reput<?php echo $id;?>"></div>
<div class="row justify-content-center">Transacciones</div>
<div class="row justify-content-center" id="trans<?php echo $id;?>"></div>
<div class="row justify-content-center">Calificaciones</div>
<div class="row justify-content-center" id="calif<?php echo $id;?>"></div>
<hr>
<div class="row">
<div class="col-auto ml-auto" data-toggle="tooltip" title="Como Vendedor">
<a class="btn btn-link" href="javascript:void(0)" id="ant_pub<?php echo $id;?>" data-toggle="modal" data-target=".public-<?php echo $id;?>">Ver Publicaciones del Cliente</a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<script>
//Nickname
$("#user<?php echo $id;?>").click(function(){
$("#title_head<?php echo $id;?>").empty();$("#contenido<?php echo $id;?>").empty();
$("#reput<?php echo $id;?>").empty();$("#trans<?php echo $id;?>").empty();
$("#calif<?php echo $id;?>").empty();$('#titulo2<?php echo $id;?>').empty();
var user=<?php echo $seller;?>,url='https://api.mercadolibre.com/users/'+user;
$("#contenido<?php echo $id;?>").append("");
$.getJSON(url,function(datos){
$("#title_head<?php echo $id;?>").append("<h6 class='modal-title'><a href='"+datos.permalink+"' target='_blank'>"+datos.nickname+"</a></h6>");
$('#titulo2<?php echo $id;?>').append(datos.nickname);
$("#titulo2<?php echo $id;?>").addClass(datos.nickname);
var fecha_registro=datos.registration_date.substr(0,10);
$("#contenido<?php echo $id;?>").append("<div class='col-auto'><b>Usuario de Mercado Libre desde:</b></div><div class='col-auto ml-auto'><span class='text-muted'> "+fecha_registro+".</span></div>");
switch(datos.seller_reputation.level_id){
case '1_red':
var nivel="<div class='container mb-2'><div class='row justify-content-center'><div class='col-auto'><span class='mal bord' style='background-color: #ff191d;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span></div></div></div>";
break;case '2_orange':
var nivel="<div class='container mb-2'><div class='row justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #ffc6a5;'></span><span class='medio bord' style='background-color: #ff8419;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span></div></div></div>";
break;case '3_yellow':
var nivel="<div class='container mb-2'><div class='row  justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med bord' style='background-color: #ffff36;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span></div></div></div>";
break;case '4_light_green':
var nivel="<div class='container mb-2'><div class='row  justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien bord' style='background-color: #58ff3f;'></span><span class='exc' style='background-color: #d2ffb0;'></span></div></div></div>";
break;case '5_green':
var nivel="<div class='container mb-2'><div class='row  justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc bord' style='background-color: #00ca00;'></span></div></div></div>";
break;default:
var nivel="<div class='container mb-2'><div class='row  justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #beccc1;'></span><span class='medio' style='background-color: #beccc1;'></span><span class='med' style='background-color: #beccc1;'></span><span class='bien' style='background-color: #beccc1;'></span><span class='exc' style='background-color: #beccc1;'></span></div></div></div>";
break;}
$("#reput<?php echo $id;?>").append(nivel);
if(datos.seller_reputation.transactions.total==0){
$("#trans<?php echo $id;?>").append("<div><b class='mb-2'>Sin Transacciones.</b></div>");
$("#calif<?php echo $id;?>").append("<div><b class='mb-2'>Sin Calificaciones.</b></div>");
}else{
var positivo=datos.seller_reputation.transactions.ratings.positive*100;
var neutral=datos.seller_reputation.transactions.ratings.neutral*100;
var negativo=datos.seller_reputation.transactions.ratings.negative*100;
$("#trans<?php echo $id;?>").append("<div class='container mb-2'><div class='row  justify-content-center'><b>"+datos.seller_reputation.transactions.total+"(<span class='text-success' title='Completadas' data-toggle='tooltip'>"+datos.seller_reputation.transactions.completed+"</span>)</b></div></div>");
$("#calif<?php echo $id;?>").append("<div class='container'><div class='progress'><div class='progress-bar bg-success' role='progressbar' style='width: "+positivo+"%' aria-valuenow='"+positivo+"' aria-valuemin='0' aria-valuemax='100'>"+positivo+"%</div><div class='progress-bar' role='progressbar' style='width:"+neutral+"%' aria-valuenow='"+neutral+"' aria-valuemin='0' aria-valuemax='100'>"+neutral+"%</div><div class='progress-bar bg-danger' role='progressbar' style='width: "+negativo+"%' aria-valuenow='"+negativo+"' aria-valuemin='0' aria-valuemax='100'>"+negativo+"%</div></div></div>");
}
});
});
</script>
<!-- Publicaciones Cliente -->
<div class="modal fade public-<?php echo $id;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel2" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content container">
<div class="modal-header">
<div class="container">
<div class="row">
<div class="col-11"><div class="row font-weight-bold"><div id="titulo2<?php echo $id;?>"></div></div></div>
<button class="col-1 close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
</div>
</div>
<div class="modal-body text-muted" id="publicaciones<?php echo $id;?>"></div>
<div class="modal-footer"><small class="text-muted">Se mostrarán máximo 50 publicaciones.</small></div>
</div>
</div>
</div>
<script>
//Ver publicaciones del cliente
$("#ant_pub<?php echo $id;?>").click(function(){
$('#publicaciones<?php echo $id;?>').empty();$("#cant_publicaciones<?php echo $id;?>").remove();
var nickname=$("#titulo2<?php echo $id;?>").attr('class');nickname=encodeURI(nickname);
$.get('../preguntas/publicaciones_user.php',{nick:nickname},verificar,'json');
function verificar(respuesta){
$("#titulo2<?php echo $id;?>").append("<small class='col-auto ml-auto' id='cant_publicaciones<?php echo $id;?>'>"+respuesta.paging.total+" Publicacion(es) Activas.</small>");
if(respuesta.paging.total==0){
$("#publicaciones<?php echo $id;?>").append("<div class='row text-success ml-3'>El cliente no tiene publicaciones activas</div>");
}else{
$.each(respuesta.results,function(i,resultado){
$("#publicaciones<?php echo $id;?>").append("<div class='row align-items-center justify-content-center'><img class='img-fluid img-thumbnail imagen_publicacion_user' src='"+resultado.thumbnail+"'><a href='"+resultado.permalink+"' class='col-sm-9 text-center' target='_blank'>"+resultado.title+"</a><div class='col-sm-2 text-center text-success'>"+resultado.price+"&nbspBs.</div></div><hr>");
});
}
}
});
</script>
<!-- Mercado Lider -->
<div class="modal fade mercadolider<?php echo $id?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-sm" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title" id="nick<?php echo $id;?>"></h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class="container">
      <div class="row align-items-center">
        <?php if ($power_status=="platinum"): ?>
          <svg xmlns="http://www.w3.org/2000/svg" width='15px' viewBox="0 0 448 512"><path d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34zm192-34l-136-136c-9.4-9.4-24.6-9.4-33.9 0l-22.6 22.6c-9.4 9.4-9.4 24.6 0 33.9l96.4 96.4-96.4 96.4c-9.4 9.4-9.4 24.6 0 33.9l22.6 22.6c9.4 9.4 24.6 9.4 33.9 0l136-136c9.4-9.2 9.4-24.4 0-33.8z"/></svg>
        <?php endif; ?>
        <div class="col-auto">
          <svg xmlns="http://www.w3.org/2000/svg" width='30px' viewBox="0 0 512 512"><path fill='#38ff3e' d="M223.75 130.75L154.62 15.54A31.997 31.997 0 0 0 127.18 0H16.03C3.08 0-4.5 14.57 2.92 25.18l111.27 158.96c29.72-27.77 67.52-46.83 109.56-53.39zM495.97 0H384.82c-11.24 0-21.66 5.9-27.44 15.54l-69.13 115.21c42.04 6.56 79.84 25.62 109.56 53.38L509.08 25.18C516.5 14.57 508.92 0 495.97 0zM256 160c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm92.52 157.26l-37.93 36.96 8.97 52.22c1.6 9.36-8.26 16.51-16.65 12.09L256 393.88l-46.9 24.65c-8.4 4.45-18.25-2.74-16.65-12.09l8.97-52.22-37.93-36.96c-6.82-6.64-3.05-18.23 6.35-19.59l52.43-7.64 23.43-47.52c2.11-4.28 6.19-6.39 10.28-6.39 4.11 0 8.22 2.14 10.33 6.39l23.43 47.52 52.43 7.64c9.4 1.36 13.17 12.95 6.35 19.59z"/></svg>
        </div>
        <div class="col-auto ml-auto">
          Mercado Lider Platinum
        </div>
      </div>
      <div class="row mt-3 align-items-center">
        <?php if ($power_status=="gold"): ?>
            <svg xmlns="http://www.w3.org/2000/svg" width='15px' viewBox="0 0 448 512"><path d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34zm192-34l-136-136c-9.4-9.4-24.6-9.4-33.9 0l-22.6 22.6c-9.4 9.4-9.4 24.6 0 33.9l96.4 96.4-96.4 96.4c-9.4 9.4-9.4 24.6 0 33.9l22.6 22.6c9.4 9.4 24.6 9.4 33.9 0l136-136c9.4-9.2 9.4-24.4 0-33.8z"/></svg>
        <?php endif; ?>
        <div class="col-auto">
          <svg xmlns="http://www.w3.org/2000/svg" width='30px' viewBox="0 0 512 512"><path fill='#ffc107' d="M223.75 130.75L154.62 15.54A31.997 31.997 0 0 0 127.18 0H16.03C3.08 0-4.5 14.57 2.92 25.18l111.27 158.96c29.72-27.77 67.52-46.83 109.56-53.39zM495.97 0H384.82c-11.24 0-21.66 5.9-27.44 15.54l-69.13 115.21c42.04 6.56 79.84 25.62 109.56 53.38L509.08 25.18C516.5 14.57 508.92 0 495.97 0zM256 160c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm92.52 157.26l-37.93 36.96 8.97 52.22c1.6 9.36-8.26 16.51-16.65 12.09L256 393.88l-46.9 24.65c-8.4 4.45-18.25-2.74-16.65-12.09l8.97-52.22-37.93-36.96c-6.82-6.64-3.05-18.23 6.35-19.59l52.43-7.64 23.43-47.52c2.11-4.28 6.19-6.39 10.28-6.39 4.11 0 8.22 2.14 10.33 6.39l23.43 47.52 52.43 7.64c9.4 1.36 13.17 12.95 6.35 19.59z"/></svg>
        </div>
        <div class="col-auto ml-auto">
          Mercado Lider Gold
        </div>
      </div>
      <div class="row mt-3 align-items-center">
        <?php if ($power_status=="silver"): ?>
            <svg xmlns="http://www.w3.org/2000/svg" width='15px' viewBox="0 0 448 512"><path d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34zm192-34l-136-136c-9.4-9.4-24.6-9.4-33.9 0l-22.6 22.6c-9.4 9.4-9.4 24.6 0 33.9l96.4 96.4-96.4 96.4c-9.4 9.4-9.4 24.6 0 33.9l22.6 22.6c9.4 9.4 24.6 9.4 33.9 0l136-136c9.4-9.2 9.4-24.4 0-33.8z"/></svg>
        <?php endif; ?>
        <div class="col-auto">
          <svg xmlns="http://www.w3.org/2000/svg" width='30px' viewBox="0 0 512 512"><path fill='#6c757d' d="M223.75 130.75L154.62 15.54A31.997 31.997 0 0 0 127.18 0H16.03C3.08 0-4.5 14.57 2.92 25.18l111.27 158.96c29.72-27.77 67.52-46.83 109.56-53.39zM495.97 0H384.82c-11.24 0-21.66 5.9-27.44 15.54l-69.13 115.21c42.04 6.56 79.84 25.62 109.56 53.38L509.08 25.18C516.5 14.57 508.92 0 495.97 0zM256 160c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm92.52 157.26l-37.93 36.96 8.97 52.22c1.6 9.36-8.26 16.51-16.65 12.09L256 393.88l-46.9 24.65c-8.4 4.45-18.25-2.74-16.65-12.09l8.97-52.22-37.93-36.96c-6.82-6.64-3.05-18.23 6.35-19.59l52.43-7.64 23.43-47.52c2.11-4.28 6.19-6.39 10.28-6.39 4.11 0 8.22 2.14 10.33 6.39l23.43 47.52 52.43 7.64c9.4 1.36 13.17 12.95 6.35 19.59z"/></svg>
        </div>
        <div class="col-auto ml-auto">
          Mercado Lider
        </div>
      </div>
      <div class="row mt-3 align-items-center">
        <?php if ($power_status!="silver" && $power_status!="gold" && $power_status!="platinum"): ?>
            <svg xmlns="http://www.w3.org/2000/svg" width='15px' viewBox="0 0 448 512"><path d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34zm192-34l-136-136c-9.4-9.4-24.6-9.4-33.9 0l-22.6 22.6c-9.4 9.4-9.4 24.6 0 33.9l96.4 96.4-96.4 96.4c-9.4 9.4-9.4 24.6 0 33.9l22.6 22.6c9.4 9.4 24.6 9.4 33.9 0l136-136c9.4-9.2 9.4-24.4 0-33.8z"/></svg>
        <?php endif; ?>
        <div class="col-auto">
          <svg xmlns="http://www.w3.org/2000/svg" width='30px' viewBox="0 0 512 512"><path fill='#000' d="M223.75 130.75L154.62 15.54A31.997 31.997 0 0 0 127.18 0H16.03C3.08 0-4.5 14.57 2.92 25.18l111.27 158.96c29.72-27.77 67.52-46.83 109.56-53.39zM495.97 0H384.82c-11.24 0-21.66 5.9-27.44 15.54l-69.13 115.21c42.04 6.56 79.84 25.62 109.56 53.38L509.08 25.18C516.5 14.57 508.92 0 495.97 0zM256 160c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm92.52 157.26l-37.93 36.96 8.97 52.22c1.6 9.36-8.26 16.51-16.65 12.09L256 393.88l-46.9 24.65c-8.4 4.45-18.25-2.74-16.65-12.09l8.97-52.22-37.93-36.96c-6.82-6.64-3.05-18.23 6.35-19.59l52.43-7.64 23.43-47.52c2.11-4.28 6.19-6.39 10.28-6.39 4.11 0 8.22 2.14 10.33 6.39l23.43 47.52 52.43 7.64c9.4 1.36 13.17 12.95 6.35 19.59z"/></svg>
        </div>
        <div class="col-auto ml-auto">
          No es Mercado Lider
        </div>
      </div>
    </div>
</div>
<div class="modal-footer">
  <small>Para ser Mercado Líder se debe:.</small>
</div>
</div>
</div>
</div>
<script>
//Nickname
$("#mercadolider<?php echo $id;?>").click(function(){
$("#nick<?php echo $id;?>").empty();
var user=<?php echo $seller;?>,url='https://api.mercadolibre.com/users/'+user;
$.getJSON(url,function(datos){
$('#nick<?php echo $id;?>').append(datos.nickname);
});
});
</script>
<!-- Descripcion -->
<div class="modal fade description<?php echo $id?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content container">
<div class="modal-header">
<h5 class="modal-title"><?php echo $title;?></h5>
<button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body text-muted" ><div id="text<?php echo $id?>"></div></div>
<div class="modal-footer">
<div class="container-fluid">
<div class="row">
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
<script>
//Descripcion de la publicacion
$("#but<?php echo $id?>").click(function(){
var item="<?php echo $id?>",url='https://api.mercadolibre.com/items/'+item+'/description';
$.getJSON(url,function(datos){
var description=datos.plain_text,auxiliar=description.split('\n').length+3;
description= "<textarea class='textarea_description' rows='"+auxiliar+"' disabled>"+description+"</textarea>";
document.getElementById("text<?php echo $id?>").innerHTML=description;
var update=datos.last_updated;
document.getElementById("last<?php echo $id?>").innerHTML=update.substr(0,10);
});
});
</script>
<!-- Imagenes -->
<div class="modal fade images-<?php echo $id;?>-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content container">
<div class="modal-header">
<div class="container"><div class="row"><h5 class="modal-title" id="exampleModalLabel"><?php echo $title;?></h5></div></div>
<button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
<div class="row">
<?php foreach ($imagenes as $value): ?>
<div class="col-auto mb-2 mr-3">
<div class="row">
<img class='col-auto images-publicaciones img-fluid img-thumbnail' src='<?php echo $value->secure_url;?>'>
</div>
<div class="row align-items-end">
<small class="text-secondary" title='Pixeles de la imagen' data-toggle='tooltip'><?php echo $value->size;?> px</small>
</div>
</div>
<?php endforeach; ?>
</div>
</div>
<div class="modal-footer text-muted">Imágenes de la publicación.</div>
</div>
</div>
</div>
<!-- Estadisticas -->
<div class="modal fade modal-estadist-<?php echo $id?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content container">
<div class="modal-header">
<div class="container">
<div class="row"><h5 class="modal-title" id="exampleModalLabel"><?php echo $title;?></h5></div>
<div class="row"><span class="d-block text-muted">Estadisticas de Visitas</span></div>
<div class="row text-muted"><small id="visitas-<?php echo $id?>"></small></div>
</div>
<button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body ">
<div class="container">
Estadisticas de la publicación:
<div class="row mb-3">
<span class="col-auto text-muted">Selecciona el mes que deseas:</span>
<div class="col-auto">
<div class='input-group input-group-sm'>
<div class='input-group-prepend'><label class='input-group-text'>Mes</label></div>
<select class='custom-select custom-select-sm' id='mes_pub<?php echo $id?>'>
<option value="Ene">Enero</option><option value="Feb">Febrero</option><option value="Mar">Marzo</option>
<option value="Abr">Abril</option><option value="May">Mayo</option><option value="Jun">Junio</option>
<option value="Jul">Julio</option><option value="Ago">Agosto</option><option value="Sep">Septiembre</option>
<option value="Oct">Octubre</option><option value="Nov">Noviembre</option><option value="Dic">Diciembre</option>
</select>
</div>
</div>
<div class="col-auto">
<div class='input-group input-group-sm'>
<div class='input-group-prepend'><label class='input-group-text'>Año</label></div>
<select class="custom-select custom-select-sm" id='anio_pub<?php echo $id?>'>
  <?php for($i=($year-1);$i<=$year; $i++){?>
    <option value='<?php echo $i;?>'><?php echo $i;?></option>
    <?php } ?>
</select>
</div>
</div>
<div class="col-auto"><button class="btn btn-success btn-sm" type="button" id='search_estadist_pub<?php echo $id?>'>Buscar</button></div>
</div>
<div class="row">
<canvas id="myAreaChart<?php echo $id?>" width="100%" height="30"></canvas>
<span class="text-muted ml-auto" id='chart_foot_pub<?php echo $id?>'></span>
</div>
</div>
<hr>
<div class="container">
Estadisticas del vendedor:
<div class="row mb-3">
<span class="col-auto text-muted">Selecciona el mes que deseas:</span>
<div class="col-auto">
<div class='input-group input-group-sm'>
<div class='input-group-prepend'><label class='input-group-text'>Mes</label></div>
<select class='custom-select custom-select-sm' id='mes_user<?php echo $id?>'>
<option value="Ene">Enero</option><option value="Feb">Febrero</option><option value="Mar">Marzo</option>
<option value="Abr">Abril</option><option value="May">Mayo</option><option value="Jun">Junio</option>
<option value="Jul">Julio</option><option value="Ago">Agosto</option><option value="Sep">Septiembre</option>
<option value="Oct">Octubre</option><option value="Nov">Noviembre</option><option value="Dic">Diciembre</option>
</select>
</div>
</div>
<div class="col-auto">
<div class='input-group input-group-sm'>
<div class='input-group-prepend'><label class='input-group-text'>Año</label></div>
<select class="custom-select custom-select-sm" id='anio_user<?php echo $id?>'>
  <?php for($i=($year-1);$i<=$year; $i++){?>
    <option value='<?php echo $i;?>'><?php echo $i;?></option>
    <?php } ?>
</select>
</div>
</div>
<div class="col-auto"><button class="btn btn-success btn-sm" type="button" id='search_estadist_user<?php echo $id?>'>Buscar</button></div>
</div>
<div class="row">
<canvas id="chartSeller<?php echo $id?>" width="100%" height="30"></canvas>
<span class="text-muted ml-auto" id='chart_foot_user<?php echo $id?>'></span>
</div>
</div>
</div>
</div>
</div>
</div>
<script>
//estadisticas
$("#search_estadist_pub<?php echo $id?>").click(function(){
$('#chart_foot_pub<?php echo $id?>').empty();
var mes=$("#mes_pub<?php echo $id?>").val(),anio=$("#anio_pub<?php echo $id?>").val(),item='<?php echo $id?>';
$.get("estadisticas.php",{anio:anio,mes:mes,item:item},verificar,'text');
function verificar(respuesta){
var array=respuesta.split('%'),totaly=parseInt(array[0]),mayor=parseInt(array[1]),fechas=array[2];fechas=fechas.split('|');
var cantidades=array[3],totales=array[4];
$('#chart_foot_pub<?php echo $id?>').append("<small class='text-muted'>Esta publicación obtuvo "+totales+" visitas en este período.</small>")
cantidades=cantidades.split('|');
if(window.graficaVisitas){window.graficaVisitas.clear();window.graficaVisitas.destroy();}
var ctx=document.getElementById("myAreaChart<?php echo $id?>");
window.graficaVisitas=new Chart(ctx,{
type: 'line',
data: {labels: fechas,
datasets: [{
label: "Visitas",lineTension: 0.3,backgroundColor: "rgba(70,203,62,0.3)",borderColor: "rgba(70,203,62,1)",pointRadius: 5,
pointBackgroundColor: "rgba(70,203,62,1)",pointBorderColor: "rgba(255,255,255,0.8)",pointHoverRadius: 5,
pointHoverBackgroundColor: "rgba(70,203,62,1)",pointHitRadius: 20,pointBorderWidth: 2,data: cantidades,
}],
},
options:{
scales:{
xAxes:[{time:{unit:'Días'},gridLines:{display:false},ticks:{maxTicksLimit: totaly}}],
yAxes:[{ticks:{min:0,max: mayor,maxTicksLimit:8},gridLines:{color:"rgba(0, 0, 0, .125)",}}],
},
legend:{display:false}
}
});
}
});
$("#search_estadist_user<?php echo $id?>").click(function(){
$('#chart_foot_user<?php echo $id?>').empty();
var mes=$("#mes_user<?php echo $id?>").val(),anio=$("#anio_user<?php echo $id?>").val(),seller='<?php echo $seller?>';
$.get("estadisticas.php",{anio:anio,mes:mes,seller:seller},verificar,'text');
function verificar(respuesta){
var array=respuesta.split('%'),totaly=parseInt(array[0]),mayor=parseInt(array[1]),fechas=array[2];fechas=fechas.split('|');
var cantidades=array[3],totales=array[4];
$('#chart_foot_user<?php echo $id?>').append("<small class='text-muted'>Este vendedor obtuvo "+totales+" visitas en este período.</small>")
cantidades=cantidades.split('|');
if(window.graficaVisitaSeller){window.graficaVisitaSeller.clear();window.graficaVisitaSeller.destroy();}
var ctx=document.getElementById("chartSeller<?php echo $id?>");
window.graficaVisitaSeller=new Chart(ctx,{
type:'line',
data:{
labels:fechas,datasets:[{
label: "Visitas",lineTension: 0.3,backgroundColor: "rgba(70,203,62,0.3)",borderColor: "rgba(70,203,62,1)",pointRadius: 5,
pointBackgroundColor: "rgba(70,203,62,1)",pointBorderColor: "rgba(255,255,255,0.8)",pointHoverRadius: 5,
pointHoverBackgroundColor: "rgba(70,203,62,1)",pointHitRadius: 20,pointBorderWidth: 2,data: cantidades,
}],
},
options:{
scales:{
xAxes:[{time:{unit:'Días'},gridLines:{display:false},ticks:{maxTicksLimit: totaly}}],
yAxes:[{ticks:{min:0,max: mayor,maxTicksLimit:8},gridLines:{color:"rgba(0, 0, 0, .125)",}}],
},legend:{display:false}
}
});
}
});
</script>
<!-- Tienda Oficial -->
<?php if($official_store!=null): ?>
<div class="modal fade tienda_off<?php echo $id?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-sm" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title" id="title_oficial<?php echo $id?>"></h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="container modal-body"><div class="row justify-content-center" id="cont_official<?php echo $id?>"></div></div>
<div class="modal-footer" id="foot_official<?php echo $id?>"></div>
</div>
</div>
</div>
<script>
$("#to<?php echo $id?>").click(function(){
$('#title_oficial<?php echo $id?>').empty();$('#cont_official<?php echo $id?>').empty();$('#foot_official<?php echo $id?>').empty();
var meses=['','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],oficial=<?php echo $official_store;?>;
var seller=<?php echo $seller;?>,url='https://api.mercadolibre.com/users/'+seller+'/brands/'+oficial;
$.getJSON(url,function(datos){
$('#title_oficial<?php echo $id?>').append(datos.name);
$('#cont_official<?php echo $id?>').append("<div class='col-auto'><a href="+datos.permalink+" target='_blank'><img src="+datos.pictures[0].secure_url+" width='150px' height='auto'></a></div>");
var fecha=datos.date_created,mes =fecha.substr(5,2);
if(mes<10){mes=meses[mes.substr(1,1)];}else{mes=meses[mes]}
$('#foot_official<?php echo $id?>').append("<span class='text-muted'>Tienda Oficial desde el "+fecha.substr(8,2)+" de "+mes+" del "+fecha.substr(0,4)+"</span>");
});
});
</script>
<?php endif;
} //Fin Foreach principal
} //fin else si hay publicaciones
?>
<div class="container mt-2">
<div class="row justify-content-center">
<?php
if($items_paginacion>50){
$NroPag=ceil($items_paginacion/50);
?>
<nav aria-label="Page navigation example">
<ul class="pagination">
<?php
if(isset($_GET['page']) && !empty($_GET['page'])){
$pagina=$_GET['page'];
if($pagina>6 && $NroPag>6){
?>
<li class="page-item">
<a class="page-link" href="index.php?search=<?php echo $query;?>&page=<?php echo ($pagina-6);?>" aria-label="Previous">
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
<li class="page-item active"><a class="page-link" href="index.php?search=<?php echo $query;?>&page=<?php echo $i;?>"><?php echo $i;?></a></li>
<?php }else{ ?>
<li class="page-item"><a class="page-link" href="index.php?search=<?php echo $query;?>&page=<?php echo $i;?>"><?php echo $i;?></a></li>
<?php } } }
}else{
for($i=1;$i<11;$i++){
if(($i-1)==$NroPag){
break;
}else{
if($i==$pagina){
?>
<li class="page-item active"><a class="page-link" href="index.php?search=<?php echo $query;?>&page=<?php echo $i;?>"><?php echo $i;?></a></li>
<?php }else{ ?>
<li class="page-item"><a class="page-link" href="index.php?search=<?php echo $query;?>&page=<?php echo $i;?>"><?php echo $i;?></a></li>
<?php } } } } ?>
<?php if($NroPag>10 && $pagina<($NroPag-4)){?>
<li class="page-item">
<a class="page-link" href="index.php?search=<?php echo $query;?>&page=<?php echo (5+$pagina);?>" aria-label="Next">
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
</div>
<?php include '../common/footer.php';?>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin.min.js"></script>
</div>
</body>
</html>
