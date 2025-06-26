<?php 
require_once "../../clases/Conexion.php";
require_once "../../clases/Mesas.php";

$obj = new mesas();

echo json_encode($obj->obtenDatosMesa($_POST['idmesa']));
?>