<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
    header('Content-Type: application/json');

    require("./conexion.php");
    $con = returnConection();

    $json = file_get_contents('php://input');
    $params = json_decode($json, true);
    $method = $_SERVER['REQUEST_METHOD'];
    $response = [];

    if ('GET' === $method && isset($_GET['cliente']) && !empty($_GET['cliente']) ) {
        $cliente = $_GET['cliente'];
        $query = "CALL mgsp_SolicitudesValida("
            . " {$cliente}, "
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
        $response = mysqli_fetch_assoc($row);

        $response['data'] = [];

        // if ( '000' === $response['errorClave'] ) {

            $query = "SELECT"
                    . " cli.NumeroCliente AS 'numeroCliente', "
                    . " suc.Sucursal_id AS 'sucursal', "
                    . " suc.Sucursal_nom AS 'sucursalDesc', "
                    . " prom.Promotor_id AS 'clavePromotor', "
                    . " prom.PromotorNombre AS 'clavePromotorDesc', "
                    . " cli.EstatusCliente AS 'estatusCliente', "
                    . " cli.RFC AS 'rfc', "
                    . " cli.PersonalidadJuridica AS 'personalidadJuridica', "
                    . " if (cli.PersonalidadJuridica = '03', 'moral', 'fisica') AS 'personalidadJuridicaDesc', "
                    . " cli.RazonSocial AS 'razonSocial', "
                    . " CONCAT(cli.PrimerNombre, ' ', cli.SegundoNombre, ' ', cli.ApellidoPaterno, ' ', cli.ApellidoMaterno) AS 'nombreCompleto' "
                . " FROM mg_clientes cli "
                    . " LEFT JOIN mg_promotores prom "
                        . " ON cli.ClavePromotor = prom.Promotor_id "
                    . " LEFT JOIN mg_sucursales suc "
                        . " ON cli.Sucursal = suc.Sucursal_id "
                . " WHERE NumeroCliente = {$cliente}";
            error_log($query);

            $registro = mysqli_query($con, $query);

            array_push($response['data'], mysqli_fetch_assoc($registro));
        // }

    }

    mysqli_close($con);

    $ret = json_encode($response, JSON_INVALID_UTF8_IGNORE);
    echo $ret;

?>
