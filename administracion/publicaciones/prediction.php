<?php
require '../common/take_at.php';
session_start();
$id_user=$_COOKIE['id_user'];$category_root=$_GET['category_root'];$title=$_GET['title'];
$title=urlencode($title);$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,"https://api.mercadolibre.com/sites/$siteId/category_predictor/predict?title=".$title."&seller_id=".$id_user."&category_from=".$category_root);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);echo $result;
 ?>
