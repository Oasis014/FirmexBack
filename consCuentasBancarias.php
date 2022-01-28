<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);

require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select cteban.consecutivo,cteban.nombrecuentabancariactaban,cteban.bancoctaban, ctbanco.desc_45 as descbanco, cteban.numerocuentactaBan,cteban.claveinterbancariactaban from mg_clientes cte inner join mg_ctectaban cteban on cte.numerocliente=cteban.numerocliente inner join mg_catcod ctbanco on ctbanco.catalogo_cve=cteban.bancoctaban and ctbanco.catalogo_id='bancos' where cte.numerocliente=".$params->idCliente.";");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){
    //$vec[]=$reg;
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;

?>