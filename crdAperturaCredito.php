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

        $query = "CALL mgsp_CrdAperturaCredito("
            . " {$params['numeroCredito']},"
            . " {$params['numeroProducto']},"     
            . " {$params['lineaCredito']}," 
            . " {$params['solicitudCredito']}," 
            . " {$params['numeroCliente']},"
            . " {$params['ejecutivo']},"     
            . " {$params['tipoCredito']}," 
            . " {$params['montoProgramado']}," 
            . " {$params['estatusContable']},"
            . " {$params['fuenteFondeo']},"     
            . " {$params['programaFondeo']}," 
            . " {$params['revolvente']},"   
            . " {$params['refinanciamiento']},"
            . " {$params['divisa']},"     
            . " '{$params['fechaApertura']}'," 
            . " '{$params['fechaVencimiento']}'," 
            . " {$params['actividadEconomica']},"
            . " {$params['actividadDetallada']},"     
            . " {$params['statusCredito']}," 
            . " {$params['tipoCalculoInteres']}," 
            . " {$params['tipoRevisionTasa']},"
            . " {$params['tasaNormalReferencia']},"     
            . " {$params['factorTasaNormal']}," 
            . " {$params['puntosAdicionales']},"  
            . " {$params['tasaNormal']}," 
            . " {$params['tasaMoratoriaReferencia']}," 
            . " {$params['factorTasaMoratoria']},"
            . " {$params['sobreTasaMoratoria']},"     
            . " {$params['tasaMoratoria']}," 
            . " {$params['tipoDisposicion']}," 
            . " {$params['periodoPagoCapital']},"
            . " {$params['periodoPagoInteres']},"     
            . " {$params['numeroGraciaCapital']}," 
            . " {$params['numeroGraciaInteres`']}," 
            . " {$params['pagoEspecial']},"     
            . " '{$params['fechaPagoEspecial']}'," 
            . " {$params['montoPagoEspecial`']},"  
            . " @OutNumeroCredito, "
            . " @OutErrorClave, "
            . " @OutErrorProcedure, "
            . " @OutErrorDescripcion)";
        error_log($query);

        $registro = mysqli_query($con, $query);

        $row = mysqli_query($con,
            "SELECT
                @OutNumeroCredito as numeroCredito,
                @OutErrorClave as errorClave,
                @OutErrorProcedure as errorSp,
                @OutErrorDescripcion as errorDescripcion");

        $response = mysqli_fetch_assoc($row);
    
}

    mysqli_close($con);

    $ret = json_encode($response, JSON_INVALID_UTF8_IGNORE);
    echo $ret;

?>