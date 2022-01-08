<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select Pais_id,Pais_desc from mg_paises where pais_id='MX' order by 2;");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){    
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;
?>