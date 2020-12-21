<?php
include 'administracion/common/conexion.php';
$id_user = 72520274;
$access_token = "APP_USR-1153047962046613-061720-e1857425b41c3be34f25d75fc3e69aa9-72520274";
//barrido de publicaciones activas
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.mercadolibre.com/users/$id_user/items/search?status=active&access_token=$access_token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);
$result = json_decode($result);
$total = $result->paging->total;
$aux = ceil($total / 50);
for ($i = 0; $i <= $aux; $i++) {
    $offset = 50 * $i;
    //busco las publicaciones activas
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.mercadolibre.com/users/$id_user/items/search?status=active&access_token=$access_token&offset=$offset");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $consulta = json_decode($result);
    $items = $consulta->results;
    //Este ciclo sera de solo items activos
    foreach ($items as $item) {
        $sql = "SELECT * FROM publicacion where IDUSUARIO=$id_user AND CODIGO='$item' LIMIT 1;";
        $res = $conn->query($sql);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $estatus = $row['ESTATUS'];
            }
        }
        //si no esta activo en BD le cambio el estatus a 1
        if($estatus==0){
            $sql = "UPDATE publicacion SET ESTATUS=1 WHERE CODIGO='$item';";
            if($conn->query($sql)===TRUE){}
        }
    }
}

//barrido de publicaciones pausadas
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.mercadolibre.com/users/$id_user/items/search?status=paused&access_token=$access_token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);
$result = json_decode($result);
$total = $result->paging->total;
$aux = ceil($total / 50);
for ($i = 0; $i <= $aux; $i++) {
    $offset = 50 * $i;
    //busco las publicaciones activas
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.mercadolibre.com/users/$id_user/items/search?status=paused&access_token=$access_token&offset=$offset");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $consulta = json_decode($result);
    $items = $consulta->results;
    //Este ciclo sera de solo items activos
    foreach ($items as $item) {
        $sql = "SELECT * FROM publicacion where IDUSUARIO=$id_user AND CODIGO='$item' LIMIT 1;";
        $res = $conn->query($sql);
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $estatus = $row['ESTATUS'];
            }
        }
        //si esta activa en BD le cambio el estatus a 0(pausada)
        if($estatus==1){
            $sql = "UPDATE publicacion SET ESTATUS=0 WHERE CODIGO='$item';";
            if($conn->query($sql)===TRUE){}
        }
    }
}