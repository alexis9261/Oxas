<?php session_start();$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,'https://api.mercadolibre.com/sites/'.$_GET['site'].'/trends/search?category='.$_GET['category']);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);$result=curl_exec($ch);curl_close($ch);echo $result; ?>
