<?php
session_start();
require '../common/meli.php';require '../common/conexion.php';require '../common/take_at.php';
//Busqueda de fechas de suscripcion
$sql="SELECT DATE_FORMAT(FECHAREGISTRO, '%d/%m/%Y') as FECHAR FROM suscripcion WHERE IDUSUARIO='$id_user'";
$result=$conn->query($sql);
if($result->num_rows>0){while($row=$result->fetch_assoc()){$fechaR=$row['FECHAR'];}}
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
<script src="../vendor/jquery/jquery.min.js"></script>
<link href="../css/sb-admin.min.css" rel="stylesheet">
<link href="../css/style.css" rel="stylesheet">
<link rel="icon" type="image/vnd.microsoft.icon" href="../../img/favicon.ico">
</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">
<?php include '../common/navbar.php';?>
<div class="content-wrapper">
<div class="container-fluid">
  <div class="breadcrumb">
    <h5>Mensajes a ser enviados al momento de crearse una venta</h5>
    <button class="col-auto ml-auto px-4 btn btn-primary btn-sm" type="button" id="nuevo">Crear Nuevo Mensaje</button>
  </div>
  <div class="breadcrumb align-items-center" style="display: none;" id="contenedor">
    <span class="dias_semana_checkbok mr-2 ml-3" title="Domingo" data-toggle="tooltip">D</span>
    <span class="dias_semana_checkbok mr-2" title="Lunes" data-toggle="tooltip">L</span>
    <span class="dias_semana_checkbok mr-2" title="Martes" data-toggle="tooltip">M</span>
    <span class="dias_semana_checkbok mr-2" title="Miércoles" data-toggle="tooltip">X</span>
    <span class="dias_semana_checkbok mr-2" title="Jueves" data-toggle="tooltip">J</span>
    <span class="dias_semana_checkbok mr-2" title="Viernes" data-toggle="tooltip">V</span>
    <span class="dias_semana_checkbok mr-2" title="Sábado" data-toggle="tooltip">S</span>
    <div class="col-sm-5 my-1 ml-5">
      <select class="select_horas_mensajes" id="hora_inicio">
        <option value="00">00</option>
        <option value="01">01</option>
        <option value="02">02</option>
        <option value="03">03</option>
        <option value="04">04</option>
        <option value="05">05</option>
        <option value="06">06</option>
        <option value="07">07</option>
        <option value="08">08</option>
        <option value="09">09</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        <option value="20">20</option>
        <option value="21">21</option>
        <option value="22">22</option>
        <option value="23">23</option>
      </select> :
      <select class="select_horas_mensajes" id="minutos_inicio">
        <option value="00">00</option>
        <option value="01">01</option>
        <option value="02">02</option>
        <option value="03">03</option>
        <option value="04">04</option>
        <option value="05">05</option>
        <option value="06">06</option>
        <option value="07">07</option>
        <option value="08">08</option>
        <option value="09">09</option>
        <option value="10">10</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        <option value="20">20</option>
        <option value="21">21</option>
        <option value="22">22</option>
        <option value="23">23</option>
        <option value="24">24</option>
        <option value="25">25</option>
        <option value="26">26</option>
        <option value="27">27</option>
        <option value="28">28</option>
        <option value="29">29</option>
        <option value="30">30</option>
        <option value="31">31</option>
        <option value="32">32</option>
        <option value="33">33</option>
        <option value="34">34</option>
        <option value="35">35</option>
        <option value="36">36</option>
        <option value="37">37</option>
        <option value="38">38</option>
        <option value="39">39</option>
        <option value="40">40</option>
        <option value="41">41</option>
        <option value="42">42</option>
        <option value="43">43</option>
        <option value="44">44</option>
        <option value="45">45</option>
        <option value="46">46</option>
        <option value="47">47</option>
        <option value="48">48</option>
        <option value="49">49</option>
        <option value="50">50</option>
        <option value="51">51</option>
        <option value="52">52</option>
        <option value="53">53</option>
        <option value="54">54</option>
        <option value="55">55</option>
        <option value="56">56</option>
        <option value="57">57</option>
        <option value="58">58</option>
        <option value="59">59</option>
      </select>
      <strong>&nbsp;-&nbsp;</strong>
      <select class="select_horas_mensajes" id="hora_final">
        <option value="00">00</option>
        <option value="01">01</option>
        <option value="02">02</option>
        <option value="03">03</option>
        <option value="04">04</option>
        <option value="05">05</option>
        <option value="06">06</option>
        <option value="07">07</option>
        <option value="08">08</option>
        <option value="09">09</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        <option value="20">20</option>
        <option value="21">21</option>
        <option value="22">22</option>
        <option value="23">23</option>
      </select> :
      <select class="select_horas_mensajes" id="minutos_final">
        <option value="00">00</option>
        <option value="01">01</option>
        <option value="02">02</option>
        <option value="03">03</option>
        <option value="04">04</option>
        <option value="05">05</option>
        <option value="06">06</option>
        <option value="07">07</option>
        <option value="08">08</option>
        <option value="09">09</option>
        <option value="10">10</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        <option value="20">20</option>
        <option value="21">21</option>
        <option value="22">22</option>
        <option value="23">23</option>
        <option value="24">24</option>
        <option value="25">25</option>
        <option value="26">26</option>
        <option value="27">27</option>
        <option value="28">28</option>
        <option value="29">29</option>
        <option value="30">30</option>
        <option value="31">31</option>
        <option value="32">32</option>
        <option value="33">33</option>
        <option value="34">34</option>
        <option value="35">35</option>
        <option value="36">36</option>
        <option value="37">37</option>
        <option value="38">38</option>
        <option value="39">39</option>
        <option value="40">40</option>
        <option value="41">41</option>
        <option value="42">42</option>
        <option value="43">43</option>
        <option value="44">44</option>
        <option value="45">45</option>
        <option value="46">46</option>
        <option value="47">47</option>
        <option value="48">48</option>
        <option value="49">49</option>
        <option value="50">50</option>
        <option value="51">51</option>
        <option value="52">52</option>
        <option value="53">53</option>
        <option value="54">54</option>
        <option value="55">55</option>
        <option value="56">56</option>
        <option value="57">57</option>
        <option value="58">58</option>
        <option value="59">59</option>
      </select>
    </div>
    <div class="col-12 mt-3">
      <textarea class="form-control" width="100%" rows="6" placeholder="Escribe aqui el mensaje que le enviaras al comprador al momento de crear la venta" id="mensaje"></textarea>
    </div>
    <div class="col-auto justify-self-center mt-2">
      <button class="btn btn-primary btn-sm" type="button" id="crearMensaje">Crear Mensaje</button>
    </div>
  </div>
  <hr class="my-0 my-1">
  <div class="breadcrumb">
    <span class="text-muted">Mensajes Automáticos Creados</span>
  </div>
  <?php
  $sql="SELECT IDMENSAJE,MENSAJE,HORA,DIAS FROM mensajes WHERE USUARIOID=$id_user";
  $result=$conn->query($sql);
  if($result->num_rows>0){
    while($row=$result->fetch_assoc()){
      $id_mensaje=$row['IDMENSAJE'];
      $mensaje=$row['MENSAJE'];
      $hora=$row['HORA'];
      if(strlen($hora)<8){
        $hora_inicial=substr($hora,0,1);
        $minutos_inicial=substr($hora,1,2);
        $hora_final=substr($hora,3,2);
        $minutos_final=substr($hora,5,2);
      }else{
        $hora_inicial=substr($hora,0,2);
        $minutos_inicial=substr($hora,2,2);
        $hora_final=substr($hora,4,2);
        $minutos_final=substr($hora,6,2);
      }
      $dias=$row['DIAS'];
      $array_dias=explode(",",$dias);
      $active_D="";$active_L="";$active_M="";$active_X="";$active_J="";$active_V="";$active_S="";
      if(in_array("D",$array_dias)){$active_D=" active ";}
      if(in_array("L",$array_dias)){$active_L=" active ";}
      if(in_array("M",$array_dias)){$active_M=" active ";}
      if(in_array("X",$array_dias)){$active_X=" active ";}
      if(in_array("J",$array_dias)){$active_J=" active ";}
      if(in_array("V",$array_dias)){$active_V=" active ";}
      if(in_array("S",$array_dias)){$active_S=" active ";}
      ?>
      <div class="row" id="contenedor<?php echo $id_mensaje;?>">
        <span class="dias_semana_checkbok_BD <?php echo $active_D;?> mr-2 ml-3" title="Domingo" data-toggle="tooltip">D</span>
        <span class="dias_semana_checkbok_BD <?php echo $active_L;?> mr-2" title="Lunes" data-toggle="tooltip">L</span>
        <span class="dias_semana_checkbok_BD <?php echo $active_M;?> mr-2" title="Martes" data-toggle="tooltip">M</span>
        <span class="dias_semana_checkbok_BD <?php echo $active_X;?> mr-2" title="Miércoles" data-toggle="tooltip">X</span>
        <span class="dias_semana_checkbok_BD <?php echo $active_J;?> mr-2" title="Jueves" data-toggle="tooltip">J</span>
        <span class="dias_semana_checkbok_BD <?php echo $active_V;?> mr-2" title="Viernes" data-toggle="tooltip">V</span>
        <span class="dias_semana_checkbok_BD <?php echo $active_S;?> mr-2" title="Sábado" data-toggle="tooltip">S</span>
        <div class="col-sm-5 my-1 ml-5">
          <select class="select_horas_mensajes" id="hora_inicio" disabled>
            <option value="<?php if(strlen($hora)<8){echo "0".$hora_inicial;}else{echo $hora_inicial;} ?>"><?php if(strlen($hora)<8){echo "0".$hora_inicial;}else{echo $hora_inicial;} ?></option>
            <option value="00">00</option>
            <option value="01">01</option>
            <option value="02">02</option>
            <option value="03">03</option>
            <option value="04">04</option>
            <option value="05">05</option>
            <option value="06">06</option>
            <option value="07">07</option>
            <option value="08">08</option>
            <option value="09">09</option>
            <option value="10">10</option>
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
            <option value="16">16</option>
            <option value="17">17</option>
            <option value="18">18</option>
            <option value="19">19</option>
            <option value="20">20</option>
            <option value="21">21</option>
            <option value="22">22</option>
            <option value="23">23</option>
          </select> :
          <select class="select_horas_mensajes" id="minutos_inicio" disabled>
            <option value="<?php echo $minutos_inicial;?>"><?php echo $minutos_inicial;?></option>
            <option value="00">00</option>
            <option value="01">01</option>
            <option value="02">02</option>
            <option value="03">03</option>
            <option value="04">04</option>
            <option value="05">05</option>
            <option value="06">06</option>
            <option value="07">07</option>
            <option value="08">08</option>
            <option value="09">09</option>
            <option value="10">10</option>
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
            <option value="16">16</option>
            <option value="17">17</option>
            <option value="18">18</option>
            <option value="19">19</option>
            <option value="20">20</option>
            <option value="21">21</option>
            <option value="22">22</option>
            <option value="23">23</option>
            <option value="24">24</option>
            <option value="25">25</option>
            <option value="26">26</option>
            <option value="27">27</option>
            <option value="28">28</option>
            <option value="29">29</option>
            <option value="30">30</option>
            <option value="31">31</option>
            <option value="32">32</option>
            <option value="33">33</option>
            <option value="34">34</option>
            <option value="35">35</option>
            <option value="36">36</option>
            <option value="37">37</option>
            <option value="38">38</option>
            <option value="39">39</option>
            <option value="40">40</option>
            <option value="41">41</option>
            <option value="42">42</option>
            <option value="43">43</option>
            <option value="44">44</option>
            <option value="45">45</option>
            <option value="46">46</option>
            <option value="47">47</option>
            <option value="48">48</option>
            <option value="49">49</option>
            <option value="50">50</option>
            <option value="51">51</option>
            <option value="52">52</option>
            <option value="53">53</option>
            <option value="54">54</option>
            <option value="55">55</option>
            <option value="56">56</option>
            <option value="57">57</option>
            <option value="58">58</option>
            <option value="59">59</option>
          </select>
          <strong>&nbsp;-&nbsp;</strong>
          <select class="select_horas_mensajes" id="hora_final" disabled>
            <option value="<?php echo $hora_final;?>"><?php echo $hora_final;?></option>
            <option value="00">00</option>
            <option value="01">01</option>
            <option value="02">02</option>
            <option value="03">03</option>
            <option value="04">04</option>
            <option value="05">05</option>
            <option value="06">06</option>
            <option value="07">07</option>
            <option value="08">08</option>
            <option value="09">09</option>
            <option value="10">10</option>
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
            <option value="16">16</option>
            <option value="17">17</option>
            <option value="18">18</option>
            <option value="19">19</option>
            <option value="20">20</option>
            <option value="21">21</option>
            <option value="22">22</option>
            <option value="23">23</option>
          </select> :
          <select class="select_horas_mensajes" id="minutos_final" disabled>
            <option value="<?php echo $minutos_final;?>"><?php echo $minutos_final;?></option>
            <option value="00">00</option>
            <option value="01">01</option>
            <option value="02">02</option>
            <option value="03">03</option>
            <option value="04">04</option>
            <option value="05">05</option>
            <option value="06">06</option>
            <option value="07">07</option>
            <option value="08">08</option>
            <option value="09">09</option>
            <option value="10">10</option>
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
            <option value="16">16</option>
            <option value="17">17</option>
            <option value="18">18</option>
            <option value="19">19</option>
            <option value="20">20</option>
            <option value="21">21</option>
            <option value="22">22</option>
            <option value="23">23</option>
            <option value="24">24</option>
            <option value="25">25</option>
            <option value="26">26</option>
            <option value="27">27</option>
            <option value="28">28</option>
            <option value="29">29</option>
            <option value="30">30</option>
            <option value="31">31</option>
            <option value="32">32</option>
            <option value="33">33</option>
            <option value="34">34</option>
            <option value="35">35</option>
            <option value="36">36</option>
            <option value="37">37</option>
            <option value="38">38</option>
            <option value="39">39</option>
            <option value="40">40</option>
            <option value="41">41</option>
            <option value="42">42</option>
            <option value="43">43</option>
            <option value="44">44</option>
            <option value="45">45</option>
            <option value="46">46</option>
            <option value="47">47</option>
            <option value="48">48</option>
            <option value="49">49</option>
            <option value="50">50</option>
            <option value="51">51</option>
            <option value="52">52</option>
            <option value="53">53</option>
            <option value="54">54</option>
            <option value="55">55</option>
            <option value="56">56</option>
            <option value="57">57</option>
            <option value="58">58</option>
            <option value="59">59</option>
          </select>
        </div>
        <span class="col-auto ml-auto">
          <button class="btn btn-info btn-sm px-3" type="button" id="editar<?php echo $id_mensaje;?>">Editar</button>
          <button class="btn btn-danger btn-sm px-3" type="button" id="eliminar<?php echo $id_mensaje;?>">Eliminar</button>
        </span>
        <div class="col-12 mt-3">
          <textarea class="form-control" width="100%" rows="4" disabled><?php echo $mensaje;?></textarea>
        </div>
      </div>
      <hr>
      <script>
        $(document).on('click',"#eliminar<?php echo $id_mensaje;?>",function(){
          var id_mensaje=<?php echo $id_mensaje;?>;
          $.get('ajax/delete_mensaje.php',{id_mensaje:id_mensaje},verificar,'text');
          function verificar(respuesta){
            if(respuesta==1){
              const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
              toast({type:'success',title:'¡El Mensaje fue Eliminado Exitosamente!'})
              $("#contenedor<?php echo $id_mensaje;?>").remove();
            }else{
              const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
              toast({type:'error',title:"¡Hubo un problema! \n Inténtalo de nuevo."})
            }
          }
        });
      </script>
      <?php
    }
  }
   ?>
