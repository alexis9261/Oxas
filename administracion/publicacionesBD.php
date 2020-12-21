<?php
set_time_limit(0);
session_start();
require 'common/meli.php';
require 'common/configApp.php';
require 'common/conexion.php';
require 'common/take_at.php';
require 'Oxa/Funciones.php';
require 'vendor/PHPExcel/Classes/PHPExcel.php';
$id_sellers=array();
$site="MLV";
$id_categ_principal="MLV3025";
$nombre_categ_princ="Libros";
//otras categorias
//$ids_categorias_excluyentes=array("MLV2818","MLV122664","MLV1367","MLV1740","MLV40433","MLV44011","MLV3530");
//falta: MLV3530
//electronica
//de electronica faltarian: MLV 88886
//$ids_categorias_excluyentes=array("MLV8886","MLV3835","MLV8626","MLV3837","MLV1060","MLV117274","MLV1001","MLV1941","MLV6473","MLV8702","MLV1012","MLV2912","MLV1002","MLV1006","MLV1070");
//Electrodomesticos
//$ids_categorias_excluyentes=array("MLV112138","MLV1578","MLV8458","MLV27576","MLV1576","MLV1645","MLV5977");
//falta MLV8458
$ids_categorias_excluyentes=array("MLV3044");
$ids_categorias_hijos_excluyentes=array("MLV4344","MLV74777","MLV23042","MLV30946","MLV74789");
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/categories/$id_categ_principal");
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
$result=curl_exec($ch);
curl_reset($ch);
$consulta=json_decode($result);
$categoriasHijos=$consulta->children_categories;
ini_set('memory_limit','2G');
$objPhpExcel=new PHPExcel();
/* Cambiar este comentario dependiendo de la prueba a realizar: */
/*$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
$cacheSettings = array(
//'memoryCacheSize' => '20'
'memoryCacheSize' => '8MB'
);
PHPExcel_Settings::setCacheStorageMethod($cacheMethod);*/
$objPhpExcel->getProperties()->setCreator("$id_categ_principal")->setTitle($nombre_categ_princ);
$objPhpExcel->setActiveSheetIndex(0);
$objPhpExcel->getActiveSheet()->setTitle("$id_categ_principal");
function recorridoGeneralpublicaciones($cantidad_publicac,$ch,$site,$id_categ,$AccessToken,$objPhpExcel,$i,$id_estado,$id_condicion,$id_precio){
  for($c=0;$c<$cantidad_publicac;$c++){
    $offset=50*$c;
    if($id_estado!=""){
      if($id_condicion!=""){
        if($id_precio!=""){
          curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ&access_token=$AccessToken&state=$id_estado&condition=$id_condicion&price=$id_precio&offset=$offset");
        }else{
          curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ&access_token=$AccessToken&state=$id_estado&condition=$id_condicion&offset=$offset");
        }
      }else{
        curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ&access_token=$AccessToken&state=$id_estado&offset=$offset");
      }
    }else{
      curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ&access_token=$AccessToken&offset=$offset");
    }
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $result=curl_exec($ch);
    curl_reset($ch);
    $t=json_decode($result);
    $publicaciones_result=$t->results;
    recorrerPublicaciones($objPhpExcel,$publicaciones_result,$i,$ch);
  }
}
function recorrerPublicaciones($objPhpExcel,$publicaciones_result,$i,$ch){
  foreach($publicaciones_result as $valor){
    $id_publicacion=$valor->id;
    $id_seller=$valor->seller->id;
    $titulo=$valor->title;
    $categoria=$valor->category_id;
    $mercadoLider=$valor->seller->power_seller_status;
    $estado=$valor->address->state_name;
    $ciudad=$valor->address->city_name;
    $enlace=$valor->permalink;
    $condicion=$valor->condition;
    $estado=$valor->address->state_name;
    $ciudad=$valor->address->city_name;
    $tipo_publicacion=$valor->listing_type_id;
    curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items/$id_publicacion");
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $result=curl_exec($ch);
    curl_reset($ch);
    $resultado=json_decode($result);
    $fecha_creacion=$resultado->date_created;
    $precio=$resultado->price;
    $cantidad_vendida=$resultado->sold_quantity;
    $stock_actual=$resultado->available_quantity;
    $stock_inicial=$resultado->initial_quantity;
    $tienda_oficial=$resultado->official_store_id;
    $imagenes=$resultado->pictures;
    curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/items/$id_publicacion/description");
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $result=curl_exec($ch);
    curl_reset($ch);
    $resultado=json_decode($result);
    if(isset($resultado->plain_text)){$descripcion=$resultado->plain_text;}
    ++$i;
    crearCampos($objPhpExcel,$i,$id_publicacion,$id_seller,$titulo,$categoria,$precio,$stock_inicial,$cantidad_vendida,$stock_actual,$mercadoLider,$tipo_publicacion,$estado,$ciudad,$enlace,$condicion,$descripcion,$tienda_oficial,$fecha_creacion,$imagenes);
  }
}
function crearCampos($objPhpExcel,$i,$id_publicacion,$id_seller,$titulo,$categoria,$precio,$stock_inicial,$cantidad_vendida,$stock_actual,$mercadoLider,$tipo_publicacion,$estado,$ciudad,$enlace,$condicion,$descripcion,$tienda_oficial,$fecha_creacion,$imagenes){
  $objPhpExcel->getActiveSheet()->setCellValue("A$i",$i);
  $objPhpExcel->getActiveSheet()->setCellValue("B$i",$id_publicacion);
  $objPhpExcel->getActiveSheet()->setCellValue("C$i",$id_seller);
  $objPhpExcel->getActiveSheet()->setCellValue("D$i",$titulo);
  $objPhpExcel->getActiveSheet()->setCellValue("E$i",$categoria);
  $objPhpExcel->getActiveSheet()->setCellValue("F$i",$precio);
  $objPhpExcel->getActiveSheet()->setCellValue("G$i",$stock_inicial);
  $objPhpExcel->getActiveSheet()->setCellValue("H$i",$cantidad_vendida);
  $objPhpExcel->getActiveSheet()->setCellValue("I$i",$stock_actual);
  $objPhpExcel->getActiveSheet()->setCellValue("J$i",$mercadoLider);
  $objPhpExcel->getActiveSheet()->setCellValue("K$i",$tipo_publicacion);
  $objPhpExcel->getActiveSheet()->setCellValue("L$i",$estado);
  $objPhpExcel->getActiveSheet()->setCellValue("M$i",$ciudad);
  $objPhpExcel->getActiveSheet()->setCellValue("N$i",$enlace);
  $objPhpExcel->getActiveSheet()->setCellValue("O$i",$condicion);
  $objPhpExcel->getActiveSheet()->setCellValue("P$i",$descripcion);
  $objPhpExcel->getActiveSheet()->setCellValue("Q$i",$tienda_oficial);
  $objPhpExcel->getActiveSheet()->setCellValue("R$i",$fecha_creacion);
  $x=0;
  foreach($imagenes as $imagen){
    if($x==0){
      $objPhpExcel->getActiveSheet()->setCellValue("S$i",$i);
    }elseif($x==1){
      $objPhpExcel->getActiveSheet()->setCellValue("T$i",$i);
    }elseif($x==2){
      $objPhpExcel->getActiveSheet()->setCellValue("U$i",$i);
    }elseif($x==3){break;}
    ++$x;
  }
}
?>
<!DOCTYPE html>
<html dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Base de Datos posibles clientes</title>
</head>
<body>
  <?php echo $AccessToken;$i=1;?>
  <br>
  <?php
  foreach($categoriasHijos as $key){
    $id_categ=$key->id;
    if(!in_array($id_categ,$ids_categorias_excluyentes)){
      $nombre_categ=$key->name;
      $total_items=$key->total_items_in_this_category;
      echo "La categoria Ppal $nombre_categ Tiene $total_items items. <br>";
      if($total_items>10000){
        curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/categories/$id_categ");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $result=curl_exec($ch);
        curl_reset($ch);
        $consulta=json_decode($result);
        $categoriasHijosHijos=$consulta->children_categories;
        if(count($categoriasHijosHijos)>0){
          foreach($categoriasHijosHijos as $child){
            $id_categ=$child->id;
            if(!in_array($id_categ,$ids_categorias_hijos_excluyentes)){
              $total_items_child=$child->total_items_in_this_category;
              echo "--La categoria hijo $child->name Tiene $total_items_child items. <br>";
              if($total_items_child>10000){
                curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/categories/$id_categ");
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                $result=curl_exec($ch);
                curl_reset($ch);
                $consulta=json_decode($result);
                $categoriasHijosHijosHijos=$consulta->children_categories;
                if(count($categoriasHijosHijosHijos)>0){
                  foreach($categoriasHijosHijosHijos as $child2){
                    $id_categ=$child2->id;
                    $namechild2=$child2->name;
                    $total_items_child_child=$child2->total_items_in_this_category;
                    if($total_items>10000){
                      curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ");
                      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                      $result=curl_exec($ch);
                      curl_reset($ch);
                      $t=json_decode($result);
                      $array_filtros=$t->available_filters;
                      //encuentro los filtros, por estado, precio y condicion(nuevo o usado)
                      foreach($array_filtros as $filtros){
                        if($filtros->id=="state"){
                          $array_estados=$filtros->values;
                        }else if($filtros->id=="price"){
                          $array_precio=$filtros->values;
                        }else if($filtros->id=="condition"){
                          $array_condicion=$filtros->values;
                        }
                      }
                      foreach($array_estados as $estado){
                        $id_estado=$estado->id;
                        curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ&state=$id_estado");
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        $result=curl_exec($ch);
                        curl_reset($ch);
                        $result=json_decode($result);
                        $total_items_estado=$result->paging->total;
                        if($total_items_estado>10000){
                          foreach($array_condicion as $condicion){
                            $id_condicion=$condicion->id;
                            curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ&state=$id_estado&condition=$id_condicion");
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            $result=curl_exec($ch);
                            curl_reset($ch);
                            $result=json_decode($result);
                            $total_items_estado_condicion=$result->paging->total;
                            if($total_items_estado_condicion>10000){
                              foreach($array_precio as $precio){
                                $id_precio=$precio->id;
                                curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ&state=$id_estado&condition=$id_condicion&price=$id_precio");
                                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                                $result=curl_exec($ch);
                                curl_reset($ch);
                                $result=json_decode($result);
                                $total_items_estado_condicion_precio=$result->paging->total;
                                if($total_items_estado_condicion_precio>10000){
                                  // hay q hacer otra cosa para filtrar
                                }else{
                                  echo "-----La categoria $id_categ Tiene $total_items_estado items. Menos de 10mil<br>";
                                  $cantidad_publicac=ceil($total_items_estado_condicion_precio/50);
                                  recorridoGeneralpublicaciones($cantidad_publicac,$ch,$site,$id_categ,$AccessToken,$objPhpExcel,$i,$id_estado,$id_condicion,$id_precio);
                                }
                              }
                            }else{
                              echo "-----La categoria $id_categ Tiene $total_items_estado items. Menos de 10mil<br>";
                              $cantidad_publicac=ceil($total_items_estado_condicion/50);
                              recorridoGeneralpublicaciones($cantidad_publicac,$ch,$site,$id_categ,$AccessToken,$objPhpExcel,$i,$id_estado,$id_condicion,"");
                            }
                          }
                        }else{
                          echo "-----La categoria $id_categ Tiene $total_items_estado items. Menos de 10mil<br>";
                          $cantidad_publicac=ceil($total_items_estado/50);
                          recorridoGeneralpublicaciones($cantidad_publicac,$ch,$site,$id_categ,$AccessToken,$objPhpExcel,$i,$id_estado,"","");
                        }
                      }
                    }else{
                      echo "-----La categoria hijo del Hijo anterior es $namechild2 Tiene $total_items_child_child items. <br>";
                      $cantidad_publicac=ceil($total_items_child_child/50);
                      recorridoGeneralpublicaciones($cantidad_publicac,$ch,$site,$id_categ,$AccessToken,$objPhpExcel,$i);
                    }
                  }
                }else{
                  // No tiene catagorias hijos y es mayor q 10mil, se debe filtrar por filtros
                  curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ");
                  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                  $result=curl_exec($ch);
                  curl_reset($ch);
                  $t=json_decode($result);
                  $array_filtros=$t->available_filters;
                  //encuentro los filtros, por estado, precio y condicion(nuevo o usado)
                  foreach($array_filtros as $filtros){
                    if($filtros->id=="state"){
                      $array_estados=$filtros->values;
                    }else if($filtros->id=="price"){
                      $array_precio=$filtros->values;
                    }else if($filtros->id=="condition"){
                      $array_condicion=$filtros->values;
                    }
                  }
                  foreach($array_estados as $estado){
                    $id_estado=$estado->id;
                    curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ&state=$id_estado");
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    $result=curl_exec($ch);
                    curl_reset($ch);
                    $result=json_decode($result);
                    $total_items_estado=$result->paging->total;
                    if($total_items_estado>10000){
                      foreach($array_condicion as $condicion){
                        $id_condicion=$condicion->id;
                        curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ&state=$id_estado&condition=$id_condicion");
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        $result=curl_exec($ch);
                        curl_reset($ch);
                        $result=json_decode($result);
                        $total_items_estado_condicion=$result->paging->total;
                        if($total_items_estado_condicion>10000){
                          foreach($array_precio as $precio){
                            $id_precio=$precio->id;
                            curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ&state=$id_estado&condition=$id_condicion&price=$id_precio");
                            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                            $result=curl_exec($ch);
                            curl_reset($ch);
                            $result=json_decode($result);
                            $total_items_estado_condicion_precio=$result->paging->total;
                            if($total_items_estado_condicion_precio>10000){
                              // hay q hacer otra cosa para filtrar
                            }else{
                              echo "-----La categoria $id_categ Tiene $total_items_estado items. Menos de 10mil<br>";
                              $cantidad_publicac=ceil($total_items_estado_condicion_precio/50);
                              recorridoGeneralpublicaciones($cantidad_publicac,$ch,$site,$id_categ,$AccessToken,$objPhpExcel,$i,$id_estado,$id_condicion,$id_precio);
                            }
                          }
                        }else{
                          echo "-----La categoria $id_categ Tiene $total_items_estado_condicion items. Menos de 10mil<br>";
                          $cantidad_publicac=ceil($total_items_estado_condicion/50);
                          recorridoGeneralpublicaciones($cantidad_publicac,$ch,$site,$id_categ,$AccessToken,$objPhpExcel,$i,$id_estado,$id_condicion,"");
                        }
                      }
                    }else{
                      echo "-----La categoria $id_categ Tiene $total_items_estado items. Menos de 10mil<br>";
                      $cantidad_publicac=ceil($total_items_estado/50);
                      recorridoGeneralpublicaciones($cantidad_publicac,$ch,$site,$id_categ,$AccessToken,$objPhpExcel,$i,$id_estado,"","");
                    }
                  }
                }
              }else{
                $cantidad_publicac=ceil($total_items_child/50);
                recorridoGeneralpublicaciones($cantidad_publicac,$ch,$site,$id_categ,$AccessToken,$objPhpExcel,$i);
              }
            }
          }
        }else{
          curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ");
          curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
          $result=curl_exec($ch);
          curl_reset($ch);
          $t=json_decode($result);
          $array_filtros=$t->available_filters;
          foreach($array_filtros as $filtros){
            if($filtros->id=="state"){
              $array_estados=$filtros->values;
            }else if($filtros->id=="price"){
              $array_precio=$filtros->values;
            }else if($filtros->id=="condition"){
              $array_condicion=$filtros->values;
            }
          }
          foreach($array_estados as $estado){
            $id_estado=$estado->id;
            curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ&state=$id_estado");
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            $result=curl_exec($ch);
            curl_reset($ch);
            $result=json_decode($result);
            $total_items_estado=$result->paging->total;
            if($total_items_estado>10000){
              foreach($array_condicion as $condicion){
                $id_condicion=$condicion->id;
                curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ&state=$id_estado&condition=$id_condicion");
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                $result=curl_exec($ch);
                curl_reset($ch);
                $result=json_decode($result);
                $total_items_estado_condicion=$result->paging->total;
                if($total_items_estado_condicion>10000){
                  foreach($array_precio as $precio){
                    $id_precio=$precio->id;
                    curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$site/search?category=$id_categ&state=$id_estado&condition=$id_condicion&price=$id_precio");
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    $result=curl_exec($ch);
                    curl_reset($ch);
                    $result=json_decode($result);
                    $total_items_estado_condicion_precio=$result->paging->total;
                    if($total_items_estado_condicion_precio>10000){
                      // hay q hacer otra cosa para filtrar
                    }else{
                      echo "----- Entro en Estado, Condicion y Precio. Hay $total_items_estado_condicion_precio items. Menos de 10mil<br>";
                      $cantidad_publicac=ceil($total_items_estado_condicion_precio/50);
                      recorridoGeneralpublicaciones($cantidad_publicac,$ch,$site,$id_categ,$AccessToken,$objPhpExcel,$i,$id_estado,$id_condicion,$id_precio);
                    }
                  }
                }else{
                  echo "-----La categoria $id_categ Tiene $total_items_estado items. Menos de 10mil<br>";
                  $cantidad_publicac=ceil($total_items_estado_condicion/50);
                  recorridoGeneralpublicaciones($cantidad_publicac,$ch,$site,$id_categ,$AccessToken,$objPhpExcel,$i,$id_estado,$id_condicion,"");
                }
              }
            }else{
              echo "-----La categoria $id_categ Tiene $total_items_estado items. Menos de 10mil<br>";
              $cantidad_publicac=ceil($total_items_estado/50);
              recorridoGeneralpublicaciones($cantidad_publicac,$ch,$site,$id_categ,$AccessToken,$objPhpExcel,$i,$id_estado,"","");
            }
          }
        }
      }else{
        $cantidad_publicac=ceil($total_items/50);
        recorridoGeneralpublicaciones($cantidad_publicac,$ch,$site,$id_categ,$AccessToken,$objPhpExcel,$i,"","","");
      }
    }
  }
  curl_close($ch);
  header('Content-Type: aplication/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment;filename="Publicaciones Mercado Libre.xlsx"');
  header('Cache-Control: max-age=0');
  $objWriter=PHPExcel_IOFactory::createWriter($objPhpExcel,'Excel2007');
  ob_end_clean();
  $objWriter->save('php://output');
  ?>
</body>
</html>
