<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');

require("./conexion.php");
$con = returnConection();

$registro=mysqli_query($con ,"select NumeroCLiente, Sucursal, PrimerNombre, ApellidoPaterno, RazonSocial, RFC, RazonSocial, PersonalidadJuridica, EmailPersonal, Celular from mg_clientes");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){
    $vec[]=$reg;
}
$cad = json_encode($vec, JSON_INVALID_UTF8_IGNORE);
echo $cad;