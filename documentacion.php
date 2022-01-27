<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
  header('Content-Type: application/json; charset=UTF-8');

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
        Consecutivo AS consecutivo,
        TipoDocumento AS tipoDocumento,
        UrlDocumento AS urlDocumento,
        FechaAlta AS fechaAlta
      FROM mg_ctedoctos
      WHERE NumeroCliente = {$_GET['id']}
      ORDER BY TipoDocumento";

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

    mkdir($uploadFileDir, 0777, true );

    if ( move_uploaded_file($fileTmpPath, $dest_path) ) {
      $response['message'] = 'File is successfully uploaded.';

      $registro = mysqli_query($conn,
        "CALL mgsp_ClientesDocumentos(
          '$data->userId',
          '$data->consDoc',
          '$data->idDoc',
          '$dest_path',
          '$curDate',
          @OutErrorClave,
          @OutErrorProcedure,
          @OutErrorDescripcion)"
      );

      $row = mysqli_query(
        $conn,
        "SELECT @OutErrorClave AS errorClave, @OutErrorProcedure AS errorSp, @OutErrorDescripcion AS errorDescripcion"
      );
      while( $reg = mysqli_fetch_assoc($row) ) {
        $response['data'] = $reg;
      }
    } else {
      $response['message'] = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
    }

  }

  // ELIMINAR DOCUMENTO
  if ( 'DELETE' === $method && $_GET['userId'] && $_GET['docId'] ) {
    $userId = $_GET['userId'];
    $docId = $_GET['docId'];
    $unlinkFile = "./documentacion/user_{$userId}/user_{$userId}_doc_{$docId}";

    unlink($unlinkFile);

    $registro = mysqli_query($conn ,"CALL mgsp_ClientesDocumentosBorrar('$params->userId','$params->consDocumento',@OutErrorClave,@OutErrorProcedure,@OutErrorDescripcion)");
    $row = mysqli_query($con,"SELECT @OutErrorClave as errorClave,@OutErrorProcedure as errorSp,@OutErrorDescripcion as errorDescripcion");
    while( $reg = mysqli_fetch_assoc($row) ) {
      array_push($response['data'], $reg);
    }
    $response['status'] = 'ok';
  }

  echo json_encode($response, JSON_INVALID_UTF8_IGNORE);

?>
