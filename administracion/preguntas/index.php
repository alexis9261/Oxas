<?php
session_start();
require '../common/meli.php';require '../common/conexion.php';require '../common/take_at.php';
?>
<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='utf-8'>
<meta http-equiv='X-UA-Compatible' content='IE=edge'>
<meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
<meta name='description' content=''>
<meta name='author' content='EutuxiaWeb'>
<link rel='shortcut icon' href='../../img/favicon.ico'>
<title>Oxas - Preguntas Pendientes</title>
<link href='../vendor/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
<link href='../css/sb-admin.min.css' rel='stylesheet'>
<link href='../css/style.css' rel='stylesheet'>
<script src='../vendor/jquery/jquery.min.js'></script>
</head>
<body class='fixed-nav sticky-footer bg-dark' id='page-top'>
<?php include '../common/navbar.php';
$ch=curl_init();
if(isset($_GET['page']) & !empty($_GET['page'])){
$page_offset=$_GET['page']-1;
if($page_offset>=21){$offset=0;}else{$offset=$page_offset*50;}
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/questions/search?seller_id='.$id_user.'&status=unanswered&access_token='.$AccessToken.'&offset='.$offset);
}else{curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/questions/search?seller_id='.$id_user.'&status=unanswered&access_token='.$AccessToken);}
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);
$total_preguntas=$consulta->total;
$preguntas=$consulta->questions;
?>
<div class='content-wrapper'>
<div class='container-fluid'>
<?php if($total_preguntas!=0){ ?>
<div class='breadcrumb'>
<div class='text-danger col-12'>Tienes <strong><?php echo $total_preguntas;?></strong> Preguntas Pendientes.<br></div>
<small class='text-secondary col-12'>Ten en cuenta que estas preguntas no han sido respondidas, debido a que no tienes configurada ninguna respuesta automática para ellas.</small>
</div>
<?php
$items_array=array();
foreach($preguntas as $valor){array_push($items_array,$valor->item_id);}
$items_array=array_unique($items_array);
$total_items=count($items_array);
$ultimo_elemento=array_search(end($items_array),$items_array);
if($total_items==1){$contador=1;$band=1;}else{$contador=0;$band=0;}
while($contador!=$ultimo_elemento){
$array_publicaciones_veinte=array();$aux=$contador+19;
if($band==1){
array_push($array_publicaciones_veinte,$items_array[0]);$contador=0;
}else{
for($i=$contador;$i<=$aux;$i++){
if(isset($items_array[$i])){array_push($array_publicaciones_veinte,$items_array[$i]);}
if($contador==$ultimo_elemento){break;}
++$contador;
}
}
$str_public=implode(',',$array_publicaciones_veinte);
$array_publicaciones_veinte=array();
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items?ids='.$str_public.'&attributes=id,title,permalink,thumbnail,price,available_quantity,status&access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);
foreach($consulta as $valuex){
$id_publicacion=$valuex->body->id;$thumbnail=$valuex->body->thumbnail;
$title=$valuex->body->title;$permalink=$valuex->body->permalink;
$price=$valuex->body->price;$quantity=$valuex->body->available_quantity;
$status=$valuex->body->status;
?>
<div class='container-fluid'>
<div class='row'>
<div class='col-1 d-none d-sm-block'>
<img class='img-fluid img-thumbnail rounded' src='<?php echo $thumbnail;?>' alt='<?php echo $title;?>' width='50px' height='50px' data-toggle='tooltip'>
</div>
<div class='col-11'>
<div class='row'>
<div>
<a href='<?php echo $permalink;?>' target='_blank'><?php echo $title;?></a> -
<?php if($status=='paused'){ ?><small class="text-primary">Publicación Pausada</small>
<?php }elseif($status=='active'){ ?><small class="text-success">Publicación Activa</small>
<?php }elseif($status=='under_review'){ ?><small class="text-warning">Publicación Pendiente por revisar</small>
<?php }elseif($status=='closed'){ ?><small class="text-danger">Publicación Finalizada</small><?php } ?>
</div>
<div class='col-auto ml-auto d-none d-sm-block'><span class='text-muted'><?php $decimales=number_format($price,2,',','.'); echo "$decimales";?> Bs.</span></div>
</div>
<div class='row'><div><small class='text-muted' title='Stock' data-toggle='tooltip'>Cantidad: <?php echo $quantity;?></small></div></div>
</div>
</div>
</div>
<?php
foreach($preguntas as $value){
if($value->item_id==$id_publicacion){
$idp=$value->id;$id_cliente=$value->from->id;$anteriores=$value->from->answered_questions;
?>
<section class='container bg-preguntas my-1 rounded'>
<div class='row mb-2'>
<div class='col-10 ml-2'><small><?php echo $value->text;?></small></div>
<div class='col-auto ml-auto align-self-center'>
<?php
$diasmes=[0,31,28,31,30,31,30,31,31,30,31,30,31];$actual=getdate();
if(substr($value->date_created,5,2)>9){
$mes_pregunta=substr($value->date_created,5,2);
}else{$mes_pregunta=substr($value->date_created,6,1);}
if($actual['mon']==$mes_pregunta){
if($actual['mday']==substr($value->date_created,8,2)){
if($actual['hours']==substr($value->date_created,11,2)){
$resta=(int)$actual['minutes'] - (int)substr($value->date_created,14,2);
?>
<small class='text-muted'><?php echo "Hace ".$resta.' minutos';?></small>
<?php
}else{
$resta=(int)$actual['hours'] - (int)substr($value->date_created,11,2);
?>
<small class='text-muted'><?php echo 'Hace '.$resta.' hora(s)';?></small>
<?php
}
}else{
$resta=(int)$actual['mday'] - (int)substr($value->date_created,8,2);
?>
<small class='text-muted'><?php echo 'Hace '.$resta.' día(s)';?></small>
<?php
}
}elseif($actual['mon'] > $mes_pregunta){
$resta=(int)$actual['mday'] + $diasmes[$mes_pregunta] - (int)substr($value->date_created,8,2);
?>
<small class='text-muted'><?php echo 'Hace '.$resta.' día(s)';?></small>
<?php
}else{
$resta=(int)$actual['mday'] + $diasmes[$mes_pregunta] - (int)substr($value->date_created,8,2);
?>
<small class='text-muted'><?php echo 'Hace '.$resta.' día(s)';?></small>
<?php } ?>
</div>
<div class="col-auto">
<svg xmlns="http://www.w3.org/2000/svg" width='12px' class="svg-secondary cursor-pointer" id='up<?php echo $idp;?>' viewBox="0 0 320 512"><path d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z"/></svg>
<svg xmlns="http://www.w3.org/2000/svg" width='12px' class="svg-secondary cursor-pointer" id='down<?php echo $idp;?>' viewBox="0 0 320 512"><path d="M177 159.7l136 136c9.4 9.4 9.4 24.6 0 33.9l-22.6 22.6c-9.4 9.4-24.6 9.4-33.9 0L160 255.9l-96.4 96.4c-9.4 9.4-24.6 9.4-33.9 0L7 329.7c-9.4-9.4-9.4-24.6 0-33.9l136-136c9.4-9.5 24.6-9.5 34-.1z"/></svg>
</div>
</div>
<div class="container-fluid" id='contenedor_<?php echo $idp;?>'>
<div class='row px-3'>
<textarea class='col-auto textarea_calif' rows='2' name='respuesta' required placeholder='Respuesta' id='textarea<?php echo $idp;?>'></textarea>
</div>
<div class='row'>
<div class='col-auto'>
<a class='enlace' href='javascript:void(0)' id='ant<?php echo $idp;?>' data-toggle='modal' data-target='.ant-<?php echo $idp;?>-modal-lg'><small title='Del mismo usuario, en esta publicacion' data-toggle='tooltip'>Ver anteriores (<?php echo $anteriores;?>)</small></a>
</div>
<div class='col-auto'>
<small><a class='enlace' href='javascript:void(0)' id='nic<?php echo $idp;?>' data-toggle='modal' data-target='#user_<?php echo $idp;?>'>Ver cliente</a></small>
</div>
<div class='col-auto offset-md-3'><button type='button' class='btn btn-link btn-sm' id='responder<?php echo $idp;?>'>Responder</button></div>
<div class='col-auto ml-auto'><button class='btn btn-link btn-sm text-danger' id='eliminar_ok<?php echo $idp;?>'>Eliminar Pregunta</button></div>
</div>
</div>
<input type='hidden' name='id_p' id='id_p<?php echo $idp;?>' value="<?php echo $idp;?>">
</section>
<script>
$('#contenedor_<?php echo $idp;?>').hide();$('#up<?php echo $idp;?>').show();$('#down<?php echo $idp;?>').hide();
$(document).on('click',"#down<?php echo $idp;?>",function(){$('#contenedor_<?php echo $idp;?>').hide();$('#down<?php echo $idp;?>').hide();$('#up<?php echo $idp;?>').show();});
$(document).on('click',"#up<?php echo $idp;?>",function(){$('#contenedor_<?php echo $idp;?>').show();$('#up<?php echo $idp;?>').hide();$('#down<?php echo $idp;?>').show();});
</script>
<!-- Anteriores-->
<div class='modal fade ant-<?php echo $idp;?>-modal-lg' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel2' aria-hidden='true'>
<div class='modal-dialog modal-lg'>
<div class='modal-content container'>
<div class='modal-header'>
<div class='container'>
<div class='row'>
<div class='col-11'>
<div class='row'>
<div class='col-auto d-none d-sm-block'>
<img class='img-fluid img-thumbnail rounded' src='<?php echo $thumbnail;?>' alt='<?php echo $title;?>' width='70px' height='70px'/>
</div>
<div><h5 class'modal-title' id='exampleModalLabel'><?php echo $title;?></h5></div>
</div>
</div>
<button class='col-1 close' type='button' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>×</span></button>
</div>
</div>
</div>
<div class'modal-body text-muted' id='text<?php echo $idp;?>'></div>
</div>
</div>
</div>
<!-- Ver cliente -->
<div class="modal fade bd-example-modal-lg" id="user_<?php echo $idp;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<div id="title_head<?php echo $idp;?>"></div>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div class="container-fluid">
<div class="row mb-2" id="contenido<?php echo $idp;?>"></div>
<hr>
<div class="row mb-2">
<div class="container">
<h6>Datos como vendedor</h6>
<div class="row justify-content-center">Reputación</div>
<div class="row justify-content-center" id="reput<?php echo $idp;?>"></div>
<div class="row justify-content-center">Transacciones</div>
<div class="row justify-content-center" id="trans<?php echo $idp;?>"></div>
<div class="row justify-content-center">Calificaciones</div>
<div class="row justify-content-center" id="calif<?php echo $idp;?>"></div>
</div>
</div>
<hr>
<div class="row">
<div class="col-auto ml-auto" data-toggle="tooltip" title="Como Vendedor">
<a class="btn btn-link" href="javascript:void(0)" id="ant_pub<?php echo $idp;?>" data-toggle="modal" data-target=".public-<?php echo $idp;?>-modal-lg">Ver Publicaciones del Cliente</a>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- Publicaciones-->
<div class="modal fade public-<?php echo $idp;?>-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel2" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content container">
<div class="modal-header">
<div class="container">
<div class="row">
<div class="col-11"><div class="row"><div id="titulo2<?php echo $idp;?>"></div></div></div>
<button class="col-1 close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
</div>
</div>
<div class="modal-body text-muted" id="publicaciones<?php echo $idp;?>"></div>
<div class="modal-footer"><small class="text-muted">Se mostrarán máximo 50 publicaciones.</small></div>
</div>
</div>
</div>
<script>
//Responder
$("#responder<?php echo $idp;?>").click(function(){
var aux=$("#textarea<?php echo $idp;?>").val(),id_preg=$("#id_p<?php echo $idp;?>").val();
$.ajax({url:'responder.php',type:'GET',data:{respuesta:aux,id_p:id_preg},async:false,dataType:'json',
success: function(json){
var status=json.status;
if(typeof status!='undefined'){
status=json.status;
if(status=='ANSWERED'){
const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
toast({type:'success',title:'¡Fue respondida Exitosamente!'})
$("#textarea<?php echo $idp;?>").attr('disabled','disabled');
$("#responder<?php echo $idp;?>").attr('disabled',true);$("#eliminar_ok<?php echo $idp;?>").attr('disabled',true);
}else{
const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:5000});
if(json.error=='not_active_item'){
toast({type:'error',title:'¡Debes activar la publicación para responder la pregunta! \n Tienes la publicación pausada'})
}else if(json.error=='text_is_blank'){
toast({type:'error',title:'¡Debes enviar una respuesta! \n Inténtalo de nuevo'})
}else{toast({type:'error',title:'¡Hubo un pequeño problema! \n Inténtalo de nuevo'})}
}
}
}
});
});
//Eliminar
$("#eliminar_ok<?php echo $idp;?>").click(function(){
$.get('eliminar.php',{id_p:<?php echo $idp;?>},verificar,'json');
function verificar(respuesta){
if(respuesta[0]=='Question deleted.'){
const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3500});
toast({type:'success',title:'¡La pregunta fue eliminada Exitosamente!'})
$("#textarea<?php echo $idp;?>").attr('disabled','disabled');
$("#responder<?php echo $idp;?>").attr('disabled',true);$("#eliminar_ok<?php echo $idp;?>").attr('disabled',true);
}else{
const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
toast({type:'error',title:'¡Hubo un pequeño problema! \n Inténtalo de nuevo'})
}
}
});
//Ver preguntas anteriores
$("#ant<?php echo $idp;?>").click(function(){
$("#text<?php echo $idp;?>").empty();
$.get('anteriores.php',{item:"<?php echo $id_publicacion;?>",id_cliente:<?php echo $id_cliente;?>},verificar,'json');
function verificar(respuesta){
var meses=[' ','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
if(respuesta.total==0){
$("#text<?php echo $idp;?>").append("<div class='row mb-3 text-muted ml-3'><div>Este usuario no ha preguntado antes en esta publicación.</div></div>");
}else{
$.each(respuesta.questions,function(i,resultado){
var texto=resultado.text,date_preg=resultado.date_created,dia_preg=date_preg.substr(8,2),hora_preg =date_preg.substr(11,5);
if(date_preg.substr(5,2)>9){var mes_preg=date_preg.substr(5,2);
}else{var mes_preg=date_preg.substr(6,1);}
var fecha_preg=dia_preg+' de '+meses[mes_preg]+' a las '+hora_preg;
var answer= resultado.answer.text,date_resp=resultado.answer.date_created,dia_resp=date_resp.substr(8,2),hora_resp =date_resp.substr(11,5);
if(date_resp.substr(5, 2)>9){var mes_resp=date_resp.substr(5,2);}else{var mes_resp=date_resp.substr(6,1);}
var fecha_resp=dia_resp+' de '+meses[mes_resp]+' a las '+hora_resp;
$("#text<?php echo $idp;?>").append("<div class='row mb-3 border_respuesta mx-2'><div class='col-10 pregunta mb-2'><div class='row'><div class='col-12'>"+texto+"</div><div class='col-auto ml-auto text-muted'><small>"+fecha_preg+"</small></div></div></div><div class='col-10 ml-auto respuesta'><div class='row'><div class='col-12'>"+answer+"</div><small class='col-auto ml-auto text-muted'>"+fecha_resp+"</small></div></div></div>");
});
}
};
});
//Nickname
$("#nic<?php echo $idp;?>").click(function(){
$("#title_head<?php echo $idp;?>").empty();$("#contenido<?php echo $idp;?>").empty();$("#titulo2<?php echo $idp;?>").empty();
$("#reput<?php echo $idp;?>").empty();$("#trans<?php echo $idp;?>").empty();$("#calif<?php echo $idp;?>").empty();
var user=<?php echo $id_cliente;?>,url='https://api.mercadolibre.com/users/'+user;
$.getJSON(url,function(datos){
$("#contenido<?php echo $idp;?>").append("<input type='hidden' value='"+datos.nickname+"' id='nickname_<?php echo $idp;?>'>");
$('#titulo2<?php echo $idp;?>').append("<h5 class="+datos.nickname+" id='titulo22<?php echo $idp;?>'>"+datos.nickname+"</h5>");//en Modal Publicaciones
$("#title_head<?php echo $idp;?>").append("<a href='"+datos.permalink+"' target='_blank'>"+datos.nickname+"</a>");
var city = datos.address.city ? datos.address.city : '',state='';
switch(datos.address.state){
case 'VE-A':state='Distrito Capital';break;case 'VE-C':state='Apure';break;case 'VE-D':state='Aragua';break;case 'VE-B':state='Anzoátegui';
break;case 'VE-E':state='Barinas';break;case 'VE-F':state='Bolívar';break;case "VE-G":state="Carabobo";break;case "VE-H":state="Cojedes";
break;case "VE-I":state="Falcón";break;case "VE-J":state="Guárico";break;case "VE-K":state="Lara";break;case "VE-L":state="Mérida";
break;case "VE-M":state="Miranda";break;case "VE-N":state="Monagas";break;case "VE-O":state="Nueva Esparta";break;case "VE-P":
state="Portuguesa";break;case "VE-R":state="Sucre";break;case "VE-S":state="Táchira";break;case "VE-T":state="Trujillo";break;
case "VE-V":state="Zulia";break;case "VE-W":state="Dependencias Federales";break;case "VE-X":state="Vargas";break;case "VE-Y":
state="Delta Amacuro";break;case "VE-Z":state="Amazonas";break;case "VE-U":state="Yaracuy";break;default:state=" ";break;
}
if(datos.address.state){if(datos.address.city){var direccion='Estado '+state+', '+city+'.';}}else{var direccion='Sin Información.';}
var fecha_registro=datos.registration_date.substr(0,10);
$("#contenido<?php echo $idp;?>").append("<div class='row'><div class='col-auto'><b>Dirección:</b></div><div class='col-auto ml-auto'><span class='text-muted'>"+direccion+"</span></div></div><div class='row'><div class='col-auto'><b>Usuario de Mercado Libre desde:</b></div><div class='col-auto ml-auto'><span class='text-muted'> "+fecha_registro+".</span></div></div>");
switch(datos.seller_reputation.level_id){
case '1_red':
var nivel="<div class='container mb-2'><div class='row justify-content-center'><div class='col-auto'><span class='mal bord' style='background-color: #ff191d;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span></div></div></div>";
break;
case '2_orange':
var nivel="<div class='container mb-2'><div class='row justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #ffc6a5;'></span><span class='medio bord' style='background-color: #ff8419;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span></div></div></div>";
break;
case '3_yellow':
var nivel="<div class='container mb-2'><div class='row  justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med bord' style='background-color: #ffff36;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span></div></div></div>";
break;
case '4_light_green':
var nivel="<div class='container mb-2'><div class='row  justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien bord' style='background-color: #58ff3f;'></span><span class='exc' style='background-color: #d2ffb0;'></span></div></div></div>";
break;
case '5_green':
var nivel="<div class='container mb-2'><div class='row  justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc bord' style='background-color: #00ca00;'></span></div></div></div>";
break;
default:
var nivel="<div class='container mb-2'><div class='row  justify-content-center'><div class='col-auto'><span class='mal' style='background-color: #beccc1;'></span><span class='medio' style='background-color: #beccc1;'></span><span class='med' style='background-color: #beccc1;'></span><span class='bien' style='background-color: #beccc1;'></span><span class='exc' style='background-color: #beccc1;'></span></div></div></div>";
break;
}
$("#reput<?php echo $idp;?>").append(nivel);
if(datos.seller_reputation.transactions.total==0){
$("#trans<?php echo $idp;?>").append("<div><b class='mb-2'>Sin Transacciones.</b></div>");
$("#calif<?php echo $idp;?>").append("<div><b class='mb-2'>Sin Calificaciones.</b></div>");
}else{
var positivo=datos.seller_reputation.transactions.ratings.positive*100;
var neutral=datos.seller_reputation.transactions.ratings.neutral*100;
var negativo=datos.seller_reputation.transactions.ratings.negative*100;
$("#trans<?php echo $idp;?>").append("<div class='container mb-2'><div class='row justify-content-center'><b>"+datos.seller_reputation.transactions.total+"(<span class='text-success' title='Completadas' data-toggle='tooltip'>"+datos.seller_reputation.transactions.completed+"</span>)</b></div></div>");
$("#calif<?php echo $idp;?>").append("<div class='container'><div class='progress'><div class='progress-bar bg-success' role='progressbar' style='width: "+positivo+"%' aria-valuenow='"+positivo+"' aria-valuemin='0' aria-valuemax='100' title='Positivas'>"+positivo+"%</div><div class='progress-bar' role='progressbar' style='width:"+neutral+"%' aria-valuenow='"+neutral+"' aria-valuemin='0' aria-valuemax='100' title='Neutral'>"+neutral+"%</div><div class='progress-bar bg-danger' role='progressbar' style='width: "+negativo+"%' aria-valuenow='"+negativo+"' aria-valuemin='0' aria-valuemax='100' title='Negativas'>"+negativo+"%</div></div></div>");
}
});
});
//Ver publicaciones del cliente
$("#ant_pub<?php echo $idp;?>").click(function(){
$('#publicaciones<?php echo $idp;?>').empty();$("#cant_publicaciones<?php echo $idp;?>").remove();
var nickname=$("#titulo22<?php echo $idp;?>").attr('class');nickname=encodeURI(nickname);
$.get('publicaciones_user.php',{nick:nickname},verificar,'json');
function verificar(respuesta){
$("#titulo2<?php echo $idp;?>").append("<small class='col-auto ml-auto' id='cant_publicaciones<?php echo $idp;?>'>"+respuesta.paging.total+" Publicacion(es) Activas.</small>");
if(respuesta.paging.total==0){
$("#publicaciones<?php echo $idp;?>").append("<div class='row text-success ml-3'>El cliente no tiene publicaciones activas</div>");
}else{
$.each(respuesta.results,function(i,resultado){
$("#publicaciones<?php echo $idp;?>").append("<div class='row align-items-center justify-content-center'><img class='img-fluid img-thumbnail imagen_publicacion_user' src='"+resultado.thumbnail+"'><a href='"+resultado.permalink+"' class='col-sm-7 text-center' target='_blank'>"+resultado.title+"</a><div class='col-sm-1 text-center text-success'>"+resultado.price+"&nbspBs.</div><small class='col-sm-2 text-center'><b>"+resultado.available_quantity+"&nbspDisponible(s)</b></small></div><hr>");
});
}
}
});
</script>
<?php } } ?>
</hr>
<?php
} } }else{
include '../common/breadcrumb.php';
?>
<div class='breadcrumb'><div class='text-primary col-12'>¡No tienes preguntas pendientes!</div></div>
<?php } ?>
</div>
<div class='container mt-2'>
<div class='row justify-content-center'>
<?php
if($total_preguntas>50){
$NroPag=ceil($total_preguntas/50);
?>
<nav aria-label='Page navigation example'>
<ul class='pagination'>
<?php
if(isset($_GET['page']) & !empty($_GET['page'])){
$pagina=$_GET['page'];
if($pagina>6 && $NroPag>6){
?>
<li class="page-item">
<a class="page-link" href="index.php?page=<?php echo ($pagina-6);?>" aria-label="Previous">
<span aria-hidden="true">&laquo;</span>
<span class="sr-only">Previous</span>
</a>
</li>
<?php
}
}else{$pagina=1;}
if($pagina>5){
for($i=($pagina-5);$i<($pagina+5);$i++){
if(($i-1)==$NroPag){break;}else{
if($i==$pagina){
?>
<li class='page-item active'><a class='page-link' href="index.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
<?php }else{ ?>
<li class='page-item'><a class='page-link' href="index.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
<?php } } } }else{
for($i=1;$i<11;$i++){
if(($i-1)==$NroPag){break;}else{
if($i==$pagina){
?>
<li class="page-item active"><a class="page-link" href="index.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
<?php }else{ ?>
<li class="page-item"><a class="page-link" href="index.php?page=<?php echo $i;?>"><?php echo $i;?></a></li>
<?php } } } }
if($NroPag>10 & $pagina<($NroPag-4)){?>
<li class="page-item">
<a class="page-link" href="index.php?page=<?php echo (5+$pagina);?>" aria-label="Next">
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
<?php include '../common/footer.php';?>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@7.29.0/dist/sweetalert2.all.min.js'></script>
</div>
</body>
</html>
