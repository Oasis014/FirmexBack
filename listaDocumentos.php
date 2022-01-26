<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);
require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select consecutivo,tipodocumento from mg_ctedoctos ctedoctos where numerocliente=".$params->idCliente." order by 2 ");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){   
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;
?>