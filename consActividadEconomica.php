<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);

require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select acteco.actividadeconomica,cateco.desc_45 as descactividadeconomica,acteco.actividaddetallada, catdet.desc_45 as descactividaddetallada,acteco.ingresomensual,acteco.otroingresomensual,acteco.gastosmensuales,acteco.flujoefectivo from mg_clientes cte inner join mg_cteacteco acteco on cte.numerocliente=acteco.numerocliente inner join mg_catcod cateco on cateco.catalogo_cve=acteco.actividadeconomica and cateco.catalogo_id='acteco' inner join mg_catcod catdet on catdet.catalogo_cve=acteco.actividadeconomica and catdet.catalogo_id='actdet' where cte.numerocliente=".$params->idCliente.";");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){
    //$vec[]=$reg;
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;

?>