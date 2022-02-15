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
    } else if ( 'GET' === $_SERVER['REQUEST_METHOD'] &&
    isset($_GET['userId']) && !empty($_GET['userId']) &&
    isset($_GET['domId']) && !empty($_GET['domId'])
    ) {

        $userId = $_GET['userId'];
        $domId = $_GET['domId'];

        $query = "SELECT "
        . " NumeroCliente as 'numeroCliente', "
        . " TipoDomicilio  'tipoDomicilio', "
        . " Calle as 'calle', "
        . " NumeroExterior as 'numeroExterior', "
        . " NumeroInterior as 'numeroInterior', "
        . " CodigoPostal as 'codigoPostal', "
        . " Colonia as 'colonia', "
        . " Municipio as 'municipio', "
        . " Estado as 'estado', "
        . " Pais as 'pais'  "
        . " FROM mg_ctedom "
        . " WHERE NumeroCliente = {$userId} AND TipoDomicilio = {$domId};";
        $registro = mysqli_query($con, $query);

        while ( $reg = mysqli_fetch_assoc($registro) ) {
            $vec[] = $reg;
        }

        /* OBTIENE TODOS LOS DOMICILIOS DE UN CLIENTE */
    } else if ( 'GET' === $_SERVER['REQUEST_METHOD'] && isset($_GET['userId']) && !empty($_GET['userId'])) {

        $userId = $_GET['userId'];
        // TODO hacer inner para obtener el texto de "tipoDomicilio", colonia, municipio, estaod y pais
        $query = "SELECT "
        . " TipoDomicilio  'tipoDomicilio', "
        . " Calle as 'calle', "
        . " NumeroExterior as 'numeroExterior', "
        . " NumeroInterior as 'numeroInterior', "
        . " CodigoPostal as 'codigoPostal', "
        . " Colonia as 'colonia', "
        . " Municipio as 'municipio', "
        . " Estado as 'estado', "
        . " Pais as 'pais'  "
        . " FROM mg_ctedom WHERE NumeroCliente = {$userId};";
        $registro = mysqli_query($con, $query);

        while ( $reg = mysqli_fetch_assoc($registro) ) {
            $vec[] = $reg;
        }


    /** ELIMINA UN DOMICILIO DE UN CLIENTE :: sustituye : DomicilioBorrar.php */
    } else if ( 'DELETE' === $_SERVER['REQUEST_METHOD'] &&
        isset($_GET['userId']) && !empty($_GET['userId']) &&
        isset($_GET['domId']) && !empty($_GET['domId'])
    ) {

        $json = file_get_contents('php://input');
        $params = json_decode($json);

        $query = "CALL mgsp_ClientesDomiciliosBorrar("
        . " '{$params->Id}', "
        . " '{$params->TipoDom}', "
        . " @OutErrorClave, "
        . " @OutErrorProcedure, "
        . " @OutErrorDescripcion);";

        $registro = mysqli_query($con, $query);
        $row = mysqli_query($con, "SELECT "
            . " @OutErrorClave as errorClave, "
            . " @OutErrorProcedure as errorSp, "
            . " @OutErrorDescripcion as errorDescripcion");

        while ( $reg = mysqli_fetch_assoc($row) ) {
            $vec[] = $reg;
        }

    }

    $data = json_encode($vec, JSON_INVALID_UTF8_IGNORE);
    echo $data;

?>
