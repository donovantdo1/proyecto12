<?php 
// ARCHIVO PARA DEBUGGEAR - Crea este archivo temporal
session_start();
require_once "../clases/Conexion.php";
$c = new conectar();
$conexion = $c->conexion();

echo "<h2>🔍 DIAGNÓSTICO DEL DASHBOARD</h2>";

// 1. Verificar si hay ventas en la base de datos
echo "<h3>1. ¿Hay ventas en la base de datos?</h3>";
$sqlTodasVentas = "SELECT COUNT(*) as total FROM ventas";
$result = mysqli_query($conexion, $sqlTodasVentas);
$totalVentas = mysqli_fetch_assoc($result)['total'];
echo "Total de ventas en la base de datos: <strong>$totalVentas</strong><br>";

// 2. Verificar estructura de la tabla ventas
echo "<h3>2. Estructura de la tabla 'ventas':</h3>";
$sqlEstructura = "DESCRIBE ventas";
$result = mysqli_query($conexion, $sqlEstructura);
echo "<table border='1'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['Field']}</td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>{$row['Default']}</td>";
    echo "</tr>";
}
echo "</table>";

// 3. Ver algunas ventas de ejemplo
echo "<h3>3. Últimas 5 ventas registradas:</h3>";
$sqlEjemplos = "SELECT * FROM ventas ORDER BY id_venta DESC LIMIT 5";
$result = mysqli_query($conexion, $sqlEjemplos);
if(mysqli_num_rows($result) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Fecha</th><th>Precio</th><th>Cliente</th><th>Producto</th></tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$row['id_venta']}</td>";
        echo "<td>" . (isset($row['fechaCompra']) ? $row['fechaCompra'] : 'N/A') . "</td>";
        echo "<td>" . (isset($row['precio']) ? $row['precio'] : 'N/A') . "</td>";
        echo "<td>" . (isset($row['id_cliente']) ? $row['id_cliente'] : 'N/A') . "</td>";
        echo "<td>" . (isset($row['id_producto']) ? $row['id_producto'] : 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No hay ventas registradas</p>";
}

// 4. Verificar fecha de hoy
echo "<h3>4. Fecha actual del servidor:</h3>";
echo "Fecha PHP: " . date('Y-m-d') . "<br>";
echo "Fecha MySQL: ";
$sqlFecha = "SELECT CURDATE() as fecha_actual";
$result = mysqli_query($conexion, $sqlFecha);
$fechaMySQL = mysqli_fetch_assoc($result)['fecha_actual'];
echo $fechaMySQL . "<br>";

// 5. Probar consulta de ventas de hoy
echo "<h3>5. Consulta de ventas de hoy:</h3>";
$sqlVentasHoy = "SELECT COUNT(*) as total FROM ventas WHERE DATE(fechaCompra) = CURDATE()";
echo "Consulta: <code>$sqlVentasHoy</code><br>";
$result = mysqli_query($conexion, $sqlVentasHoy);
if($result) {
    $ventasHoy = mysqli_fetch_assoc($result)['total'];
    echo "Resultado: <strong>$ventasHoy</strong> ventas hoy<br>";
} else {
    echo "❌ Error en la consulta: " . mysqli_error($conexion) . "<br>";
}
?>