<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select estado_id,estado_desc from mg_estados order by 2;");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){    
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;
?>