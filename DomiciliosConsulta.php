<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);
require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"CALL mgsp_ClientesDomiciliosConsulta('$params->Id','$params->TipoDom',@OutCalle,@OutNumeroExterior,@OutNumeroInterior,@OutCodigoPostal,@OutColonia,@OutMunicipio,@OutEstado,@OutPais,@OutErrorClave,@OutErrorProcedure,@OutErrorDescripcion);");
$row=mysqli_query($con,"SELECT @OutCalle as Calle,@OutNumeroExterior as NoEx,@OutNumeroInterior as NoIn, @OutCodigoPostal as CodPos,@OutColonia as Colonia,@OutMunicipio as Municipio, @OutEstado as Estado,@OutPais as Pais,@OutErrorClave as errorClave, @OutErrorProcedure as errorSp,@OutErrorDescripcion as errorDescripcion");
$vec=[];
while($reg=mysqli_fetch_array($row)){
    $vec[]=$reg;
}
$cad = json_encode($vec);
echo $cad;
?>