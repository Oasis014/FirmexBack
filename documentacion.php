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
    'method' => $method
  ];

  // OBTENER DOCUMENTOS
  if ( 'GET' == $method && isset($_GET['id']) && !empty($_GET['id']) ) {
    // TODO listar los documentos del usuario
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
    $data = $_POST['data']; // json con la info

    $dest_path = $uploadFileDir . $newFileName;

    if ( move_uploaded_file($fileTmpPath, $dest_path) ) {
      $response['message'] = 'File is successfully uploaded.';
    } else {
      $response['message'] = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
    }

  }

  // ELIMINAR DOCUMENTO
  if ( 'DELETE' === $method && $_POST['id'] ) {
    // TODO falta meter la eliminacion del archivo.
  }

  echo json_encode($response);

?>
