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

  $mg_catcod = ['actdet', 'acteco', 'bancos', 'catpue', 'cod_id'];

  if ( isset($_GET['catid']) && !empty($_GET['catid']) ) {
    $catId = $_GET['catid'];
    if ( in_array($catId, $mg_catcod) ) {
      $query = "SELECT catalogo_cve, desc_45 FROM mg_catcod WHERE catalogo_id = '$catId' ORDER BY catalogo_cve";
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
