<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select catalogo_cve,desc_45 from mg_catcod where catalogo_id='actdet' order by 1");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){   
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;
?>