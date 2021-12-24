<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);
require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"CALL mgsp_ClientesDomicilios('$params->Id','$params->TipoDom','$params->Calle','$params->NoEx','$params->NoIn','$params->CodPos','$params->Colonia','$params->Municipio','$params->Estado','$params->Pais',@OutErrorClave,@OutErrorProcedure,@OutErrorDescripcion)");
$row=mysqli_query($con,"SELECT @OutErrorClave as errorClave,@OutErrorProcedure as errorSp,@OutErrorDescripcion as errorDescripcion");
$vec=[];
while($reg=mysqli_fetch_array($row)){
    $vec[]=$reg;
}
$cad = json_encode($vec);
echo $cad;
?>