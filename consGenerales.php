<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);

require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select suc.sucursal_nom,cte.numeroCliente,cte.PersonalidadJuridica, cperjur.desc_45,cte.EstatusCliente, cstscte.desc_45, prom.promotor_id, prom.PromotorNombre from mg_clientes cte inner join mg_promotores prom on cte.ClavePromotor=prom.promotor_id inner join mg_catcod cperjur on cperjur.catalogo_cve=cte.personalidadJuridica and cperjur.catalogo_id='perjur' inner join mg_catcod cstscte on cstscte.catalogo_cve=cte.EstatusCliente and cstscte.catalogo_id=stscte inner join mg_sucursales suc on suc.sucursal_id= cte.sucursal where cte.numeroCliente=".$params->idCliente.";");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){
    //$vec[]=$reg;
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;

?>