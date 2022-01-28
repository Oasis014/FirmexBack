<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);

require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select cteref.consecutivo,cteref.nombrerefper,cteref.tiporelacionrefper,catrel.desc_45 as desctiporelacion,cteref.telefonorefper from mg_clientes cte inner join mg_cterefper cteref on cteref.numerocliente=cte.numerocliente inner join mg_catcod catrel on catrel.catalogo_cve=cteref.tiporelacionrefper where cte.numerocliente=".$params->idCliente.";");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){
    //$vec[]=$reg;
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;

?>