<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
  header('Content-Type: application/json');

  require("./conexion.php");
  $conn = returnConection();

  $rows = [];

  if ( 'GET' === $_SERVER['REQUEST_METHOD'] &&
        isset($_GET['estadoId']) && !empty($_GET['estadoId']) 
    ) {

    $estadoId = $_GET['estadoId'];

    $query = "SELECT"
        . " municipio_id,"
        . " municipio_desc"
        . " FROM mg_municipios"
        . " WHERE estado_id = {$estadoId}"
        . " ORDER BY municipio_desc;";
    $registro = mysqli_query($conn, $query);

    while( $reg = mysqli_fetch_assoc($registro) ){
        array_push($rows,$reg);
    }

  }

  echo json_encode($rows, JSON_INVALID_UTF8_IGNORE);

?>
