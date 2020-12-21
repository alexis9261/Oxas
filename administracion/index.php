<?php
session_start();
require 'common/meli.php';
require 'common/conexion.php';
require 'common/take_at.php';
if(!isset($_SESSION['verify'])){
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/me?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$var=json_decode($result);
$_SESSION['verify']=true;
$name=$var->first_name;
$lastname=$var->last_name;
$telppal=@$var->phone->area_code.$var->phone->number;
$telsecond=@$var->alternative_phone->area_code.$var->alternative_phone->number;
$_SESSION['permalink']=$var->permalink;
$_SESSION['date']=$var->registration_date;
$_SESSION['experiencia']=$var->seller_experience;
$_SESSION['reputacion']=$var->seller_reputation->level_id;
$_SESSION['completadas']=$var->seller_reputation->transactions->completed;
$_SESSION['canceladas']=$var->seller_reputation->transactions->canceled;
//actualizamos la ultima fecha de acceso
$fecha_actual=date("Y-m-d H:i:s");
$sql="UPDATE usuario SET NAME='$name',LASTNAME='$lastname',TELPPAL='$telppal',TELSECOND='$telsecond',ULTIMAFECHA='$fecha_actual' WHERE IDUSUARIO=$id_user;";
$result=$conn->query($sql);
}
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/messages/pending_read?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);$var=json_decode($result);
$mensajes_pendientes=$var->results;
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/'.$id_user.'/mercadopago_account/balance?access_token='.$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$var=json_decode($result);
$monto_total=$var->total_amount;$monto_disponible=$var->available_balance;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<link rel="icon" type="image/png" href=""/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Administracion de ventas en Mercado Libre">
<meta name="author" content="Euxtuxia Web">
<link rel="shortcut icon" href="../img/favicon.ico">
<title>Oxas</title>
<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<script src="vendor/jquery/jquery.min.js"></script>
<link href="css/sb-admin.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<script>
$(document).ready(function(){$('form').keypress(function(e){if(e==13){return false;}});$('input').keypress(function(e){if(e.which==13){return false;}});});
</script>
</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">
<?php include 'common/navbar.php';?>
<div class="content-wrapper">
<div class="container-fluid">
<?php include "common/breadcrumb.php";?>
<br>
<div class="alert alert-info" role="alert">
Dudas, Sugerencias o Comentarios<br> <small class="text-muted">Esta es Nuestra <span class="text-dark">Versión Beta</span>, por lo que tu opinión y comentarios son importantes para mejorar la Plataforma Oxas.
<br>Puedes llamarnos o escribirnos via <a href="https://wa.me/584124364546" target="_blank">Whatsapp</a> al: +58 414 344 62 25.</small>
</div>
<?php
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/questions/search?seller_id=".$id_user."&status=unanswered&access_token=".$AccessToken);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result2=curl_exec($ch);curl_close($ch);$consulta2=json_decode($result2);
if($consulta2->total!=0){
?>
<div class="alert alert-danger" role="alert">
¡Tienes <?php echo $consulta2->total;?> preguntas sin contestar! <br><small class="text-muted"> Ve a <a href="preguntas/">Preguntas pendientes</a> para contestarlas.</small>
</div>
<?php }
if(!empty($mensajes_pendientes)){$cantidad=0;
foreach($mensajes_pendientes as $valor){$cantidad+=$valor->count;}
?>
<div class="alert alert-success" role="alert">
¡Tienes <?php echo $cantidad;?> mensaje(s) sin leer!<br><small class="text-muted"> Ve a <a href="ventas/"> Ventas</a> y soluciona todas las dudas de tus compradores.</small>
</div>
<?php } ?>
<div class="alert alert-primary" role="alert">
Te recomendamos completes las configuraciones generales para aprovechar al máximo nuestros servicios.<br><small class="text-muted">Ve a <a href="common/generales.php">Configuración general</a> para completarlas.</small>
</div>
<div class="container-fluid">
<div class="row justify-content-around">
<div class="col-sm-5 breadcrumb text-center">
<div class="row">
<strong class="col-12 text-muted" title="Saldo de Mercado Pago" data-toggle="tooltip">Mercado Pago:</strong>
<div class="col-12 text-success my-2">Monto en cuenta:<strong><?php echo number_format($monto_total,2);?> Bs.</strong></div>
<div class="col-12"><strong>Monto disponible: <?php echo number_format($monto_disponible,2);?> Bs.</strong></div>
</div>
</div>
<div class="col-sm-5 breadcrumb text-center">
<div class="row">
<strong class="col-12 text-muted" title="Transacciones realizadas como vendedor" data-toggle="tooltip">Ventas de Mercado Libre:</strong>
<div class="col-12 text-success my-2"><strong><?php echo $_SESSION["completadas"];?> Concretadas</strong></div>
<div class="col-12 text-"><strong><?php echo $_SESSION["canceladas"];?> Canceladas</strong></div>
</div>
</div>
</div>
</div>
</div>
<script>
window.onload=function() {
      console.log("<?php echo $AccessToken;?>");
		}
</script>
<?php include 'common/footer.php';?>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/sb-admin.min.js"></script>
</div>
</body>
</html>
