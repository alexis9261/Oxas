<?php
function verificar_dia($dia_semana,$date_question){ //$dia_semana es el dia de la semana que se configuro a responder por usuario, viene de BBDD, $date_question el la fecha de la pregunta, viene de la api
  date_default_timezone_set('America/Caracas'); //indica el uso horaio con el cual trabajaran todas las funciones de fechas
  $unix=strtotime($date_question);//convierte la hora de formato iso a formato unix(integer)
  $date=getdate($unix);//convierte el formato unix en un array que contiene los minutos, horas, dias meses de la fecha....
  $day=$date["weekday"];// indica el nombre del dia de la semana, en ingles.. Sunday, monday, wednesday,....
  if ($dia_semana==$day) {
    return true;
  }else {
    return false;
  }
}
 ?>
