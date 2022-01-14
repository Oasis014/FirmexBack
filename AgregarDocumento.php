<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);
require("./conexion.php");
$con = returnConection();

$rutaDestino="C:/AppServ/www/2021/FirmexGit/Firmex/UserFiles/";
$rutaOrigen="C:/Users/casa/Downloads/Imta/";
$nombreArchivo="ElielContreras.pdf";
$urlDocumento=$rutaDestino.$nombreArchivo;
copy($rutaOrigen.$nombreArchivo, $rutaDestino.$nombreArchivo);
$registro=mysqli_query($con ,"CALL mgsp_ClientesDocumentos('$params->Id','$params->consDocumento','$params->tipDocumento','$urlDocumento',NOW(),@OutErrorClave,@OutErrorProcedure,@OutErrorDescripcion)");
$row=mysqli_query($con,"SELECT @OutErrorClave as errorClave,@OutErrorProcedure as errorSp,@OutErrorDescripcion as errorDescripcion");
$vec=[];
while($reg=mysqli_fetch_assoc($row)){
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;
?>