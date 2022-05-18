<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
    header('Content-Type: application/json');

    require("./conexion.php");
    $con = returnConection();

    $method = $_SERVER['REQUEST_METHOD'];
    $response = [];

    if ( 'POST' === $method ) {

        $json = file_get_contents('php://input');
        $params = json_decode($json, true);

        /*
        CREATE DEFINER=`root`@`localhost` PROCEDURE `mgsp_SolicitudesCredito`(
            IN `InLineaCredito` INTEGER,
            IN `InSolicitudLinea` INTEGER,
            IN `InConsecutivo` INTEGER,
            IN `InTipoCredito` CHAR(2),
            IN `InPlazo` CHAR(4),
            IN `InDestino` CHAR(250),
            IN `InMontoSolicitado` DECIMAL(15,2),

            OUT `OutErrorClave` CHAR(4),
            OUT `OutErrorProcedure` CHAR(80),
            OUT `OutErrorDescripcion` CHAR(120)
        )
        */

        $query = "CALL mgsp_SolicitudesCredito("
            . " {$params['solicitudLinea']},"
            . " {$params['numeroCliente']},"
            . " '{$params['tipoSolicitud']}',"
            . " '{$params['estatusSolicitud']}',"
            . " '{$params['destinoCredito']}',"
            . " '{$params['origenRecursos']}',"
            . " {$params['montoFrecuenciaDisposicion']},"
            . " '{$params['frecuenciaDisposicion']}',"
            . " {$params['numeroDisposiciones']},"
            . " {$params['montoFrecuenciaPago']},"
            . " '{$params['frecuenciaPago']}',"
            . " {$params['numeroPagos']},"
            . " '{$params['divisa']}',"
            . " {$params['montoLineaCredito']},"
            . " DATE(NOW()),"
            . " @OutSolicitudLinea, "
            . " @OutErrorClave, "
            . " @OutErrorProcedure, "
            . " @OutErrorDescripcion)";
        error_log($query);

        $registro = mysqli_query($con, $query);

        $row = mysqli_query($con,
            "SELECT
                @OutSolicitudLinea as solicitudLinea,
                @OutErrorClave as errorClave,
                @OutErrorProcedure as errorSp,
                @OutErrorDescripcion as errorDescripcion");

        $response = mysqli_fetch_assoc($row);

    }

    mysqli_close($con);

    $ret = json_encode($response, JSON_INVALID_UTF8_IGNORE);
    echo $ret;

?>
