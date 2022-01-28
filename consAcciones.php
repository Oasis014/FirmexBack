<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);

require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select cteacc.consecutivo,cteacc.fechacompra1aaccion,cteacc.parteinicialsocial,cteacc.fechapago,cteacc.partesocialactual, cteacc.costoacciones,cteacc.formapagoacciones,cteacc.retirablesa,cteacc.retirablesb from mg_clientes cte inner join mg_cteaccion cteacc on cte.numerocliente= cteacc.numerocliente where cte.numerocliente = ".$params->idCliente.";");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){
    //$vec[]=$reg;
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;

?>