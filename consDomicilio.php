<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);

require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select dom.tipodomicilio,catdom.desc_45 as desctipodomicilio,dom.calle,dom.numeroexterior,dom.numerointerior,dom.codigopostal,dom.colonia,dom.municipio,dom.estado,edo.estado_desc,dom.municipio,mpio.municipio_desc,dom.pais from mg_clientes cte inner join mg_ctedom dom on cte.numerocliente=dom.numerocliente inner join mg_catcod catdom on dom.tipodomicilio=catdom.catalogo_cve and catdom.catalogo_id='tipodom' inner join mg_estados edo on edo.estado_id=dom.estado inner join mg_municipios mpio on mpio.estado_id=edo.estado_id and mpio.municipio_id=dom.municipio where cte.numerocliente=".$params->idCliente.";");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){
    //$vec[]=$reg;
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;

?>