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

        $query = "CALL mgsp_ClientesGruposSocioeconomicos("
            . " '{$params['NumeroCliente']}', "
            . " '{$params['Consecutivo']}', "
            . " '{$params['GrupoSocioeconomicoGpoSoc']}', "
            . " '{$params['NombreGpoSoc']}', "
            . " '{$params['RFCGpoSoc']}', "
            . " '{$params['DireccionGpoSoc']}', "
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
        isset($_GET['userId']) && !empty($_GET['userId'])
    ) {
        $userId = $_GET['userId'];
        // TODO join para obtener los nombres , en lugar del id 
        $query = "SELECT"
            . " Consecutivo,"
            . " GrupoSocioeconomicoGpoSoc,"
            . " NombreGpoSoc,"
            . " RFCGpoSoc,"
            . " DireccionGpoSoc"
        . " FROM mg_ctegposoc"
        . " WHERE NumeroCliente = '{$userId}'";

        $registro = mysqli_query($con, $query);

        while ( $reg = mysqli_fetch_assoc($registro) ) {
            $vec[] = $reg;
        }
    } 
    else if ( 'DELETE' === $_SERVER['REQUEST_METHOD'] &&
    isset($_GET['userId']) && !empty($_GET['userId'])) {
        // TODO opcion para elmimar un registro, usando metodo "DELETE"
        $json = file_get_contents('php://input');
        $params = json_decode($json, true);

        $query = "CALL mgsp_ClientesGruposSocioeconomicosBorrar("
            . " '{$params['NumeroCliente']}', "
            . " '{$params['Consecutivo']}', "      
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

    
    // TODO opcion para ACTUALIZAR un registro. o se usa el mismo de GUARDAR??

    $data = json_encode($vec, JSON_INVALID_UTF8_IGNORE);
    echo $data;

?>
