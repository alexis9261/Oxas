<?php
require 'conexion.php';
$sql="SELECT NAME,LASTNAME FROM usuario WHERE CORREO='$correoUser'";
$result=$conn->query($sql);
if($result->num_rows>0){while($row=$result->fetch_assoc()){$name=$row['NAME'];$lastname=$row['LASTNAME'];}}
?>
<div class='breadcrumb justify-content-between'>
<div class='col-sm-auto align-self-start'>
<a target='_blank' href="<?php echo $_SESSION['permalink'];?>" title='Ve a tu cuenta en Mercado Libre' data-toggle='tooltip'><?php echo $name.' '.$lastname.' ';?></a>
</div>
<div class='col-sm-auto align-self-center' title='Reputación en Mercado Libre' data-toggle='tooltip'>
<?php
$cad=$_SESSION['reputacion'];
switch($cad[0]){
case '1': ?>
<span class='mal bord' style='background-color: #ff191d;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span>
<?php break;case '2':?>
<span class='mal' style='background-color: #ffc6a5;'></span><span class='medio bord' style='background-color: #ff8419;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span>
<?php break;case '3': ?>
<span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med bord' style='background-color: #ffff36;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc' style='background-color: #d2ffb0;'></span>
<?php break;case '4': ?>
<span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien bord' style='background-color: #58ff3f;'></span><span class='exc' style='background-color: #d2ffb0;'></span>
<?php break;case '5': ?>
<span class='mal' style='background-color: #ffc6a5;'></span><span class='medio' style='background-color: #ffffb0;'></span><span class='med' style='background-color: #ffffa2;'></span><span class='bien' style='background-color: #cbffa6;'></span><span class='exc bord' style='background-color: #00ca00;'></span>
<?php break;default:?>
<span class='mal' style='background-color: #beccc1;'></span><span class='medio' style='background-color: #beccc1;'></span><span class='med' style='background-color: #beccc1;'></span><span class='bien' style='background-color: #beccc1;'></span><span class='exc' style='background-color: #beccc1;'></span>
<?php break;} ?>
</div>
<?php
switch ($_SESSION['experiencia']){
case 'NEWBIE':echo "<div class='col-sm-auto text-warning text-left align-self-end' title='Tu experiencia como vendedor en ML' data-toggle='tooltip'> Novato</div>";break;
case 'INTERMEDIATE':echo "<div class='col-sm-auto text-primary text-left align-self-end' title='Tu experiencia como vendedor en ML' data-toggle='tooltip'> Intermedio</div>";break;
case 'ADVANCED':echo "<div class='col-sm-auto text-success text-left align-self-end' title='Tu experiencia como vendedor en ML' data-toggle='tooltip'> Avanzado</div>";break;
}?>
</div>
