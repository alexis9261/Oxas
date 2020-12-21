<?php
//Funcion para enviar un correo de bienvenida
$asunto = "";
$cuerpo = '';
//para el envío en formato HTML
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
//dirección del remitente
$headers .= "From: OxasApp <root@oxas.tech>\r\n";
mail($destinatario,$asunto,$cuerpo,$headers);

?>
