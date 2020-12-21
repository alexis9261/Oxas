<?php
ob_start();
set_time_limit(0);
session_start();
require '../common/conexion.php';require '../common/take_at.php'; require '../common/account-off.php';require '../common/callback.php';require '../vendor/PHPExcel/Classes/PHPExcel.php';
$status='';
if(isset($_GET['activas']) && $_GET['activas']==1){$status='active';}
if(isset($_GET['pausadas']) && $_GET['pausadas']==1){$status.=',paused';}
if(isset($_GET['inactivas']) && $_GET['inactivas']==1){$status.=',closed';}
if($status!=''){
$objPhpExcel=new PHPExcel();
$objPhpExcel->getProperties()->setCreator('Oxas App')->setTitle('Publicaciones de Mercado Libre')->setKeywords('OxasApp Publicaciones MercadoLibre')->setCategory('Publicaciones');
$objPhpExcel->setActiveSheetIndex(0);$objPhpExcel->getActiveSheet()->setTitle('Publicaciones');
$objPhpExcel->getActiveSheet()->setCellValue('A1','ID');$objPhpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);$objPhpExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);$objPhpExcel->getActiveSheet()->getStyle("A")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('C4C4C4');
$objPhpExcel->getActiveSheet()->setCellValue('B1','Titulo');$objPhpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);$objPhpExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('C1','Precio');$objPhpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);$objPhpExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('D1','Stock');$objPhpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);$objPhpExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('E1','Descripción');$objPhpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);$objPhpExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('F1','Condición');$objPhpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);$objPhpExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('G1','Exposición');$objPhpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);$objPhpExcel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('H1','Categoria');$objPhpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);$objPhpExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('I1','Modo de Venta');$objPhpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);$objPhpExcel->getActiveSheet()->getStyle('I1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('J1','Estatus');$objPhpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);$objPhpExcel->getActiveSheet()->getStyle('J1')->getFont()->setBold(true);
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/'.$id_user.'/items/search?status='.$status.'&access_token='.$AccessToken.'&callback='.$callback);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);
$length=strlen($result)-6;$string=substr($result,4,$length);$var=json_decode($string);$consulta=$var[2];
$total=$consulta->paging->total;$total_items=count($consulta->results);
if($total_items!=0){
$filas=2;$resultados=$consulta->results;$contador=0;
while($contador!=$total_items){
$array_publicaciones_veinte=array();$aux=$contador+19;
for($i=$contador;$i<=$aux;$i++){if(isset($resultados[$i])){++$contador;array_push($array_publicaciones_veinte,$resultados[$i]);}}
$str_public=implode(',',$array_publicaciones_veinte);
$array_publicaciones_veinte=array();$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items?ids='.$str_public.'&attributes=id,title,available_quantity,price,category_id,listing_type_id,buying_mode,status,condition&access_token='.$AccessToken.'&callback='.$callback);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);
$length=strlen($result)-6;$string=substr($result,4,$length);$var=json_decode($string);$result=$var[2];
foreach($result as $valor){
$id=$valor->body->id;$objPhpExcel->getActiveSheet()->setCellValue("A$filas",$id);
$title=$valor->body->title;$objPhpExcel->getActiveSheet()->setCellValue("B$filas",$title);
$precio=$valor->body->price;$objPhpExcel->getActiveSheet()->setCellValue("C$filas",$precio);
$stock=$valor->body->available_quantity;$objPhpExcel->getActiveSheet()->setCellValue("D$filas",$stock);
$condicion=$valor->body->condition;$condicion=($condicion=='new')?'Nuevo':'Usado';$objPhpExcel->getActiveSheet()->setCellValue("F$filas",$condicion);
$exposicion=$valor->body->listing_type_id;if($exposicion=='free'){$exposicion='Gratis';}elseif($exposicion=='bronze'){$exposicion='Clásico';}else{$exposicion='Premium';}
$objPhpExcel->getActiveSheet()->setCellValue("G$filas",$exposicion);
$buying_mode=$valor->body->buying_mode;if($buying_mode=='buy_it_now'){$buying_mode='Precio Fijo';}elseif($buying_mode=='auction'){$buying_mode='Subasta';}else{$buying_mode='Clasificado';}
$objPhpExcel->getActiveSheet()->setCellValue("I$filas",$buying_mode);
$status=$valor->body->status;
if($status=='active'){$status='Activa';}elseif($status=='paused'){$status='Pausada';}elseif($status=='closed'){$status='Finalizada';}else{$status='Otro';}
$objPhpExcel->getActiveSheet()->setCellValue("J$filas",$status);
$categoria=$valor->body->category_id;$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/categories/'.$categoria.'?callback='.$callback);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_reset($ch);
$length=strlen($result)-6;$string=substr($result,4,$length);
$var=json_decode($string);$result=$var[2];
$categoria=$result->path_from_root[0]->name;
$objPhpExcel->getActiveSheet()->setCellValue("H$filas",$categoria);
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/items/'.$id.'/description?callback='.$callback);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);
$length=strlen($result)-6;$string=substr($result,4,$length);$var=json_decode($string);$result=$var[2];
$description=$result->plain_text;$objPhpExcel->getActiveSheet()->setCellValue("E$filas",$description);
++$filas;}}}
header('Content-Type: aplication/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Publicaciones Mercado Libre.xlsx"');
header('Cache-Control: max-age=0');
$objWriter=PHPExcel_IOFactory::createWriter($objPhpExcel,'Excel2007');
ob_end_clean();
$objWriter->save('php://output');
}
