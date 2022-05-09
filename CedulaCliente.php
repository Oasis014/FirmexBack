<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
  header('Content-Type: application/json');

  require("./conexion.php");
  require("vendor/fpdf/fpdf.php"); 
  $con = returnConection();


  class MiPdf extends FPDF {
    public function Header(){   
    $this -> SetFont('Arial','',12);    
    $this -> Cell(195,5,utf8_decode("FINANCIERA RURAL DE MÉXICO (FIRMEX)"),0,0,'C',false);
    $this -> Ln(5);
    $this -> Cell(195,5,utf8_decode("MÓDULO GENERAL -  CÉDULA DE CLIENTES"),0,0,'C',false);
    $this -> Ln(5);
    $this -> SetFont('Arial','',10);  
    $this -> Cell(150,5,utf8_decode("FECHA: ".date('d-m-Y')),0,0,'R',false); 
    $this -> Ln(5);    
  
  }
      
  function Footer(){
    Global $intHoja;      
    $this->SetY(-15);      
    $this->SetFont('Arial','I',7);
      //Número de página    
    $this->Cell(0,10,'Pag. '.$this->PageNo()  ." - " .date('d-m-Y'),0,0,'C');    
    $this -> Ln(4);    
  }
}

if ( isset($_GET['idCliente']) && !empty($_GET['idCliente']) ) {
  $idCliente = $_GET['idCliente'];

  

  $numcte=  $idCliente;
  $currency='MXN';
  
  $mipdf = new MiPdf('P','mm','A4');
  $mipdf->SetMargins(5, 10 , 10,10);
  $mipdf->SetAutoPageBreak(true,1); 
  $mipdf -> addPage();  

  $datosGenerales=mysqli_query($con ,"select suc.sucursal_nom,cte.numeroCliente,cte.PersonalidadJuridica,"
                          ." cperjur.desc_45 as desperjur,cte.EstatusCliente, cstscte.desc_45 desStatus, prom.promotor_id, prom.PromotorNombre"
                          ." from mg_clientes cte inner join mg_promotores prom on cte.ClavePromotor=prom.promotor_id inner join "
                          ." mg_catcod cperjur on cperjur.catalogo_cve=cte.personalidadJuridica and cperjur.catalogo_id='perjur' "
                          ." inner join mg_catcod cstscte on cstscte.catalogo_cve=cte.EstatusCliente and cstscte.catalogo_id='stscte'" 
                          ." inner join mg_sucursales suc on suc.sucursal_id= cte.sucursal where cte.numeroCliente=".$numcte.";");

  if(mysqli_num_rows($datosGenerales)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("================================DATOS GENERALES================================"),0,0,'C',false);
    $mipdf -> Ln(10); 
    while($fila=$datosGenerales->fetch_object()){
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(30,5,utf8_decode("SUCURSAL: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(30,5,utf8_decode($fila->sucursal_nom),0,0,'C',false);
      $mipdf -> SetFont('Arial','B',10);  
      $mipdf -> Cell(48,5,utf8_decode("PROMOTOR: "),0,0,'R',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(60,5,utf8_decode($fila->PromotorNombre),0,0,'C',false);
      $mipdf -> Ln(5);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(48,5,utf8_decode("NÚMERO DE CLIENTE: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(15,5,utf8_decode($fila->numeroCliente),0,0,'C',false);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(70,5,utf8_decode("PERSONALIDAD JURÍDICA: "),0,0,'R',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(60,5,utf8_decode($fila->desperjur),0,0,'C',false);
      $mipdf -> Ln(5);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(50,5,utf8_decode("ESTATUS DEL CLIENTE: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(30,5,utf8_decode($fila->desStatus),0,0,'C',false);    
      $mipdf -> Ln(10);
    }
  }    

  $perFisica=mysqli_query($con ,"select ctpf.FechaNacimiento,cte.rfc,ctpf.curp,ctpf.sexo,catsx.desc_45 as sexodesc,"
                                ." ctpf.estadocivil,catecv.desc_45 as edocivdesc,ctpf.tipoidentificacion,"
                                ." catiden.desc_45 as idendesc, ctpf.numeroidentificacion,ctpf.profesion,"
                                ." catprof.desc_45 as descprofesion,cte.nacionalidad,catnac.desc_45 as desnacionalidad,"
                                ." cte.emailpersonal,cte.emailempresa,cte.parterelacionada,catparrel.desc_45 as descparterelacionada,"
                                ." cte.grupoconsejo, cte.gruporiesgocomun, catrgocom.desc_45 as descgruporiesgocomun"
                                ." from mg_clientes cte  inner join mg_ctepf ctpf on cte.numerocliente= ctpf.numerocliente"
                                ." inner join mg_catcod catsx on  catsx.catalogo_cve= ctpf.sexo and  catsx.catalogo_id='sexo'" 
                                ." inner join mg_catcod catecv on catecv.catalogo_cve= ctpf.estadocivil and catecv.catalogo_id='edociv'"
                                ." inner join mg_catcod catiden on catiden.catalogo_cve= ctpf.tipoidentificacion and catiden.catalogo_id='identif'"
                                ." inner join mg_catcod catprof on catprof.catalogo_cve= ctpf.profesion and catprof.catalogo_id='profes'"
                                ." inner join mg_catcod catnac on catnac.catalogo_cve= cte.nacionalidad and catnac.catalogo_id='nacion'"
                                ."inner join mg_catcod catparrel on catparrel.catalogo_cve= cte.parterelacionada and catparrel.catalogo_id='tiprel'"
                                ." inner join mg_catcod catrgocom on catrgocom.catalogo_cve= cte.gruporiesgocomun and catrgocom.catalogo_id='tiprrc'"
                                ." where cte.numerocliente=".$numcte.";");

  if(mysqli_num_rows($perFisica)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("==================================PERSONA FISICA=================================="),0,0,'C',false);
    $mipdf -> Ln(10);
    while($fila=$perFisica->fetch_object()){
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(50,5,utf8_decode("FECHA DE NACIMIENTO: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(25,5,utf8_decode($fila->FechaNacimiento),0,0,'C',false); 
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(30,5,utf8_decode("RFC: "),0,0,'R',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(30,5,utf8_decode($fila->rfc),0,0,'L',false);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(30,5,utf8_decode("CURP: "),0,0,'R',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(30,5,utf8_decode($fila->curp),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(18,5,utf8_decode("SEXO: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(15,5,utf8_decode($fila->sexodesc),0,0,'C',false);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(45,5,utf8_decode("ESTADO CIVIL: "),0,0,'R',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(35,5,utf8_decode($fila->edocivdesc),0,0,'L',false);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(45,5,utf8_decode("IDENTIFICACIÓN: "),0,0,'R',false); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(45,5,utf8_decode($fila->idendesc),0,0,'L',false);
      $mipdf -> Ln(5);
      $mipdf -> SetFont('Arial','B',10);  
      $mipdf -> Cell(30,5,utf8_decode("PROFESIÓN: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(35,5,utf8_decode($fila->descprofesion),0,0,'L',false);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(92,5,utf8_decode("NACIONALIDAD: "),0,0,'R',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(45,5,utf8_decode($fila->desnacionalidad),0,0,'L',false);
      $mipdf -> Ln(5);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(40,5,utf8_decode("EMAIL PERSONAL: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(45,5,utf8_decode($fila->emailpersonal),0,0,'C',false);
      $mipdf -> Ln(5);  
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(38,5,utf8_decode("EMAIL EMPRESA: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(45,5,utf8_decode($fila->emailempresa),0,0,'C',false);
      $mipdf -> Ln(5);  
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(48,5,utf8_decode("PARTE RELACIONADA: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(55,5,utf8_decode($fila->descparterelacionada),0,0,'C',false);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(57,5,utf8_decode("GRUPO CONSEJO: "),0,0,'R',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(40,5,utf8_decode($fila->grupoconsejo),0,0,'L',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(55,5,utf8_decode("GRUPO DE RIESGO COMÚN: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(45,5,utf8_decode($fila->descgruporiesgocomun),0,0,'C',false);
      $mipdf -> Ln(10);
    }
  }

  $perMoral=mysqli_query($con ,"select ctem.FechaConstitucion,cte.rfc,ctem.nombresociedad,ctem.representantelegal,"
                              ." ctem.presidenteconsejo,ctem.consejero,ctem.secretario,cte.emailpersonal,cte.emailempresa,"
                              ."  cprel.nombreparrel,cte.grupoconsejo,cte.gruporiesgocomun,catrgocom.nombrergocom "                        
                              ." from mg_ctepm ctem inner join mg_clientes cte on cte.numerocliente=ctem.numerocliente "
                              ." left join mg_ctergocom catrgocom  on catrgocom.gruporiesgocomunrgocom= cte.gruporiesgocomun and cte.numerocliente=catrgocom.numerocliente"
                              ." left join mg_cteparrel cprel  on cte.numerocliente=cprel.numerocliente "                          
                              ." where cte.numerocliente=".$numcte.";");

  if(mysqli_num_rows($perMoral)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("=================================PERSONA MORAL================================="),0,0,'C',false);
    $mipdf -> Ln(10);

    while($fila=$perMoral->fetch_object()){
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(55,5,utf8_decode("FECHA DE CONSTITUCIÓN: "),0,0,'C',false); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(30,5,utf8_decode($fila->FechaConstitucion),0,0,'C',false);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(60,5,utf8_decode("RFC: "),0,0,'R',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(50,5,utf8_decode($fila->rfc),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(55,5,utf8_decode("NOMBRE DE LA SOCIEDAD: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(50,5,utf8_decode($fila->nombresociedad),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(53,5,utf8_decode("REPRESENTANTE LEGAL: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(50,5,utf8_decode($fila->representantelegal),0,0,'C',false);
      $mipdf -> Ln(5);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(57,5,utf8_decode("PRESIDENTE DEL CONSEJO: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(50,5,utf8_decode($fila->presidenteconsejo),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(30,5,utf8_decode("CONSEJERO: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(50,5,utf8_decode($fila->consejero),0,0,'C',false);
      $mipdf -> Ln(5);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(40,5,utf8_decode("EMAIL PERSONAL "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(50,5,utf8_decode($fila->emailpersonal),0,0,'C',false);
      $mipdf -> Ln(5);  
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(38,5,utf8_decode("EMAIL EMPRESA: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(60,5,utf8_decode($fila->emailempresa),0,0,'C',false);
      $mipdf -> Ln(5);  
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(48,5,utf8_decode("PARTE RELACIONADA: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(44,5,utf8_decode($fila->nombreparrel),0,0,'C',false);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(60,5,utf8_decode("GRUPO CONSEJO: "),0,0,'R',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(40,5,utf8_decode($fila->grupoconsejo),10,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(54,5,utf8_decode("GRUPO DE RIESGO COMÚN: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(54,5,utf8_decode($fila->nombrergocom),0,0,'C',false);   
      $mipdf -> Ln(10);     
    }
  }

    
  $telefonos=mysqli_query($con ,"select telefonoDomicilio, ExtensionDomicilio, TelefonoOficina, ExtensionOficina from "
  ."mg_clientes where numeroCliente=".$numcte.";");

  if(mysqli_num_rows($telefonos)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("===================================TELÉFONOS==================================="),0,0,'C',false);
    $mipdf -> Ln(10);

    while($fila=$telefonos->fetch_object()){
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(45,5,utf8_decode("TELÉFONO DOMICILIO: "),0,0,'C',false); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(40,5,utf8_decode($fila->telefonoDomicilio),0,0,'C',false);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(40,5,utf8_decode("EXTENSIÓN: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(20,5,utf8_decode($fila->ExtensionDomicilio),0,0,'L',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(40,5,utf8_decode("TELÉFONO OFICINA: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(55,5,utf8_decode($fila->TelefonoOficina),0,0,'C',false);  
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(20,5,utf8_decode("EXTENSIÓN: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(20,5,utf8_decode($fila->ExtensionOficina),0,0,'L',false);
      $mipdf -> Ln(5);
    }
  }

  $red_social=mysqli_query($con ,"select redsocial1,redsocial2 from mg_clientes where numerocliente=".$numcte.";");

  if(mysqli_num_rows($red_social)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("=================================REDES SOCIALES================================="),0,0,'C',false);
    $mipdf -> Ln(10);

    while($fila=$red_social->fetch_object()){
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(50,5,utf8_decode("RED SOCIAL 1: "),0,0,'C',false); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(40,5,utf8_decode($fila->redsocial1),0,0,'C',false);    
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(50,5,utf8_decode("RED SOCIAL 2: "),0,0,'C',false);  
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(40,5,utf8_decode($fila->redsocial2),0,0,'C',false);
      $mipdf -> Ln(5);
    }
  }

  $domicilio=mysqli_query($con ,"select dom.tipodomicilio,catdom.desc_45 as desctipodomicilio,dom.calle,"
            ." dom.numeroexterior,dom.numerointerior,dom.codigopostal,dom.colonia,dom.municipio,dom.estado,"
            ." edo.estado_desc,dom.municipio,mpio.municipio_desc,dom.pais from mg_clientes cte inner join "
            ." mg_ctedom dom on cte.numerocliente=dom.numerocliente inner join mg_catcod catdom on "
            ." dom.tipodomicilio=catdom.catalogo_cve and catdom.catalogo_id='tipdom' inner join mg_estados" 
            ." edo on edo.estado_id=dom.estado inner join mg_municipios mpio on mpio.estado_id=edo.estado_id "
            ." and mpio.municipio_id=dom.municipio where cte.numerocliente=".$numcte.";");

  if(mysqli_num_rows($domicilio)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("===================================DOMICILIOS==================================="),0,0,'C',false);
    $mipdf -> Ln(10);

    while($fila=$domicilio->fetch_object()){
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(40,5,utf8_decode("TIPO DE DOMICILIO: "),0,0,'C',false); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(35,5,utf8_decode($fila->desctipodomicilio),0,0,'L',false);  
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(20,5,utf8_decode("CALLE: "),0,0,'C',false); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(95,5,utf8_decode($fila->calle),0,0,'L',false); 
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(20,5,utf8_decode("NUM: "),0,0,'R',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(10,5,utf8_decode($fila->numeroexterior),0,0,'R',false);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(30,5,utf8_decode("INTERIOR: "),0,0,'C',false); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(10,5,utf8_decode($fila->numerointerior),0,0,'C',false); 
      $mipdf -> Ln(5);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(30,5,utf8_decode("COD POSTAL: "),0,0,'C',false);
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(20,5,utf8_decode($fila->codigopostal),0,0,'C',false);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(25,5,utf8_decode("COLONIA: "),0,0,'C',false); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(45,5,utf8_decode($fila->colonia),0,0,'L',false); 
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(25,5,utf8_decode("MUNICIPIO: "),0,0,'C',false);   
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(45,5,utf8_decode($fila->municipio_desc),0,0,'L',false);  
      $mipdf -> Ln(5);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(22,5,utf8_decode("ESTADO: "),0,0,'C',false);  
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(91,5,utf8_decode($fila->estado_desc),0,0,'L',false);
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(30,5,utf8_decode("PAÍS: "),0,0,'C',false); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(30,5,utf8_decode($fila->pais),0,0,'L',false); 
      $mipdf -> Ln(5);
    }
  }

  $actividad_economica=mysqli_query($con ,"select acteco.actividadeconomica,cateco.desc_45 as descactividadeconomica,"
                                          ." acteco.actividaddetallada, catdet.desc_45 as descactividaddetallada,"
                                          ." acteco.ingresomensual,acteco.otroingresomensual,acteco.gastosmensuales,"
                                          ." acteco.flujoefectivo from mg_clientes cte "
                                          ." inner join mg_cteacteco acteco on cte.numerocliente=acteco.numerocliente "
                                          ." inner join mg_catcod cateco on cateco.catalogo_cve=acteco.actividadeconomica and cateco.catalogo_id='acteco'"
                                          ." inner join mg_catcod catdet on catdet.catalogo_id='actdet' and catdet.catalogo_cve=acteco.actividaddetallada"
                                          ." where cte.numerocliente=".$numcte.";");
  
  
  if(mysqli_num_rows($actividad_economica)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("===============================ACTIVIDAD ECONOMICA==============================="),0,0,'C',false);
    $mipdf -> Ln(10); 
    $totingM=0;
    $tototIngM=0; 
    $totGasMes=0; 
    $totfluE=0; 
    while($fila=$actividad_economica->fetch_object()){
      $mipdf -> SetFont('Arial','B',9);
      $mipdf -> Cell(30,5,utf8_decode("ACT. ECONOM "),0,0,'C',false);   
      $mipdf -> Cell(50,5,utf8_decode("ACT. DETALL  "),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("INGRESO MENSUAL"),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("OTR ING. MES "),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("GASTOS MES "),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode("FLUJO EFECTIVO "),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','',8);
      $mipdf -> Cell(30,5,utf8_decode($fila->actividadeconomica),0,0,'C',false);  
      $mipdf -> Cell(60,5,utf8_decode($fila->descactividaddetallada),0,0,'C',false);      
      $mipdf -> Cell(25,5,utf8_decode("$ ".number_format($fila->ingresomensual,2)),0,0,'C',false);       
      $mipdf -> Cell(25,5,utf8_decode("$ ".number_format($fila->otroingresomensual,2)),0,0,'C',false);        
      $mipdf -> Cell(25,5,utf8_decode("$ ".number_format($fila->gastosmensuales,2)),0,0,'C',false);        
      $mipdf -> Cell(25,5,utf8_decode("$ ".number_format($fila->flujoefectivo,2)),0,0,'C',false);
      $mipdf -> Ln(5); 


      $totingM=$totingM+$fila->ingresomensual;
      $tototIngM=$tototIngM+$fila->otroingresomensual;
      $totGasMes=$totGasMes+$fila->gastosmensuales;
      $totfluE= $totfluE+$fila->flujoefectivo;
     
    }
    $formatter = new NumberFormatter('es_MX',  NumberFormatter::CURRENCY);
    $mipdf -> SetFont('Arial','B',9);
    $mipdf -> Cell(85,5,utf8_decode("TOTALES:"),0,0,'R',false);
    $mipdf -> Cell(30,5,utf8_decode($formatter->formatCurrency($totingM, $currency)),0,0,'C',false);
    $mipdf -> Cell(30,5,utf8_decode($formatter->formatCurrency($tototIngM, $currency)),0,0,'C',false);
    $mipdf -> Cell(20,5,utf8_decode($formatter->formatCurrency($totGasMes, $currency)),0,0,'C',false);
    $mipdf -> Cell(25,5,utf8_decode($formatter->formatCurrency($totfluE, $currency)),0,0,'C',false);    
  }



  $refPersonal=mysqli_query($con ,"select cteref.consecutivo,cteref.nombrerefper,cteref.tiporelacionrefper,"
                      ." catrel.desc_45 as desctiporelacion,cteref.telefonorefper from mg_clientes cte inner "
                      ." join mg_cterefper cteref on cteref.numerocliente=cte.numerocliente inner "
                      ." join mg_catcod catrel on catrel.catalogo_cve=cteref.tiporelacionrefper and catalogo_id='tiprpe' "
                      ." where cte.numerocliente=".$numcte.";");
   
  if(mysqli_num_rows($refPersonal)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("==========================REFERENCIAS PERSONALES=============================="),0,0,'C',false);
    $mipdf -> Ln(10);    
    while($fila=$refPersonal->fetch_object()){ 
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(15,5,utf8_decode("CONS "),0,0,'C',false);   
      $mipdf -> Cell(100,5,utf8_decode("NOMBRE "),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode("TIP RELACIÓN"),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("TELÉFONO "),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(15,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(100,5,utf8_decode($fila->nombrerefper),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode($fila->desctiporelacion),0,0,'C',false); 
      $mipdf -> Cell(30,5,utf8_decode($fila->telefonorefper),0,0,'C',false); 
      $mipdf -> Ln(5); 
    }
  }


$refComercial=mysqli_query($con ,"select refcom.consecutivo,refcom.nombrerefcom,refcom.limitecreditorefcom,"
                        ." refcom.saldocuentarefcom from mg_clientes cte inner join mg_cterefcom refcom" 
                        ." on cte.numerocliente= refcom.numerocliente where cte.numerocliente=".$numcte.";");                        

  if(mysqli_num_rows($refComercial)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("===========================REFERENCIAS COMERCIALES============================"),0,0,'C',false);
    $mipdf -> Ln(10);
    while($fila=$refComercial->fetch_object()){ 
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(15,5,utf8_decode("CONS "),0,0,'C',false);   
      $mipdf -> Cell(100,5,utf8_decode("NOMBRE "),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode("LIMITE DE CRED"),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("SALDO CUENTA "),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(15,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(100,5,utf8_decode($fila->nombrerefcom),0,0,'C',false);      
      $mipdf -> Cell(50,5,utf8_decode("$".number_format($fila->limitecreditorefcom,2)),0,0,'C',false);     
      $mipdf -> Cell(30,5,utf8_decode("$".number_format($fila->saldocuentarefcom,2)),0,0,'C',false);
      $mipdf -> Ln(5); 
    }
  }
/*
$proveedor=mysqli_query($con ,"select prov.consecutivo,prov.nombreprovee,prov.limitecreditoprovee,"
                      ." prov.saldocuentaprovee from mg_clientes cte inner join mg_cteprovee prov on"
                      ." cte.numerocliente= prov.numerocliente where cte.numerocliente=".$numcte.";");

  if(mysqli_num_rows($proveedor)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("================================PROVEEDORES==============================="),0,0,'C',false);
    $mipdf -> Ln(10);
    while($fila=$proveedor->fetch_object()){
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(30,5,utf8_decode("CONS: "),0,0,'C',false);   
      $mipdf -> Cell(30,5,utf8_decode("NOMBRE "),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode("LIMITE DE CRED"),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("SALDO CUENTA "),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(30,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(30,5,utf8_decode($fila->nombreprovee),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode($fila->limitecreditoprovee),0,0,'C',false); 
      $mipdf -> Cell(30,5,utf8_decode($fila->saldocuentaprovee),0,0,'C',false); 
      $mipdf -> Ln(5);
    }
  }*/

$refBancarias=mysqli_query($con ,"select refban.consecutivo,refban.institucionrefban, refban.limitecreditorefban,"
                                ." refban.saldocuentarefban,bnct.desc_45 from mg_clientes cte inner join mg_cterefban"
                                ." refban on cte.numerocliente=refban.numerocliente inner join mg_catcod bnct"
                                ." on bnct.catalogo_cve=refban.InstitucionRefBan and bnct.catalogo_id='bancos'"
                                ." where cte.numerocliente=".$numcte.";");


  if(mysqli_num_rows($refBancarias)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("=============================REFERENCIAS BANCARIAS============================="),0,0,'C',false);
    $mipdf -> Ln(10);
    while($fila=$refBancarias->fetch_object()){
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(15,5,utf8_decode("CONS "),0,0,'C',false);   
      $mipdf -> Cell(100,5,utf8_decode("INSTITUCIÓN "),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode("LIMITE DE CRED"),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("SALDO CUENTA "),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(15,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(100,5,utf8_decode($fila->desc_45),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode("$".number_format($fila->limitecreditorefban,2)),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("$".number_format($fila->saldocuentarefban,2)),0,0,'C',false);
      $mipdf -> Ln(5);
    }
  }

$acciones=mysqli_query($con ,"select cteacc.consecutivo,cteacc.fechacompra1aaccion,cteacc.parteinicialsocial,"
                        ." cteacc.fechapago,cteacc.partesocialactual, cteacc.costoacciones,cteacc.formapagoacciones,"
                        ." cteacc.retirablesa,cteacc.retirablesb from mg_clientes cte inner join mg_cteaccion cteacc"
                        ." on cte.numerocliente= cteacc.numerocliente where cte.numerocliente = ".$numcte.";");

  if(mysqli_num_rows($acciones)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("====================================ACCIONES===================================="),0,0,'C',false);
    $mipdf -> Ln(10);    
    $tot=0;
    while($fila=$acciones->fetch_object()){
      $mipdf -> SetFont('Arial','B',8);
      $mipdf -> Cell(10,5,utf8_decode("CONS"),0,0,'C',false);   
      $mipdf -> Cell(15,5,utf8_decode("F.1ª.ACC."),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("PARTE INIC SOC"),0,0,'C',false);
      $mipdf -> Cell(15,5,utf8_decode("F. PAGO"),0,0,'C',false);

      $mipdf -> Cell(28,5,utf8_decode("PARTE SOC ACT"),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode("COSTO ACCIONES"),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("FORMA PAGO"),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode("RETIRABLES A"),0,0,'C',false);
      $mipdf -> Cell(20,5,utf8_decode("RETIRABLES B"),0,0,'C',false);
      $mipdf -> Ln(5);
      $mipdf -> SetFont('Arial','',8);
      $mipdf -> Cell(10,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(15,5,utf8_decode($fila->fechacompra1aaccion),0,0,'C',false);     
      $mipdf -> Cell(30,5,utf8_decode("$".number_format($fila->parteinicialsocial,2)),0,0,'C',false); 
      $mipdf -> Cell(15,5,utf8_decode($fila->fechapago),0,0,'C',false);       
      $mipdf -> Cell(28,5,utf8_decode("$".number_format($fila->partesocialactual,2)),0,0,'C',false);  
      $mipdf -> Cell(25,5,utf8_decode("$".number_format($fila->costoacciones,2)),0,0,'C',false); 
      $mipdf -> Cell(30,5,utf8_decode($fila->formapagoacciones),0,0,'C',false); 
      $mipdf -> Cell(25,5,utf8_decode("$".number_format($fila->retirablesa,2)),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode("$".number_format($fila->retirablesb,2)),0,0,'C',false);     
      $mipdf -> Ln(5);    
      $tot=$fila->retirablesa+$fila->retirablesb;
    }
    $mipdf->SetFont('Arial','B',10);
    $mipdf -> Cell(190,5,utf8_decode("Total de acciones: $".number_format($tot,2)),0,0,'R',false);
  } 

$cuentasBancarias=mysqli_query($con ,"select cteban.consecutivo,"
                      ." cteban.bancoctaban, ctbanco.desc_45 as descbanco, cteban.numerocuentactaBan,"
                      ." cteban.claveinterbancariactaban from mg_clientes cte inner join mg_ctectaban "
                      ." cteban on cte.numerocliente=cteban.numerocliente inner join mg_catcod ctbanco on" 
                      ." ctbanco.catalogo_cve=cteban.bancoctaban and ctbanco.catalogo_id='bancos'"
                      ." where cte.numerocliente=".$numcte.";");
  if(mysqli_num_rows($cuentasBancarias)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("===============================CUENTAS BANCARIAS==============================="),0,0,'C',false);
    $mipdf -> Ln(10);    
    while($fila=$cuentasBancarias->fetch_object()){
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(15,5,utf8_decode("CONS"),0,0,'C',false);         
      $mipdf -> Cell(100,5,utf8_decode("BANCO"),0,0,'C',false);           
      $mipdf -> Cell(50,5,utf8_decode("CUENTA BANCARIA"),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("CVE INTERBANCARIA"),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(15,5,utf8_decode($fila->consecutivo),0,0,'C',false); 
      $mipdf -> Cell(100,5,utf8_decode($fila->descbanco),0,0,'C',false);       
      $mipdf -> Cell(50,5,utf8_decode($fila->numerocuentactaBan),0,0,'C',false);  
      $mipdf -> Cell(30,5,utf8_decode($fila->claveinterbancariactaban),0,0,'C',false);
    }
  }

$partesRelacionadas=mysqli_query($con ,"select cparrel.consecutivo,cparrel.parterelacionadaparrel,catparrel.desc_45 as descparrel,"
                            ." cparrel.nombreparrel,cparrel.rfcparrel,cparrel.direccionparrel  from mg_clientes cte inner join"
                            ." mg_cteparrel cparrel on cte.numerocliente=cparrel.numerocliente  inner join mg_catcod catparrel on"
                            ." cparrel.parterelacionadaparrel=catparrel.catalogo_cve and catalogo_id='tiprel' where cte.numerocliente=".$numcte.";");

  if(mysqli_num_rows($partesRelacionadas)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("==============================PARTES RELACIONADAS=============================="),0,0,'C',false);
    $mipdf -> Ln(10);
   

    while($fila=$partesRelacionadas->fetch_object()){
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(12,5,utf8_decode("CONS"),0,0,'C',false);   
      $mipdf -> Cell(75,5,utf8_decode("PARTE RELAC"),0,0,'C',false);     
      $mipdf -> Cell(45,5,utf8_decode("NOMBRE"),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode("RFC"),0,0,'C',false);
      $mipdf -> Cell(40,5,utf8_decode("DIRECCIÓN"),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(12,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(75,5,utf8_decode($fila->descparrel),0,0,'C',false);       
      $mipdf -> Cell(45,5,utf8_decode($fila->nombreparrel),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode($fila->rfcparrel),0,0,'C',false);  
      $mipdf -> Cell(40,5,utf8_decode($fila->direccionparrel),0,0,'C',false);
      }
    }

$gposoc=mysqli_query($con ,"select gposoc.consecutivo, gposoc.gruposocioeconomicogposoc,"
                    ." cgseg.desc_45 as descgruposocioeconomico,gposoc.nombregposoc,"
                    ." gposoc.rfcgposoc,gposoc.direcciongposoc from mg_clientes cte inner"
                    ." join mg_ctegposoc gposoc on cte.numerocliente=gposoc.numerocliente inner"
                    ." join mg_catcod cgseg on gposoc.gruposocioeconomicogposoc=cgseg.catalogo_cve"
                    ." and catalogo_id='tipgse' where cte.numerocliente=".$numcte.";");

  if(mysqli_num_rows($gposoc)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("==========================GRUPOS SOCIOECONOMICOS=============================="),0,0,'C',false);
    $mipdf -> Ln(10);
    
    while($fila=$gposoc->fetch_object()){
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(12,5,utf8_decode("CONS"),0,0,'C',false);   
      $mipdf -> Cell(60,5,utf8_decode("GPO SOCIOEC"),0,0,'C',false);     
      $mipdf -> Cell(50,5,utf8_decode("NOMBRE"),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode("RFC"),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode("DIRECCIÓN"),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(12,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(60,5,utf8_decode($fila->descgruposocioeconomico),0,0,'C',false);      
      $mipdf -> Cell(50,5,utf8_decode($fila->nombregposoc),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode($fila->rfcgposoc),0,0,'C',false);  
      $mipdf -> Cell(50,5,utf8_decode($fila->direcciongposoc),0,0,'C',false);
    }
  }


$gporiesgocom=mysqli_query($con ,"select ctegcom.consecutivo,ctegcom.gruporiesgocomunrgocom,ctrc.desc_45 as descriesgocomun,"
                          ." ctegcom.nombrergocom,ctegcom.rfcrgocom,ctegcom.direccionrgocom from mg_clientes cte inner"
                          ." join mg_ctergocom ctegcom on cte.numerocliente=ctegcom.numerocliente inner join mg_catcod ctrc on"
                          ." ctrc.catalogo_cve=ctegcom.gruporiesgocomunrgocom and catalogo_id='tiprrc' where cte.numerocliente=".$numcte.";");

  if(mysqli_num_rows($gporiesgocom)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("==============================GRUPO DE RIESGO COMÚN============================="),0,0,'C',false);
    $mipdf -> Ln(10);

    while($fila=$gporiesgocom->fetch_object()){
      $mipdf -> SetFont('Arial','B',10);
      $mipdf -> Cell(12,5,utf8_decode("CONS"),0,0,'C',false);   
      $mipdf -> Cell(60,5,utf8_decode("GPO RGO COMUN"),0,0,'C',false);      
      $mipdf -> Cell(50,5,utf8_decode("NOMBRE"),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode("RFC"),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode("DIRECCION"),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(12,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(60,5,utf8_decode($fila->descriesgocomun),0,0,'C',false);       
      $mipdf -> Cell(50,5,utf8_decode($fila->nombrergocom),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode($fila->rfcrgocom),0,0,'C',false);  
      $mipdf -> Cell(50,5,utf8_decode($fila->direccionrgocom),0,0,'C',false);
   
    }
  }

  $mipdf -> output() ;
}
?>