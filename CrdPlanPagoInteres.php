<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');

    require("./conexion.php");
    $con = returnConection();

    $method = $_SERVER['REQUEST_METHOD'];
    $response = [];

    if ( 'POST' === $method ) {
        $json = file_get_contents('php://input');
        $params = json_decode($json, true);

        $query = "CALL mgsp_CrdPlanPagoInteres("
            . " {$params['numeroCredito']},"
            . " '{$params['fechaCuota']}',"           
            . " @OutErrorClave, "
            . " @OutErrorProcedure, "
            . " @OutErrorDescripcion)";
        error_log($query);

        $registro = mysqli_query($con, $query);

        $row = mysqli_query($con,
            "SELECT
                @OutErrorClave as errorClave,
                @OutErrorProcedure as errorSp,
                @OutErrorDescripcion as errorDescripcion");

        $response = mysqli_fetch_assoc($row);

    } else if ( 'DELETE' === $method) {
        $json = file_get_contents('php://input');
        $params = json_decode($json, true);

        $query = "CALL mgsp_CrdPlanPagoInteresBorrar("
            . " {$params['numeroCredito']}, "
            . " '{$params['fechaCuota']}', "
            . " @OutErrorClave, "
            . " @OutErrorProcedure, "
            . " @OutErrorDescripcion)";
        error_log($query);

        $registro = mysqli_query($con, $query);

        $row = mysqli_query($con,
            "SELECT
                @@OutErrorClave as errorClave,
                @OutErrorProcedure as errorSp,
                @OutErrorDescripcion as errorDescripcion");

        $response = mysqli_fetch_assoc($row);
    }

    mysqli_close($con);

    $ret = json_encode($response, JSON_INVALID_UTF8_IGNORE);
    echo $ret;

?>
