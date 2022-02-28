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
                . " fis.NumeroCliente,"
                . " fis.FechaNacimiento,"
                . " fis.Sexo,"
                . " fis.EstadoCivil,"
                . " fis.CURP,"
                . " fis.TipoIdentificacion,"
                . " fis.NumeroIdentificacion,"
                . " fis.ListaNegra,"
                . " fis.Profesion"
            . " FROM mg_clientes cli"
                . " INNER JOIN mg_ctepf fis"
                    . " ON cli.NumeroCliente = fis.NumeroCliente"
            . " WHERE cli.NumeroCliente = {$userId};";

        $registro = mysqli_query($con, $query);

        while ( $reg = mysqli_fetch_assoc($registro) ) {
            $vec[] = $reg;
        }

    } else if ( 'GET' === $method ) {

        $query = "SELECT"
            . " NumeroCLiente,"
            . " FechaNacimiento,"
            . " Sexo,"
            . " EstadoCivil,"
            . " CURP,"
            . " TipoIdentificacion,"
            . " NumeroIdentificacion,"
            . " ListaNegra,"
            . " Profesion"
            . " FROM mg_ctepf;";

        $registro = mysqli_query($con, $query);

        while ( $reg = mysqli_fetch_assoc($registro) ) {
            $vec[] = $reg;
        }

    }

    $data = json_encode($vec, JSON_INVALID_UTF8_IGNORE);
    echo $data;

?>





