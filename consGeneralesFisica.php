<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$params = json_decode($json);

require("./conexion.php");
$con = returnConection();
$registro=mysqli_query($con ,"select ctpf.FechaNacimiento,cte.rfc,ctpf.curp,ctpf.sexo,catsx.desc_45 as sexodesc,ctpf.estadocivil,catecv.desc_45 as edocivdesc,ctpf.tipoidentificacion,catiden.desc_45 as idendesc, ctpf.numeroidentificacion,ctpf.profesion,catprof.desc_45 as descprofesion,cte.nacionalidad,catnac.desc_45 as desnacionalidad,cte.emailpersonal,cte.emailempresa,cte.parterelacionada,catparrel.desc_45 as descparterelacionada, cte.grupoconsejo, catgpocon.desc_45 as descgrupoconsejo, cte.gruporiesgocomun, catrgocom.desc_45 as descgruporiesgocomun from mg_clientes cte  inner join mg_ctepf ctpf on cte.numerocliente= ctpf.numerocliente inner join mg_catcod catsx on  catsx.catalogo_cve= ctpf.sexo and  catsx.catalogo_id='sexo' inner join mg_catcod catecv on catecv.catalogo_cve= ctpf.estadocivil and catecv.catalogo_id='edociv' inner join mg_catcod catiden on catiden.catalogo_cve= ctpf.tipoidentificacion and catiden.catalogo_id='identif' inner join mg_catcod catprof on catprof.catalogo_cve= ctpf.profesion and catprof.catalogo_id='profes' inner join mg_catcod catnac on catnac.catalogo_cve= cte.nacionalidad and catnac.catalogo_id='nacion' inner join mg_catcod catparrel on catparrel.catalogo_cve= cte.parterelacionada and catparrel.catalogo_id='parrel' inner join mg_catcod catgpocon on catgpocon.catalogo_cve= cte.grupoconsejo and catgpocon.catalogo_id='gpocon' inner join mg_catcod catrgocom on catrgocom.catalogo_cve= cte.grupoconsejo and catrgocom.catalogo_id='rgocom' where cte.numerocliente=".$params->idCliente.";");
$vec=[];
while($reg=mysqli_fetch_assoc($registro)){
    //$vec[]=$reg;
    array_push($vec,$reg);
}
$cad = json_encode($vec);
echo $cad;
?>