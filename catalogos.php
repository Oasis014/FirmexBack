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
  // CatalogoActdet.php => 'actdet'
  // CatalogoActeco.php => 'acteco'
  // CatalogoBancos.php => 'bancos'
  // CatalogoCatpue.php => 'catpue'
  // CatalogoCod_id.php => 'cod_id'
  // CatalogoEdociv.php => 'edociv'
  // CatalogoIdentif.php => 'identif'
  // CatalogonaCION.php => 'naCION'
  // CatalogoPerjur.php => 'perjur'
  // CatalogoProfes.php => 'profes'
  // CatalogoSexo.php => 'sexo'
  // CatalogoStscte.php => 'stscte'
  // CatalogoTipded.php => 'tipded'
  // CatalogoTipdom.php => 'tipdom'
  // CatalogoTipgse.php => 'tipgse'
  // CatalogoTipman.php => 'tipman'
  // CatalogoTipred.php => 'tipred'
  // CatalogoTiprel.php => 'tiprel'
  // CatalogoTiprpe.php => 'tiprpe'
  // CatalogoTiprrc.php => 'tiprrc'
  // CatalogoTiptel.php => 'tiptel'

  $mg_catcod = [
    'actdet',  'acteco',  'bancos',  'catpue',
    'cod_id',  'edociv',  'identif', 'naCION',
    'perjur',  'profes',  'sexo',    'stscte',
    'tipded',  'tipdom',  'tipgse',  'tipman',
    'tipred',  'tiprel',  'tiprpe',  'tiprrc',
    'tiptel',  'tipdoc'
  ];

  if ( isset($_GET['catid']) && !empty($_GET['catid']) ) {
    $catId = $_GET['catid'];
    if ( in_array($catId, $mg_catcod) ) {
      $query = "SELECT
          Catalogo_cve AS catalogo_cve,
          desc_45
        FROM mg_catcod WHERE Catalogo_id = '$catId' ORDER BY desc_45 DESC";
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
