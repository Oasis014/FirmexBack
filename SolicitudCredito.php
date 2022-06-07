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

        $query = "CALL mgsp_SolicitudesCredito("
            . " {$params['lineaCredito']},"
            . " {$params['solicitudLinea']},"
            . " '{$params['consecutivo']}',"
            . " '{$params['tipoCredito']}',"
            . " '{$params['noCuenta']}',"
            . " {$params['montoSolicitado']},"
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

    } else if ( 'GET' === $method
        && isset($_GET['linea']) && !empty($_GET['linea'])
    ) {
        $line = $_GET['linea'];
        $response['data'] = [];

        $query = "SELECT"
                . " cre.LineaCredito AS 'lineaCredito',"
                . " cre.SolicitudLinea AS 'solicitudLinea',"
                . " cre.Consecutivo AS 'consecutivo',"
                . " cre.TipoCredito AS 'tipoCredito',"
                . " cat.desc_45 AS 'tipoCreditoDesc',"
                . " cre.Destino AS 'destino',"
                . " cre.MontoSolicitado AS 'montoSolicitado'"
            . " FROM mg_solcredit cre"
            . " INNER JOIN mg_catcod cat"
                . " ON cre.TipoCredito = cat.catalogo_cve"
                . " AND cat.catalogo_id = 'tipcre'"
            . " WHERE SolicitudLinea = {$line}";

        error_log($query);
        $registro = mysqli_query($con, $query);
        while ( $reg = mysqli_fetch_assoc($registro) ) {
            array_push($response['data'], $reg);
        }

    } else if ( 'DELETE' === $method
        && isset($_GET['solicitudLinea']) && !empty($_GET['solicitudLinea'])
        && isset($_GET['consecutivo']) && !empty($_GET['consecutivo'])
    ) {
        $line = $_GET['solicitudLinea'];
        $cons = $_GET['consecutivo'];

        $query = "CALL mgsp_SolicitudesCreditoBorrar("
            . " {$line}, "
            . " {$cons}, "
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

    }

    mysqli_close($con);

    $ret = json_encode($response, JSON_INVALID_UTF8_IGNORE);
    echo $ret;

?>
