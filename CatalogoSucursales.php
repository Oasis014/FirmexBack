<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
  header('Content-Type: application/json');

  require("./conexion.php");
  $conn = returnConection();

  $rows = [];

  if ( 'GET' === $_SERVER['REQUEST_METHOD'] ) {

    $query = "SELECT"
        . " sucursal_id AS catalogo_cve,"
        . " sucursal_nom AS desc_45"
        . " FROM mg_sucursales ORDER BY 2";
    $registro = mysqli_query($conn, $query);

    while( $reg = mysqli_fetch_assoc($registro) ){
        array_push($rows,$reg);
    }

  }

  echo json_encode($rows, JSON_INVALID_UTF8_IGNORE);

?>
