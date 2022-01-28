<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);

require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select ctem.FechaConstitucion,cte.rfc,ctem.nombresociedad,ctem.representantelegal,ctem.presidenteconsejo,ctem.consejero,ctem.secretario,cte.emailpersonal,cte.emailempresa,cte.parterelacionada,cte.grupoconsejo,catgpocon.desc_45 as descgrupoconsejo,cte.gruporiesgocomun, catrgocom.desc_45 as descgruporiesgocomun from mg_ctepm ctem inner join mg_clientes cte on cte.numerocliente=ctem.numerocliente inner join mg_catcod catgpocon on catgpocon.catalogo_cve= cte.grupoconsejo and catgpocon.catalogo_id='gpocon' inner join mg_catcod catrgocom on catrgocom.catalogo_cve= cte.grupoconsejo and catrgocom.catalogo_id='rgocom' where cte.numerocliente=".$params->idCliente.";");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){
    //$vec[]=$reg;
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;

?>