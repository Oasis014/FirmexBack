<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);
require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"CALL mgsp_ClientesProveedores('$params->Id','$params->Consecutivo','$params->NombreProvee','$params->LimiteCreditoProvee','$params->SaldoCuentaProvee',@OutErrorClave,@OutErrorProcedure,@OutErrorDescripcion)");
$sql = $connect->prepare($sql);
//$registro->execute();.
?>