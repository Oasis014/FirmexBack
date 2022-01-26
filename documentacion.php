<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Headers: Origin, X-Requestes-Whit, Content-Type, Accept');
  header('Content-Type: application/json');

  require("./conexion.php");
  $conn = returnConection();

  $method = $_SERVER['REQUEST_METHOD'];

  $response = [
    'data' => [],
    'message' => '',
    'method' => $method,
    'status' => 'error'
  ];

  // OBTENER DOCUMENTOS
  if ( 'GET' == $method && isset($_GET['id']) && !empty($_GET['id']) ) {
    $sql = "SELECT Consecutivo,TipoDocumento, UrlDocumento, FechaAlta
      FROM mg_ctedoctos WHERE NumeroCliente = {$params->id} ORDER BY TipoDocumento ";

    $rows = mysqli_query($conn ,$sql);
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

    // TODO definir estrategia de guardado de archivo (nombre archivo, path)
    $newFileName = $_FILES['file']['name'];
    $fileTmpPath = $_FILES['file']['tmp_name'];
    $uploadFileDir = './documentacion/';
    $data = json_decode($_POST['data']);

    $dest_path = $uploadFileDir . $newFileName;

    if ( move_uploaded_file($fileTmpPath, $dest_path) ) {
      $response['message'] = 'File is successfully uploaded.';
      $registro = mysqli_query($conn ,
        "CALL mgsp_ClientesDocumentos(
          '$data->userId',
          '$data->idDoc',
          '$data->consDoc',
          '$dest_path',
          NOW(),
          @OutErrorClave,
          @OutErrorProcedure,
          @OutErrorDescripcion)"
      );
      $row = mysqli_query(
        $conn,
        "SELECT @OutErrorClave AS errorClave, @OutErrorProcedure AS errorSp, @OutErrorDescripcion AS errorDescripcion"
      );
      while( $reg = mysqli_fetch_assoc($row) ) {
        array_push($response['data'], $reg);
      }
    } else {
      $response['message'] = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
    }

  }

  // ELIMINAR DOCUMENTO
  if ( 'DELETE' === $method && $_POST['id'] ) {
    // TODO falta meter la eliminacion del archivo.
    $registro = mysqli_query($conn ,"CALL mgsp_ClientesDocumentosBorrar('$params->userId','$params->consDocumento',@OutErrorClave,@OutErrorProcedure,@OutErrorDescripcion)");
    $row = mysqli_query($con,"SELECT @OutErrorClave as errorClave,@OutErrorProcedure as errorSp,@OutErrorDescripcion as errorDescripcion");
    while( $reg = mysqli_fetch_assoc($row) ) {
      array_push($response['data'], $reg);
    }
    $response['status'] = 'ok';
  }

  echo json_encode($response);

?>
