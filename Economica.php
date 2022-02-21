<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');

    require("./conexion.php");
    $con = returnConection();
    $data = [];
    $vec = [];
    $method = $_SERVER['REQUEST_METHOD'];

    if ( 'POST' === $method ) {

        $json = file_get_contents('php://input');
        $params = json_decode($json, true);

        $query = "CALL mgsp_ClientesActividadEconomica("
          . " '{$params['Id']}', "
          . " '{$params['ActividadEconomica']}', "
          . " '{$params['ActividadDetallada']}', "
          . " '{$params['IngresoMensual']}', "
          . " '{$params['OtroIngresoMensual']}', "
          . " '{$params['GastosMensuales']}', "
          . " '{$params['FlujoEfectivo']}', "
          . " @OutErrorClave, "
          . " @OutErrorProcedure, "
          . " @OutErrorDescripcion) ";

        error_log($query);

        $registro = mysqli_query($con, $query);
        $row = mysqli_query($con,
            "SELECT @OutErrorClave as errorClave, "
            . " @OutErrorProcedure as errorSp, "
            . " @OutErrorDescripcion as errorDescripcion");

        while( $reg = mysqli_fetch_assoc($row) ) {
            $vec[] = $reg;
        }

    } else if ( 'GET' === $method &&
      isset($_GET['userId']) && !empty($_GET['userId'])
    ) {
        // TODO join para obtener los nombres , en lugar del id
        $userId = $_GET['userId'];
        $query = "SELECT"
          . " NumeroCliente, "
          . " ActividadEconomica, "
          . " ActividadDetallada, "
          . " IngresoMensual, "
          . " OtroIngresoMensual, "
          . " GastosMensuales, "
          . " FlujoEfectivo "
          . " FROM mg_cteacteco WHERE NumeroCliente = '{$userId}'";

        $registro = mysqli_query($con, $query);

        while ( $reg = mysqli_fetch_assoc($registro) ) {
            $vec[] = $reg;
        }

    } else if ( 'DELETE' === $method &&
      isset($_GET['NumeroCliente']) && !empty($_GET['NumeroCliente']) &&
      isset($_GET['ActividadEconomica']) && !empty($_GET['ActividadEconomica']) &&
      isset($_GET['ActividadDetallada']) && !empty($_GET['ActividadDetallada'])
    ) {

        $numeroCliente = $_GET['NumeroCliente'];
        $actividadEconomica = $_GET['ActividadEconomica'];
        $actividadDetallada = $_GET['ActividadDetallada'];

        $query = "CALL mgsp_ClientesActividadEconomicaBorrar("
          . " {$numeroCliente}, "
          . " '{$actividadEconomica}', "
          . " '{$actividadDetallada}', "
          . " @OutErrorClave, "
          . " @OutErrorProcedure, "
          . " @OutErrorDescripcion) ";

        error_log($query);

        $registro = mysqli_query($con, $query);
        $row = mysqli_query($con, "SELECT "
            . " @OutErrorClave as errorClave, "
            . " @OutErrorProcedure as errorSp, "
            . " @OutErrorDescripcion as errorDescripcion");

        while ( $reg = mysqli_fetch_assoc($row) ) {
            $vec[] = $reg;
        }

    }

    $data = json_encode($vec, JSON_INVALID_UTF8_IGNORE);
    echo $data;

?>