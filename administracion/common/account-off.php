<?php
if(isset($_COOKIE['id_user'],$_COOKIE['_validate'],$_COOKIE['expires_in'])){
$validate=$_COOKIE['_validate'];
if($validate==1){header('location: https://app.oxas.tech/administracion');}}
?>
