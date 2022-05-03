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
    // $this -> Image("../images/logo.png",5,5,30,20);
    $this -> Cell(195,5,utf8_decode("FINANCIERA RURAL DE MÉXICO (FIRMEX)"),0,0,'C',false);
    $this -> Ln(5);
    $this -> Cell(195,5,utf8_decode("MÓDULO GENERAL -  CÉDULA DE CLIENTES"),0,0,'C',false);
    $this -> Ln(5);   
    $this -> Cell(50,5,utf8_decode("SUCURSAL: GUANAJUATO "),0,0,'L',false); 
    $this -> Cell(150,5,utf8_decode("FECHA: ".date('d-m-Y')),0,0,'R',false); 
    $this -> Ln(15);    
  /*$this -> SetFont('Arial','',7);   
        $this -> Cell(8,5,"No.",0,0,'C',false);
      $this -> Cell(15,5,"Contrato",0,0,'C',false);        
      $this -> Cell(70,5,"Nombre",0,0,'C',false);
      $this -> Cell(85,5,utf8_decode("Dirección"),0,0,'L',false);
      $this -> Cell(10,5,"Lote",0,0,'L',false);
      $this -> Cell(10,5,"Mza.",0,0,'C',false);
      $this -> Cell(15,5,"Medidor",0,0,'C',false);
      $this -> Cell(35,5,"Tarifa",0,0,'L',false);
      $this -> Cell(10,5,"Sector",0,0,'L',false); 
      $this -> Cell(10,5,"Ruta",0,0,'L',false); 
      $this -> Cell(10,5,"Folio",0,0,'L',false); 
      $this -> Ln(5); 
          
      $this -> SetFont('Arial','',7);
      $this->SetFillColor(204,206,210);*/
  }
      
  function Footer(){
    Global $intHoja;
      //Posición: a 3,5 cm del final
    $this->SetY(-15);
      //Arial italic 8
    $this->SetFont('Arial','I',7);
      //Número de página
    $this -> Ln(4);
    $this->Cell(0,10,'Pag. '.$this->PageNo()  ." - " .date('d-m-Y'),0,0,'C');
      //$this->Cell(0,10,$_SESSION['domicilio'],0,0,'C');
    $this -> Ln(4);
      //$this->Cell(0,10,$_SESSION['telefono'].", ".$_SESSION['email'],0,0,'C');
  }
}

  $numcte=$params->idCliente;
   
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
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(50,5,utf8_decode("Sucursal: ".$fila->sucursal_nom),0,0,'C',false); 
      $mipdf -> Cell(80,5,utf8_decode("Promotor: ".$fila->PromotorNombre),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(30,5,utf8_decode("Número de Cliente: ".$fila->numeroCliente),0,0,'L',false);
      $mipdf -> Cell(110,5,utf8_decode("Personalidad Jurídica: ".$fila->desperjur),0,0,'C',false);
      $mipdf -> Cell(40,5,utf8_decode("Estatus del Cliente: ".$fila->desStatus),0,0,'C',false);   
      $mipdf -> Ln(10);
    }
  }    

  $perFisica=mysqli_query($con ,"select ctpf.FechaNacimiento,cte.rfc,ctpf.curp,ctpf.sexo,catsx.desc_45 as "
                      ." sexodesc,ctpf.estadocivil,catecv.desc_45 as edocivdesc,ctpf.tipoidentificacion,"
                      ." catiden.desc_45 as idendesc, ctpf.numeroidentificacion,ctpf.profesion,"
                      ." catprof.desc_45 as descprofesion,cte.nacionalidad,catnac.desc_45 as desnacionalidad,"
                      ." cte.emailpersonal,cte.emailempresa,cte.parterelacionada,catparrel.desc_45 as descparterelacionada,"
                      ." cte.grupoconsejo, catgpocon.desc_45 as descgrupoconsejo, cte.gruporiesgocomun, catrgocom.desc_45 as descgruporiesgocomun"
                      ." from mg_clientes cte  inner join mg_ctepf ctpf on cte.numerocliente= ctpf.numerocliente inner join mg_catcod"
                      ." catsx on  catsx.catalogo_cve= ctpf.sexo and  catsx.catalogo_id='sexo' inner join mg_catcod catecv on "
                      ." catecv.catalogo_cve= ctpf.estadocivil and catecv.catalogo_id='edociv' inner join mg_catcod catiden on "
                      ." catiden.catalogo_cve= ctpf.tipoidentificacion and catiden.catalogo_id='identif' inner join mg_catcod catprof on "
                      ." catprof.catalogo_cve= ctpf.profesion and catprof.catalogo_id='profes' inner join mg_catcod catnac on "
                      ." catnac.catalogo_cve= cte.nacionalidad and catnac.catalogo_id='nacion' inner join mg_catcod catparrel on"
                      ." catparrel.catalogo_cve= cte.parterelacionada and catparrel.catalogo_id='parrel' inner join mg_catcod catgpocon"
                      ." on catgpocon.catalogo_cve= cte.grupoconsejo and catgpocon.catalogo_id='gpocon' inner join mg_catcod catrgocom on"
                      ." catrgocom.catalogo_cve= cte.grupoconsejo and catrgocom.catalogo_id='rgocom' where cte.numerocliente=".$numcte.";");

  if(mysqli_num_rows($perFisica)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(195,5,utf8_decode("=====================================PERSONA FISICA====================================="),0,0,'C',false);
    $mipdf -> Ln(10);
    while($fila=$perFisica->fetch_object()){
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(50,5,utf8_decode("Fecha Nacimiento: ".$fila->FechaNacimiento),0,0,'C',false); 
      $mipdf -> Cell(80,5,utf8_decode("RFC: ".$fila->rfc),0,0,'C',false);
      $mipdf -> Cell(80,5,utf8_decode("CURP: ".$fila->curp),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(30,5,utf8_decode("Sexo: ".$fila->sexodesc),0,0,'C',false);
      $mipdf -> Cell(65,5,utf8_decode("Estado civil: ".$fila->edocivdesc),0,0,'C',false);
      $mipdf -> Cell(65,5,utf8_decode("Identificación: ".$fila->idendesc),0,0,'C',false); 
      $mipdf -> Ln(5);  
      $mipdf -> Cell(30,5,utf8_decode("Profesión: ".$fila->descprofesion),0,0,'C',false);
      $mipdf -> Cell(65,5,utf8_decode("Nacionalidad: ".$fila->desnacionalidad),0,0,'C',false);
      $mipdf -> Ln(5);
      $mipdf -> Cell(30,5,utf8_decode("Email Personal: ".$fila->emailpersonal),0,0,'C',false);
      $mipdf -> Ln(5);  
      $mipdf -> Cell(30,5,utf8_decode("Email Empresa: ".$fila->emailempresa),0,0,'C',false);
      $mipdf -> Ln(5);  
      $mipdf -> Cell(30,5,utf8_decode("Parte Relacionada: ".$fila->descparterelacionada),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("Grupo consejo : ".$fila->descgrupoconsejo),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(30,5,utf8_decode("Grupo de riesgo en común : ".$fila->descgruporiesgocomun),0,0,'C',false);
      $mipdf -> Ln(10);
    }
  }

  $perMoral=mysqli_query($con ,"select ctem.FechaConstitucion,cte.rfc,ctem.nombresociedad,ctem.representantelegal,"
                            ." ctem.presidenteconsejo,ctem.consejero,ctem.secretario,cte.emailpersonal,cte.emailempresa,"
                            ." cte.parterelacionada,cte.grupoconsejo,catgpocon.desc_45 as descgrupoconsejo,cte.gruporiesgocomun,"
                            ." catrgocom.desc_45 as descgruporiesgocomun from mg_ctepm ctem inner join mg_clientes cte on "
                            ." cte.numerocliente=ctem.numerocliente inner join mg_catcod catgpocon on catgpocon.catalogo_cve= cte.grupoconsejo"
                            ." and catgpocon.catalogo_id='gpocon' inner join mg_catcod catrgocom on catrgocom.catalogo_cve= cte.grupoconsejo "
                            ." and catrgocom.catalogo_id='rgocom' where cte.numerocliente=".$numcte.";");

  if(mysqli_num_rows($perMoral)>0){
    $mipdf -> Ln(10);
    $mipdf -> Cell(195,5,utf8_decode("=====================================PERSONA MORAL====================================="),0,0,'C',false);
    $mipdf -> Ln(10);

    while($fila=$perMoral->fetch_object()){
      $mipdf -> Cell(50,5,utf8_decode("Fecha de constitución: ".$fila->FechaConstitucion),0,0,'C',false); 
      $mipdf -> Cell(80,5,utf8_decode("RFC: ".$fila->rfc),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(30,5,utf8_decode("Nombre de la Sociedad: ".$fila->nombresociedad),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(65,5,utf8_decode("Representante legal: ".$fila->representantelegal),0,0,'C',false);
      $mipdf -> Ln(5);
      $mipdf -> Cell(65,5,utf8_decode("Presidente del consejo: ".$fila->presidenteconsejo),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(65,5,utf8_decode("Consejero: ".$fila->consejero),0,0,'C',false);
      $mipdf -> Ln(5);
      $mipdf -> Cell(30,5,utf8_decode("Email Personal: ".$fila->emailpersonal),0,0,'C',false);
      $mipdf -> Ln(5);  
      $mipdf -> Cell(30,5,utf8_decode("Email Empresa: ".$fila->emailempresa),0,0,'C',false);
      $mipdf -> Ln(5);  
      $mipdf -> Cell(30,5,utf8_decode("Parte Relacionada: ".$fila->parterelacionada),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("Grupo consejo : ".$fila->descgrupoconsejo),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(30,5,utf8_decode("Grupo de riesgo en común : ".$fila->descgruporiesgocomun),0,0,'C',false);  
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
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(50,5,utf8_decode("Teléfono domicilio: ".$fila->telefonoDomicilio),0,0,'C',false); 
      $mipdf -> Cell(70,5,utf8_decode("extensión: ".$fila->ExtensionDomicilio),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(50,5,utf8_decode("Teléfono oficina: ".$fila->TelefonoOficina),0,0,'C',false);      
      $mipdf -> Cell(70,5,utf8_decode("extensión: ".$fila->ExtensionOficina),0,0,'C',false);
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
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(50,5,utf8_decode("Red social 1: ".$fila->redsocial1),0,0,'C',false);     
      $mipdf -> Ln(5); 
      $mipdf -> Cell(50,5,utf8_decode("Red social 2: ".$fila->redsocial2),0,0,'C',false);  
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
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(50,5,utf8_decode("TIPO DE DOMICILIO: ".$fila->desctipodomicilio),0,0,'C',false);     
      $mipdf -> Ln(5); 
      $mipdf -> Cell(50,5,utf8_decode("CALLE: ".$fila->calle),0,0,'C',false);  
      $mipdf -> Cell(70,5,utf8_decode("NUM: ".$fila->numeroexterior),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode("INTERIOR: ".$fila->numerointerior),0,0,'C',false);   
      $mipdf -> Ln(5);
      $mipdf -> Cell(50,5,utf8_decode("COD POSTAL: ".$fila->codigopostal),0,0,'C',false);         
      $mipdf -> Cell(70,5,utf8_decode("COLONIA: ".$fila->colonia),0,0,'C',false);  
      $mipdf -> Cell(50,5,utf8_decode("MUNICIPIO: ".$fila->municipio_desc),0,0,'C',false);   
      $mipdf -> Ln(5);
      $mipdf -> Cell(50,5,utf8_decode("ESTADO: ".$fila->estado_desc),0,0,'C',false);         
      $mipdf -> Cell(50,5,utf8_decode("PAÍS: ".$fila->pais),0,0,'C',false);   
      $mipdf -> Ln(5);
    }
  }

  $actividad_economica=mysqli_query($con ,"select acteco.actividadeconomica,cateco.desc_45 as descactividadeconomica"
                        ." ,acteco.actividaddetallada, catdet.desc_45 as descactividaddetallada,acteco.ingresomensual,"
                        ." acteco.otroingresomensual,acteco.gastosmensuales,acteco.flujoefectivo from mg_clientes cte "
                        ." inner join mg_cteacteco acteco on cte.numerocliente=acteco.numerocliente inner join mg_catcod "
                        ." cateco on cateco.catalogo_cve=acteco.actividadeconomica and cateco.catalogo_id='acteco' inner "
                        ." join mg_catcod catdet on catdet.catalogo_cve=acteco.actividadeconomica and catdet.catalogo_id='actdet'"
                        ." where cte.numerocliente=".$numcte.";");
  
  
if(mysqli_num_rows($actividad_economica)>0){
$mipdf -> Ln(10);
$mipdf->SetFont('Arial','B',12);
$mipdf -> Cell(195,5,utf8_decode("=====================================ACTIVIDAD ECONOMICA====================================="),0,0,'C',false);
$mipdf -> Ln(10);
$mipdf -> Cell(50,5,utf8_decode("Tipo de domicilio: ".$fila->tipodomicilio),0,0,'C',false);    
while($fila=$actividad_economica->fetch_object()){
  $mipdf -> SetFont('Arial','',10);
 
$mipdf -> Cell(50,5,utf8_decode("Calle: ".$fila->numeroexterior),0,0,'C',false);   
$mipdf -> Ln(5);
$mipdf -> Cell(50,5,utf8_decode("Num.: ".$fila->calle),0,0,'C',false);   
$mipdf -> Ln(5);
$mipdf -> Cell(50,5,utf8_decode("Interior: ".$fila->numerointerior),0,0,'C',false);   
$mipdf -> Ln(5);
$mipdf -> Cell(50,5,utf8_decode("Cod postal: ".$fila->codigopostal),0,0,'C',false);   
$mipdf -> Ln(5);
$mipdf -> Cell(50,5,utf8_decode("Colonia: ".$fila->colonia),0,0,'C',false);   
$mipdf -> Ln(5);
$mipdf -> Cell(50,5,utf8_decode("Municipio: ".$fila->municipio_desc),0,0,'C',false);   
$mipdf -> Ln(5);
$mipdf -> Cell(50,5,utf8_decode("Estado: ".$fila->estado_desc),0,0,'C',false);   
$mipdf -> Ln(5);
}
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
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(15,5,utf8_decode("CONS "),0,0,'C',false);   
      $mipdf -> Cell(80,5,utf8_decode("NOMBRE "),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode("TIP RELACIÓN"),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("TELÉFONO "),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(15,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(80,5,utf8_decode($fila->nombrerefper),0,0,'C',false);
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
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(15,5,utf8_decode("CONS: "),0,0,'C',false);   
      $mipdf -> Cell(80,5,utf8_decode("NOMBRE "),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode("LIMITE DE CRED"),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("SALDO CUENTA "),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(15,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(80,5,utf8_decode($fila->nombrerefcom),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode($fila->limitecreditorefcom),0,0,'C',false); 
      $mipdf -> Cell(30,5,utf8_decode($fila->saldocuentarefcom),0,0,'C',false); 
      $mipdf -> Ln(5); 
    }
  }

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
  }

$refBancarias=mysqli_query($con ,"select refban.consecutivo,refban.institucionrefban,"
                      ." refban.limitecreditorefban,refban.saldocuentarefban from mg_clientes cte inner"
                      ." join mg_cterefban refban on cte.numerocliente=refban.numerocliente"
                      ." where cte.numerocliente=".$numcte.";");


  if(mysqli_num_rows($refBancarias)>0){
    $mipdf -> Ln(10);
    $mipdf->SetFont('Arial','B',12);
    $mipdf -> Cell(200,5,utf8_decode("=============================REFERENCIAS BANCARIAS============================="),0,0,'C',false);
    $mipdf -> Ln(10);
    while($fila=$refBancarias->fetch_object()){
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(15,5,utf8_decode("CONS "),0,0,'C',false);   
      $mipdf -> Cell(80,5,utf8_decode("INSTITUCIÓN "),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode("LIMITE DE CRED"),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("SALDO CUENTA "),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(15,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(80,5,utf8_decode($fila->institucionrefban),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode($fila->limitecreditorefban),0,0,'C',false); 
      $mipdf -> Cell(30,5,utf8_decode($fila->saldocuentarefban),0,0,'C',false); 
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

    while($fila=$acciones->fetch_object()){
      $mipdf -> SetFont('Arial','',8);
      $mipdf -> Cell(10,5,utf8_decode("CONS"),0,0,'C',false);   
      $mipdf -> Cell(15,5,utf8_decode("F.1ª.ACC."),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode("PARTE INIC SOC"),0,0,'C',false);
      $mipdf -> Cell(15,5,utf8_decode("F. PAGO"),0,0,'C',false);

      $mipdf -> Cell(25,5,utf8_decode("PARTE SOC ACT"),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("COSTO ACCIONES"),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode("FORMA PAGO"),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode("RETIRABLES A"),0,0,'C',false);
      $mipdf -> Cell(20,5,utf8_decode("RETIRABLES B"),0,0,'C',false);
      $mipdf -> Ln(5);
      $mipdf -> Cell(10,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(15,5,utf8_decode($fila->fechacompra1aaccion),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode($fila->parteinicialsocial),0,0,'C',false); 
      $mipdf -> Cell(15,5,utf8_decode($fila->fechapago),0,0,'C',false);

      $mipdf -> Cell(25,5,utf8_decode($fila->partesocialactual),0,0,'C',false);  
      $mipdf -> Cell(30,5,utf8_decode($fila->costoacciones),0,0,'C',false);


      $mipdf -> Cell(25,5,utf8_decode($fila->formapagoacciones),0,0,'C',false); 
      $mipdf -> Cell(25,5,utf8_decode($fila->retirablesa),0,0,'C',false);
      $mipdf -> Cell(20,5,utf8_decode($fila->retirablesb),0,0,'C',false);     
      $mipdf -> Ln(5);
    }
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
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(15,5,utf8_decode("CONS"),0,0,'C',false);   
      $mipdf -> Cell(20,5,utf8_decode("NOMBRE"),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode("BANCO"),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode("DESC"),0,0,'C',false);      
      $mipdf -> Cell(50,5,utf8_decode("CUENTA BANCARIA"),0,0,'C',false);
      $mipdf -> Cell(40,5,utf8_decode("CVE INTERBANCARIA"),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(15,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(20,5,utf8_decode($fila->nombrecuentabancariactaban),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode($fila->bancoctaban),0,0,'C',false); 
      $mipdf -> Cell(50,5,utf8_decode($fila->descbanco),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode($fila->numerocuentactaBan),0,0,'C',false);  
      $mipdf -> Cell(40,5,utf8_decode($fila->claveinterbancariactaban),0,0,'C',false);
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
      $mipdf -> SetFont('Arial','',8);
      $mipdf -> Cell(10,5,utf8_decode("CONS"),0,0,'C',false);   
      $mipdf -> Cell(20,5,utf8_decode("PARTE RELAC"),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode("DESC"),0,0,'C',false);
      $mipdf -> Cell(40,5,utf8_decode("NOMBRE"),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode("RFC"),0,0,'C',false);
      $mipdf -> Cell(55,5,utf8_decode("DIRECCION"),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(10,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(20,5,utf8_decode($fila->parterelacionadaparrel),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode($fila->descparrel),0,0,'C',false); 
      $mipdf -> Cell(40,5,utf8_decode($fila->nombreparrel),0,0,'C',false);
      $mipdf -> Cell(25,5,utf8_decode($fila->rfcparrel),0,0,'C',false);  
      $mipdf -> Cell(55,5,utf8_decode($fila->direccionparrel),0,0,'C',false);
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
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(15,5,utf8_decode("CONS"),0,0,'C',false);   
      $mipdf -> Cell(25,5,utf8_decode("GPO SOCIOEC"),0,0,'C',false);
      $mipdf -> Cell(40,5,utf8_decode("DESC"),0,0,'C',false);
      $mipdf -> Cell(45,5,utf8_decode("NOMBRE"),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("RFC"),0,0,'C',false);
      $mipdf -> Cell(45,5,utf8_decode("DIRECCION"),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(15,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(25,5,utf8_decode($fila->gruposocioeconomicogposoc),0,0,'C',false);
      $mipdf -> Cell(40,5,utf8_decode($fila->descgruposocioeconomico),0,0,'C',false); 
      $mipdf -> Cell(45,5,utf8_decode($fila->nombregposoc),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode($fila->rfcgposoc),0,0,'C',false);  
      $mipdf -> Cell(45,5,utf8_decode($fila->direcciongposoc),0,0,'C',false);
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
      $mipdf -> SetFont('Arial','',10);
      $mipdf -> Cell(10,5,utf8_decode("CONS"),0,0,'C',false);   
      $mipdf -> Cell(35,5,utf8_decode("GPO RGO COMUN"),0,0,'C',false);
      $mipdf -> Cell(40,5,utf8_decode("DESC"),0,0,'C',false);
      $mipdf -> Cell(35,5,utf8_decode("NOMBRE"),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode("RFC"),0,0,'C',false);
      $mipdf -> Cell(50,5,utf8_decode("DIRECCION"),0,0,'C',false);
      $mipdf -> Ln(5); 
      $mipdf -> Cell(10,5,utf8_decode($fila->consecutivo),0,0,'C',false);  
      $mipdf -> Cell(35,5,utf8_decode($fila->gruporiesgocomunrgocom),0,0,'C',false);
      $mipdf -> Cell(40,5,utf8_decode($fila->descriesgocomun),0,0,'C',false); 
      $mipdf -> Cell(35,5,utf8_decode($fila->nombrergocom),0,0,'C',false);
      $mipdf -> Cell(30,5,utf8_decode($fila->rfcrgocom),0,0,'C',false);  
      $mipdf -> Cell(50,5,utf8_decode($fila->direccionrgocom),0,0,'C',false);
   
    }
  }

  /*$no=1;
  while($fila=$rs1->fetch_object()){
    $mipdf -> Cell(8,5,$no,0,0,'L',false);
    $mipdf -> Cell(15,5,$fila->folioCont,0,0,'L',false);
    $mipdf -> Cell(70,5,utf8_decode(trim($fila->nombre)." ".trim($fila->paterno). " ".trim($fila->materno)),0,0,'L',false);
    $mipdf -> Cell(85,5,trim($fila->colonia)." ".trim($fila->calle)." ".trim($fila->num_ext)." ".trim($fila->num_int),0,0,'L',false);
    $mipdf -> Cell(10,5,trim($fila->lote),0,0,'L',false);
    $mipdf -> Cell(10,5,trim($fila->manzana),0,0,'L',false);
    $mipdf -> Cell(15,5,trim($fila->medidor_),0,0,'L',false);
    $mipdf -> Cell(35,5,trim($fila->tarifa),0,0,'L',false);
    $mipdf -> Cell(10,5,trim($fila->sector),0,0,'L',false);
    $mipdf -> Cell(10,5,trim($fila->ruta),0,0,'L',false);
    $mipdf -> Cell(10,5,trim($fila->folioReparto),0,0,'L',false);
    $mipdf -> Ln(10);    
    $no++;
  } */
  
  
  mysqli_close($cnx);
  $mipdf -> output()
  

?>