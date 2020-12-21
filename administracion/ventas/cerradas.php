<?php
session_start();
require '../common/meli.php';require '../common/conexion.php';require '../common/take_at.php'; require '../common/account-off.php';;require '../Oxa/Funciones.php';
if(isset($_GET['delete'])){$idsipnapsis=$_GET['delete'];deleteInfo($idsipnapsis, $id_user);}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
<meta name="description" content=""/>
<meta name="author" content=""/>
<link rel="shortcut icon" href="../../img/favicon.ico">
<title>Oxas - Ventas</title>
<link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
<link href="../css/sb-admin.min.css" rel="stylesheet">
<link href="../css/style.css" rel="stylesheet"/>
<script src="../vendor/jquery/jquery.min.js"></script>
</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">
<?php
if(isset($_GET['page']) && !empty($_GET['page'])){$conteo=50*($_GET['page']-1);}else{$conteo=0;}
/*
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/messages/pending_read?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);$consulta2=json_decode($result);
$mensajes_pendientes=$consulta2->results;*/
$ch=curl_init();
if(isset($_GET['page'])){
$offset=($_GET['page']-1)*50;
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/orders/search?seller='.$id_user.'&sort=date_desc&access_token='.$AccessToken.'&offset='.$offset);
}else{
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/orders/search?seller='.$id_user.'&sort=date_desc&access_token='.$AccessToken);
}
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);$consulta=json_decode($result);
$total_ventas=$consulta->paging->total;
if($total_ventas!=0){
$resultados=$consulta->results;$items=array();
foreach($resultados as $value){$order_items=$value->order_items;foreach($order_items as $valor){$item=$valor->item->id;array_push($items,$item);}}
$public=array_unique($items);$contador=0;$band=0;$total_items=count($public);
while($contador!=$total_items){
$array_publicaciones_veinte=array();
foreach($public as $value){
++$contador;array_push($array_publicaciones_veinte,$value);
if(($contador==20 || $contador==$total_items) && $band==0){
$band=1;$str_public=implode(',',$array_publicaciones_veinte);$array_publicaciones_veinte=array();break;
}elseif(($contador==40 || $contador==$total_items) && $band==1){
$band=2;$str_public=implode(',',$array_publicaciones_veinte);$array_publicaciones_veinte=array();break;
}elseif(($contador==50 || $contador==$total_items) && $band==2){
$band=3;$str_public=implode(',',$array_publicaciones_veinte);$array_publicaciones_veinte=array();break;
}
}
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items?ids='.$str_public.'&attributes=id,permalink,thumbnail&access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$result=json_decode($result);
$public_act=array();$imagenes_p=array();$permalink_p=array();
foreach($result as $valor){
$publicac=$valor->body->id;array_push($public_act,$publicac);
$imagen=$valor->body->thumbnail;array_push($imagenes_p,$imagen);
$permal=$valor->body->permalink;array_push($permalink_p,$permal);
$total_items=count($public_act);
}
}
$mes=[0,'Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
include '../common/navbar.php';?>
<div class="content-wrapper p-0">
<div class="container-fluid background_activas py-2">
<div class="row align-items-center">
<div class="col-auto ml-2">
<?php if(isset($_SESSION['array_publicaciones'][0]) && $_SESSION['array_publicaciones'][0]==1 && count($_SESSION['array_publicaciones'])<2){ ?>
<input class='pl-1' type='checkbox' id='selectall' checked>
<?php }else{ ?><input class='pl-1' type='checkbox' id='selectall'><?php } ?>
</div>
<div class='col-auto pt-1 px-0 dropdown cursor-pointer' id='dropdown'>
<a class='dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
<svg xmlns='http://www.w3.org/2000/svg' width='12px' class='svg-dark' viewBox='0 0 448 512'><path d='M16 132h416c8.837 0 16-7.163 16-16V76c0-8.837-7.163-16-16-16H16C7.163 60 0 67.163 0 76v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z'/></svg>
</a>
<div class='dropdown-menu'><div class='ml-3 cursor-pointer' id='calificar'>Calificar</div></div>
</div>
<div class='col-auto ml-4'><a class='text-muted' href='index.php' data-toggle='tooltip' title='Ventas Abiertas'>Abiertas</a></div>
<div class='col-auto'>
<span class='text-success'>Cerradas</span>
(<small class='font-weight-bold' title='Ventas Cerradas' data-toggle='tooltip' id='ventas_cerradas'></small>)
</div>
<div class="col-auto ml-auto"><a class="btn btn-link text-success btn-sm" data-toggle='tooltip' title='En formato Excel' href='BD_clientes.php'>Descargar Clientes</a></div>
</div>
</div>
<div class="container-fluid">
<?php
if($total_ventas!=0){
foreach($resultados as $valor){
$hoy=getdate();$hour_hoy= $hoy["hours"];$minutes_hoy= $hoy["minutes"];$dia_hoy= $hoy["mday"];$mes_hoy= $hoy["mon"];$year_hoy= $hoy["year"];
if(strlen($minutes_hoy)==1){ $minutes_hoy="0".$minutes_hoy;}if(strlen($hour_hoy)==1){ $hour_hoy="0".$hour_hoy;}
if(strlen($dia_hoy)==1){ $dia_hoy="0".$dia_hoy;}if(strlen($mes_hoy)==1){ $mes_hoy="0".$mes_hoy;}
$fecha_hoy="$year_hoy-"."$mes_hoy-"."$dia_hoy"."T"."$hour_hoy".":"."$minutes_hoy".":00.000-04:00";
if(isset($valor->feedback->sale) || isset($valor->feedback->purchase) || $valor->expiration_date < $fecha_hoy){
$idc=$valor->id;$id_user_buyer=$valor->buyer->id;
$fecha_compra=$valor->date_created;$items_order=$valor->order_items;
foreach($items_order as $valor3){
$cantidad=$valor3->quantity;$titulo=$valor3->item->title;
$id_publicac=$valor3->item->id;$comision=$valor3->sale_fee;
$precio_unidad=$valor3->unit_price;
}
if(isset($valor->feedback->sale->date_created)){
$sale=$valor->feedback->sale->date_created;
if($valor->expiration_date>$sale){$fin=$sale;
}elseif($sale>$valor->expiration_date){$fin=$valor->expiration_date;}
}else{$fin=$valor->expiration_date;}
?>
<div class="container-fluid my-2 bordes py-2 rounded">
<div class="row">
<div class="col-1">
<span class="text-secondary conteo"><?php ++$conteo; echo $conteo;?></span>
<?php
$clave=array_search($id_publicac,$public_act);
if($clave){$permalink=$permalink_p[$clave];$img=$imagenes_p[$clave];}else{$img="";$permalink="#";}
?>
<img class="d-none d-sm-block img-ventas" src="<?php echo $img;?>" width="40" height="40"/>
</div>
<div class="col-11 m-0 p-0 pr-2">
<div class="row justify-content-sm-center">
<div>(<b title="Artículos comprados" data-toggle="tooltip"><?php echo $cantidad;?></b>) <a href="<?php echo $permalink;?>" target="_blank"><?php echo $titulo;?></a></div>
<?php
$pendientes='';
//foreach($mensajes_pendientes as $value1){if($value1->order_id==$idc){$pendientes=$value1->count;}}
?>
<div class="col-auto">
<a class="enlace" href="javascript:void(0)" data-toggle="modal" data-target=".lg_questions<?php echo $idc;?>" id="questions<?php echo $idc;?>"><small>Ver Preguntas</small></a>
<a class="enlace ml-1" href="javascript:void(0)" data-toggle="modal" data-target=".mensajes_<?php echo $idc;?>-modal-lg" id="men<?php echo $idc;?>"><small>Ver Mensajes</small></a>
<b><small class="text-primary" title="Pendientes de leer" data-toggle="tooltip"><?php echo " ".$pendientes;?></small></b>
</div>
<div class="col-auto ml-auto">
<?php
$status=$valor->status;
$tipo_pago='Acordar con el vendedor';$metodo_pago='Otro';$comisiones=0;
$total_monto=$valor->total_amount;
if(!empty($valor->payments)){foreach($valor->payments as $valor2){$status=$valor2->status;}}
if($status=='approved'){
?>
<span class="text-success" title="Acreditado por Mercado Pago" data-toggle="tooltip"><?php echo $valor->total_amount;?> Bs</span>
<?php }elseif($status=='rejected'){ ?>
<span class="text-danger" title="Pago rechazado por Mercado Pago" data-toggle="tooltip"><?php echo $valor->total_amount;?> Bs</span>
<?php }else{ ?>
<span class="text-primary" title="Acuerdas el pago" data-toggle="tooltip"><?php echo $valor->total_amount;?> Bs</span>
<?php } ?>
</div>
</div>
<div class="row">
<div class="col-auto">
<small><?php echo $valor->buyer->first_name.' '.$valor->buyer->last_name;?></small> - <small title="Nickname" data-toggle="tooltip"><a href="javascript:void(0)" id="nic<?php echo $idc;?>" data-toggle="modal" data-target="#user_<?php echo $idc;?>"><?php echo $valor->buyer->nickname;?></a></small>
</div>
<?php
$sql_form="SELECT * FROM formulario WHERE ORDENID=$idc LIMIT 1";
$band_form=0;
  $result_form=$conn->query($sql_form);
  if($result_form->num_rows>0){
    while($row_form=$result_form->fetch_assoc()){
      $cedula=$row_form['CIBUYER'];
      $telefono=$row_form['TELEFONOBUYER'];
      $correo=$row_form['CORREOBUYER'];
      $banco_emisor=$row_form['BANKEMISOR'];
      $banco_receptor=$row_form['BANKEMISOR'];
      $pago=$row_form['PAGO'];
      $fecha_pago=$row_form['FECHAPAGO'];
      $mes_pago=$mes[intval(substr($fecha_pago,4,2))];
      $fecha_pago=substr($fecha_pago,6,2)." de $mes_pago";
      $referencia=$row_form['REFERENCIA'];
      $agencia=$row_form['AGENCIA'];
      $estado=$row_form['ESTADOENVIO'];
      $municipio=$row_form['MUNICIPIOENVIO'];
      $codigo_agencia=$row_form['CODIGOAGENCIA'];
      $direccion=$row_form['DIRECCIONENVIO'];
      $band_form=1;
    }
  }
  if($band_form==1){
  ?>
  <div class="col-auto">
    <a class="text-success" href="#" data-toggle="modal" data-target="#form_<?php echo $idc;?>">Ver formulario</a>
  </div>
  <?php } ?>
<?php
if(substr($fin,5,2)>9){$fecha_compra_proc=substr($fin,8,2).' de '.$mes[substr($fin,5,2)].' a las '.substr($fin,11,5);
}else{$fecha_compra_proc=substr($fin,8,2).' de '.$mes[substr($fin,6,1)].' a las '.substr($fin,11,5);}
?>
<div class="col-auto ml-auto">
<small class="text-muted" title="Fecha de cierre de la venta" data-toggle="tooltip"><b><?php echo $fecha_compra_proc;?></b></small>
<?php if(is_null($valor->feedback->sale)){ ?>
<a href="#" data-toggle="modal" data-target=".calif_<?php echo $idc;?>-modal-lg"><small title="Ver calificaciones" data-toggle="tooltip" id="calificaciones<?php echo $idc;?>">
<svg xmlns="http://www.w3.org/2000/svg" width="8px" class="svg-danger" viewBox="0 0 448 512"><path d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/></svg>
</small></a>
<?php }elseif(!is_null($valor->feedback->purchase)){ ?>
<a href="#" data-toggle="modal" data-target=".calif_<?php echo $idc;?>-modal-lg"><small title="Ver calificaciones" data-toggle="tooltip" id="calificaciones<?php echo $idc;?>">
<svg xmlns="http://www.w3.org/2000/svg" width="8px" class="svg-success" viewBox="0 0 448 512"><path d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/></svg>
</small></a>
<?php }else{ ?>
<a href="#" data-toggle="modal" data-target=".calif_<?php echo $idc;?>-modal-lg"><small title="Ver calificaciones" data-toggle="tooltip" id="calificaciones<?php echo $idc;?>">
<svg xmlns="http://www.w3.org/2000/svg" width="8px" class="svg-primary" viewBox="0 0 448 512"><path d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/></svg>
</small></a>
<?php } ?>
<a href="#" data-toggle="modal" data-target="#notes_modal<?php echo $idc;?>" id="notes<?php echo $idc;?>"><small class="cursor-pointer" title="Notas" data-toggle="tooltip"><svg xmlns="http://www.w3.org/2000/svg" width="8px" class="svg-dark" viewBox="0 0 448 512"><path d="M12 192h424c6.6 0 12 5.4 12 12v260c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V204c0-6.6 5.4-12 12-12zm436-44v-36c0-26.5-21.5-48-48-48h-48V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H160V12c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v52H48C21.5 64 0 85.5 0 112v36c0 6.6 5.4 12 12 12h424c6.6 0 12-5.4 12-12z"/></svg></small></a>
</div>
</div>
</div>
</div>
</div>
<?php if($band_form==1){ ?>
  <!-- Modal formulario -->
  <div class="modal fade" id="form_<?php echo $idc;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title"><?php echo $valor->buyer->first_name . " " . $valor->buyer->last_name;?></h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_calificar<?php echo $idc;?>"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row mb-3">
              <div class="col-12">
                <h6 class="text-muted mb-2">Datos del comprador</h6>
              </div>
              <span class="col-sm-6">Cédula: <span class="text-muted"><?php echo $cedula;?></span> </span>
              <span class="col-sm-6">Teléfono: <span class="text-muted"><?php echo $telefono;?></span></span>
              <span class="col-12">Correo: <span class="text-muted"><?php echo $correo;?></span></span>
            </div>
            <div class="row mb-3">
              <div class="col-12">
                <h6 class="text-muted mb-2">Datos de pago</h6>
              </div>
              <span class="col-sm-6">B. Emisor: <span class="text-muted"><?php echo $banco_emisor;?></span></span>
              <span class="col-sm-6">B. Receptor: <span class="text-muted"><?php echo $banco_receptor;?></span></span>
              <span class="col-sm-6">Pago: <span class="text-muted"><?php $decimales=number_format($pago,2,',','.'); echo "$decimales";?> Bs.</span> </span>
              <span class="col-sm-6">Referencia: <span class="text-muted"><?php echo $referencia;?></span></span>
              <span class="col-12">Fecha pago: <span class="text-muted"><?php echo $fecha_pago;?></span></span>
            </div>
            <div class="row">
              <div class="col-12 mb-2">
                <h6 class="text-muted">Datos de envío</h6>
              </div>
              <span class="col-sm-6">Agencia: <span class="text-muted"><?php echo $agencia;?></span></span>
              <span class="col-sm-6">Estado: <span class="text-muted"><?php echo $estado;?></span></span>
              <span class="col-sm-6">Municipio: <span class="text-muted"><?php echo $municipio;?></span></span>
              <span class="col-sm-6">Codigo Agencia: <span class="text-muted"><?php echo $codigo_agencia;?></span></span>
              <span class="col-12">Dirección: <span class="text-muted"><?php echo $direccion;?></span></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
<!-- Notas -->
<div class="modal fade" id="notes_modal<?php echo $idc;?>" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h6 class="modal-title"><?php echo $valor->buyer->first_name.' '.$valor->buyer->last_name;?></h6>
<button type="button" class="close" data-dismiss="modal" aria-label="Close" id="notes_close<?php echo $idc;?>"><span aria-hidden="true">&times;</span></button>
</div>
<form action="notes.php" method="post"><div class="modal-body" id="notas<?php echo $idc;?>"><div class="container" id="option_not<?php echo $idc;?>"></div></div></form>
<div class="modal-footer">
<div class="container">
<div class="row">
<input type="hidden" name="order" value="<?php echo $idc;?>" id="nota<?php echo $idc;?>">
<input class="form-control col-12 mb-2" type="text" name="nota" placeholder="Nueva Nota" required id="textnote<?php echo $idc;?>">
<button class="btn btn-success btn-sm col-auto mr-auto" type="button" id="newnote<?php echo $idc;?>">Agregar Nota</button>
</div>
</div>
</div>
</div>
</div>
</div>
<script>
//nuevas Notas
$("#newnote<?php echo $idc;?>").click(function(){
var order=$("#nota<?php echo $idc;?>").val(),nota=$("#textnote<?php echo $idc;?>").val();
$.ajax({url:'new_notes.php',type:'GET',data:{order:order,nota:nota},async:false,dataType:'json',
success: function(json){
if(typeof json.note.date_created!='undefined'){
const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
toast({type:'success',title:'¡Se agregó Exitosamente la Nota!'})
$('#notes_close<?php echo $idc;?>').click();$('#textnote<?php echo $idc;?>').val('');
}else{
swal({type:'error',title:'Oops...',text:'Hubo un pequeño problema.',footer:'¡Intentalo de nuevo!'});
$('#notes_close<?php echo $idc;?>').click();
}
}
});
});
//ver notas
$("#notes<?php echo $idc;?>").click(function(){
$("#option_not<?php echo $idc;?>").remove();
$("#notas<?php echo $idc;?>").append("<div class='container' id='option_not<?php echo $idc;?>'></div>");
$.get('notes.php',{order:"<?php echo $idc;?>"},verificar,'json');
function verificar(respuesta){
$.each(respuesta,function(i,resultado){
if(resultado.results!=0){
$.each(resultado.results, function(i,resultado2){
var id=resultado2.id;
$("#option_not<?php echo $idc;?>").append("<div class='row alert alert-warning'><div class='col-10'>"+resultado2.note+"</div><div class='col-auto'><span class='eliminarnotas' id='"+id+"'><svg xmlns='http://www.w3.org/2000/svg' width='10px' class='cursor-pointer svg-danger' viewBox='0 0 448 512'><path d='M0 84V56c0-13.3 10.7-24 24-24h112l9.4-18.7c4-8.2 12.3-13.3 21.4-13.3h114.3c9.1 0 17.4 5.1 21.5 13.3L312 32h112c13.3 0 24 10.7 24 24v28c0 6.6-5.4 12-12 12H12C5.4 96 0 90.6 0 84zm415.2 56.7L394.8 467c-1.6 25.3-22.6 45-47.9 45H101.1c-25.3 0-46.3-19.7-47.9-45L32.8 140.7c-.4-6.9 5.1-12.7 12-12.7h358.5c6.8 0 12.3 5.8 11.9 12.7z'/></svg></span></div></div>");
});
}else{$("#option_not<?php echo $idc;?>").append("<div class='row alert alert-warning'><div class='col-12'>No hay notas</div></div>");}
});
};
});
/* Eliminar Notas*/
$('body').on('click','#option_not<?php echo $idc;?> span', function(){
id=$(this).attr('id');
$.get('delete_notes.php',{id_note:id,order:"<?php echo $idc;?>"},verificar,'json');
function verificar(respuesta){
if(respuesta.message!='undefined'){
const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
toast({type:'success',title:'¡La nota fue eliminada Exitosamente!'})
$('#notes_close<?php echo $idc;?>').click();
}
}
});
</script>
<!-- Calificaciones -->
<div class="modal fade calif_<?php echo $idc;?>-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h6 class="modal-title" id="exampleModalLabel"><?php echo $valor->buyer->first_name.' '.$valor->buyer->last_name;?></h6>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div class="container">
<div class="row"><b>Vendedor:</b></div>
<div class="row">
<div class="col-auto">
<?php
if(!is_null($valor->feedback->sale)){
switch($valor->feedback->sale->rating){
case 'positive':
?><span title="Positivo" data-toggle="tooltip">
<svg xmlns="http://www.w3.org/2000/svg" width="15px" class="calif-posit" viewBox="0 0 512 512"><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z"/></svg>
</span>
<?php break; case 'neutral': ?>
<span title="Neutral" data-toggle="tooltip">
<svg xmlns="http://www.w3.org/2000/svg" width="15px" class="calif-neutro" viewBox="0 0 512 512"><path d="M256 8C119.033 8 8 119.033 8 256s111.033 248 248 248 248-111.033 248-248S392.967 8 256 8zm80 248c0 44.112-35.888 80-80 80s-80-35.888-80-80 35.888-80 80-80 80 35.888 80 80z"/></svg>
</span>
<?php break; default: ?>
<span title="Negativo" data-toggle="tooltip">
<svg xmlns="http://www.w3.org/2000/svg" width="15px" class="calif-negat" viewBox="0 0 512 512"><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zM124 296c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h264c6.6 0 12 5.4 12 12v56c0 6.6-5.4 12-12 12H124z"/></svg>
</span>
<?php break; } } ?>
</div>
<div id="text-calif<?php echo $idc;?>"></div>
<?php if(is_null($valor->feedback->sale)){ ?>
<div class="col-auto ml-auto">
<button class="btn btn-link btn-sm" type="button" data-toggle="modal" data-target="#calificar_<?php echo $idc;?>" id="calificar_button<?php echo $idc;?>">Calificar</button>
</div>
<?php } ?>
</div>
<div class="row">
<div class="col-auto"><small class="text-muted" id='fulfilled_vendedor<?php echo $idc;?>'></small></div>
<div class="col-auto ml-auto" title="Fecha de la calificación" data-toggle="tooltip">
<small class="text-muted" id="fecha_calif_vendedor<?php echo $idc;?>"></small>
</div>
</div>
<hr>
<div class="row"><b>Comprador:</b></div>
<?php
if(isset($valor->feedback->purchase)){
if($valor->feedback->purchase->status=='active'){
?>
<div class="row">
<div class="col-auto">
<?php
switch($valor->feedback->purchase->rating){
case 'positive':
?>
<span title="Positivo" data-toggle="tooltip">
<svg xmlns="http://www.w3.org/2000/svg" width="15px" class="calif-posit" viewBox="0 0 512 512"><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z"/></svg>
</span>
<?php break; case 'neutral': ?>
<span title="Neutral" data-toggle="tooltip">
<svg xmlns="http://www.w3.org/2000/svg" width="15px" class="calif-neutro" viewBox="0 0 512 512"><path d="M256 8C119.033 8 8 119.033 8 256s111.033 248 248 248 248-111.033 248-248S392.967 8 256 8zm80 248c0 44.112-35.888 80-80 80s-80-35.888-80-80 35.888-80 80-80 80 35.888 80 80z"/></svg>
</span>
<?php break; default: ?>
<span title="Negativo" data-toggle="tooltip">
<svg xmlns="http://www.w3.org/2000/svg" width="15px" class="calif-negat" viewBox="0 0 512 512"><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zM124 296c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h264c6.6 0 12 5.4 12 12v56c0 6.6-5.4 12-12 12H124z"/></svg>
</span>
<?php break; } ?>
</div>
<div id="text-calif-comp<?php echo $idc;?>"></div>
</div>
<div class="row">
<div class="col-auto"><small class="text-muted" id='fulfilled_comprador<?php echo $idc;?>'></small></div>
<div class="col-auto ml-auto" title="Fecha de la calificación" data-toggle="tooltip">
<small class="text-muted" id="fecha_calif_comprador<?php echo $idc;?>"></small>
</div>
</div>
<?php }elseif($valor->feedback->purchase->status=='inactive'){ ?>
<div class="row"><p class="text-info">Calificación Inactivada</p></div>
<?php } }else{ ?>
<div class="row"><p class="text-secondary ml-3">No ha calificado el cliente</p></div>
<?php } ?>
</div>
</div>
</div>
</div>
</div>
<script>
//Calificaiones
$("#calificaciones<?php echo $idc;?>").click(function(){
$("#text-calif-comp<?php echo $idc;?>").empty();$("#text-calif<?php echo $idc;?>").empty();
$("#fecha_calif_vendedor<?php echo $idc;?>").empty();$("#fecha_calif_comprador<?php echo $idc;?>").empty();
$('#fulfilled_comprador<?php echo $idc;?>').empty();$('#fulfilled_vendedor<?php echo $idc;?>').empty();
$.get('calificaciones.php',{order:"<?php echo $idc;?>"},verificar,'json');
function verificar(respuesta){
var meses=new Array(0,'Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic');
if(respuesta.error=='feedback_not_found'){
$("#text-calif<?php echo $idc;?>").append("<p class='text-muted'>No has calificado</p>");
}else{
if(respuesta.sale){
$("#text-calif<?php echo $idc;?>").append('<p>'+respuesta.sale.message+'</p>');
var date=respuesta.sale.date_created;
if(date.substr(5,2)>9){var fecha_calificacion=date.substr(8,2)+' de '+meses[date.substr(5,2)]+' a las '+date.substr(11,5);
}else{var fecha_calificacion=date.substr(8,2)+' de '+meses[date.substr(6,1)]+' a las '+date.substr(11,5);}
$("#fecha_calif_vendedor<?php echo $idc;?>").append(fecha_calificacion);
var fulfilled=respuesta.sale.fulfilled;
if(fulfilled){$('#fulfilled_vendedor<?php echo $idc;?>').append('Entregado');
}else{$('#fulfilled_vendedor<?php echo $idc;?>').append('No entregado');}
}else{$("#text-calif<?php echo $idc;?>").append("<p class='text-muted'>No has calificado</p>");}
if(respuesta.purchase){
if(respuesta.purchase.status=='active'){
if(respuesta.purchase.message){
$("#text-calif-comp<?php echo $idc;?>").append('<p>'+respuesta.purchase.message+'</p>');
var date=respuesta.purchase.date_created;
if(date.substr(5,2)>9){
var fecha_calificacion=date.substr(8,2)+' de '+meses[date.substr(5,2)]+' a las '+date.substr(11,5);
}else{var fecha_calificacion=date.substr(8,2)+' de '+meses[date.substr(6,1)]+' a las '+date.substr(11,5);}
$("#fecha_calif_comprador<?php echo $idc;?>").append(fecha_calificacion);
var fulfilled=respuesta.purchase.fulfilled;
if(fulfilled){$('#fulfilled_comprador<?php echo $idc;?>').append('Recibido');
}else{$('#fulfilled_comprador<?php echo $idc;?>').append('No recibido');}
}
}
}
}
}
});
$('#container_no<?php echo $idc;?>').hide();
var k<?php echo $idc;?>=0;
$("#no_entregar<?php echo $idc;?>").click(function(){if(k<?php echo $idc;?>==0){$('#container_no<?php echo $idc;?>').show();k<?php echo $idc;?>=1;}});
$("#si_entregar<?php echo $idc;?>").click(function(){$("#container_no<?php echo $idc;?>").hide();k<?php echo $idc;?>=0;});
$("#restock<?php echo $idc;?>").click(function(){$("#rest<?php echo $idc;?>").append("<label><input type='radio' name='restock' class='mb-2'> Reponer Stock.</label>");});
$("#restock2<?php echo $idc;?>").click(function(){$("#rest<?php echo $idc;?>").append("<label><input type='radio' name='restock' class='mb-2'> Reponer Stock.</label>");});
</script>
<!-- Calificar -->
<div class="modal fade" id="calificar_<?php echo $idc;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h6 class="modal-title" id="exampleModalLabel"><?php echo $valor->buyer->first_name . " " . $valor->buyer->last_name;?></h6>
<button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_calificar<?php echo $idc;?>"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div class="container">
<b>¿Ya entregaste el producto?</b>
<div class="row mt-2">
<div class="col-auto" id="select_full<?php echo $idc;?>">
<label><input type="radio" name="full<?php echo $idc;?>" value="true" class="mb-2" id="si_entregar<?php echo $idc;?>" checked> ¡Si, ya lo entregue!</label><br/>
<label><input type="radio" name="full<?php echo $idc;?>" value="false" class="mb-2" id="no_entregar<?php echo $idc;?>"> No, no lo voy a entregar.</label>
</div>
</div>
<div class="col-auto" id="container_no<?php echo $idc;?>">
<select name='reason' id="reason<?php echo $idc;?>">
<option>Elegir</option>
<option value='SELLER_OUT_OF_STOCK'>Me quedé sin stock</option>
<option value='SELLER_DIDNT_TRY_TO_CONTACT_BUYER' id='restock2<?php echo $idc;?>'>Decidí no venderlo</option>
<option value='BUYER_NOT_ENOUGH_MONEY' id='restock<?php echo $idc;?>'>El comprador se arrepintió</option>
</select>
</div>
<div id="rest<?php echo $idc;?>"></div>
<hr>
<div class="row"><b class="text-muted ml-2">Tu Calificación para el comprador.</b></div>
<div class="row mt-2">
<div class="col-auto" id="rating<?php echo $idc;?>">
<label><input type="radio" name="rat<?php echo $idc;?>" value="positive" class="mt-2" checked> Positivo</label><br/>
<label><input type="radio" name="rat<?php echo $idc;?>" value="neutral" class="mt-2"> Neutral</label><br/>
<label><input type="radio" name="rat<?php echo $idc;?>" value="negative" class="my-2" disabled> Negativo</label><small class="text-muted">No puedes calificar negativo a una venta que ya expiró.</small>
</div>
</div>
<div class="row mt-2">
<textarea class="col-auto textarea_calif" name="message" rows="4" maxlength="160" id="message_calif<?php echo $idc;?>"></textarea>
<small class="text-muted">Máximo 160 caracteres</small>
</div>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
<button type="button" class="btn btn-primary" id="calificar<?php echo $idc;?>">Calificar</button>
</div>
</div>
</div>
</div>
<script>
$('#container_no<?php echo $idc;?>').hide();
var k<?php echo $idc;?>=0;
$("#no_entregar<?php echo $idc;?>").click(function(){if(k<?php echo $idc;?>==0){$('#container_no<?php echo $idc;?>').show();k<?php echo $idc;?>=1;}});
$("#si_entregar<?php echo $idc;?>").click(function(){$("#container_no<?php echo $idc;?>").hide();k<?php echo $idc;?>=0;});
$("#restock<?php echo $idc;?>").click(function(){$("#rest<?php echo $idc;?>").append("<label><input type='radio' name='restock' class='mb-2'> Reponer Stock.</label>");});
$("#restock2<?php echo $idc;?>").click(function(){$("#rest<?php echo $idc;?>").append("<label><input type='radio' name='restock' class='mb-2'> Reponer Stock.</label>");});
$("#calificar<?php echo $idc;?>").click(function(){
var rating=$('#rating<?php echo $idc;?> input:checked').val(),message=$('#message_calif<?php echo $idc;?>').val();
var id=<?php echo $idc;?>,reason=$('#reason<?php echo $idc;?>').val(),fullfilled=$('#select_full<?php echo $idc;?> input:checked').val();
$.ajax({url:'calificar.php',type:'GET',data:{order_id:id,reason:reason,fulfilled:fullfilled,rating:rating,message:message},async:false,dataType:'json',
success: function(json){
if(typeof json.date_created!='undefined'){
const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
toast({type:'success',title:'¡Fue Calificado Exitosamente!'})
$('#close_calificar<?php echo $idc;?>').click();
$("#message_calif<?php echo $idc;?>").attr('disabled','disabled');
$("#calificar<?php echo $idc;?>").attr('disabled',true);
$("#calificar_button<?php echo $idc;?>").remove();
}else{
const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
toast({type:'error',title:'¡Hubo un problema! \n Inténtao de nuevo.'})
}
}
});
});
</script>
<!-- Modal Nickname -->
<div class="modal fade bd-example-modal-lg" id="user_<?php echo $idc;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<div id="title_head<?php echo $idc;?>"></div>
<button type="button" class="close" data-dismiss="modal" aria-label="Close" id="nic_close<?php echo $idc;?>"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div class="container-fluid">
<div class="row mb-2" id="contenido<?php echo $idc;?>"></div>
<div class="row">
<div class="col-auto"><b>Teléfono Principal:</b></div>
<div class="col-auto ml-auto"><span class="text-muted"><?php echo $valor->buyer->phone->area_code.' '.$valor->buyer->phone->number;?></span></div>
</div>
<div class="row">
<div class="col-auto"><b>Teléfono Secundario:</b></div>
<div class="col-auto ml-auto">
<span class="text-muted"><?php echo $valor->buyer->alternative_phone->area_code.' '.$valor->buyer->alternative_phone->number;?></span>
</div>
</div>
<hr>
<div class="row mb-2">
<div class="container">
<h6>Datos como vendedor</h6>
<div class="row justify-content-center">Reputación</div>
<div class="row justify-content-center" id="reput<?php echo $idc;?>"></div>
<div class="row justify-content-center">Transacciones</div>
<div class="row justify-content-center" id="trans<?php echo $idc;?>"></div>
<div class="row justify-content-center">Calificaciones</div>
<div class="row justify-content-center" id="calif<?php echo $idc;?>"></div>
<hr>
<div class="row">
<div class="col-auto ml-auto" data-toggle="tooltip" title="Como Vendedor">
<a class="btn btn-link" href="javascript:void(0)" id="ant_pub<?php echo $idc;?>" data-toggle="modal" data-target=".public-<?php echo $idc;?>-modal-lg">Ver Publicaciones del Cliente</a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- Modal Publicaciones -->
<div class="modal fade public-<?php echo $idc;?>-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel2" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content container">
<div class="modal-header">
<div class="container">
<div class="row">
<div class="col-11"><div class="row"><div><h5 id="titulo2<?php echo $idc;?>"></h5></div></div></div>
<button class="col-1 close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
</div>
</div>
<div class="modal-body text-muted" id="publicaciones<?php echo $idc;?>"></div>
</div>
</div>
</div>
<!-- Modal Mensajes -->
<div class="modal fade mensajes_<?php echo $idc;?>-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<div class="container">
<div class="row">
<div class="col-11">
<div class="row">
<div class="col-auto d-none d-sm-block">
<img class="img-fluid img-thumbnail rounded" src="<?php echo $img;?>" alt="<?php echo $title_p[$i];?>" width="70px" height="70px"/>
</div>
<h5 class="d-block d-sm-none" id="exampleModalLabel"><?php echo $titulo;?></h5>
<div class="col-auto">
<div class="row d-none d-sm-block"><h5 id="exampleModalLabel"><?php echo $titulo;?></h5></div>
<div class="row"><?php echo $valor->buyer->first_name.' '.$valor->buyer->last_name;?></div>
<small class="row text-muted d-none d-sm-block">
<?php
$dia=substr($fecha_compra,8,2);
$hora=substr($fecha_compra,11,5);
if(substr($fecha_compra,5,2)>9){$mes1=substr($fecha_compra,5,2);}else{$mes1=substr($fecha_compra,6,1);}
$mes1=$mes[$mes1];
echo 'Compra realizada el '.$dia.' de '.$mes1.' a las '.$hora;
?>
</small>
</div>
<small class="text-muted d-block d-sm-none"><?php echo 'Compra realizada el '.$dia.' de '.$mes1.' a las '.$hora;?></small>
</div>
</div>
<button class="col-1 close" type="button" data-dismiss="modal" aria-label="Close" id="mensajes_eliminar_<?php echo $idc;?>"><span aria-hidden="true">×</span></button>
</div>
</div>
</div>
<div class="modal-body text-muted" id="text<?php echo $idc;?>"></div>
<div class="modal-footer p-0 py-2">
<div class="container">
<div class="row">
<div class="col-10"><textarea class="textarea1" name="text" width="100%" id="text-message<?php echo $idc;?>" autofocus required></textarea></div>
<div class="col-2"><button class="btn btn-sm btn-success px-4" type="button" id="sendmessage<?php echo $idc;?>">Enviar</button></div>
</div>
</div>
</div>
</div>
</div>
</div>
<script>
    //Ver mensajes
    $("#men<?php echo $idc;?>").click(function(){
      $("#text<?php echo $idc;?>").empty();$('#text-message<?php echo $idc;?>').val('');
      var meses=[0,'Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
      var order_id=<?php echo $idc;?>,id_seller="<?php echo $id_user;?>";
      $.get('mensajes.php',{order_id:order_id},verificar,'json');
      function verificar(respuesta){
        if(respuesta.paging.total!=0){
          $.each(respuesta.messages.reverse(),function(i,resultado){
            var attachments='';
            if(resultado.message_attachments){
              $.each(resultado.message_attachments,function(i,resultadoatt){
                if(attachments==''){
                  attachments="<span class='text-primary cursor-pointer attachment' id='"+resultadoatt.filename+"'>"+resultadoatt.original_filename+"<input type='hidden' class='"+resultadoatt.type+"' id='"+resultadoatt.original_filename+"'></span>";
                }else{
                  attachments=attachments+' , '+"<span class='text-primary cursor-pointer attachment' id='"+resultadoatt.filename+"'>"+resultadoatt.original_filename+"<input type='hidden' class='"+resultadoatt.type+"' id='"+resultadoatt.original_filename+"'></span>";
                }
              });
            }
            var remitente=resultado.from.user_id,mensaje=resultado.text,auxiliar=mensaje.split('\n').length;
            for(var i=0;i<auxiliar;i++){mensaje=mensaje.replace('\n','<br>');}
            if(resultado.message_date.read){
              var $fecha_aux=resultado.message_date.read;
            }else if(resultado.message_date.notified){
              var $fecha_aux=resultado.message_date.notified;
            }else{
              var $fecha_aux=resultado.message_date.available;
            }
            var mes=meses[parseInt($fecha_aux.substr(5,2))],date_dia=$fecha_aux.substr(8,2),date_min=$fecha_aux.substr(14,2),date_hora_aux=$fecha_aux.substr(11,2);
            if(date_hora_aux>=04){date_hora_aux=date_hora_aux-04;}else{date_dia=date_dia-1;date_dia=date_dia.toString();date_hora_aux=20+date_hora_aux;}
            var fecha=date_dia+' de '+mes+' a las '+date_hora_aux.toString()+':'+date_min;
            var id_mensaje=resultado.message_id;
            if(id_seller==remitente){
              if(resultado.message_date.read){
                var ent="<span title='Leído' data-toggle='tooltip'><svg xmlns='http://www.w3.org/2000/svg' width='14px' class='svg-primary' viewBox='0 0 512 512'><path d='M504.5 171.95l-36.2-36.41c-10-10.05-26.21-10.05-36.2 0L192 377.02 79.9 264.28c-10-10.06-26.21-10.06-36.2 0L7.5 300.69c-10 10.05-10 26.36 0 36.41l166.4 167.36c10 10.06 26.21 10.06 36.2 0l294.4-296.09c10-10.06 10-26.36 0-36.42zM166.57 282.71c6.84 7.02 18.18 7.02 25.21.18L403.85 72.62c7.02-6.84 7.02-18.18.18-25.21L362.08 5.29c-6.84-7.02-18.18-7.02-25.21-.18L179.71 161.19l-68.23-68.77c-6.84-7.02-18.18-7.02-25.2-.18l-42.13 41.77c-7.02 6.84-7.02 18.18-.18 25.2l122.6 123.5z'/></svg>";
                  $("#text<?php echo $idc;?>").append("<div class='row mx-2 mb-1 border_respuesta'><div class='col-10 ml-auto respuesta'><div class='row'><div class='col-12'>"+mensaje+attachments+"</div><small class='col-auto ml-auto text-muted'>"+fecha+" "+ent+"</small></div></div></div>");
              }else if(resultado.message_date.notified){
                  var ent="<span title='Entregado' data-toggle='tooltip'><svg xmlns='http://www.w3.org/2000/svg' width='14px' class='svg-secondary' viewBox='0 0 512 512'><path d='M504.5 171.95l-36.2-36.41c-10-10.05-26.21-10.05-36.2 0L192 377.02 79.9 264.28c-10-10.06-26.21-10.06-36.2 0L7.5 300.69c-10 10.05-10 26.36 0 36.41l166.4 167.36c10 10.06 26.21 10.06 36.2 0l294.4-296.09c10-10.06 10-26.36 0-36.42zM166.57 282.71c6.84 7.02 18.18 7.02 25.21.18L403.85 72.62c7.02-6.84 7.02-18.18.18-25.21L362.08 5.29c-6.84-7.02-18.18-7.02-25.21-.18L179.71 161.19l-68.23-68.77c-6.84-7.02-18.18-7.02-25.2-.18l-42.13 41.77c-7.02 6.84-7.02 18.18-.18 25.2l122.6 123.5z'/></svg></svg></span>";
                  $("#text<?php echo $idc;?>").append("<div class='row mx-2 mb-1 border_respuesta'><div class='col-10 ml-auto respuesta'><div class='row'><div class='col-12'>"+mensaje+" "+attachments+"</div><small class='col-auto ml-auto text-muted'>"+fecha+" "+ent+"</small></div></div></div>");
                }else{
                  var env="<span title='Enviado' data-toggle='tooltip'><svg xmlns='http://www.w3.org/2000/svg' width='12px' class='svg-secondary' viewBox='0 0 512 512'><path d='M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z'/></svg></span>";
                  $("#text<?php echo $idc;?>").append("<div class='row mx-2 mb-1 border_respuesta'><div class='col-10 ml-auto respuesta'><div class='row'><div class='col-12'>"+mensaje+" "+attachments+"</div><small class='col-auto ml-auto text-muted'>"+fecha+" "+env+"</small></div></div></div>");
                }
            }else{
                if(resultado.message_date.read){
                  $("#text<?php echo $idc;?>").append("<div class='row mx-2 mb-1 border_respuesta'><div class='col-10 pregunta'><div class='row'><div class='col-12'>"+mensaje+" "+attachments+"</div><small class='col-auto mr-auto' title='Leído por ti'><svg xmlns='http://www.w3.org/2000/svg' width='14px' class='svg-primary' viewBox='0 0 512 512'><path d='M504.5 171.95l-36.2-36.41c-10-10.05-26.21-10.05-36.2 0L192 377.02 79.9 264.28c-10-10.06-26.21-10.06-36.2 0L7.5 300.69c-10 10.05-10 26.36 0 36.41l166.4 167.36c10 10.06 26.21 10.06 36.2 0l294.4-296.09c10-10.06 10-26.36 0-36.42zM166.57 282.71c6.84 7.02 18.18 7.02 25.21.18L403.85 72.62c7.02-6.84 7.02-18.18.18-25.21L362.08 5.29c-6.84-7.02-18.18-7.02-25.21-.18L179.71 161.19l-68.23-68.77c-6.84-7.02-18.18-7.02-25.2-.18l-42.13 41.77c-7.02 6.84-7.02 18.18-.18 25.2l122.6 123.5z'/></svg></small><div class='col-auto ml-auto text-muted'><small>"+fecha+"</small></div></div></div></div>");
                }else{
                  $("#text<?php echo $idc;?>").append("<div class='row mx-2 mb-1 border_respuesta'><div class='col-10 pregunta'><div class='row'><div class='col-12'>"+mensaje+" "+attachments+"</div><small class='col-auto cursor-pointer mr-auto' title='Marcar como leído'><span class='noleido<?php echo $idc;?>' id='"+id_mensaje+"'><svg xmlns='http://www.w3.org/2000/svg' width='14px' class='svg-secondary' viewBox='0 0 512 512'><path d='M504.5 171.95l-36.2-36.41c-10-10.05-26.21-10.05-36.2 0L192 377.02 79.9 264.28c-10-10.06-26.21-10.06-36.2 0L7.5 300.69c-10 10.05-10 26.36 0 36.41l166.4 167.36c10 10.06 26.21 10.06 36.2 0l294.4-296.09c10-10.06 10-26.36 0-36.42zM166.57 282.71c6.84 7.02 18.18 7.02 25.21.18L403.85 72.62c7.02-6.84 7.02-18.18.18-25.21L362.08 5.29c-6.84-7.02-18.18-7.02-25.21-.18L179.71 161.19l-68.23-68.77c-6.84-7.02-18.18-7.02-25.2-.18l-42.13 41.77c-7.02 6.84-7.02 18.18-.18 25.2l122.6 123.5z'/></svg></span></small><div class='col-auto ml-auto text-muted'><small>"+fecha+"</small></div></div></div></div>");
                }
              }
          });
        }else{$("#text<?php echo $idc;?>").append("<div class='col-12'>No hay mensajes en esta venta</div>");}
      }
    });
    //Adjuntos
    $(document).on('click',"#text<?php echo $idc;?> .attachment", function(){
      var attachment=$(this).attr('id'),type=$("#text<?php echo $idc;?> input:hidden").attr('id'),title=$("#text<?php echo $idc;?> input:hidden").attr("class");
      $.ajax({url:'attachment.php',type:'GET',data:{attachment:attachment,type:type,title:title},async:false,dataType:'text',
      success: function(json){}
    });
    });
    //Marcar como leido
    $(document).on('click',".noleido<?php echo $idc;?>",function(){
      var aux=$(this).attr('id');
      $.ajax({url:'marcar.php',type:'GET',data:{id_message:aux},async:false,dataType:'json',
      success:function(json){
        var status=json.status;
        if(typeof status!='undefined'){
          status=json.status;
          if(status==200){
            const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
            toast({type:'success',title:'¡Fue marcado Exitosamente!'})
            $('#mensajes_eliminar_<?php echo $idc;?>').click();
          }else{
            const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
            toast({type:'error',title:'¡Hubo un problema! \n Inténtalo de nuevo.'})
          }
        }
      }
    });
    });
//Enviar mensajes
$(document).on("click","#sendmessage<?php echo $idc;?>",function(){
  var text= $("#text-message<?php echo $idc;?>").val();
  $.get('send_message.php',{order:<?php echo $idc;?>,text:text},publicaciones,'text');
  function publicaciones(respuesta){
    if(respuesta==1){
      const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
      toast({type:'success',title:'¡El mensaje fue enviado Exitosamente!'});
      $('#mensajes_eliminar_<?php echo $idc;?>').click();
    }else{
      const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
      toast({type:'error',title:'¡Hubo un problema! \n Inténtalo de nuevo'});
    }
  }
});
</script>
<!-- Preguntas -->
<div class="modal fade lg_questions<?php echo $idc;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content container">
<div class="modal-header">
<div class="container">
<div class="row"><h5 class="modal-title" id="exampleModalLabel"><?php echo $titulo;?></h5></div>
<div class="row text-muted"><small id="sub<?php echo $valor->id;?>"></small></div>
</div>
<button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body" id="container_questions<?php echo $idc;?>"></div>
<div class="modal-footer"><small class="text-muted">Preguntas del usuario en esta publicación antes de realizar la compra.</small></div>
</div>
</div>
</div>
<script>
//Nickname
$("#nic<?php echo $idc;?>").click(function(){
$("#titulo<?php echo $idc;?>").remove();$("#contenido2<?php echo $idc;?>").remove();
$("#nivel_reput<?php echo $idc;?>").remove();$("#trans_div<?php echo $idc;?>").remove();
$("#calif_div<?php echo $idc;?>").remove();
var user=<?php echo $valor->buyer->id;?>,url="https://api.mercadolibre.com/users/"+user;
$("#title_head<?php echo $idc;?>").append("<h6 class='modal-title' id='titulo<?php echo $idc;?>'></h6>");
$("#contenido<?php echo $idc;?>").append("<div class='container' id='contenido2<?php echo $idc;?>'></div>");
$('#titulo2<?php echo $idc;?>').append("<?php echo $valor->buyer->first_name." ".$valor->buyer->last_name;?>");//en Modal Publicaciones
$("#titulo2<?php echo $idc;?>").addClass("<?php echo $valor->buyer->nickname;?>");
$.getJSON(url,function(datos){
$("#titulo<?php echo $idc;?>").append("<a href='"+datos.permalink+"' target='_blank'><?php echo $valor->buyer->first_name." ".$valor->buyer->last_name;?></a>");
if(datos.address.city){var city=datos.address.city;}else{var city="";}
var state='';
switch(datos.address.state){
case 'VE-A':state='Distrito Capital';break;case 'VE-C':state='Apure';break;case 'VE-D':state='Aragua';break;case 'VE-B':state='Anzoátegui';
break;case 'VE-E':state='Barinas';break;case 'VE-F':state='Bolívar';break;case 'VE-G':state='Carabobo';break;case 'VE-H':state='Cojedes';
break;case 'VE-I':state='Falcón';break;case 'VE-J':state='Guárico';break;case 'VE-K':state='Lara';break;case 'VE-L':state='Mérida';
break;case 'VE-M':state='Miranda';break;case 'VE-N':state='Monagas';break;case 'VE-O':state='Nueva Esparta';break;case 'VE-P':
state='Portuguesa';break;case 'VE-R':state='Sucre';break;case 'VE-S':state='Táchira';break;case 'VE-T':state='Trujillo';break;
case 'VE-V':state='Zulia';break;case 'VE-W':state='Dependencias Federales';break;case 'VE-X':state='Vargas';break;case 'VE-Y':
state='Delta Amacuro';break;case 'VE-Z':state='Amazonas';break;case 'VE-U':state='Yaracuy';break;default:state=' ';break;
}
if(datos.address.state){if(datos.address.city){var direccion='Estado '+state+', '+city+'.';}}else{var direccion='Sin Información.';}
var fecha_registro=datos.registration_date.substr(0,10);
$("#contenido2<?php echo $idc;?>").append("<div class='row'><div class='col-auto'><b>Dirección:</b></div><div class='col-auto ml-auto'><span class='text-muted'>"+direccion+"</span></div></div><div class='row'><div class='col-auto'><b>Usuario de Mercado Libre desde:</b></div><div class='col-auto ml-auto'><span class='text-muted'> "+fecha_registro+".</span></div></div>");
switch(datos.seller_reputation.level_id){
case '1_red':
var nivel="<div class='container mb-2' id='nivel_reput<?php echo $idc;?>'><div class='row justify-content-center'><div class='col-auto'><span class='mal bord' style='background-color: #ff191d;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span></div></div></div>";
break;
case '2_orange':
var nivel="<div class='container mb-2' id='nivel_reput<?php echo $idc;?>'><div class='row justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #ffc6a5;'></span><span class='medio bord' style='background-color: #ff8419;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span></div></div></div>";
break;
case '3_yellow':
var nivel="<div class='container mb-2' id='nivel_reput<?php echo $idc;?>'><div class='row  justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med bord' style='background-color: #ffff36;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span></div></div></div>";
break;
case '4_light_green':
var nivel="<div class='container mb-2' id='nivel_reput<?php echo $idc;?>'><div class='row  justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien bord' style='background-color: #58ff3f;'></span><span class='exc' style='background-color: #d2ffb0;'></span></div></div></div>";
break;
case '5_green':
var nivel="<div class='container mb-2' id='nivel_reput<?php echo $idc;?>'><div class='row  justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc bord' style='background-color: #00ca00;'></span></div></div></div>";
break;
default:
var nivel="<div class='container mb-2' id='nivel_reput<?php echo $idc;?>'><div class='row  justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #beccc1;'></span><span class='medio' style='background-color: #beccc1;'></span><span class='med' style='background-color: #beccc1;'></span><span class='bien' style='background-color: #beccc1;'></span><span class='exc' style='background-color: #beccc1;'></span></div></div></div>";
break;
}
$("#reput<?php echo $idc;?>").append(nivel);
if(datos.seller_reputation.transactions.total==0){
$("#trans<?php echo $idc;?>").append("<div id='trans_div<?php echo $idc;?>'><b class='mb-2'>Sin Transacciones.</b></div>");
$("#calif<?php echo $idc;?>").append("<div id='calif_div<?php echo $idc;?>'><b class='mb-2'>Sin Calificaciones.</b></div>");
}else{
var positivo=datos.seller_reputation.transactions.ratings.positive*100;
var neutral=datos.seller_reputation.transactions.ratings.neutral*100;
var negativo=datos.seller_reputation.transactions.ratings.negative*100;
$("#trans<?php echo $idc;?>").append("<div class='container mb-2' id='trans_div<?php echo $idc;?>'><div class='row  justify-content-center'><b>"+datos.seller_reputation.transactions.total+"(<span class='text-success' title='Completadas' data-toggle='tooltip'>"+datos.seller_reputation.transactions.completed+"</span>)</b></div></div>");
$("#calif<?php echo $idc;?>").append("<div class='container' id='calif_div<?php echo $idc;?>'><div class='progress'><div class='progress-bar bg-success' role='progressbar' style='width: "+positivo+"%' aria-valuenow='"+positivo+"' aria-valuemin='0' aria-valuemax='100'>"+positivo+"%</div><div class='progress-bar' role='progressbar' style='width:"+neutral+"%' aria-valuenow='"+neutral+"' aria-valuemin='0' aria-valuemax='100'>"+neutral+"%</div><div class='progress-bar bg-danger' role='progressbar' style='width: "+negativo+"%' aria-valuenow='"+negativo+"' aria-valuemin='0' aria-valuemax='100'>"+negativo+"%</div></div></div>");
}
});
});
//Ver publicaciones del cliente
$("#ant_pub<?php echo $idc;?>").click(function(){
$("#publicaciones<?php echo $idc;?>").empty();$('#auxiliar_publicaciones<?php echo $idc;?>').remove();
var nickname=$("#titulo2<?php echo $idc;?>").attr('class');nickname=encodeURI(nickname);
$.get('publicaciones_user.php',{nick:nickname},publicaciones,'json');
function publicaciones(respuesta){
$("#titulo2<?php echo $idc;?>").append("<small class='col-auto ml-auto' id='auxiliar_publicaciones<?php echo $idc;?>'>"+respuesta.paging.total+" Publicacion(es) Activas.</small>");
if(respuesta.paging.total==0){$("#publicaciones<?php echo $idc;?>").append("<div class='row text-success'>El cliente no tiene publicaciones activas</div>");
}else{
$.each(respuesta.results, function(i, resultado){$("#publicaciones<?php echo $idc;?>").append("<div class='row align-items-center justify-content-center'><img class='img-fluid img-thumbnail imagen_publicacion_user' src='"+resultado.thumbnail+"'><a href='"+resultado.permalink+"' class='col-sm-7 text-center' target='_blank'>"+resultado.title+"</a><div class='col-sm-1 text-center text-success'>"+resultado.price+"&nbspBs.</div><small class='col-sm-2 textcenter'><b>"+resultado.available_quantity+"&nbspDisponible(s)</b></small></div><hr>");});
}
}
});
//Preguntas
$("#questions<?php echo $idc;?>").click(function(){
$("#container_questions<?php echo $idc;?>").empty();
var meses=[0,'Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
var item="<?php echo $id_publicac;?>",client=<?php echo $id_user_buyer;?>;
$.get('questions.php',{item:item,client:client},preguntas,'json');
function preguntas(respuesta){
document.getElementById("sub<?php echo $valor->id;?>").innerHTML=respuesta.total+' preguntas obtenidas.';
if(respuesta.questions.length>0){
$.each(respuesta.questions,function(i,resultado){
var texto=resultado.text,date_preg=resultado.date_created,dia_preg=date_preg.substr(8,2),hora_preg =date_preg.substr(11,5);
if(date_preg.substr(5,2)>9){var mes_preg=date_preg.substr(5,2);}else{var mes_preg=date_preg.substr(6,1);}
var fecha_preg=dia_preg+' de '+meses[mes_preg]+' a las '+hora_preg,answer= resultado.answer.text;
var date_resp=resultado.answer.date_created,dia_resp=date_resp.substr(8,2),hora_resp =date_resp.substr(11,5);
if(date_resp.substr(5,2)>9){var mes_resp=date_resp.substr(5,2);}else{var mes_resp=date_resp.substr(6,1);}
var fecha_resp=dia_resp+' de '+meses[mes_resp]+' a las '+hora_resp;
$("#container_questions<?php echo $idc;?>").append("<div class='row mb-3 border_respuesta'><div class='col-10 pregunta mb-2'><div class='row'><div class='col-12'>"+texto+"</div><div class='col-auto ml-auto text-muted'><small>"+fecha_preg+"</small></div></div></div><div class='col-10 ml-auto respuesta'><div class='row'><div class='col-12'>"+answer+"</div><div class='col-auto ml-auto text-muted'><small>"+fecha_resp+"</small></div></div></div></div>");
});
}else{$("#container_questions<?php echo $idc;?>").append("<div class='ml-3 row text-muted'>No hizo preguntas antes de ofertar</div>");}
}
});
</script>
<?php } } } ?>
<hr>
<div class="container mt-2">
<div class="row justify-content-center">
<?php
if($total_ventas>50){
$NroPag=ceil($total_ventas/50);
?>
<nav aria-label="Page navigation example">
<ul class="pagination">
<?php
if(isset($_GET['page']) & !empty($_GET['page'])){
$pagina=$_GET['page'];
if($pagina>6 && $NroPag>6){
?>
<li class="page-item">
<a class="page-link" href="cerradas.php?page=<?php echo ($pagina-6);?>" aria-label="Previous">
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
<li class="page-item active"><a class="page-link" href="cerradas.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
<?php }else{ ?>
<li class="page-item"><a class="page-link" href="cerradas.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
<?php } } } }else{
for($i=1;$i<11;$i++){
if(($i-1)==$NroPag){
break;
}else{
if($i==$pagina){
?>
<li class="page-item active"><a class="page-link" href="cerradas.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
<?php }else{?>
<li class="page-item"><a class="page-link" href="cerradas.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
<?php } } } } ?>
<?php if($NroPag>10 & $pagina<($NroPag-4)){?>
<li class="page-item">
<a class="page-link" href="cerradas.php?page=<?php echo (5+$pagina);?>" aria-label="Next">
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
<?php }else{?>
<div class="container mt-3">
<div class="alert alert-success">
<strong>¡No tienes ventas Cerradas!</strong>
</br>
</div>
</div>
<?php }?>
</div>
</div>
<script>$(document).ready(function(){$('#ventas_cerradas').append(<?php echo $total_ventas;?>);});</script>
<?php include '../common/footer.php';?>
<script src='../vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
<script src='../vendor/jquery-easing/jquery.easing.min.js'></script>
<script src='../js/sb-admin.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@7.29.0/dist/sweetalert2.all.min.js'></script>
</div>
</body>
</html>
