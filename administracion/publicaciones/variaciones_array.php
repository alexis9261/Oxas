<?php session_start();$name=$_GET['name'];$name=str_replace(" ","|",$name);$band=$_GET['band'];
if($band==0){array_push($_SESSION['array_variaciones'],$name);print_r($_SESSION['array_variaciones']);}else{array_push($_SESSION['array_atributos'],$name);print_r($_SESSION['array_atributos']);}
 ?>
