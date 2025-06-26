<?php 
session_start();
require_once "../../clases/Conexion.php";
require_once "../../clases/Ventas.php";

$c = new conectar();
$conexion = $c->conexion();
$obj = new ventas();

$idventa = $_POST['idventa'];

// Obtener todos los productos de esta venta
$sql = "SELECT ve.id_producto,
               art.nombre,
               art.descripcion,
               ve.precio,
               ve.id_cliente,
               ve.cantidad
        FROM ventas as ve
        INNER JOIN articulos as art ON ve.id_producto = art.id_producto
        WHERE ve.id_venta = '$idventa'";

$result = mysqli_query($conexion, $sql);

// Limpiar la tabla temporal actual
unset($_SESSION['tablaComprasTemp']);

// Cargar los productos de la venta en la tabla temporal
while($ver = mysqli_fetch_row($result)) {
    $idproducto = $ver[0];
    $nombreproducto = $ver[1];
    $descripcion = $ver[2];
    $precio = $ver[3];
    $idcliente = $ver[4];
    $cantidad = $ver[5];
    
    // Obtener nombre del cliente
    $ncliente = $obj->nombreCliente($idcliente);
    
    $articulo = $idproducto."||".
                $nombreproducto."||".
                $descripcion."||".
                $precio."||".
                $ncliente."||".
                $idcliente."||".
                $cantidad;
    
    $_SESSION['tablaComprasTemp'][] = $articulo;
}

echo 1;
?>