<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);

require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select cparrel.consecutivo,cparrel.parterelacionadaparrel,catparrel.desc_45 as descparrel,cparrel.nombreparrel,cparrel.rfcparrel,cparrel.direccionparrel  from mg_clientes cte inner join mg_cteparrel cparrel on cte.numerocliente=cparrel.numerocliente  inner join mg_catcod catparrel on  cparrel.parterelacionadaparrel=catparrel.catalogo_cve and catalogo_id='tiprel' where cte.numerocliente=".$params->idCliente.";");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){
    //$vec[]=$reg;
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;

?>