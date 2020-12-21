<?php
session_start();
require '../common/conexion.php';
require '../common/take_at.php'; require '../common/account-off.php';
$_SESSION['array_categorias']=array();
$_SESSION['array_variaciones']=array();
$_SESSION['array_atributos']=array();
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
<title>Oxas - Publicaciones</title>
<link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../css/sb-admin.min.css" rel="stylesheet">
<link href="../css/style.css" rel="stylesheet">
<script src="../vendor/jquery/jquery.min.js"></script>
<script>function pulsar(e){tecla=(document.all) ? e.keyCode:e.which;return(tecla!=13);}</script>
</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">
<?php include '../common/navbar.php';?>
<div class="content-wrapper p-0">
<div class="container-fluid background_activas py-2">
<div class="row align-items-center">
<div class='col-auto'>
<a class='text-muted ml-2' href='index.php' data-toggle='tooltip' title='Publicaciones'>Volver</a>
</div>
<div class="col-auto ml-auto">
<button class="ml-1 btn btn-link btn-sm text-success" type="button" data-toggle="modal" data-target="#publicaciones_masivas">Publicar Masivamente</button>
</div>
</div>
</div>
<div class="container-fluid">
<form action="crear_publicaciones.php" method="post" enctype="multipart/form-data">
<div class="row mb-2 align-items-center breadcrumb">
<div class="col-1 text-success">
<strong class="align-self-center">Categoria:</strong>
</div>
<div class="col-auto">
<?php
$ch=curl_init();curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$siteId/categories");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$consulta=json_decode($result);
?>
<select class="form-control" id="category_id" onchange="category()">
<?php
foreach($consulta as $value){
if($value->name!="Servicios" && $value->name!="Inmuebles" && $value->name!="Carros, Motos y Otros"){
?>
<option value='<?php echo $value->id;?>'><?php echo $value->name;?></option>
<?php } } ?>
</select>
</div>
<div class="col-7">
<input class="form-control" type="text" placeholder="Titulo de la publicación (Max. 60 caracteres)" maxlength="60" id="title" name='title'/>
</div>
<div class="col-12 ml-2 mr-5 mt-2" id="prediction_category"></div>
</div>
<div class="row mb-2 align-items-center breadcrumb" id='complementos'></div>
<div class="row breadcrumb m-0 p-0" id='atributos'></div>
<div class="row breadcrumb m-0 p-0" id='variaciones'></div>
<div class="col-12">
<label class="btn btn-link" for="fileUpload" id='selec_image'>Seleccionar Imagenes</label>
<input type="file" multiple id='fileUpload' name="images[]">
<div class="row justify-content-around mb-2" id="image-holder"></div>
</div>
<script>
$("#fileUpload").on('change',function(){
var countFiles=$(this)[0].files.length;var imgPath=$(this)[0].value;var extn=imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
var image_holder=$("#image-holder");image_holder.empty();
if (extn=="gif" || extn=="png" || extn=="jpg" || extn=="jpeg"){
if(typeof (FileReader)!='undefined'){
if(countFiles>8){countFiles=8;}
for(var i=0;i<countFiles;i++){
var reader=new FileReader();
reader.onload=function(e){
$("<div class='col-1-sm border-rect draggable' draggable='true'><img src='"+e.target.result+"' class='public p-4'/></div>").appendTo(image_holder);}
image_holder.show();reader.readAsDataURL($(this)[0].files[i]);
}
}else{alert("This browser does not support FileReader.");}
}else{const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
toast({type:'error',title:"¡Desbes subir imagenes tipo jpg, jpge, png o gif!"})
}
});
</script>
<div class="row mb-2"><div class="container" id='contenedor_description'></div></div>
<div class="row align-items-center mb-2">
<div class="col-4" id="nivles_publicacion">
<div class="row align-items-center">
<strong class="col-4 text-secondary">Exposición:</strong>
<select class="col-5 form-control form-control-sm" id="listing_type" name='listing_type'>
<option value="gold_special">Premium (11%)</option><option value="free">Gratis (0%)</option><option value="bronze">Clásico (9%)</option>
</select>
</div>
</div>
<div class="col-8" id="warranty">
<div class="row">
<div class="col-3" id="garantia">
<b class="text-muted">Garantía:</b>
<label class="text-muted"><input type="radio" id='no_garantia' name="garantia" value="no" checked> No</label>
<label class="text-muted"><input type="radio" id='si_garantia' name="garantia" value="si"> Si</label>
</div>
<div class="col-7"><input class="form-control form-control-sm" type="text" id="input_garantia" value="Defectos de fábrica" name='texto_garantia'/></div>
</div>
</div>
</div>
<div class="row justify-content-center mb-2"><button type="submit" class="btn btn-sm btn-success col-sm-2 text-white" id='crear'>¡Publicar!</button></div>
</form>
</div>
<script>
function atributos_variaciones(valor,tipo,id,name,tooltip,max,unidad_x_defecto,valores_lista){
if(name!='SKU '){
switch(tipo){
case 'string':
if(id=='GTIN'){
var aux="<div class='col-auto p-0 pr-1 m-0 mb-2' id='gtin'><div class='input-group input-group-sm'><div class='input-group-prepend'><span class='input-group-text' title='"+tooltip+"' id='inputGroup-sizing-sm'>Código GTIN</span></div><input type='text' class='form-control' maxlength='"+max+"' aria-describedby='inputGroup-sizing-sm'>";
aux=aux+"<span class='input-group-text' id='inputGroup-sizing-sm'><svg class='d-inline svg-secondary' width='20px' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path d='M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zM262.655 90c-54.497 0-89.255 22.957-116.549 63.758-3.536 5.286-2.353 12.415 2.715 16.258l34.699 26.31c5.205 3.947 12.621 3.008 16.665-2.122 17.864-22.658 30.113-35.797 57.303-35.797 20.429 0 45.698 13.148 45.698 32.958 0 14.976-12.363 22.667-32.534 33.976C247.128 238.528 216 254.941 216 296v4c0 6.627 5.373 12 12 12h56c6.627 0 12-5.373 12-12v-1.333c0-28.462 83.186-29.647 83.186-106.667 0-58.002-60.165-102-116.531-102zM256 338c-25.365 0-46 20.635-46 46 0 25.364 20.635 46 46 46s46-20.636 46-46c0-25.365-20.635-46-46-46z'/></svg></span></div></div>";
$('#'+valor).append(aux);
}else{
if(valor=='variaciones'){name_aux=name.replace(/ /g, "|");
$('#'+valor).append("<div class='col-2 p-0 pr-1 m-0 mb-2'><div class='input-group input-group-sm'><div class='input-group-prepend'><span class='input-group-text' title='"+tooltip+"' id='inputGroup-sizing-sm'>"+name+"</span></div><input type='text' class='form-control' maxlength='"+max+"' name='"+name_aux+"' aria-describedby='inputGroup-sizing-sm'></div></div>");
}else{$('#'+valor).append("<div class='col-2 p-0 pr-1 m-0 mb-2'><div class='input-group input-group-sm'><div class='input-group-prepend'><span class='input-group-text' title='"+tooltip+"' id='inputGroup-sizing-sm'>"+name+"</span></div><input type='text' class='form-control' maxlength='"+max+"' name='"+id+"' aria-describedby='inputGroup-sizing-sm'></div></div>");}
}
break;
case 'boolean':
var id_aux="id_"+id;
$('#'+valor).append("<div class='col-auto p-0 pr-1 m-0 mb-2'><div class='input-group input-group-sm'><div class='input-group-prepend'><label class='input-group-text' for='"+id_aux+"'>"+name+"</label></div><select class='custom-select custom-select-sm' id='"+id_aux+"'></select></div></div>");
$.each(valores_lista,function(i,resultado){$('#'+id_aux).append("<option value='"+resultado.id+"'>"+resultado.name+"</option>");});
break;
case 'number_unit'://lista excluyente
$('#'+valor).append("<div class='col-2 p-0 pr-1 m-0 mb-2'><div class='input-group input-group-sm'><div class='input-group-prepend'><label class='input-group-text' title='"+tooltip+"'>"+name+"</label></div><input type='number' class='form-control' maxlength='"+max+"' step='1' aria-describedby='inputGroup-sizing-sm'><div class='input-group-append'><label class='input-group-text'>"+unidad_x_defecto+"</label></div></div></div>");
break;
case 'list'://Lista
var id_aux="id_"+id;
$('#'+valor).append("<div class='col-auto p-0 pr-1 m-0 mb-2'><div class='input-group input-group-sm'><div class='input-group-prepend'><label class='input-group-text' for='"+id_aux+"'>"+name+"</label></div><select class='custom-select custom-select-sm' id='"+id_aux+"'></select></div></div>");
$.each(valores_lista,function(i,resultado){$('#'+id_aux).append("<option value='"+resultado.id+"'>"+resultado.name+"</option>");});
break;
case 'number':
$('#'+valor).append("<div class='col-2 p-0 pr-1 m-0 mb-2'><div class='input-group col-auto'><div class='input-group-prepend'><span class='input-group-text' title='"+tooltip+"' id='inputGroup-sizing-sm'>"+name+"</span></div><input type='number' name='' class='form-control' maxlength='"+max+"' aria-describedby='inputGroup-sizing-sm'></div></div>");
break;
}
}
}
$('#selec_image').hide();$('#crear').hide();$('#description').hide();$('#warranty').hide();$('#atributos').hide();$('#complementos').hide();
$('#nivles_publicacion').hide();$('#input_garantia').hide();$('#variaciones').hide();var k=0;
$("#si_garantia").click(function(){if (k==0){$('#input_garantia').show();k=1;}});
$("#no_garantia").click(function(){$("#input_garantia").hide();k=0;});
//Modificacion del titulo
$("#title").blur(function(){
var otro_aux=<?php echo 1;$_SESSION['array_variaciones']=array();$_SESSION['array_atributos']=array();?>;
var variaciones, min_price, max_description, url_catergoria, valores_lista;
$('#selec_image').show();$('#footer_category').empty();$('#atributos').empty();$('#atributos').show();$('#variaciones').empty();
$('#description').show();$('#complementos').empty();$('#complementos').show();$('#contenedor_description').empty();
if($("#prediction_category").length>0){$("#prediction_category").empty();}
var category_root=$("#category_id").val(), title=$("#title").val();
if($("#title").val().length>0){
$("#footer_category_real").remove();
$.get("prediction.php",{category_root:category_root,title:title},verificar,'json');
function verificar(respuesta){
var id_category=respuesta.id, array=respuesta.path_from_root, length=array.length-1, array_categories='';
$.each(array,function(i,resultado){
if(array_categories==''){array_categories=resultado.id;}else{array_categories=array_categories+','+resultado.id;}
if(i==0){$("#prediction_category").append('<small>'+resultado.name+'</small>');$('#titulo_no_estoy_acuerdo').append(resultado.name);
}else if(length==i){//ultimo valor
$("#prediction_category").append("-><small><strong class='mr-1'>"+resultado.name+"</strong></small>(<small class='text-danger cursor-pointer' data-toggle='modal' data-target='.categories-modal-lg' id='no_estoy_de_acuerdo'>No estoy de acuerdo</small>)<input type='hidden' id='category_real' name='category' value='"+respuesta.id+"'>");
url_catergoria='https://api.mercadolibre.com/categories/'+id_category;
}else{$("#prediction_category").append("-><small>"+resultado.name+"</small>");}
});
$.getJSON(url_catergoria,function(datos){
variaciones=datos.attribute_types;min_price=datos.settings.minimum_price;max_description=datos.settings.max_description_length;
var estado=datos.settings.item_conditions;
$('#complementos').append("<div class='col-auto'><b class='text-muted'>Condición: </b><div class='d-inline-flex' id='estados_publicac'></div></div>");
$.each(estado,function(i,resultado){
if(resultado=='new'){
$("#estados_publicac").append("<label class='text-muted ml-2'><input type='radio' name='condicion' value='"+resultado+"' checked/>Nuevo</label>");
}else if(resultado=='used'){
$("#estados_publicac").append("<label class='text-muted ml-2'><input type='radio' name='condicion' value='"+resultado+"'/>Usado</label>");
}
});
var buying=datos.settings.buying_modes;
$('#complementos').append("<div class='col-auto'><b class='text-muted'>Precio: </b><div class='d-inline-flex' id='buying_modes'></div></div>");
$.each(buying,function(i,resultado){
if(resultado=="buy_it_now"){
$("#buying_modes").append("<label class='text-muted ml-2'><input type='radio' name='buying_modes' value='buy_it_now' checked> Fijo</label>");
}else if(resultado=="auction"){
$("#buying_modes").append("<label class='text-muted ml-2'><input type='radio' name='buying_modes' value='auction'> Subasta</label>");
}else if(resultado=="classified"){
$("#buying_modes").append("<label class='text-muted'><input type='radio' name='buying_modes' value='classified' checked> Clasificado</label>");
}
});
$('#complementos').append("<div class='col-2 ml-auto'><div class='input-group input-group-sm'><input class='form-control' type='number' placeholder='Precio' step='.01' id='price' min='min='"+min_price+"' name='price' aria-describedby='inputGroup-sizing-sm'><div class='input-group-prepend'><span class='input-group-text' id='inputGroup-sizing-sm'>Bs.</span></div></div></div>");
$('#contenedor_description').append("<textarea class='form-control' id='description' rows='7' cols='80' placeholder='Redacta la descipción de tu producto..' id='description' name='description' maxlength='"+max_description+"' required></textarea>");
});
$("#footer_category").append("<input type='hidden' value='"+array_categories+"' id='footer_category_real'>");
//atributos de la publicacion
var url='https://api.mercadolibre.com/categories/'+id_category+'/attributes';
$.getJSON(url,function(datos){
var cont=0,vart_attr=0;//variable q indica si hubo o no variaciones
if(variaciones=='variations'){
$('#variaciones').show();$('#variaciones').append("<div class='col-12 text-muted mb-2'>Variaciones:</div>");
$.each(datos,function(i,resultado){
var id=resultado.id,tipo=resultado.value_type,name=resultado.name,tags=resultado.tags;
if(typeof resultado.value_max_length!=='undefined'){var max=resultado.value_max_length;}else{var max='';}
if(typeof resultado.default_unit!=='undefined'){var unidad_x_defecto=resultado.default_unit;}else{var unidad_x_defecto='';}
if(typeof resultado.tooltip!=='undefined'){var tooltip=resultado.tooltip;}else{var tooltip='';}
if(typeof resultado.values!=='undefined'){var valores_lista=resultado.values;}else{var valores_lista='';}
if(typeof tags.allow_variations!=='undefined' && tags.allow_variations==true){
atributos_variaciones("variaciones",tipo,id,name,tooltip,max,unidad_x_defecto,valores_lista);vart_attr=1;
//agrego la variacion al array
$.get('variaciones_array.php',{name:name,band:0},verificar,'text');function verificar(respuesta){}
}else if(typeof tags.catalog_required!=='undefined' && tags.catalog_required==true){
if(cont==0){$('#atributos').append("<div class='col-12 text-muted mb-2'>Complete la ficha técnica:</div>");}
++cont;atributos_variaciones("atributos",tipo,id,name,tooltip,max,unidad_x_defecto,valores_lista);
//agrego el atributo al array
$.get('variaciones_array.php',{name:id,band:1},verificar,'text');function verificar(respuesta){}
}
});
}else{
$.each(datos,function(i,resultado){
if(typeof resultado.tags.required!=='undefined' && resultado.tags.required==true){
var id=resultado.id,tipo=resultado.value_type,name=resultado.name;
if(typeof resultado.value_max_length!=='undefined'){var max=resultado.value_max_length;}else{var max='';}
if(typeof resultado.default_unit!=='undefined'){var unidad_x_defecto=resultado.default_unit;}else{var unidad_x_defecto='';}
if(typeof resultado.tooltip!=='undefined'){var tooltip=resultado.tooltip;}else{var tooltip='';}
if(typeof resultado.values!=='undefined'){var valores_lista=resultado.values;}else{var valores_lista='';}
if($cont==0){$('#atributos').append("<div class='col-12 text-muted mb-2'>Complete la ficha técnica:</div>");}
++cont;
atributos_variaciones("atributos",tipo,id,name,tooltip,max,unidad_x_defecto,valores_lista);
}
});
}
if(vart_attr==0){
$('#complementos').append("<div class='col-2'><input class='form-control form-control-sm' type='number' placeholder='Cantidad (Stock)' step='1' id='cantidad' name='quantity' required/></div>");
}else{
$('#variaciones').append("<div class='col-auto p-0 pr-5 m-0 mb-2'><div class='input-group input-group-sm'><input class='form-control' type='number' placeholder='Cantidad' step='1' id='cantidad' name='quantity' required aria-describedby='inputGroup-sizing-sm'/></div></div>");
//$('#variaciones').append("<div class='col-12 text-success'><svg xmlns='http://www.w3.org/2000/svg' width='8px' class='svg-dark' viewBox='0 0 448 512'><path d='M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z'/></svg>Agregar Variación</div>");
}
});
$('#crear').show();$('#nivles_publicacion').show();$('#warranty').show();
}
}
});
//no estoy de acuerdo
$(document).on('click','#no_estoy_de_acuerdo',function(){
$('#category_modal').empty();var items=$('#footer_category_real').val(), array_items=items.split(',');
$('#category_modal').append("<div id='contenedor_no_1'></div>");
$.each(array_items,function(i,item){
if((array_items.length-1)!=i){
var categoria_real=array_items[i+1], contenedor=i+1, contenedor_aux=contenedor+1, url='https://api.mercadolibre.com/categories/'+item;
$.getJSON(url,function(datos){
var childrens=datos.children_categories;
if(childrens.length>0){
$('#contenedor_no_'+contenedor).append("<div class='d-flex' id='contenedor_no_"+contenedor_aux+"'></div>");
$('#contenedor_no_'+contenedor_aux).append("<div class='border-rect heigth_category ml-2 p-1 d-inline' id='raiz_no_"+contenedor_aux+"'></div>");
$.each(childrens,function(i,valor){
if(categoria_real==valor.id){
$('#raiz_no_'+contenedor_aux).append("<button class='btn btn-link m-0 p-0' type='button'><small class='categoria_select px-2' id='"+valor.id+"_no_"+contenedor_aux+"'>"+valor.name+"</small></button></br>");
}else{$('#raiz_no_'+contenedor_aux).append("<button class='btn btn-link m-0 p-0' type='button'><small id='"+valor.id+"_no_"+contenedor_aux+"'>"+valor.name+"</small></button></br>");}
});
}
});
}
});
});
//editar no estoy de acuerdo
$(document).on('click','#category_modal small',function(){
$('#footer_category').empty();var item=$(this).attr('id'), valores=item.split('_'), id=valores[0], contenedor=valores[2], count=0;
$('#raiz_no_'+contenedor+' small').each(function(){$(this).removeClass('categoria_select');});
$(this).addClass('categoria_select');contenedor_aux=parseInt(contenedor)+1;$('#contenedor_no_'+contenedor_aux).remove();
var url='https://api.mercadolibre.com/categories/'+id;
$.getJSON(url,function(datos){
var childrens=datos.children_categories;
if(childrens.length>0){
$("#contenedor_no_"+contenedor).append("<div class='d-flex' id='contenedor_no_"+contenedor_aux+"'></div>");
$("#contenedor_no_"+contenedor_aux).append("<div class='border-rect heigth_category ml-2 p-1 d-inline' id='raiz_no_"+contenedor_aux+"'></div>");
$.each(childrens,function(i,valor){
$('#raiz_no_'+contenedor_aux).append("<button class='btn btn-link m-0 p-0' type='button'><small id='"+valor.id+"_no_"+contenedor_aux+"'>"+valor.name+"</small></button></br>");
});
}else{
$('#prediction_category').empty();
$('#footer_category').append("<span class='text-success'>"+datos.name+"</span>");
$('#footer_category').append("<button class='btn btn-sm btn-success px-5 ml-4' id='listo_no_de_acuerdo'>Listo</button>");
//ultimo valor
var path_from_root=datos.path_from_root;
$.each(path_from_root,function(i,value){
if(datos.id==value.id){
$("#prediction_category").append("<small><strong class='mr-1'>"+datos.name+"</strong></small>(<small class='text-danger cursor-pointer' data-toggle='modal' data-target='.categories-modal-lg' id='no_estoy_de_acuerdo'>No estoy de acuerdo</small>)<input type='hidden' id='category_real' name='category' value='"+value.id+"'>");
}else{$("#prediction_category").append("<small class='class='text-muted''>"+value.name+"</small>->");}
});
}
});
});
//Listo no estoy de acuerdo
$(document).on('click','#listo_no_de_acuerdo',function(){$('#modal_no_de_acuerdo').modal('hide')});
</script>
<!-- Modal No estoy acuerdo -->
<div class="modal fade categories-modal-lg" id='modal_no_de_acuerdo' tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content container">
<div class="modal-header">
<div class="container">
<div class="row"><h5 class="modal-title" id="titulo_no_estoy_acuerdo"></h5></div>
<small class="text-muted">Selecciona la categoria que piensas es la correcta.</small>
</div>
<button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body" id="category_modal"></div>
<div class="modal-footer text-muted" id="footer_category"></div>
</div>
</div>
</div>
<!-- Modal Load -->
<input type="hidden" data-toggle="modal" data-target="#loader_modal" id="loader_now">
<div class="modal fade" id="loader_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" id="loader_real">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content bg-transparent no_border mt-5 pt-5">
<button type="button" class="close bg-transparent" data-dismiss="modal" aria-label="Close" id="close_loader"></button>
<div class="container mt-5" id='loader_content'>
<div class="row justify-content-center">
<div class="col-auto background_loader">
<div class="loader algin-self-middle"></div>
</div>
</div>
</div>
</div>
</div>
</div>
<script>
//funcion de envio masivo
function comprueba_extension(formulario,archivo){
extensiones_permitidas=new Array(".xlsx",".xls");
if(!archivo){
const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
toast({type:'error',title:'¡Debes seleccionar algún archivo!'})
}else{
extension=(archivo.substring(archivo.lastIndexOf("."))).toLowerCase();permitida=false;
for(var i=0;i<extensiones_permitidas.length;i++){if(extensiones_permitidas[i]==extension){permitida=true;break;}}
if(!permitida){
const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
toast({type:'error',title:'¡Debes subir archivos con extensiones .xslx o .xsl!'})
}else{$("#loader_now").click();$("#close_masivas").click();
$('#loader_content').append("<b class='row justify-content-center text-white'>¡¡Puede tardar unos minutos!!</b><b class='row justify-content-center text-white'>Ten en cuenta que tardará apróximadamente entre 1 a 3 segundos por cada publicación.</b>");
formulario.submit();return 1;}
}
}
</script>
<!-- Publicar Masivas -->
<div class="modal fade" id="publicaciones_masivas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title lead" id="exampleModalLabel">Publicar Masivamente <button class="btn btn-link btn-sm" data-toggle='modal' data-target='#formato' id='formato_excel_categorias'>Descargar Formato de Excel</button> </h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close" id='close_masivas'><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div class="container">
<form method="post" action="subir_archivo.php" enctype="multipart/form-data">
<input class="nuestroinput" type="file" name="archivoupload" required id='FormControlFile'/>
<div class="row ml-2 mt-4">
<small class="text-muted">Si no sabes como publicar masivamente, puedes ver el siguiente tutorial (<a href="#">Ver tutorial</a>).</small>
</div>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancelar</button>
<input class="btn btn-success btn-sm px-5" type=button name="Submit" value="Publicar" onclick="comprueba_extension(this.form, this.form.archivoupload.value)">
</div>
</form>
</div>
</div>
</div>
<script>
$(document).on("click","#formato_excel_categorias",function(){
$("#descargar_categorias input[type=checkbox]").each(function(){$(this).prop('checked',false);});
$.get('array_categorias.php',{band:3},verificar,'text');function verificar(respuesta){};});
</script>
<!-- Formato Masivas -->
<div class="modal fade" id="formato" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title lead" id="exampleModalLabel">Descargar Formato Masivas <small class="text-success cursor-pointer" data-toggle='modal' data-target='#condiciones'>(Condiciones)</small></h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close" id='close_formato_masiv'><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<b class="ml-3 text-muted">Selecciona las categorias que vas a publicar:</b>
<form method="get" action="descargar_masivas.php" enctype="multipart/form-data">
<div class="container mt-3">
<div class="row" id='descargar_categorias'>
<div class="col-6">
<label class="categories">Accesorios para Vehículos<input type="checkbox" value="<?php echo $siteId;?>1747€Accesorios%para%Vehículos" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Animales y Mascotas<input type="checkbox" value="<?php echo $siteId;?>1071€Animales%y%Mascotas" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Bebés<input type="checkbox" value="<?php echo $siteId;?>1384€Bebés" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Cámaras y Accesorios<input type="checkbox" value="<?php echo $siteId;?>1039€Cámaras%y%Accesorios" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Celulares y Teléfonos<input type="checkbox" value="<?php echo $siteId;?>1051€Celulares%y%Teléfonos" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Coleccionables y Hobbies<input type="checkbox" value="<?php echo $siteId;?>1798€Coleccionables%y%Hobbies" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Computación<input type="checkbox" value="<?php echo $siteId;?>1648€Computación" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Consolas y Videojuegos<input type="checkbox" value="<?php echo $siteId;?>1144€Consolas%y%Videojuegos" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Deportes y Fitness<input type="checkbox" value="<?php echo $siteId;?>1276€Deportes%y%Fitness" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Electrodomésticos<input type="checkbox" value="<?php echo $siteId;?>5726€Electrodomésticos" name='categorias[]'><span class="checkmark"></span>
</label>
</div>
<div class="col-6">
<label class="categories">Electrónica, Audio y Video<input type="checkbox" value="<?php echo $siteId;?>1000€Electrónica,%Audio%y%Video" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Estética y Belleza<input type="checkbox" value="<?php echo $siteId;?>1246€Estética%y%Belleza" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Hogar y Muebles<input type="checkbox" value="<?php echo $siteId;?>1574€Hogar%y%Muebles" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Industrias<input type="checkbox" value="<?php echo $siteId;?>1499€Industrias" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Instrumentos Musicales<input type="checkbox" value="<?php echo $siteId;?>1182€Instrumentos%Musicales" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Juegos y Juguetes<input type="checkbox" value="<?php echo $siteId;?>1132€Juegos%y%Juguetes" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Libros, Música y Películas<input type="checkbox" value="<?php echo $siteId;?>3025€Libros,%Música%y%Películas" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Relojes, Joyas y Bisutería<input type="checkbox" value="<?php echo $siteId;?>3937€Relojes,%Joyas%y%Bisutería" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Ropa, Zapatos y Accesorios<input type="checkbox" value="<?php echo $siteId;?>1430€Ropa,%Zapatos%y%Accesorios" name='categorias[]'><span class="checkmark"></span>
</label>
<label class="categories">Otras Categorías<input type="checkbox" value="<?php echo $siteId;?>1953€Otras%Categorías" name='categorias[]'><span class="checkmark"></span>
</label>
</div>
</div>
</div>
</div>
<div class="modal-footer"><input class="btn btn-success btn-sm" type="submit" value="Descargar" id='descargar_ok'></div>
</form>
<button type="hidden" data-toggle='modal' data-target='#categoria_raiz' id='click_modal_categ_raiz'></button>
</div>
</div>
</div>
<script>
//click en la categoria principal
$(document).on("click","#descargar_categorias input[type=checkbox]",function(){
var item=$(this).val();
if($(this).is(':checked')){
$('#close_modal_categorias input').remove();$('#contenedor_categorias').empty();
$('#contenedor_categorias').append("<b class='ml-2 text-muted'>Sé un poco más especifico en lo que deseas publicar:</b><div class='mt-3'><div class='row contenedor_categorias_varias_1'></div></div>");
$('#title_categorias_raiz').empty();$('#click_modal_categ_raiz').click();
var valores=item.split("€"), name=valores[1].replace(/%/g,' ');$('#title_categorias_raiz').append(name);
var categoria_principal=valores[0];$('#close_modal_categorias').append("<input type='hidden' value='"+categoria_principal+"'>");
//agregar las categorias a elegir
var url='https://api.mercadolibre.com/categories/'+categoria_principal;
$.getJSON(url,function(datos){
var childrens=datos.children_categories;
$("#contenedor_categorias .contenedor_categorias_varias_1").append("<div class='d-flex' id='contenedor_1_1'><div class='border-rect heigth_category ml-2 p-1 d-inline' id='raiz_1_1'></div></div>");
$.each(childrens,function(i,valor){
$("#raiz_1_1").append("<button class='btn btn-link m-0 p-0' type='button'><small id='"+valor.id+"_1_1'>"+valor.name+"</small></button></br>");
});
});
}
});
//click en cualquier categoria (small-enlace) del modal para elegir la categoria raiz
$(document).on('click','#contenedor_categorias small',function(){
var item=$(this).attr('id'),valores=item.split('_'),id=valores[0],contenedor=valores[1],contenedor_principal=valores[2],count=0;
$('#contenedor_'+contenedor+'_'+contenedor_principal+' small').each(function(){$(this).removeClass('categoria_select');});
$(this).addClass('categoria_select');contenedor_aux=parseInt(contenedor)+1;
$("#contenedor_"+contenedor_aux+"_"+contenedor_principal).remove();$('#category_raiz_aux_'+contenedor_principal).remove();
var url='https://api.mercadolibre.com/categories/'+id;
$.getJSON(url,function(datos){
var childrens=datos.children_categories;
if(childrens.length>0){
$("#contenedor_"+contenedor+"_"+contenedor_principal).append("<div class='d-flex' id='contenedor_"+contenedor_aux+"_"+contenedor_principal+"'></div>");
$("#contenedor_"+contenedor_aux+"_"+contenedor_principal).append("<div class='border-rect heigth_category ml-2 p-1 d-inline' id='raiz_"+contenedor_aux+"_"+contenedor_principal+"'></div>");
$.each(childrens,function(i,valor){
$("#raiz_"+contenedor_aux+"_"+contenedor_principal).append("<button class='btn btn-link m-0 p-0' type='button'><small id='"+valor.id+"_"+contenedor_aux+"_"+contenedor_principal+"'>"+valor.name+"</small></button></br>");
});
}else{
$("#contenedor_categorias .contenedor_categorias_varias_"+contenedor_principal).append("<div class='col-12 ml-2 mt-2' id='category_raiz_aux_"+contenedor_principal+"'></div>");
var path_from_root=datos.path_from_root;
$.each(path_from_root,function(i,value){
if(datos.id==value.id){
$('#category_raiz_aux_'+contenedor_principal).append("<small class='text-success cat_root' id='"+datos.id+"'>"+value.name+"</small>");
var cont_aux=parseInt(contenedor_principal)+1;
if($("#contenedor_1_"+cont_aux).length==0){
$('#category_raiz_aux_'+contenedor_principal).append("<div class='col-12 cursor-pointer agregar_otro' id='"+path_from_root[0].id+"_"+contenedor_principal+"'><b>+</b> <span class='text-success'>Agregar Categoria</span></div>");
}
}else{$('#category_raiz_aux_'+contenedor_principal).append("<small class='text-muted'>"+value.name+" -> </small>");}
});
}
});
});
//boton agregar otra categoria
$(document).on("click","#contenedor_categorias .agregar_otro",function(){
$(this).remove();var aux=$(this).attr('id'),valores=aux.split("_"),id=valores[0],contenedor_principal=valores[1],contenedor_nuevo=parseInt(contenedor_principal)+1;
$('#contenedor_categorias').append("<div class='mt-3'><div class='row contenedor_categorias_varias_"+contenedor_nuevo+"'></div></div>");//scroll-navegacion
var url='https://api.mercadolibre.com/categories/'+id;
$.getJSON(url,function(datos){
var childrens=datos.children_categories;
$("#contenedor_categorias .contenedor_categorias_varias_"+contenedor_nuevo).append("<div class='d-flex' id='contenedor_1_"+contenedor_nuevo+"'><div class='border-rect heigth_category ml-2 p-1 d-inline' id='raiz_1_"+contenedor_nuevo+"'></div></div>");
$.each(childrens,function(i,valor){
$("#raiz_1_"+contenedor_nuevo).append("<button class='btn btn-link m-0 p-0' type='button'><small id='"+valor.id+"_1_"+contenedor_nuevo+"'>"+valor.name+"</small></button></br>");
});
});
});
</script>
<!-- Modal rama de categorias a descargar -->
<div class="modal fade" id="categoria_raiz" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard='false' data-backdrop='false'>
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title lead" id="title_categorias_raiz"></h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close" id='close_modal_categorias'><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body" id='contenedor_categorias'></div>
<div class="modal-footer ml-auto">
<button class="btn btn-success btn-sm px-5" type="button" id='listo_categorias'>Listo</button>
<button class="btn btn-danger btn-sm" type="button" id='cancel_categorias'>Cancelar</button>
</div>
</div>
</div>
</div>
<script>
$(document).on('click','#cancel_categorias',function(){$('#close_modal_categorias').click();});
//desactivar el input checkbox al cancelar la eleccion de categrias raices en modal formato
$(document).on('click','#close_modal_categorias',function(){
var id=$('#close_modal_categorias input').val();
$("#descargar_categorias input[type=checkbox]").each(function(){
var valor=$(this).val(),valores=valor.split('€'),id_ppal=valores[0];if(id_ppal==id){$(this).prop('checked',false);}
});
});
//agregar las categorias al array_categorias de sesion
$(document).on("click","#listo_categorias",function(){
var cat_ppal=$('#close_modal_categorias input').val(),cats_root='';
$("#contenedor_categorias .cat_root").each(function(){
if(cats_root==''){cats_root=$(this).attr('id');}else{cats_root=cats_root+'|'+$(this).attr('id');}
});
$.get("array_categorias.php",{band:1,cat_ppal:cat_ppal,cats_root:cats_root},verificar,"text");
function verificar(respuesta){}
$('#categoria_raiz').modal('hide');
const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
toast({type:'success',title:'¡Listo! \n Si deseas elige otra categoria.'})
});
</script>
<!-- Condicones -->
<div class="modal fade" id="condiciones" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title lead text-success" id="exampleModalLabel">Condiciones</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close" id='close_formato'><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<b class="ml-3 text-muted">Para publicar masivamente debes tener en cuenta las siguientes condiciones:</b>
<ul class="mt-3">
<li>Ten en cuenta que no podrás cambiar columnas, ni nombres de filas u hojas en el Formato de Excel que descargarás.</li>
<li><img width='40px' src="../../img/image.png" alt="OxasApp"> Todas las publicaciones se crearán con esta imagen, Luego podrás cambiar las imagenes.</li>
<li>Todas las publicaciones se crearán con "Precio Fijo".</li>
<li>Todas las publicaciones se crearán con nivel "Premium".</li>
<li>Todas las publicaciones se crearán sin garantia.</li>
<li>Todas las publicaciones se crearán con condicion de "Nuevo".</li>
</ul>
</div>
<div class="modal-footer"><p class="text-muted">Ten en cuenta que todos estos valores podrás editarlos en la edicion masiva de publicaciones.</p></div>
</div>
</div>
</div>
<script>
$(document).on("click","#descargar_ok", function(){
$('#close_formato_masiv').click();const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:5000});
toast({type:'success',title:'¡Tardará unos segundos en descargar! \n Ten en cuenta que durará 1 segundo (aprox.) por categoria.'})
});
$(document).on("click","#crear",function(){$("#loader_now").click();$('#loader_content').append("<b class='row justify-content-center text-white'>¡¡Puede tardar unos segundos!!</b>");});
</script>
<?php include '../common/footer.php';?>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script><script src="../vendor/jquery-easing/jquery.easing.min.js"></script><script src="../js/sb-admin.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.29.0/dist/sweetalert2.all.min.js"></script>
</div>
</body>
</html>
