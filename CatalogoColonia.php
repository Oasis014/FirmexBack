<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);
require("./conexion.php");
//echo $params;
$con = returnConection();
$registro=mysqli_query($con ,"select distinct(asentamiento_desc) as asentamiento_desc from mg_sepomex where estado_id='$params->edoId' and municipio_id= '$params->mpioId'  and codigopostal_id='$params->codPosID' order by 1;");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){    
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;
?>