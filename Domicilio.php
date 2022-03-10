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

        $query = "CALL mgsp_ClientesDomicilios("
            . " '{$params['NumeroCliente']}', "
            . " '{$params['TipoDomicilio']}', "
            . " '{$params['Calle']}', "
            . " '{$params['NumeroExterior']}', "
            . " '{$params['NumeroInterior']}', "
            . " '{$params['CodigoPostal']}', "
            . " '{$params['Colonia']}', "
            . " '{$params['Municipio']}', "
            . " '{$params['Estado']}', "
            . " '{$params['Pais']}', "
            . " @OutErrorClave, "
            . " @OutErrorProcedure, "
            . " @OutErrorDescripcion)";

        $registro = mysqli_query($con, $query);
        $row = mysqli_query($con,
            "SELECT @OutErrorClave as errorClave, "
            . " @OutErrorProcedure as errorSp, "
            . " @OutErrorDescripcion as errorDescripcion");

        while( $reg = mysqli_fetch_assoc($row) ) {
            $vec[] = $reg;
        }


    /* OBTIENE UN DOMICILIO UNICAMENTE DE UN CLIENTE ESPECIFICO :: sustituye : DomiciliosConsulta.php */
    } else if ( 'GET' === $method &&
        isset($_GET['userId']) && !empty($_GET['userId']) &&
        isset($_GET['domId']) && !empty($_GET['domId'])
    ) {

        $userId = $_GET['userId'];
        $domId = $_GET['domId'];

        $query = "SELECT "
        . " NumeroCliente , "
        . " TipoDomicilio , "
        . " Calle , "
        . " NumeroExterior , "
        . " NumeroInterior , "
        . " CodigoPostal , "
        . " Colonia , "
        . " Municipio , "
        . " Estado , "
        . " Pais   "
        . " FROM mg_ctedom "
        . " WHERE NumeroCliente = {$userId} AND TipoDomicilio = {$domId};";
        $registro = mysqli_query($con, $query);

        while ( $reg = mysqli_fetch_assoc($registro) ) {
            $vec[] = $reg;
        }

        /* OBTIENE TODOS LOS DOMICILIOS DE UN CLIENTE */
    } else if ( 'GET' === $method && isset($_GET['userId']) && !empty($_GET['userId'])) {

        $userId = $_GET['userId'];       
        $query = "SELECT" 
                    ."ctedom.TipoDomicilio,"
                    ."cdom.desc_45 as desc_tipodomicilio,"
                    ."ctedom.NumeroExterior,"
                    ."ctedom.NumeroInterior,"
                    ."ctedom.CodigoPostal," 
                    ."ctedom.Colonia,"
                    ."ctedom.Municipio,"
                    ."cEdo.municipio_desc,"
                    ."ctedom.Estado,"
                    ."cEdo.estado_desc,"      
                    ."ctedom.Pais,"
                    ."pa.pais_desc"
                ."FROM mg_ctedom ctedom"
                ."INNER JOIN mg_paises pa on pa.pais_id=ctedom.pais"
                ."INNER JOIN mg_catcod cdom on ctedom.tipodomicilio=cdom.catalogo_cve and cdom.catalogo_id='tipdom'"
                ."INNER JOIN  mg_sepomex cEdo on cEdo.estado_id=ctedom.estado and cEdo.municipio_id=ctedom.municipio"
                ."and cEdo.codigopostal_id=ctedom.codigopostal and ctedom.colonia=cEdo.asentamiento_desc"      
                ."WHERE NumeroCliente = {$userId};";
        $registro = mysqli_query($con, $query);

        while ( $reg = mysqli_fetch_assoc($registro) ) {
            $vec[] = $reg;
        }


    /** ELIMINA UN DOMICILIO DE UN CLIENTE :: sustituye : DomicilioBorrar.php */
    } else if ( 'DELETE' === $method &&
        isset($_GET['userId']) && !empty($_GET['userId']) &&
        isset($_GET['domId']) && !empty($_GET['domId'])
    ) {

        $userId = $_GET['userId'];
        $domId = $_GET['domId'];

        $query = "CALL mgsp_ClientesDomiciliosBorrar("
        . " '{$userId}', "
        . " '{$domId}', "
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
