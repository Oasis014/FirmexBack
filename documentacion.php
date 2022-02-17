<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
  header('Content-Type: application/json; charset=UTF-8');
  header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');

  require("./conexion.php");
  $conn = returnConection();

  $method = $_SERVER['REQUEST_METHOD'];

  $response = [
    'data' => [],
    'message' => '',
    'status' => 'error'
  ];

  // OBTENER DOCUMENTOS
  if ( 'GET' == $method && isset($_GET['id']) && !empty($_GET['id']) ) {
    $sql = "SELECT
        doc.NumeroCliente AS idCliente,
        doc.TipoDocumento AS tipoDocumento,
        doc.NombreDocumento AS nombreDocumento,
        doc.FechaAlta AS fechaAlta,
        cat.desc_45 AS descCorta
      FROM mg_ctedoctos doc
      LEFT JOIN mg_catcod cat
        ON doc.TipoDocumento = cat.Catalogo_cve
      WHERE doc.NumeroCliente = {$_GET['id']}
        AND cat.Catalogo_id = 'tipdoc'
      ORDER BY TipoDocumento;";

    $rows = mysqli_query($conn, $sql);
    while( $reg = mysqli_fetch_assoc($rows) ) {
        array_push($response['data'], $reg);
    }

    $response['status'] = 'ok';
  }

  // GUARDAR DOCUMENTO NUEVO
  if ( 'POST' === $method &&
      isset($_POST['data']) &&
      isset($_FILES['file']) &&
      isset($_FILES['file']['error'])
  ) {
    $curDate = date("Y-m-d");
    $fileTmpPath = $_FILES['file']['tmp_name'];
    $infoFile = pathinfo($_FILES['file']['name']);
    $data = json_decode($_POST['data']);

    $uploadFileDir = './documentacion/user_'.$data->userId.'/';
    $newFileName = 'user_'.$data->userId.'_doc_'.$data->idDoc.'.'.$infoFile['extension'];
    $dest_path = $uploadFileDir . $newFileName;

    mkdir($uploadFileDir, 0777, true);

    if ( move_uploaded_file($fileTmpPath, $dest_path) ) {
      $response['message'] = 'File is successfully uploaded.';

      $query = "CALL mgsp_ClientesDocumentos("
        . " {$data->userId},"
        . " '{$data->idDoc}',"
        . " '$newFileName',"
        . " '$curDate',"
        . " @OutErrorClave,"
        . " @OutErrorProcedure,"
        . " @OutErrorDescripcion)";
      $registro = mysqli_query($conn, $query);

      $row = mysqli_query(
        $conn,
        "SELECT @OutErrorClave AS errorClave, @OutErrorProcedure AS errorSp, @OutErrorDescripcion AS errorDescripcion"
      );
      while( $reg = mysqli_fetch_assoc($row) ) {
        array_push($response['data'], $reg);
      }
      $response['status'] = 'ok';
    } else {
      $response['message'] = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
    }

  }

  // ELIMINAR DOCUMENTO
  if ( 'DELETE' === $method && 
      isset($_GET['userId']) && !empty($_GET['userId']) &&
      isset($_GET['tipoId']) && !empty($_GET['tipoId']) &&
      isset($_GET['nomDoc']) && !empty($_GET['nomDoc'])
  ) {

    $userId = $_GET['userId'];
    $tipoId = $_GET['tipoId'];
    $nomDoc = $_GET['nomDoc'];

    $unlinkFile = "./documentacion/user_{$userId}/{$nomDoc}";
    unlink($unlinkFile);

    $query = "CALL mgsp_ClientesDocumentosBorrar("
    . " {$userId},"
    . " '{$tipoId}',"
    . " @OutErrorClave,"
    . " @OutErrorProcedure,"
    . " @OutErrorDescripcion)";
    $registro = mysqli_query($conn, $query);

    $row = mysqli_query($con,"SELECT"
      . " @OutErrorClave as errorClave,"
      . " @OutErrorProcedure as errorSp,"
      . " @OutErrorDescripcion as errorDescripcion");

    while( $reg = mysqli_fetch_assoc($row) ) {
      array_push($response['data'], $reg);
    }
    $response['status'] = 'ok';
  }

  echo json_encode($response, JSON_INVALID_UTF8_IGNORE);

?>
