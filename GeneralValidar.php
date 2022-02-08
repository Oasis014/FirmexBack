<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
    header('Content-Type: application/json');

    if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
        require("./conexion.php");
        $con = returnConection();

        $json = file_get_contents('php://input');
        $params = json_decode($json, true);

        $registro = mysqli_query($con,
            "CALL mgsp_ClientesDatosGeneralesValida(
                '{$params['Sucursal']}',
                '{$params['ApellidoPaterno']}',
                '{$params['ApellidoMaterno']}',
                '{$params['PrimerNombre']}',
                '{$params['SegundoNombre']}',
                '{$params['RazonSocial']}',
                '{$params['PersonalidadJuridica']}',
                '{$params['RFC']}',
                @OutErrorClave,
                @OutErrorProcedure,
                @OutErrorDescripcion)");

        $row = mysqli_query($con,
            "SELECT
                @OutErrorClave as errorClave,
                @OutErrorProcedure as errorSp,
                @OutErrorDescripcion as errorDescripcion");

        $vec = [];

        while ( $reg = mysqli_fetch_assoc($row )){
            $vec[] = $reg;
        }

        $cad = json_encode($vec, JSON_INVALID_UTF8_IGNORE);

        echo $cad;
    }

?>
