<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');

require("./conexion.php");
$con = returnConection();

$registro=mysqli_query($con ,"select NumeroCLiente, Sucursal, ApellidoPaterno, ApellidoMaterno, PrimerNombre, SegundoNombre, RazonSocial, ClavePromotor, EstatusCliente, FechaAlta, PersonalidadJuridica, RFC, Nacionalidad, EmailPersonal, EmailEmpresa, ParteRelacionada, GrupoConsejo, GrupoRiesgoComun from mg_clientes");
$vec=[];
while($reg=mysqli_fetch_array($registro)){
    $vec[]=$reg;
}
$cad = json_encode($vec);
echo $cad;