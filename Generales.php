<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);
require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con,"CALL mgsp_ClientesDatosGenerales('$params->Id','$params->Sucursal','$params->ApellidoPaterno','$params->ApellidoMaterno','$params->PrimerNombre','$params->SegundoNombre','$params->RazonSocial','$params->ClavePromotor','$params->EstatusCliente','$params->FechaAlta','$params->PersonalidadJuridica','$params->RFC','$params->Nacionalidad','$params->EmailPersonal','$params->EmailEmpresa','$params->ParteRelacionada','$params->GrupoConsejo','$params->GrupoRiesgoComun','$params->FechaNacimiento','$params->Sexo','$params->EstadoCivil','$params->CURP','$params->TipoIdentificacion','$params->NumeroIdentificacion','$params->ListaNegra','$params->Profesion','$params->NombreSociedad','$params->FechaConstitucion','$params->RepresentanteLegal','$params->PresidenteConsejo','$params->Consejero',@OutNumeroCLiente,@OutErrorClave,@OutErrorProcedure,@OutErrorDescripcion)");
$row=mysqli_query($con,"SELECT @OutNumeroCLiente AS noCliente,@OutErrorClave as errorClave,@OutErrorProcedure as errorSp,@OutErrorDescripcion as errorDescripcion");
$vec=[];
while($reg=mysqli_fetch_array($row)){
    $vec[]=$reg;
}
$cad = json_encode($vec);
echo $cad;
?>