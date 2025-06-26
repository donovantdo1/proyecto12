<?php 
require_once "../../clases/Conexion.php";
require_once "../../clases/Ventas.php";

$obj = new ventas();

$idventa = $_POST['idventa'];

if($obj->marcarComoPagado($idventa)){
    echo 1;
} else {
    echo 0;
}
?>