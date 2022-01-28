<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);

require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select ctegcom.consecutivo,ctegcom.gruporiesgocomunrgocom,ctrc.desc_45 as descriesgocomun,ctegcom.nombrergocom,ctegcom.rfcrgocom,ctegcom.direccionrgocom from mg_clientes cte inner join mg_ctergocom ctegcom on cte.numerocliente=ctegcom.numerocliente inner join mg_catcod ctrc on ctrc.catalogo_cve=ctegcom.gruporiesgocomunrgocom and catalogo_id='tiprrc' where cte.numerocliente=".$params->idCliente.";");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){
    //$vec[]=$reg;
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;

?>