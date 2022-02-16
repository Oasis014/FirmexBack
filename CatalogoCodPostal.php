<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
  header('Content-Type: application/json');

  require("./conexion.php");
  $conn = returnConection();
  $rows = [];

  if ( 'GET' === $_SERVER['REQUEST_METHOD'] &&
        isset($_GET['estadoId']) && !empty($_GET['estadoId']) &&
        isset($_GET['municipioId']) && !empty($_GET['municipioId']) 
    ) {

    $estadoId = $_GET['estadoId'];
    $municipioId = $_GET['municipioId'];

    $query = "SELECT"
        . " DISTINCT(codigoPostal_id) AS cpostal"
        . " FROM mg_sepomex"
        . " WHERE estado_id = '{$estadoId}' AND municipio_id = '{$municipioId}'"
        . " ORDER BY codigoPostal_id;";
    $registro = mysqli_query($conn, $query);

    while( $reg = mysqli_fetch_assoc($registro) ){
        array_push($rows,$reg);
    }

  }

  echo json_encode($rows, JSON_INVALID_UTF8_IGNORE);

?>
