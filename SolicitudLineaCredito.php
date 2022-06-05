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

        $query = "CALL mgsp_SolicitudesLineasCredito("
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
        /*. " '{$params['fechaAlta']}'," */
        $registro = mysqli_query($con, $query);

        $row = mysqli_query($con,
            "SELECT
                @OutSolicitudLinea as solicitudLinea,
                @OutErrorClave as errorClave,
                @OutErrorProcedure as errorSp,
                @OutErrorDescripcion as errorDescripcion");

        $response = mysqli_fetch_assoc($row);

    } if ('GET' === $method && isset($_GET['cliente']) && isset($_GET['linea'])
        && !empty($_GET['cliente']) && !empty($_GET['linea'])
    ) {

        $cliente = $_GET['cliente'];
        $linea = $_GET['linea'];
        $response['data'] = [];

        $query = "SELECT "
            . "LineaCredito as 'lineaCredito', "
            . "SolicitudLinea as 'solicitudLinea', "
            . "NumeroCliente as 'numeroCliente', "
            . "TipoSolicitud as 'tipoSolicitud', "
            . "EstatusSolicitud as 'estatusSolicitud', "
            . "DestinoCredito as 'destinoCredito', "
            . "OrigenRecursos as 'origenRecursos', "
            . "MontoFrecuenciaDisposicion as 'montoFrecuenciaDisposicion', "
            . "FrecuenciaDisposicion as 'frecuenciaDisposicion', "
            . "NumeroDisposiciones as 'numeroDisposiciones', "
            . "MontoFrecuenciaPago as 'montoFrecuenciaPago', "
            . "FrecuenciaPago as 'frecuenciaPago', "
            . "NumeroPagos as 'numeroPagos', "
            . "Divisa as 'divisa', "
            . "MontoLineaCredito as 'montoLineaCredito', "
            . "FechaAlta as 'fechaAlta' "
        . "FROM mg_sollincre "
        . "WHERE NumeroCliente = {$cliente} "
            . "AND SolicitudLinea = {$linea}";

        error_log($query);
        $registro = mysqli_query($con, $query);
        array_push($response['data'], mysqli_fetch_assoc($registro));
    }   else if ( 'GET' === $method ) {
        $query = "SELECT"
            . " LineaCredito as 'lineaCredito', "
            . " SolicitudLinea as 'solicitudLinea', "
            . " NumeroCliente as 'numeroCliente', "
            . " TipoSolicitud as 'tipoSolicitud', "
            . " EstatusSolicitud as 'estatusSolicitud', "
            . " DestinoCredito as 'destinoCredito', "
            . " OrigenRecursos as 'origenRecursos', "
            . " MontoFrecuenciaDisposicion as 'montoFrecuenciaDisposicion', "
            . " FrecuenciaDisposicion as 'frecuenciaDisposicion', "
            . " NumeroDisposiciones as 'numeroDisposiciones', "
            . " MontoFrecuenciaPago as 'montoFrecuenciaPago', "
            . " FrecuenciaPago as 'frecuenciaPago', "
            . " NumeroPagos as 'numeroPagos', "
            . " Divisa as 'divisa', "
            . " MontoLineaCredito as 'montoLineaCredito', "
            . " FechaAlta as 'fechaAlta' "
            . " FROM mg_sollincre";
        error_log($query);
        $registro = mysqli_query($con, $query);
        array_push($response, mysqli_fetch_assoc($registro));

    }

    mysqli_close($con);

    $ret = json_encode($response, JSON_INVALID_UTF8_IGNORE);
    echo $ret;

?>
