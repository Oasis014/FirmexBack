<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);

require("./conexion.php");
$con = returnConection();
//$registro=mysqli_query($con ," use firmex; CALL mgsp_ClientesDatosGenerales('$params->id'$params->sucursal,'$params->ApellidoPaterno','$params->ApellidoMaterno','$params->PrimerNombre','$params->SegundoNombre','$params->RazonSocial','$params->ClavePromotor','$params->EstatusCliente','$params->FechaAlta','$params->PersonalidadJuridica,'$params->RFC','$params->Nacionalidad','$params->EmailPersonal','$params->EmailEmpresa','$params->ParteRelacionada','$params->GrupoConsejo','$params->GrupoRiesgoComun','$params->FechaNacimiento','$params->Sexo','$params->EstadoCivil','$params->CURP','$params->TipoIdentificacion','$params->NumeroIdentificacion','$params->ListaNegra','$params->Profesion','$params->NombreSociedad','$params->FechaConstitucion','$params->RepresentanteLegal','$params->PresidenteConsejo','$params->Consejero')");
$registro=mysqli_query($con ,"insert into prueba_1 values ('$params->id_usuario','$params->nombre','$params->apellido','$params->correo','$params->numero','$params->clave' )");

//$registro->execute();.

?>