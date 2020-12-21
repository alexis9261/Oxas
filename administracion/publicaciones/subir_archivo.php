<?php
session_start();
require '../common/conexion.php';require '../common/take_at.php';require '../common/account-off.php';
$archivo=(isset($_FILES['archivoupload'])) ? $_FILES['archivoupload']:null;
if($archivo){$extension=pathinfo($archivo['name'],PATHINFO_EXTENSION);$extension=strtolower($extension);
$extension_correcta=($extension=='xlsx' or $extension=='xls');
if($extension_correcta){$ruta_destino_archivo="temporal/$id_user.$extension";
$archivo_ok=move_uploaded_file($archivo['tmp_name'],$ruta_destino_archivo);
$_SESSION['archivo']=$ruta_destino_archivo;}}
header('Location: publicar_masiva.php');