</div>
<script>
  $(document).on('click',"#nuevo",function(){$("#contenedor").fadeIn(1500);});
</script>
<script>
  $(document).on('click',"span.dias_semana_checkbok",function(){
    $(this).addClass('active');
  });
  $(document).on('click',"span.dias_semana_checkbok.active",function(){
    $(this).removeClass('active');
  });
</script>
<script>
  $(document).on('click',"#crearMensaje",function(){
    var mensaje=$("#mensaje").val();
    var hora_inicio=$("#hora_inicio").val();
    var minutos_inicio=$("#minutos_inicio").val();
    var hora_final=$("#hora_final").val();
    var minutos_final=$("#minutos_final").val();
    var horas=hora_inicio+minutos_inicio+hora_final+minutos_final;
    var dias="";
    var user_id=<?php echo $id_user;?>;
    $("span.dias_semana_checkbok.active").each(function(){
      if(dias==""){dias=this.innerHTML;}else{dias=dias+","+this.innerHTML;}
    });
    if(mensaje=="" || dias=="" || horas==00000000){
      const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
      toast({type:'info',title:'¡Falta algún campo por completar!'})
    }else{
      $.get('ajax/new_mensaje.php',{mensaje:mensaje,horas:horas,dias:dias,user_id:user_id},verificar,'json');
      function verificar(respuesta){
        if(respuesta==1){
          const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
          toast({type:'success',title:'¡El Mensaje fue Creado Exitosamente!'})
          $("#mensaje").val("");$("#hora_inicio").val("00");$("#minutos_inicio").val("00");$("#hora_final").val("00");$("#minutos_final").val("00");
          $("span.dias_semana_checkbok.active").each(function(){$(this).removeClass('active');});
        }else{
          const toast=swal.mixin({toast:true,position:'top-end',showConfirmButton:false,timer:3000});
          toast({type:'error',title:"¡Hubo un problema! \n Inténtalo de nuevo."})
        }
      }
    }
  });
</script>
<?php include '../common/footer.php';?>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@7.29.0/dist/sweetalert2.all.min.js'></script>
</div>
</body>
</html>                                                                                                                                                                                                 
