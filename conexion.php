<?php
function returnConection() {
    $con = mysqli_connect("firmex.mysql.database.azure.com","firmex_admin","qaz123WSX345","firmex");
    if (!$con) {
        echo "Error: No se pudo conectar a MySQL." . PHP_EOL;
        echo "errno de depuración: " . mysqli_connect_errno() . PHP_EOL;
        echo "error de depuración: " . mysqli_connect_error() . PHP_EOL;
        exit;
    }
    return $con;
}