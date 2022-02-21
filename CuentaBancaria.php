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

        $query = "CALL mgsp_ClientesCuentasBancarias("
          . " {$params['NumeroCliente']}, "
          . " 0, "
          . " '{$params['NombreCuentaBancariaCtaBan']}', "
          . " '{$params['BancoCtaBan']}', "
          . " '{$params['NumeroCuentaCtaBan']}', "
          . " '{$params['ClaveInterbancariaCtaBan']}', "
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

    } else if ( 'GET' === $method &&
        isset($_GET['userId']) && !empty($_GET['userId'])
    ) {
        $userId = $_GET['userId'];

        $query = "SELECT"
            . " NumeroCliente,"
            . " Consecutivo,"
            . " NombreCuentaBancariaCtaBan,"
            . " BancoCtaBan,"
            . " NumeroCuentaCtaBan,"
            . " ClaveInterbancariaCtaBan"
        . " FROM mg_ctectaban"
        . " WHERE NumeroCliente = '{$userId}'";

        $registro = mysqli_query($con, $query);

        while ( $reg = mysqli_fetch_assoc($registro) ) {
            $vec[] = $reg;
        }
    } else if ( 'PUT' === $method ) {

        $json = file_get_contents('php://input');
        $params = json_decode($json, true);

        $query = "CALL mgsp_ClientesCuentasBancarias("
          . " {$params['NumeroCliente']}, "
          . " {$params['Consecutivo']}, "
          . " '{$params['NombreCuentaBancariaCtaBan']}', "
          . " '{$params['BancoCtaBan']}', "
          . " '{$params['NumeroCuentaCtaBan']}', "
          . " '{$params['ClaveInterbancariaCtaBan']}', "
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

    } else if ( 'DELETE' === $method
        && isset($_GET['NumeroCliente']) && !empty($_GET['NumeroCliente'])
        && isset($_GET['Consecutivo'])
    ) {

        $id = $_GET['NumeroCliente'];
        $cons = $_GET['Consecutivo'];

        $query = "CALL mgsp_ClientesCuentasBancariasBorrar("
        . " {$id}, "
        . " {$cons}, "
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

    }

    // TODO opcion para elmimar un registro, usando metodo "DELETE" y IDs necesarios
    // TODO opcion para ACTUALIZAR un registro. o se usa el mismo de GUARDAR??

    $data = json_encode($vec, JSON_INVALID_UTF8_IGNORE);
    echo $data;

?>