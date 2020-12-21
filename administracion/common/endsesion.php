<?php
session_start();
session_destroy();
setcookie("_validate", "", time() - 10, "/");
setcookie("expires_in", "", time() - 10, "/");
setcookie("id_user", "", time() - 10, "/");
header("Location: https://app.oxas.tech/");
die();
?>
