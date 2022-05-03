<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
    header('Content-Type: application/json');

    if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {

        require("./conexion.php");
        $con = returnConection();

        $json = file_get_contents('php://input');
        $params = json_decode($json, true);

      

        $query = "CALL mgsp_SolicitudesCredito("
            . " {$params['LineaCredito']}, "
            . " '{$params['SolicitudLinea']}', "
            . " {$params['Consecutivo']}, "
            . " '{$params['TipoCredito']}', "
            . " '{$params['Plazo']}', "
            . " '{$params['Destino']}', "
            . " {$params['MontoSolicitado']}, "
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
        $vec = [];
        while ( $reg = mysqli_fetch_assoc($row) ) {
            $vec[] = $reg;
        }

        $cad = json_encode($vec, JSON_INVALID_UTF8_IGNORE);
        echo $cad;
    }

    /*
    PRUEBA CRUDO
            $query = "CALL mgsp_SolicitudesCredito("
            . " '55', "
            . " '03', "
            . " '3', "
            . " '02', "
            . " '12', "
            . " '02', "
            . " '120000', "
            . " @OutErrorClave, "
            . " @OutErrorProcedure, "
            . " @OutErrorDescripcion)";
    */

?>
