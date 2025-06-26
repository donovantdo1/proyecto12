<?php 
session_start();
require_once "../../clases/Conexion.php";
require_once "../../clases/Mesas.php";

$obj = new mesas();

$datos = array($_POST['nombre_mesa']);

echo $obj->agregaMesa($datos);
?>