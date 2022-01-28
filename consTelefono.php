<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);

require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select  ctel.tipotelefono,cattel.desc_45 as tipTelefono,cte.telefonodomicilio,cte.extension,ctel.telefono,ctel.extension from mg_clientes cte inner join  mg_ctetel ctel on cte.numeroCliente= ctel.numeroCliente inner join mg_catcod cattel on cattel.catalogo_cve=ctel.tipotelefono where cte.numerocliente=".$params->idCliente.";");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){
    //$vec[]=$reg;
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;

?>