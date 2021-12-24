<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);
require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"CALL mgsp_ClientesActividadEconomica('$params->Id','$params->ActividadEconomica','$params->ActividadDetallada','$params->IngresoMensual','$params->OtroIngresoMensual','$params->GastosMensuales','$params->FlujoEfectivo',@OutErrorClave,@OutErrorProcedure,@OutErrorDescripcion)");
$sql = $connect->prepare($sql);
//$registro->execute();.
?>