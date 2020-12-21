<?php
session_start();
require '../common/conexion.php';require '../common/callback.php';require '../common/take_at.php'; require '../common/account-off.php';require '../vendor/PHPExcel/Classes/PHPExcel.php';
$categorias=$_GET['categorias'];
$letras=array('B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
$objPhpExcel=new PHPExcel();
$objPhpExcel->getProperties()->setCreator('Oxas App')->setTitle('Publicaciones de Mercado Libre')->setKeywords('OxasApp Publicaciones MercadoLibre')->setCategory('Publicaciones');
$cont=0;
foreach($categorias as $value){// aca recorro todas las categorias principales
if($cont!=0){$objPhpExcel->createSheet($cont);}
$objPhpExcel->setActiveSheetIndex($cont);
$categorias_aux=explode('€',$value);$root_category=str_replace('%',' ',$categorias_aux[1]);
$objPhpExcel->getActiveSheet()->setCellValue('A1','Titulo');$objPhpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
$id_cat_ppal=$categorias_aux[0];$array_cat_ppal=$_SESSION['array_categorias'][$id_cat_ppal];$total_categorias=count($array_cat_ppal);
$cant=0;
for($k=0;$k<$total_categorias;$k++){ //aca recorro todas las categorias raices
$id_cat_raiz=$array_cat_ppal[$k];
$ch=curl_init();curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/categories/'.$id_cat_raiz.'/attributes');
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);$atributos=json_decode($result);$i=0;
foreach($atributos as $valor){
$name=$valor->name;$tags=$valor->tags;
if(isset($tags->allow_variations) && $tags->allow_variations==true){
$objPhpExcel->getActiveSheet()->setCellValue($letras[$i].'1',"$name (variante)");$objPhpExcel->getActiveSheet()->getColumnDimension($letras[$i])->setWidth(10);
++$i;
}elseif(isset($tags->catalog_required) && $tags->catalog_required==true){
$objPhpExcel->getActiveSheet()->setCellValue($letras[$i].'1',"$name (atributo)");$objPhpExcel->getActiveSheet()->getColumnDimension($letras[$i])->setWidth(10);
++$i;
}if($i>$cant){$cant=$i;}}}
$objPhpExcel->getActiveSheet()->setCellValue($letras[$cant].'1','Cantidad');$objPhpExcel->getActiveSheet()->getColumnDimension($letras[$cant])->setWidth(10);
++$cant;
$objPhpExcel->getActiveSheet()->setCellValue($letras[$cant].'1','Descripción');$objPhpExcel->getActiveSheet()->getColumnDimension($letras[$cant])->setWidth(50);
++$cant;
$objPhpExcel->getActiveSheet()->setCellValue($letras[$cant].'1','Precio');$objPhpExcel->getActiveSheet()->getColumnDimension($letras[$cant])->setWidth(12);
++$cont;
$objPhpExcel->getActiveSheet()->getStyle("A1:".$letras[$cant]."1")->getFont()->setBold(true);$objPhpExcel->getActiveSheet()->setTitle($root_category);
}$objPhpExcel->setActiveSheetIndex(0);
header('Content-Type: aplication/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Crear Publicaciones ML.xlsx"');
header('Cache-Control: max-age=0');
$objWriter= PHPExcel_IOFactory::createWriter($objPhpExcel,'Excel2007');
$objWriter->save('php://output');
?>
