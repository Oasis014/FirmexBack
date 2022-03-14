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
                . " cte.TipoDomicilio,"
                . " cte.Calle,"
                . " cte.NumeroExterior,"
                . " cte.NumeroInterior,"
                . " cte.CodigoPostal,"
                . " cte.Colonia,"
                . " cte.Municipio,"
                . " cte.Estado,"
                . " cte.Pais,"
                . " cdom.desc_45 as 'TipoDomicilioDesc',"
                . " pa.pais_desc as 'PaisDesc',"
                . " sepo.Estado_desc as 'EstadoDesc',"
                . " sepo.Municipio_desc as 'MunicipioDesc'"
            . " FROM mg_ctedom cte"
            . " INNER JOIN mg_catcod cdom"
                . " ON cte.tipodomicilio = cdom.catalogo_cve "
                . " AND cdom.catalogo_id = 'tipdom'"
            . " INNER JOIN mg_paises pa"
                . " ON pa.pais_id = cte.pais"
            . " INNER JOIN mg_sepomex sepo"
                . " ON cte.Estado = sepo.Estado_id"
                . " AND cte.Municipio = sepo.Municipio_id"
                . " AND cte.Colonia = sepo.Asentamiento_desc"
            . " WHERE cte.NumeroCliente = {$userId}";
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
