<?php 

	require_once "../../clases/Conexion.php";
	require_once "../../clases/Ventas.php";

	$c= new conectar();
	$conexion=$c->conexion();

	$obj= new ventas();

	$sql="SELECT id_venta,
				fechaCompra,
				id_cliente 
			from ventas group by id_venta, fechaCompra, id_cliente";
	$result=mysqli_query($conexion,$sql); 
	?>

<h4>Reportes y ventas</h4>
<div class="row">
	<div class="col-sm-1"></div>
	<div class="col-sm-10">
		<div class="table-responsive">
			<table class="table table-hover table-condensed table-bordered" style="text-align: center;">
				<caption><label>Ventas :)</label></caption>
				<tr>
					<td>Folio</td>
					<td>Fecha</td>
					<td>Cliente</td>
					<td>Total de compra</td>
					<td>Ticket</td>
					<td>Reporte</td>
					<td>Editar</td>
					<td>Eliminar</td>
				</tr>
		<?php while($ver=mysqli_fetch_row($result)): ?>
				<tr>
					<td><?php echo $ver[0] ?></td>
					<td><?php echo $ver[1] ?></td>
					<td>
						<?php
							if($obj->nombreCliente($ver[2])==" "){
								echo "S/C";
							}else{
								echo $obj->nombreCliente($ver[2]);
							}
						 ?>
					</td>
					<td>
						<?php 
							echo "$".$obj->obtenerTotal($ver[0]);
						 ?>
					</td>
					<td>
						<a href="../procesos/ventas/crearTicketPdf.php?idventa=<?php echo $ver[0] ?>" class="btn btn-danger btn-sm">
							Ticket <span class="glyphicon glyphicon-list-alt"></span>
						</a>
					</td>
					<td>
						<a href="../procesos/ventas/crearReportePdf.php?idventa=<?php echo $ver[0] ?>" class="btn btn-danger btn-sm">
							Reporte <span class="glyphicon glyphicon-file"></span>
						</a>	
					</td>
					<td>
						<button class="btn btn-warning btn-sm" onclick="editarVenta('<?php echo $ver[0] ?>')">
							<span class="glyphicon glyphicon-edit"></span> Editar
						</button>
					</td>
					<td>
						<button class="btn btn-danger btn-sm" onclick="eliminarVenta('<?php echo $ver[0] ?>')">
							<span class="glyphicon glyphicon-trash"></span> Eliminar
						</button>
					</td>
				</tr>
		<?php endwhile; ?>
			</table>
		</div>
	</div>
	<div class="col-sm-1"></div>
</div>

<script type="text/javascript">
function editarVenta(idVenta) {
	// Cargar los datos de la venta en el formulario de ventas
	$.ajax({
		type: "POST",
		data: "idventa=" + idVenta,
		url: "../procesos/ventas/cargarVentaParaEditar.php",
		success: function(r) {
			if(r == 1) {
				// Cambiar a la pestaña de "Vender producto"
				$('#ventasTab').click();
				alertify.success("Venta cargada para editar");
			} else {
				alertify.error("Error al cargar la venta");
			}
		}
	});
}

function eliminarVenta(idVenta) {
	alertify.confirm('¿Está seguro de eliminar esta venta? Esta acción no se puede deshacer.', function(){ 
		$.ajax({
			type: "POST",
			data: "idventa=" + idVenta,
			url: "../procesos/ventas/eliminarVenta.php",
			success: function(r) {
				if(r == 1) {
					// Recargar la tabla de ventas
					$('#tablaVentasLoad').load("ventas/ventasyReportes.php");
					alertify.success("Venta eliminada correctamente");
				} else {
					alertify.error("Error al eliminar la venta");
				}
			}
		});
	}, function(){ 
		alertify.message('Operación cancelada');
	});
}
</script>