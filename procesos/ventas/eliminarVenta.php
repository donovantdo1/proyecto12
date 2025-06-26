<?php 
require_once "../../clases/Conexion.php";
require_once "../../clases/Ventas.php";

$c = new conectar();
$conexion = $c->conexion();

$idventa = $_POST['idventa'];

// Primero obtener los productos y cantidades para restaurar el inventario
$sql = "SELECT id_producto, cantidad FROM ventas WHERE id_venta = '$idventa'";
$result = mysqli_query($conexion, $sql);

// Restaurar inventario
while($ver = mysqli_fetch_row($result)) {
    $idproducto = $ver[0];
    $cantidad = $ver[1];
    
    // Devolver la cantidad al inventario
    $sqlUpdate = "UPDATE articulos SET cantidad = cantidad + '$cantidad' WHERE id_producto = '$idproducto'";
    mysqli_query($conexion, $sqlUpdate);
}

// Eliminar la venta
$sqlDelete = "DELETE FROM ventas WHERE id_venta = '$idventa'";
$resultDelete = mysqli_query($conexion, $sqlDelete);

if($resultDelete) {
    echo 1;
} else {
    echo 0;
}
?>