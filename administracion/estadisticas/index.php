<?php
session_start();
require '../common/meli.php';require '../common/conexion.php';
require '../common/take_at.php'; require '../common/account-off.php';
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
<title>Oxas - Estadísticas</title>
<link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
<link href="../css/sb-admin.min.css" rel="stylesheet"/>
<link href="../css/style.css" rel="stylesheet"/>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/chartjs/Chart.js"></script>
</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">
<?php
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/questions/search?seller_id='.$id_user.'&access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);$consulta=json_decode($result);
$totalpreg=$consulta->total;//total de preguntas
$hoy=getdate();//proporciona el dia de hoy
$dia=$hoy['mday'];$mes=$hoy['mon'];$year=$hoy['year'];//año actual
if(strlen($dia)==1){$dia='0'.$dia;}if(strlen($mes)==1){$mes='0'.$mes;}
$fecha="$year-"."$mes-"."$dia"."T00:00:00.000-04:00";//pasando la fecha formato ISO necesario para ML
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/me?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);$consulta=json_decode($result);curl_reset($ch);
$fecha_registro=$consulta->registration_date;$year_r=substr($fecha_registro,0,4);
$years=array();
for($i=$year_r;$i<=$year;$i++){array_push($years,$i);}
$years=array_reverse($years);$year-=1;
if($dia>=28){$dia=1;$mes+=1;}else{$dia+=1;}
if(strlen($dia)==1){$dia='0'.$dia;}if(strlen($mes)==1){$mes='0'.$mes;}
$fecha2="$year-"."$mes-"."$dia"."T00:00:00.000-04:00";//pasando la fecha formato ISO necesario para ML
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/'.$id_user.'/items_visits?date_from='.$fecha2.'&date_to='.$fecha);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);$consulta=json_decode($result);curl_close($ch);
$total_visit=$consulta->total_visits;//total de visitas
include '../common/navbar.php';?>
<div class="content-wrapper">
<div class="container-fluid">
<input type="hidden" id="user_id" value="<?php echo $id_user;?>">
<?php include "../common/breadcrumb.php";?>
<div class="accordion" id="accordionExample"><div>
<div id="headingOne">
<div class="container">
<div class="row align-items-center">
<h5 class="mb-0">
<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
<strong>Visitas </strong>
</button>
</h5>
<strong class="col-auto ml-auto" title="Total de visitas" data-toggle="tooltip"><?php echo "$total_visit Visitas*";?></strong>
</div>
</div>
</div>
<div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample"><div>
<div class="card mb-3">
<div class="card-header" id="header-t1"><strong class="text-primary">Selecciona el período que deseas ver.</strong> </div>
<div class="card-body"><canvas id="chartVisits" width="100%" height="30"></canvas></div>
<div class="card-footer small text-muted" id="pie-t1">Se cargará la gráfica, y podrás ver los valores de visitas.</div>
</div>
<div class="container breadcrumb">
<div class="row mb-3">
<span class="col-auto text-muted">Selecciona el mes que deseas:</span>
<div class="col-auto">
<div class='input-group input-group-sm'>
<div class='input-group-prepend'><label class='input-group-text' for='"+id_aux+"'>Mes</label></div>
<select class='custom-select custom-select-sm' id='mesVisits'>
<option value="Ene">Enero</option><option value="Feb">Febrero</option><option value="Mar">Marzo</option>
<option value="Abr">Abril</option><option value="May">Mayo</option><option value="Jun">Junio</option>
<option value="Jul">Julio</option><option value="Ago">Agosto</option><option value="Sep">Septiembre</option>
<option value="Oct">Octubre</option><option value="Nov">Noviembre</option><option value="Dic">Diciembre</option>
</select>
</div>
</div>
<div class="col-auto">
<div class='input-group input-group-sm'>
<div class='input-group-prepend'><label class='input-group-text' for='"+id_aux+"'>Año</label></div>
<select class="custom-select custom-select-sm" id='anioVisits'>
<?php foreach($years as $value){ ?>
<option value="<?php echo $value;?>"><?php echo $value;?></option>
<?php } ?>
</select>
</div>
</div>
<div class="col-auto"><button class="btn btn-success btn-sm" type="button" id='search_visits'>Buscar</button></div>
</div>
</div>
</div>
</div>
</div><hr>
<script>
Chart.defaults.global.defaultFontFamily='-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor='#292b2c';
$("#search_visits").click(function(){
var mes=$("#mesVisits").val(),anio=$("#anioVisits").val();
$.get("visitas_usuario.php",{anio:anio,mes:mes},verificar,'text');
function verificar(respuesta){
var array=respuesta.split('%'),totaly=parseInt(array[0]),mayor=parseInt(array[1]),fechas=array[2];
fechas=fechas.split('|');
var cantidades=array[3],totales=array[4];
cantidades=cantidades.split('|');
document.getElementById("header-t1").innerHTML = "Tuviste <strong>"+totales+"</strong> visitas en el período.";
if(window.graficaVisitas){window.graficaVisitas.clear();window.graficaVisitas.destroy();}
var ctx=document.getElementById("chartVisits");
window.graficaVisitas=new Chart(ctx,{
type:'line',
data:{labels:fechas,
datasets:[{label:'Visitas',lineTension:0.3,backgroundColor:'rgba(70,203,62,0.3)',borderColor:'rgba(70,203,62,1)',pointRadius:5,
pointBackgroundColor:'rgba(70,203,62,1)',pointBorderColor:'rgba(255,255,255,0.8)',pointHoverRadius:5,pointHoverBackgroundColor:'rgba(70,203,62,1)',
pointHitRadius:20,pointBorderWidth:2,data:cantidades}],
},
options:{scales:{xAxes:[{time:{unit:'Días'},gridLines:{display:false},ticks:{maxTicksLimit:totaly}}],
yAxes:[{ticks:{min:0,max:mayor,maxTicksLimit:8},gridLines:{color:'rgba(0, 0, 0, .125)',}}],},
legend:{display:false}}
});
}
});
</script>
<!-- Preguntas -->
<div>
<div id="headingTwo">
<div class="container">
<div class="row align-items-center">
<h5 class="mb-0">
<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
<strong>Preguntas</strong>
</button>
</h5>
<div class="col-auto ml-auto"><strong title="Total de preguntas" data-toggle="tooltip"><?php echo "$totalpreg Preguntas**"; ?></strong></div>
</div>
</div>
</div>
<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample"><div>
<div class="card mb-3">
<div class="card-header" id="header_preg"><strong class="text-primary">Selecciona el período que deseas ver.</strong></div>
<div class="card-body"><canvas id="chartPreg" width="100%" height="30"></canvas></div>
<div class="card-footer small text-muted" id="pie_preg">Se cargará la gráfica, y podrás ver los valores de preguntas.</div>
</div>
<div class="container breadcrumb">
<div class="row mb-3">
<span class="col-auto text-muted">Selecciona el mes que deseas:</span>
<div class="col-auto">
<div class='input-group input-group-sm'>
<div class='input-group-prepend'><label class='input-group-text' for='"+id_aux+"'>Mes</label></div>
<select class='custom-select custom-select-sm' id='mesPreg'>
<option value="Ene">Enero</option><option value="Feb">Febrero</option><option value="Mar">Marzo</option>
<option value="Abr">Abril</option><option value="May">Mayo</option><option value="Jun">Junio</option>
<option value="Jul">Julio</option><option value="Ago">Agosto</option><option value="Sep">Septiembre</option>
<option value="Oct">Octubre</option><option value="Nov">Noviembre</option><option value="Dic">Diciembre</option>
</select>
</div>
</div>
<div class="col-auto">
<div class='input-group input-group-sm'>
<div class='input-group-prepend'><label class='input-group-text' for='"+id_aux+"'>Año</label></div>
<select class="custom-select custom-select-sm" id='anioPreg'>
<?php foreach($years as $value){ ?>
<option value="<?php echo $value;?>"><?php echo $value;?></option>
<?php } ?>
</select>
</div>
</div>
<div class="col-auto"><button class="btn btn-success btn-sm" type="button" id='search_preg'>Buscar</button></div>
</div>
</div>
</div>
</div>
</div>
<hr>
<script>
$('#search_preg').click(function(){
var mes=$("#mesPreg").val(),anio=$("#anioPreg").val();
$.get("preguntas_usuario.php",{anio:anio,mes:mes},verificar,'text');
function verificar(respuesta){
var array=respuesta.split('%'),totaly=parseInt(array[0]),mayor=parseInt(array[1]),fechas=array[2];
fechas=fechas.split('|');var cantidades=array[3],totales=array[4];cantidades=cantidades.split('|');
document.getElementById('header_preg').innerHTML='Tuviste <strong>'+totales+'</strong> preguntas en el período.';
if(window.graficaPreguntas){window.graficaPreguntas.clear();window.graficaPreguntas.destroy();}
var ctx=document.getElementById('chartPreg');
window.graficaPreguntas=new Chart(ctx,{
type:'line',
data:{labels:fechas,
datasets:[{label:'Preguntas',lineTension:0.3,backgroundColor:'rgba(70,203,62,0.3)',borderColor:'rgba(70,203,62,1)',pointRadius:5,
pointBackgroundColor:'rgba(70,203,62,1)',pointBorderColor:'rgba(255,255,255,0.8)',pointHoverRadius:5,
pointHoverBackgroundColor:'rgba(70,203,62,1)',pointHitRadius:20,pointBorderWidth:2,data:cantidades}],
},
options:{scales:{xAxes:[{time:{unit:'Días'},gridLines:{display:false},ticks:{maxTicksLimit:totaly}}],
yAxes:[{ticks:{min:0,max: mayor,maxTicksLimit:8},gridLines:{color:'rgba(0, 0, 0, .125)',}}],},
legend:{display:false}}
});
}
});
</script>
<div>
<div id="headingThree">
<div class="container">
<div class="row align-items-center">
<h5 class="mb-0">
<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
<strong>Ventas</strong>
</button>
</h5>
<div class="col-auto ml-auto">
<strong title="Total de ventas" data-toggle="tooltip"><?php echo $_SESSION['completadas']." Ventas**"; ?></strong>
</div>
</div>
</div>
</div>
<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample"><div>
<div class="card mb-3">
<div class="card-header" id="header_vent"><strong class="text-primary">Selecciona el período que deseas ver.</strong></div>
<div class="card-body"><canvas id="chartVentas" width="100%" height="30"></canvas></div>
<div class="card-footer small text-muted" id="pie_vent">Se cargará la gráfica, y podrás ver los valores de las ventas.</div>
</div>
<div class="container breadcrumb">
<div class="row mb-3">
<span class="col-auto text-muted">Selecciona el mes que deseas:</span>
<div class="col-auto">
<div class='input-group input-group-sm'>
<div class='input-group-prepend'><label class='input-group-text' for='"+id_aux+"'>Mes</label></div>
<select class='custom-select custom-select-sm' id='mesVentas'>
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
<select class="custom-select custom-select-sm" id='anioVentas'>
<?php foreach($years as $value){ ?>
<option value="<?php echo $value;?>"><?php echo $value;?></option>
<?php } ?>
</select>
</div>
</div>
<div class="col-auto"><button class="btn btn-success btn-sm" type="button" id='search_venta'>Buscar</button></div>
</div>
</div>
</div>
</div>
</div>
<script>
$("#search_venta").click(function(){
var mes=$("#mesVentas").val(),anio=$("#anioVentas").val();
$.get("ventas_usuario.php",{anio:anio,mes:mes},verificar,'text');
function verificar(respuesta){
var array=respuesta.split('%'),totaly=parseInt(array[0]),mayor=parseInt(array[1]),fechas=array[2];
fechas=fechas.split('|');var cantidades=array[3],totales=array[4];cantidades=cantidades.split('|');
document.getElementById('header_vent').innerHTML='Tuviste <strong>'+totales+'</strong> ventas en el período.';
if(window.graficaVentas){window.graficaVentas.clear();window.graficaVentas.destroy();}
var ctx=document.getElementById('chartVentas');
window.graficaVentas=new Chart(ctx,{
type:'line',
data:{labels:fechas,
datasets:[{label:'Ventas',lineTension:0.3,backgroundColor:'rgba(70,203,62,0.3)',borderColor:'rgba(70,203,62,1)',pointRadius:5,
pointBackgroundColor:'rgba(70,203,62,1)',pointBorderColor:'rgba(255,255,255,0.8)',pointHoverRadius:5,pointHoverBackgroundColor:'rgba(70,203,62,1)',
pointHitRadius:20,pointBorderWidth:2,data:cantidades}],
},
options:{scales:{xAxes:[{time:{unit:'Días'},gridLines:{display:false},ticks:{maxTicksLimit: totaly}}],
yAxes:[{ticks:{min:0,max:mayor,maxTicksLimit:8},gridLines:{color:"rgba(0, 0, 0, .125)",}}],},
legend:{display:false}}
});
}
});
</script>
<hr>
<div id="headingFour">
<div class="container">
<div class="row align-items-center">
<h5 class="mb-0">
<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
<strong>Tendencias</strong>
</button>
</h5>
</div>
</div>
</div>
<div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
<div class="container my-2">
<div class="row">
<div class="col-sm-6">
<strong class="text-success">Selecciona el país que donde deseas ver las tendencias.</strong>
<div class="row mt-2">
<div class="col-auto">
<select class="form-control" required id="site_id" onchange="category()">
<option value="MLA">Argentina</option><option value="MBO">Bolivia</option><option value="MLB">Brasil</option>
<option value="MLC">Chile</option><option value="MCO">Colombia</option><option value="MCR">Costa Rica</option>
<option value="MRD">Dominicana</option><option value="MEC">Ecuador</option><option value="MSV">El Salvador</option>
<option value="MGT">Guatemala</option><option value="MHN">Honduras</option><option value="MLM">México</option>
<option value="MNI">Nicaragua</option><option value="MPA">Panamá</option><option value="MPY">Paraguay</option>
<option value="MPE">Perú</option><option value="MLU">Uruguay</option><option value="MLV">Venezuela</option>
</select>
</div>
<div class="col-auto">
<button class="btn btn-outline-primary" type="button" name="button" id="confir_tendencias" title="Tendencias principales del pais">Consultar</button>
</div>
</div>
</div>
<div class="col-sm-6">
<strong class="text-success">Selecciona la categoria donde deseas ver las tendencias.</strong>
<div class="row mt-2">
<div class="col-auto"><select class="form-control" required id="category_id"></select></div>
<div class="col-auto">
<button class="btn btn-outline-primary" type="button" name="button" id="category_tendencias" data-toggle="tooltip" title="Categoria del pais seleccionado">Consultar</button>
</div>
</div>
</div>
</div>
</div>
<div class="container"><hr><div class="row px-3" id="tendencias"></div></div>
</div>
<hr/>
<script>
$(document).ready(function(){
var site=$("#site_id").val();
$.get('categorias.php',{site:site},verificar,'json');
function verificar(respuesta){
$.each(respuesta,function(i,resultado){$('#category_id').append("<option value='"+resultado.id+"'>"+resultado.name+"</option>");});
}
});
$('#confir_tendencias').click(function(){
$('#optendencias').remove();var site=$('#site_id').val();
$('#tendencias').append("<div class='container' id='optendencias'><div class='row'><div class='col-sm-4' id='primera'></div><div class='col-sm-4' id='segunda'></div><div class='col-sm-4' id='tercera'></div></div></div>");
$.get('tendencias.php',{site:site},verificar,'json');
function verificar(respuesta){
var band=0,cantidad=respuesta,pages=Math.ceil(cantidad.length/3);
$.each(respuesta,function(i,resultado){
var k=i+1;
if(band==0){
band=1;
$('#primera').append("<div class='row'>"+(i+1)+"-<a href='"+resultado.url+"' target='_blank'>"+resultado.keyword+"</a></div>");
}else{
if(k<=pages){
$('#primera').append("<div class='row'>"+(i+1)+"-<a href='"+resultado.url+"' target='_blank'>"+resultado.keyword+"</a></div>");
}else if(k<=pages*2){
$('#segunda').append("<div class='row'>"+(i+1)+"-<a href='"+resultado.url+"' target='_blank'>"+resultado.keyword+"</a></div>");
}else{$('#tercera').append("<div class='row'>"+(i+1)+"-<a href='"+resultado.url+"' target='_blank'>"+resultado.keyword+"</a></div>");}
};
});
}
});
//agregar las categorias en el select
function category(){
$('#category_id option').each(function(){$(this).remove();});var site=$('#site_id').val();
$.get('categorias.php',{site:site},verificar,'json');
function verificar(respuesta){$.each(respuesta,function(i,resultado){$('#category_id').append("<option value='"+resultado.id+"'>"+resultado.name+'</option>');});}
};
//tendencias por categoria
$('#category_tendencias').click(function(){
$('#optendencias').remove();var site=$('#site_id').val(),category_id=$('#category_id').val();
$("#tendencias").append("<div class='container' id='optendencias'><div class='row'><div class='col-sm-4' id='primera'></div><div class='col-sm-4' id='segunda'></div><div class='col-sm-4' id='tercera'></div></div></div>");
$.get('categorias_trends.php',{site:site,category:category_id},verificar,'json');
function verificar(respuesta){
if(typeof respuesta!=='undefined' && respuesta.length>0){
var band=0,cantidad=respuesta,pages=Math.ceil(cantidad.length/3);
$.each(respuesta,function(i,resultado){
var k=i+1;
if(band==0){
band=1;
$("#primera").append("<div class='row'>"+(i+1)+"-<a href='"+resultado.url+"' target='_blank'>"+resultado.keyword+"</a></div>");
}else{
if(k<=pages){$("#primera").append("<div class='row'>"+(i+1)+"-<a href='"+resultado.url+"' target='_blank'>"+resultado.keyword+"</a></div>");
}else if(k<=pages*2){$("#segunda").append("<div class='row'>"+(i+1)+"-<a href='"+resultado.url+"' target='_blank'>"+resultado.keyword+"</a></div>");
}else{$("#tercera").append("<div class='row'>"+(i+1)+"-<a href='"+resultado.url+"' target='_blank'>"+resultado.keyword+"</a></div>");}
};
});
}else{$("#primera").append("<div class='row'><b class='text-muted'>No hay tendencias en esta categoria</b></div>");}
}
});
</script>
</div>
<div class="container-fluid my-3">
<div><small class="text-muted">*Total de visitas obtenidas en Mercado Libre, un año hacia atras desde la fecha actual.</small></div>
<div><small class="text-muted">**Total desde su comienzo como vendedor en Mercado Libre.</small></div>
</div>
</div>
<?php include '../common/footer.php';?>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin.min.js"></script>
</div>
</body>
</html>
