<?php 
	require_once "../../clases/Conexion.php";
	require_once "../../clases/Ventas.php";

	$objv= new ventas();

	$c=new conectar();
	$conexion= $c->conexion();	
	
	if(!isset($idventa)) {
		$idventa=$_GET['idventa'];
	}

 $sql="SELECT ve.id_venta,
		ve.fechaCompra,
		ve.id_cliente,
		art.nombre,
        ve.precio,
        art.descripcion,
        ve.cantidad
	from ventas  as ve 
	inner join articulos as art
	on ve.id_producto=art.id_producto
	where ve.id_venta='$idventa'";

$result=mysqli_query($conexion,$sql);

	$ver=mysqli_fetch_row($result);

	$folio=$ver[0];
	$fecha=$ver[1];
	$idcliente=$ver[2];

 ?>	

 <!DOCTYPE html>
 <html>
 <head>
 	<title>Reporte de venta</title>
 	<style>
 		body { font-family: Arial, sans-serif; }
 		table { width: 100%; border-collapse: collapse; }
 		td, th { padding: 8px; border: 1px solid #ddd; }
 	</style>
 </head>
 <body>
 		<h2>Reporte de Venta</h2>
 		<table>
 			<tr>
 				<td><strong>Fecha:</strong> <?php echo $fecha; ?></td>
 			</tr>
 			<tr>
 				<td><strong>Folio:</strong> <?php echo $folio ?></td>
 			</tr>
 			<tr>
 				<td><strong>Cliente:</strong> <?php echo $objv->nombreCliente($idcliente); ?></td>
 			</tr>
 		</table>

 		<br>

 		<table>
 			<tr>
 				<th>Producto</th>
 				<th>Precio Unitario</th>
 				<th>Cantidad</th>
 				<th>Subtotal</th>
 				<th>Descripción</th>
 			</tr>

 			<?php 
 			$result=mysqli_query($conexion,$sql);
			$total=0;
			while($mostrar=mysqli_fetch_row($result)):
				// CORREGIDO: Calcular precio unitario dividiendo precio total entre cantidad
				$precioTotal = $mostrar[4]; // Este es el precio total almacenado
				$cantidad = $mostrar[6];
				$precioUnitario = $precioTotal / $cantidad; // Calcular precio unitario
 			 ?>

 			<tr>
 				<td><?php echo $mostrar[3]; ?></td>
 				<td>$<?php echo number_format($precioUnitario, 2); ?></td>
 				<td><?php echo $cantidad; ?></td>
 				<td>$<?php echo number_format($precioTotal, 2); ?></td>
 				<td><?php echo $mostrar[5]; ?></td>
 			</tr>
 			<?php 
 				$total = $total + $precioTotal;
 			endwhile;
 			 ?>
 			 <tr>
 			 	<td colspan="3"><strong>TOTAL</strong></td>
 			 	<td><strong>$<?php echo number_format($total, 2); ?></strong></td>
 			 	<td></td>
 			 </tr>
 		</table>
 </body>
 </html>