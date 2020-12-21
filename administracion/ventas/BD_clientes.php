<?php
set_time_limit(0);
require '../common/conexion.php';
require '../common/take_at.php'; require '../common/account-off.php';
require '../common/callback.php';
require '../vendor/PHPExcel/Classes/PHPExcel.php';
$objPhpExcel=new PHPExcel();
$objPhpExcel->getProperties()->setCreator('Oxas App')->setTitle('Base de datos de clientes')->setDescription('Datos de los clientes de Mercado Libre del usuario xxxx,')->setKeywords('OxasApp BBDD Clientes MercadoLibre Ventas')->setCategory('Ventas');
$objPhpExcel->setActiveSheetIndex(0);
$objPhpExcel->getActiveSheet()->setTitle('Clientes');
$objPhpExcel->getActiveSheet()->setCellValue('A1','Nickname');
$objPhpExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$objPhpExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('B1','Nombre y Apellido');
$objPhpExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPhpExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('C1','Teléfono Principal');
$objPhpExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPhpExcel->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('D1','Teléfono secundario');
$objPhpExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPhpExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('E1','Estado');
$objPhpExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPhpExcel->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('F1','Ciudad');
$objPhpExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
$objPhpExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('G1','Fecha de Compra');
$objPhpExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPhpExcel->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('H1','Cantidad');
$objPhpExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
$objPhpExcel->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('I1','Artículos comprados');
$objPhpExcel->getActiveSheet()->getColumnDimension('I')->setWidth(55);
$objPhpExcel->getActiveSheet()->getStyle('I1')->getFont()->setBold(true);
$objPhpExcel->getActiveSheet()->setCellValue('J1','Costo total de Venta');
$objPhpExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$objPhpExcel->getActiveSheet()->getStyle('J1')->getFont()->setBold(true);
$mes=[0,'Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/orders/search?seller='.$id_user.'&sort=date_desc&access_token='.$AccessToken.'&callback='.$callback);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);
curl_close($ch);
$length=strlen($result)-6;
$string=substr($result,4,$length);
$var=json_decode($string);
$consulta=$var[2];
$total_ventas=$consulta->paging->total;
if($total_ventas!=0){
  $contador=2;
  $pages=ceil($total_ventas/50);
  for($w=0;$w<$pages;$w++){
    $offset=$w*50;
    if($offset!=0){
      $ch=curl_init();
      curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/orders/search?seller='.$id_user.'&sort=date_desc&access_token='.$AccessToken.'&offset='.$offset.'&callback='.$callback);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      $result=curl_exec($ch);
      curl_close($ch);
      $length=strlen($result)-6;
      $string=substr($result,4,$length);
      $var=json_decode($string);
      $consulta=$var[2];
    }
  $resultados=$consulta->results;
  foreach($resultados as $value){
    $client=$value->buyer->id;
    $nickname=$value->buyer->nickname;
    $objPhpExcel->getActiveSheet()->setCellValue("A$contador",$nickname);
    $first=strtolower($value->buyer->first_name);
    $last=strtolower($value->buyer->last_name);
    $name=ucwords($first.' '.$last);
    $objPhpExcel->getActiveSheet()->setCellValue("B$contador",$name);
    $principal=(isset($value->buyer->phone->area_code) && !empty($value->buyer->phone->area_code))?$value->buyer->phone->area_code.' '.$value->buyer->phone->number:$value->buyer->phone->number;
    $objPhpExcel->getActiveSheet()->setCellValue("C$contador",$principal);
    $secundario=(isset($value->buyer->alternative_phone->area_code) && !empty($value->buyer->alternative_phone->area_code))?$value->buyer->alternative_phone->area_code.' '.$value->buyer->alternative_phone->number:$value->buyer->alternative_phone->number;
    $objPhpExcel->getActiveSheet()->setCellValue("D$contador",$secundario);
    $fecha_compra=$value->date_created;
    if(substr($fecha_compra,5,2)>9){$fecha_compra_proc=substr($fecha_compra,8,2).' de '.$mes[substr($fecha_compra,5,2)].' a las '.substr($fecha_compra,11,5);
    }else{$fecha_compra_proc=substr($fecha_compra,8,2).' de '.$mes[substr($fecha_compra,6,1)].' a las '.substr($fecha_compra,11,5);}
    $objPhpExcel->getActiveSheet()->setCellValue("G$contador",$fecha_compra_proc);
    $order_items=$value->order_items;
    foreach($order_items as $valor){$item=$valor->item->title;$cantidad=$valor->quantity;}
    $objPhpExcel->getActiveSheet()->setCellValue("H$contador",$cantidad);
    $objPhpExcel->getActiveSheet()->setCellValue("I$contador",$item);
    $costo=$value->total_amount;
    $objPhpExcel->getActiveSheet()->setCellValue("J$contador",$costo);
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/users/'.$client.'?callback='.$callback);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $result=curl_exec($ch);
    curl_close($ch);
    $length=strlen($result)-6;
    $string=substr($result,4,$length);
    $var=json_decode($string);
    $consulta=$var[2];
    $state=$consulta->address->state;
    switch($state){
      case 'VE-A':$state='Distrito Capital';break;case 'VE-C':$state='Apure';break;case 'VE-D':$state='Aragua';break;case 'VE-B':$state='Anzoátegui';
      break;case 'VE-E':$state='Barinas';break;case 'VE-F':$state='Bolívar';break;case 'VE-G':$state='Carabobo';break;case 'VE-H':$state='Cojedes';
      break;case 'VE-I':$state='Falcón';break;case 'VE-J':$state='Guárico';break;case 'VE-K':$state='Lara';break;case 'VE-L':$state='Mérida';
      break;case 'VE-M':$state='Miranda';break;case 'VE-N':$state='Monagas';break;case 'VE-O':$state='Nueva Esparta';break;case 'VE-P':
      $state='Portuguesa';break;case 'VE-R':$state='Sucre';break;case 'VE-S':$state='Táchira';break;case 'VE-T':$state='Trujillo';break;
      case 'VE-V':$state='Zulia';break;case 'VE-W':$state='Dependencias Federales';break;case 'VE-X':$state='Vargas';break;case 'VE-Y':
      $state='Delta Amacuro';break;case 'VE-Z':$state='Amazonas';break;case 'VE-U':$state='Yaracuy';break;default:$state='';break;
    }
    $objPhpExcel->getActiveSheet()->setCellValue("E$contador",$state);
    $city=$consulta->address->city;
    $objPhpExcel->getActiveSheet()->setCellValue("F$contador",$city);
    ++$contador;
  }
  }
}
header('Content-Type: aplication/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Clientes Mercado Libre.xlsx"');
header('Cache-Control: max-age=0');
$objWriter=PHPExcel_IOFactory::createWriter($objPhpExcel,'Excel2007');
$objWriter->save('php://output');
?>
