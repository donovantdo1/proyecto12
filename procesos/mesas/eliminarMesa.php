<?php 
require_once "../../clases/Conexion.php";
require_once "../../clases/Mesas.php";

$obj = new mesas();

echo $obj->eliminaMesa($_POST['idmesa']);
?>