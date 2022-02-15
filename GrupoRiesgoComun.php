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

        $query = "CALL mgsp_ClientesDomicilios("
            . " '{$params['Id']}', "
            . " '{$params['TipoDom']}', "
            . " '{$params['Calle']}', "
            . " '{$params['NoEx']}', "
            . " '{$params['NoIn']}', "
            . " '{$params['CodPos']}', "
            . " '{$params['Colonia']}', "
            . " '{$params['Municipio']}', "
            . " '{$params['Estado']}', "
            . " '{$params['Pais']}', "
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


    /* OBTIENE UN DOMICILIO UNICAMENTE DE UN CLIENTE ESPECIFICO :: sustituye : DomiciliosConsulta.php */
    }

    $data = json_encode($vec, JSON_INVALID_UTF8_IGNORE);
    echo $data;

?>
