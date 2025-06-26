<?php 
require_once "../../clases/Conexion.php";
require_once "../../clases/Articulos.php";

$obj = new articulos();

$idproducto = $_POST['idproducto'];
$cantidad = $_POST['cantidad'];
$operacion = $_POST['operacion']; // 'aumentar' o 'disminuir'

// Validar que la cantidad sea positiva
if($cantidad <= 0){
    echo 0;
    exit;
}

// Si es disminuir, verificar que no quede en negativo
if($operacion == 'disminuir'){
    $datosActuales = $obj->obtenDatosArticulo($idproducto);
    $cantidadActual = $datosActuales['cantidad'];
    
    if($cantidad > $cantidadActual){
        echo -1; // No se puede disminuir más de lo disponible
        exit;
    }
}

echo $obj->ajustarCantidad($idproducto, $cantidad, $operacion);
?>