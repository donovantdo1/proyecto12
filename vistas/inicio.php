<?php 
	session_start();
	if(isset($_SESSION['usuario'])){
		require_once "../clases/Conexion.php";
		$c = new conectar();
		$conexion = $c->conexion();
 ?>

<!DOCTYPE html>
<html>
<head>
	<title>Dashboard - Ventas y Almacén</title>
	<?php require_once "menu.php"; ?>
	<style>
		.dashboard-card {
			background: white;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
			padding: 20px;
			margin-bottom: 20px;
		}
		.stat-card {
			text-align: center;
			padding: 30px 20px;
		}
		.stat-number {
			font-size: 2.5em;
			font-weight: bold;
			color: #2c3e50;
		}
		.stat-label {
			color: #7f8c8d;
			font-size: 0.9em;
			text-transform: uppercase;
		}
		.alert-low-stock {
			background-color: #f8d7da;
			border: 1px solid #f5c6cb;
			color: #721c24;
		}
		.recent-sale {
			border-bottom: 1px solid #eee;
			padding: 10px 0;
		}
		.recent-sale:last-child {
			border-bottom: none;
		}
	</style>
</head>
<body>
	<div class="container" style="margin-top: 100px;">
		<div class="row">
			<div class="col-md-12">
				<h1><span class="glyphicon glyphicon-dashboard"></span> Dashboard del Negocio</h1>
				<p class="text-muted">Resumen general de tu negocio - <?php echo date('d/m/Y'); ?></p>
			</div>
		</div>

		<!-- ESTADÍSTICAS PRINCIPALES -->
		<div class="row">
			<div class="col-md-3">
				<div class="dashboard-card stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
					<?php
					// CORREGIDO: Usar la fecha de PHP en lugar de CURDATE()
					$fechaHoy = date('Y-m-d');
					$sqlVentasHoy = "SELECT COUNT(*) as total FROM ventas WHERE DATE(fechaCompra) = '$fechaHoy'";
					$resultVentasHoy = mysqli_query($conexion, $sqlVentasHoy);
					$ventasHoy = mysqli_fetch_assoc($resultVentasHoy)['total'];
					?>
					<div class="stat-number"><?php echo $ventasHoy; ?></div>
					<div class="stat-label">Ventas Hoy</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="dashboard-card stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
					<?php
					// CORREGIDO: Usar la fecha de PHP
					$sqlIngresoHoy = "SELECT SUM(precio) as total FROM ventas WHERE DATE(fechaCompra) = '$fechaHoy'";
					$resultIngresoHoy = mysqli_query($conexion, $sqlIngresoHoy);
					$ingresoHoy = mysqli_fetch_assoc($resultIngresoHoy)['total'] ?? 0;
					?>
					<div class="stat-number">$<?php echo number_format($ingresoHoy, 2); ?></div>
					<div class="stat-label">Ingresos Hoy</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="dashboard-card stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
					<?php
					$sqlTotalProductos = "SELECT COUNT(*) as total FROM articulos";
					$resultTotalProductos = mysqli_query($conexion, $sqlTotalProductos);
					$totalProductos = mysqli_fetch_assoc($resultTotalProductos)['total'];
					?>
					<div class="stat-number"><?php echo $totalProductos; ?></div>
					<div class="stat-label">Productos</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="dashboard-card stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
					<?php
					$sqlTotalClientes = "SELECT COUNT(*) as total FROM clientes";
					$resultTotalClientes = mysqli_query($conexion, $sqlTotalClientes);
					$totalClientes = mysqli_fetch_assoc($resultTotalClientes)['total'];
					?>
					<div class="stat-number"><?php echo $totalClientes; ?></div>
					<div class="stat-label">Clientes</div>
				</div>
			</div>
		</div>

		<!-- SEGUNDA FILA DE ESTADÍSTICAS -->
		<div class="row">
			<div class="col-md-6">
				<div class="dashboard-card">
					<h4><span class="glyphicon glyphicon-calendar"></span> Ventas de los Últimos 7 Días</h4>
					<?php
					// CORREGIDO: Usar fechas relativas desde PHP
					$sqlVentasSemana = "SELECT DATE(fechaCompra) as fecha, COUNT(*) as ventas, SUM(precio) as ingresos 
					                   FROM ventas 
					                   WHERE fechaCompra >= DATE_SUB('$fechaHoy', INTERVAL 7 DAY) 
					                   GROUP BY DATE(fechaCompra) 
					                   ORDER BY fecha DESC";
					$resultVentasSemana = mysqli_query($conexion, $sqlVentasSemana);
					?>
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Fecha</th>
								<th>Ventas</th>
								<th>Ingresos</th>
							</tr>
						</thead>
						<tbody>
							<?php while($row = mysqli_fetch_assoc($resultVentasSemana)): ?>
							<tr>
								<td><?php echo date('d/m/Y', strtotime($row['fecha'])); ?></td>
								<td><span class="badge"><?php echo $row['ventas']; ?></span></td>
								<td><strong>$<?php echo number_format($row['ingresos'], 2); ?></strong></td>
							</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-6">
				<div class="dashboard-card">
					<h4><span class="glyphicon glyphicon-star"></span> Productos Más Vendidos</h4>
					<?php
					$sqlTopProductos = "SELECT a.nombre, COUNT(v.id_producto) as vendidos, SUM(v.cantidad) as cantidad_total
					                   FROM ventas v 
					                   INNER JOIN articulos a ON v.id_producto = a.id_producto 
					                   GROUP BY v.id_producto 
					                   ORDER BY vendidos DESC 
					                   LIMIT 5";
					$resultTopProductos = mysqli_query($conexion, $sqlTopProductos);
					?>
					<table class="table">
						<thead>
							<tr>
								<th>Producto</th>
								<th>Veces Vendido</th>
								<th>Cantidad Total</th>
							</tr>
						</thead>
						<tbody>
							<?php while($row = mysqli_fetch_assoc($resultTopProductos)): ?>
							<tr>
								<td><?php echo $row['nombre']; ?></td>
								<td><span class="badge badge-success"><?php echo $row['vendidos']; ?></span></td>
								<td><?php echo $row['cantidad_total']; ?></td>
							</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- ALERTAS Y ACCIONES RÁPIDAS -->
		<div class="row">
			<div class="col-md-6">
				<div class="dashboard-card">
					<h4><span class="glyphicon glyphicon-warning-sign text-danger"></span> Productos con Poco Stock</h4>
					<?php
					$sqlBajoStock = "SELECT nombre, cantidad FROM articulos WHERE cantidad <= 5 ORDER BY cantidad ASC";
					$resultBajoStock = mysqli_query($conexion, $sqlBajoStock);
					?>
					<?php if(mysqli_num_rows($resultBajoStock) > 0): ?>
						<?php while($row = mysqli_fetch_assoc($resultBajoStock)): ?>
						<div class="alert alert-low-stock">
							<strong><?php echo $row['nombre']; ?></strong> - Solo quedan <?php echo $row['cantidad']; ?> unidades
						</div>
						<?php endwhile; ?>
					<?php else: ?>
						<div class="alert alert-success">
							<span class="glyphicon glyphicon-ok"></span> Todos los productos tienen stock suficiente
						</div>
					<?php endif; ?>
				</div>
			</div>
			<!-- SECCIÓN DE VENTAS RECIENTES CORREGIDA -->
<div class="col-md-6">
    <div class="dashboard-card">
        <h4><span class="glyphicon glyphicon-time"></span> Ventas Recientes</h4>
        <?php
        // CORREGIDO: Agrupar por id_venta y sumar los precios
        $sqlVentasRecientes = "SELECT 
                                v.id_venta, 
                                v.fechaCompra, 
                                SUM(v.precio) as precio_total,
                                c.nombre, 
                                c.apellido 
                              FROM ventas v 
                              LEFT JOIN clientes c ON v.id_cliente = c.id_cliente 
                              GROUP BY v.id_venta, v.fechaCompra, c.nombre, c.apellido
                              ORDER BY v.fechaCompra DESC 
                              LIMIT 5";
        $resultVentasRecientes = mysqli_query($conexion, $sqlVentasRecientes);
        ?>
        <?php while($row = mysqli_fetch_assoc($resultVentasRecientes)): ?>
        <div class="recent-sale">
            <div class="row">
                <div class="col-md-6">
                    <strong>Venta #<?php echo $row['id_venta']; ?></strong><br>
                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($row['fechaCompra'])); ?></small>
                </div>
                <div class="col-md-3">
                    <?php 
                    $cliente = $row['nombre'] ? $row['nombre'].' '.$row['apellido'] : 'Sin cliente';
                    echo $cliente;
                    ?>
                </div>
                <div class="col-md-3 text-right">
                    <strong>$<?php echo number_format($row['precio_total'], 2); ?></strong>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
		</div>

		<!-- ACCIONES RÁPIDAS -->
		<div class="row">
			<div class="col-md-12">
				<div class="dashboard-card">
					<h4><span class="glyphicon glyphicon-flash"></span> Acciones Rápidas</h4>
					<div class="row">
						<div class="col-md-3">
							<a href="ventas.php" class="btn btn-primary btn-lg btn-block">
								<span class="glyphicon glyphicon-plus"></span><br>
								Nueva Venta
							</a>
						</div>
						<div class="col-md-3">
							<a href="articulos.php" class="btn btn-success btn-lg btn-block">
								<span class="glyphicon glyphicon-package"></span><br>
								Agregar Producto
							</a>
						</div>
						<div class="col-md-3">
							<a href="clientes.php" class="btn btn-info btn-lg btn-block">
								<span class="glyphicon glyphicon-user"></span><br>
								Nuevo Cliente
							</a>
						</div>
						<div class="col-md-3">
							<a href="ventas.php?tab=reportes" class="btn btn-warning btn-lg btn-block">
								<span class="glyphicon glyphicon-stats"></span><br>
								Ver Reportes
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
<?php 
	}else{
		header("location:../index.php");
	}
?>