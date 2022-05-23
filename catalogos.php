<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
  header('Content-Type: application/json');

  require("./conexion.php");
  $conn = returnConection();

  $response = [
    'data' => [],
    'message' => 'Error, catalogo no encontrado',
    'status' => 'error'
  ];

  // ESTO ARCHIVOS SE ELIMINARIAN:
  // TODO borar CatalogoActdet.php => 'actdet' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoActeco.php => 'acteco' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoBancos.php => 'bancos' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoCatpue.php => 'catpue' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoCod_id.php => 'cod_id' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoEdociv.php => 'edociv' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoIdentif.php => 'identif' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogonaCION.php => 'naCION' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoPerjur.php => 'perjur' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoProfes.php => 'profes' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoSexo.php => 'sexo' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoStscte.php => 'stscte' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoTipded.php => 'tipded' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoTipdom.php => 'tipdom' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoTipgse.php => 'tipgse' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoTipman.php => 'tipman' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoTipred.php => 'tipred' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoTiprel.php => 'tiprel' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoTiprpe.php => 'tiprpe' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoTiprrc.php => 'tiprrc' , ya se implementó este archivo en cliente.service.ts
  // TODO borar CatalogoTiptel.php => 'tiptel' , ya se implementó este archivo en cliente.service.ts

  /*
    NUEVOS CATALOGOS
    ftefon - Fuente de fondeo
    prgfon - Programa de fondeo
    divisa - Divisas
    tipcre - Tipos de crédito
    stscnt - Estatus Contable
    stscrd - Estatus del Crédito
    calint - Tipos de cálculo de intereses
    revtas - Tipos de revisión de tasa
    tasas - Tasas de interés
    tipdis - Tipos de disposición
    tipppc - Tipos de Plan de Pago de Capital
    tipppi - Tipos de Plan de Pago de Interés
  */

  $mg_catcod = [
    'actdet',  'acteco',  'bancos',  'catpue',
    'cod_id',  'edociv',  'identif', 'naCION',
    'perjur',  'profes',  'sexo',    'stscte',
    'tipded',  'tipdom',  'tipgse',  'tipman',
    'tipred',  'tiprel',  'tiprpe',  'tiprrc',
    'tiptel',  'tipdoc',  'ftefon',  'prgfon',
    'divisa',  'tipcre',  'stscnt',  'stscrd',
    'calint',  'revtas',  'tasas',   'tipdis',
    'tipppc',  'tipppi'
  ];

  if ( isset($_GET['catid']) && !empty($_GET['catid']) ) {
    $catId = $_GET['catid'];
    if ( in_array($catId, $mg_catcod) ) {
      $query = "SELECT"
          . " Catalogo_cve AS catalogo_cve,"
          . " desc_45"
        . " FROM mg_catcod"
        . " WHERE Catalogo_id = '{$catId}'"
          . " AND Catalogo_cve != '0000000000'"
        . " ORDER BY desc_45 ASC";
      $registro = mysqli_query($conn, $query);
      $rows = [];
      while( $reg = mysqli_fetch_assoc($registro) ){
        array_push($rows,$reg);
      }
      $response['data'] = $rows;
      $response['status'] = 'ok';
      $response['message'] = 'Operacion exitosa';

    }
  }

  echo json_encode($response);

?>
