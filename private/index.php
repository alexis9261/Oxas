<?php
session_start();
if(isset($_GET['user'], $_GET['key'])){
$usuario=$_GET['user'];
$clave=md5($_GET['key']);
// Comparar con usuario - clave:Monitoreo
if($usuario=='admin' && $clave=='e20332c50c390a95a14b2820093a45fd'){
$_SESSION['admin']='e20332c50c390a95a14b2820093a45fd';
header('Location: analitic/');
}
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="author" content="Oxas">
<title>Oxas - Private</title>
<link rel="shortcut icon" href="../img/favicon.ico">
<link href="../administracion/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<style>
html,
body{height:100%;}
body{display:-ms-flexbox;display:flex;-ms-flex-align:center;align-items:center;padding-top:40px;padding-bottom:40px;background-color:#f5f5f5;}
.form-signin{width:100%;max-width:330px;padding:15px;margin:auto;}
.form-signin .checkbox{font-weight:400;}
.form-signin .form-control{position:relative;box-sizing:border-box;height:auto;padding:10px;font-size:16px;}
.form-signin .form-control:focus{z-index:2;}
.form-signin input[type="email"]{margin-bottom:-1px;border-bottom-right-radius:0;border-bottom-left-radius:0;}
.form-signin input[type="password"]{margin-bottom:10px;border-top-left-radius:0;border-top-right-radius:0;}
.bd-placeholder-img{font-size: 1.125rem;text-anchor: middle;}
@media(min-width:768px){.bd-placeholder-img-lg {font-size:3.5rem;}}
</style>
<link href="signin.css" rel="stylesheet">
</head>
<body class="text-center">
<form class="form-signin" method="GET">
<h1 class="h3 mb-3 font-weight-normal">Acceso Privado</h1>
<label for="inputEmail" class="sr-only">Cuenta</label>
<input type="text" id="inputEmail" name="user" class="form-control" placeholder="Usuario" required autofocus>
<label for="inputPassword" class="sr-only">Contraseña</label>
<input type="password" id="inputPassword" name="key" class="form-control" placeholder="Contraseña" required>
<button class="btn btn-lg btn-primary btn-block" type="submit">Solicitar Acceso</button>
<p class="mt-5 mb-3 text-muted">&copy; Oxas - 2019</p>
</form>
</body>
</html>
