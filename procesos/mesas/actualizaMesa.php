<?php 
session_start();
require_once "../../clases/Conexion.php";
require_once "../../clases/Mesas.php";

$obj = new mesas();

$ocupada = isset($_POST['ocupadaU']) ? 1 : 0;

$datos = array(
    $_POST['idmesaU'],
    $_POST['nombre_mesaU'],
    $ocupada
);

echo $obj->actualizaMesa($datos);
?>