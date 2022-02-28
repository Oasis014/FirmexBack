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

    if ( 'GET' === $method && isset($_GET['userId']) && !empty($_GET['userId']) ) {
        $userId = $_GET['userId'];

        $query = "SELECT"
                . " cli.NumeroCliente,"
                . " cli.Sucursal,"
                . " cli.ApellidoPaterno,"
                . " cli.ApellidoMaterno,"
                . " cli.PrimerNombre,"
                . " cli.SegundoNombre,"
                . " cli.RazonSocial,"
                . " cli.ClavePromotor,"
                . " cli.EstatusCliente,"
                . " cli.FechaAlta,"
                . " cli.PersonalidadJuridica,"
                . " cli.RFC,"
                . " cli.Nacionalidad,"
                . " cli.EmailPersonal,"
                . " cli.EmailEmpresa,"
                . " cli.TelefonoDomicilio,"
                . " cli.ExtensionDomicilio,"
                . " cli.TelefonoOficina,"
                . " cli.ExtensionOficina,"
                . " cli.Celular,"
                . " cli.RedSocial1,"
                . " cli.RedSocial2,"
                . " cli.ParteRelacionada,"
                . " cli.GrupoConsejo,"
                . " cli.GrupoRiesgoComun,"
                . " mor.NumeroCliente,"
                . " mor.NombreSociedad,"
                . " mor.FechaConstitucion,"
                . " mor.RepresentanteLegal,"
                . " mor.PresidenteConsejo,"
                . " mor.Consejero,"
                . " mor.Secretario"
            . " FROM mg_clientes cli"
                . " INNER JOIN mg_ctepm mor"
                    . " ON cli.NumeroCliente = mor.NumeroCliente"
            . " WHERE cli.NumeroCliente = {$userId};";

        $registro = mysqli_query($con, $query);

        while ( $reg = mysqli_fetch_assoc($registro) ) {
            $vec[] = $reg;
        }

    } else if ( 'GET' === $method ) {

        $query = "SELECT"
            . " NumeroCLiente,"
            . " NombreSociedad,"
            . " FechaConstitucion,"
            . " RepresentanteLegal,"
            . " PresidenteConsejo,"
            . " Consejero,"
            . " Secretario"
            . " FROM mg_ctepm;";

        $registro = mysqli_query($con, $query);

        while ( $reg = mysqli_fetch_assoc($registro) ) {
            $vec[] = $reg;
        }

    }

    $data = json_encode($vec, JSON_INVALID_UTF8_IGNORE);
    echo $data;

?>





