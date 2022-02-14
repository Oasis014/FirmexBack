<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');

require("./conexion.php");
$con = returnConection();

$registro=mysqli_query($con ,"select NumeroCLiente as Id, TipoDomicilio as TipoDom, Calle as Calle, NumeroExterior as NoEx, NumeroInterior as NoIn, CodigoPostal as CodPos , Colonia as Colonia, Municipio as Municipio, Estado as Estado, Pais as Pais from mg_ctedom");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){
    $vec[]=$reg;
}
$cad = json_encode($vec);
echo $cad;