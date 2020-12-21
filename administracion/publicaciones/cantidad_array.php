<?php
session_start();
$cant=$_GET['items'];$aux=count($_SESSION['array_publicaciones']);
if((isset($_SESSION['array_publicaciones'][0]) && $_SESSION['array_publicaciones'][0]!=1) || $aux==0){echo $aux;
}else{if($aux>1){$auxiiar=(1-$aux+$cant);echo $auxiiar;}else{echo "todas las ";}}
?>
