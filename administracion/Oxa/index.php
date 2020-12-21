<?php
session_start();
include 'Funciones.php';
require 'conexion.php';
require '../common/take_at.php';
if (!isset($_SESSION['array_historial'])) {
    $_SESSION['array_historial'] = array();
}
$auxiliar = array(); //este array tendra el titulo de la publicacion, la pregunta y la respuesta respectivamente
$id_user = $_COOKIE['id_user'];
if (isset($_GET['preg'], $_GET['public'])) {
    $msn = '';
    $idpublic = $_GET['public'];
    $p = $_GET['preg'];
    $ps = decodificador($p);
    $ids = razonador($ps, $id_user, $idpublic);
    $msn = codificador($ids);
    if ($msn != NULL) {
        $sql = "SELECT SALUDO, DESPEDIDA FROM `usuario` WHERE IDUSUARIO=$id_user";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $saludo = $row['SALUDO'];
                $firma = $row['DESPEDIDA'];
            }
        }
        $msn = $saludo . ' ' . $msn . ' ' . $firma;
    }
} else {
    $msn = '';
}
$publicaciones_array = array();
$sql = "SELECT * FROM `publicacion` WHERE `IDUSUARIO`=$id_user AND `ESTATUS`=1;";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pu = array('id' => $row['IDPUBLICACION'], 'nombre' => $row['NOMBRE']);
        if (isset($_GET['preg'], $_GET['public'])) {
            if ($idpublic == $row['IDPUBLICACION']) {
                array_push($auxiliar, $row['NOMBRE']);
            }
        }
        array_push($publicaciones_array, $pu);
    }
}
$conn->close();
if (isset($_GET['preg'], $_GET['public'])) {
    array_push($auxiliar, $p);
    array_push($auxiliar, $msn);
    array_unshift($_SESSION['array_historial'], $auxiliar);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../img/favicon.ico">
    <title>Oxas - Configuración</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../css/sb-admin.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link rel="icon" type="image/vnd.microsoft.icon" href="../../img/favicon.ico">
</head>

<body class="fixed-nav sticky-footer bg-dark" id="page-top">
    <?php include '../common/navbar.php'; ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <?php include "../common/breadcrumb.php"; ?>
            <div class="container-fluid">
            <?php echo $id_user; ?>
                <form class="" action="" method="GET">
                    <div class="form-group">
                        <div class="row breadcrumb p-0 pr-1 m-0 mb-2">
                            <div class='input-group input-group-sm'>
                                <div class='input-group-prepend'><label class='input-group-text' for='"+id_aux+"'>Selecciona una publicación</label></div>
                                <select class='custom-select custom-select-sm' name='public'>
                                    <?php
                                    foreach ($publicaciones_array as $p) {
                                        if ($idpublic == $p['id']) {
                                    ?>
                                            <option value="<?= $p['id'] ?>" selected><?= $p['nombre'] ?></option>
                                        <?php } else { ?>
                                            <option value="<?= $p['id'] ?>"><?= $p['nombre'] ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row breadcrumb m-0 p-0">
                            <label class="text-success">¡Realiza una pregunta interesante!</label>
                            <input type="text" class="form-control" id="preg" aria-describedby="preg" placeholder="Ej: Realizan envios a todo el pais?" name="preg" autofocus>
                        </div>
                        <div class="row justify-content-center mt-3"><button type="submit" class="btn btn-sm btn-primary col-sm-2 text-white">Preguntar</button></div>
                    </div>
                </form>
                <label class="text-muted">Respuesta Automática:</label>
                <div class="alert alert-success heigth-response" role="alert">
                    <?php if (isset($idpublic) && $msn == '') { ?>
                        <span class="text-danger">No hay respuesta creada para esta pregunta, en esta publicación</span>
                    <?php } else {
                        echo $msn;
                    } ?>
                </div>
                <label class="text-muted">Historial:</label>
                <div class="container">
                    <?php foreach ($_SESSION['array_historial'] as $value) { ?>
                        <div class="row mt-2">
                            <small class="col-12"><b class="text-muted"><?php echo $value[0]; ?></b></small>
                            <small class="col-12">Pregunta: <span class="text-muted"><?php echo $value[1]; ?></span></small>
                            <small class="col-12">
                                Respuesta automática:
                                <?php if ($value[2] != '') { ?>
                                    <span class="text-success">
                                    <?php echo $value[2];
                                } else { ?>
                                        <span class="text-danger"><?php echo "No existe respuesta programada para esta publicación.";
                                                                } ?></span>
                            </small>
                        </div>
                    <?php } ?>
                </div>
                <div class="container text-center mt-5">
                    <a class="btn btn-sm btn-primary col-sm-2" href="../respuestas_automaticas" title="Salir simulador" data-toggle="tooltip">Volver</a>
                </div>
            </div>
        </div>
        <?php include '../common/footer.php'; ?>
        <script src="../vendor/jquery/jquery.min.js"></script>
        <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="../js/sb-admin.min.js"></script>
    </div>
</body>

</html>