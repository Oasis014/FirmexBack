<?php
function returnConection(){
    $con = mysqli_connect("127.0.0.1","root","","firmex2");
    return $con;
}