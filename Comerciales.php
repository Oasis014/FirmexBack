<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
    header('Content-Type: application/json');

    require("./conexion.php");
    $con = returnConection();
    $data = [];
    $vec = [];

    if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {

        $json = file_get_contents('php://input');
        $params = json_decode($json, true);

        $query = "CALL mgsp_ClientesReferenciasComerciales("
        . " '{$params['Id']}', "
        . " '{$params['Consecutivo']}', "
        . " '{$params['NombreRefCom']}', "
        . " '{$params['LimiteCreditoRefCom']}', "
        . " '{$params['SaldoCuentaRefCom']}', "
        . " @OutErrorClave, "
        . " @OutErrorProcedure, "
        . " @OutErrorDescripcion)";

        error_log($query);

        $registro = mysqli_query($con, $query);
        $row = mysqli_query($con,
            "SELECT @OutErrorClave as errorClave, "
            . " @OutErrorProcedure as errorSp, "
            . " @OutErrorDescripcion as errorDescripcion");

        while( $reg = mysqli_fetch_assoc($row) ) {
            $vec[] = $reg;
        }

    } else if ( 'GET' === $_SERVER['REQUEST_METHOD'] &&
      isset($_GET['userId']) && !empty($_GET['userId']) ) {

        $userId = $_GET['userId'];

        $query = "CALL mgsp_ClientesReferenciasComercialesConsulta("
        . " '{$params['Id']}', "
        . " '1', " /* '{$params['Consecutivo']}' */
        . " @OutNombreRefCom, "
        . " @OutLimiteCreditoRefCom, "
        . " @OutSaldoCuentaRefCom, "
        . " @OutErrorClave, "
        . " @OutErrorProcedure, "
        . " @OutErrorDescripcion)";

        $registro = mysqli_query($con, $query);
        $row = mysqli_query($con, "SELECT"
            . " @OutNombreRefCom as NombreRefCom, "
            . " @OutLimiteCreditoRefCom as LimiteCreditoRefCom, "
            . " @OutSaldoCuentaRefCom as SaldoCuentaRefCom, "
            . " @OutErrorClave as errorClave, "
            . " @OutErrorProcedure as errorSp, "
            . " @OutErrorDescripcion as errorDescripcion;");

        while( $reg = mysqli_fetch_assoc($row) ) {
            $vec[] = $reg;
        }

    }

    $data = json_encode($vec, JSON_INVALID_UTF8_IGNORE);
    echo $data;

?>