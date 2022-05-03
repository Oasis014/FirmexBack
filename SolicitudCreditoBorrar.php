<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
    header('Content-Type: application/json');

    
        require("./conexion.php");
        $con = returnConection();

        $json = file_get_contents('php://input');
        $params = json_decode($json, true);

      

        $query = "CALL mgsp_SolicitudesCreditoBorrar("
            . " {$params['SolicitudLinea']}, "
            . " {$params['Consecutivo']}, "
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
        if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
  
    }

    /*
    PRUEBA CRUDO            
            $query = "CALL mgsp_SolicitudesCreditoBorrar("
            . " '01', "
            . " '1', "
            . " @OutErrorClave, "
            . " @OutErrorProcedure, "
            . " @OutErrorDescripcion)";

    */

?>
