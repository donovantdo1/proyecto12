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
 	<title>Ticket de venta</title>
 	<style type="text/css">
		
@page {
            margin-top: 0.3em;
            margin-left: 0.6em;
        }
    body{
    	font-size: xx-small;
    }
	</style>

 </head>
 <body>
 		<p>Facultad autodidacta</p>
 		<p>
 			Fecha: <?php echo $fecha; ?>
 		</p>
 		<p>
 			Folio: <?php echo $folio ?>
 		</p>
 		<p>
 			Cliente: <?php echo $objv->nombreCliente($idcliente); ?>
 		</p>
 		
 		<table style="border-collapse: collapse;" border="1">
 			<tr>
 				<td>Nombre</td>
 				<td>P.Unit</td>
 				<td>Cant.</td>
 				<td>Subtotal</td>
 			</tr>
 			<?php 
				$result=mysqli_query($conexion,$sql);
				$total=0;
				while($mostrar=mysqli_fetch_row($result)){
					// CORREGIDO: Calcular precio unitario
					$precioTotal = $mostrar[4];
					$cantidad = $mostrar[6];
					$precioUnitario = $precioTotal / $cantidad;
 			 ?>
 			<tr>
 				<td><?php echo $mostrar[3]; ?></td>
 				<td>$<?php echo number_format($precioUnitario, 2) ?></td>
 				<td><?php echo $cantidad ?></td>
 				<td>$<?php echo number_format($precioTotal, 2) ?></td>
 			</tr>
 			<?php
 				$total = $total + $precioTotal;
 				} 
 			 ?>
 			 <tr>
 			 	<td colspan="3"><strong>Total:</strong></td>
 			 	<td><strong>$<?php echo number_format($total, 2) ?></strong></td>
 			 </tr>
 		</table>

 		
 </body>
 </html>