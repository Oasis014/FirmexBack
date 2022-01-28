<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);

require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select gposoc.consecutivo, gposoc.gruposocioeconomicogposoc,cgseg.desc_45 as descgruposocioeconomico,gposoc.nombregposoc,gposoc.rfcgposoc,gposoc.direcciongposoc from mg_clientes cte inner join mg_ctegposoc gposoc on cte.numerocliente=gposoc.numerocliente inner join mg_catcod cgseg on gposoc.gruposocioeconomicogposoc=cgseg.catalogo_cve and catalogo_id='tipgse' where cte.numerocliente=".$params->idCliente.";");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){
    //$vec[]=$reg;
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;

?>