<?php
session_start();$bandera=$_GET['band'];
if($bandera==1){$cat_ppal=$_GET['cat_ppal'];
array_push($_SESSION['array_categorias'],$cat_ppal);$nuevas_categorias=$_GET['cats_root'];$categorias=explode('|',$nuevas_categorias);
$clave=array_search($cat_ppal,$_SESSION['array_categorias']);
foreach($categorias as $value){$_SESSION['array_categorias'][$cat_ppal][]=$value;}
}elseif($bandera==2){$cat_ppal=$_GET['cat_ppal'];$clave=array_search($cat_ppal,$_SESSION['array_categorias']);
if($clave){unset($_SESSION['array_categorias'][$clave]);}
}elseif($bandera==3){$_SESSION['array_categorias']=array();}
print_r($_SESSION['array_categorias']);
?>
