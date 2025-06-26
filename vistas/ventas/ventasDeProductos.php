<?php 

require_once "../../clases/Conexion.php";
$c= new conectar();
$conexion=$c->conexion();
?>


<h4>Vender un producto</h4>
<div class="row">
	<div class="col-sm-4">
		<form id="frmVentasProductos">
			<label>Seleciona Cliente</label>
			<select class="form-control input-sm" id="clienteVenta" name="clienteVenta">
				<option value="A">Selecciona</option>
				<option value="0">Sin cliente</option>
				<?php
				$sql="SELECT id_cliente,nombre,apellido 
				from clientes";
				$result=mysqli_query($conexion,$sql);
				while ($cliente=mysqli_fetch_row($result)):
					?>
					<option value="<?php echo $cliente[0] ?>"><?php echo $cliente[2]." ".$cliente[1] ?></option>
				<?php endwhile; ?>
			</select>
			<label>Producto</label>
			<select class="form-control input-sm" id="productoVenta" name="productoVenta">
				<option value="A">Selecciona</option>
				<?php
				$sql="SELECT id_producto,
				nombre
				from articulos";
				$result=mysqli_query($conexion,$sql);

				while ($producto=mysqli_fetch_row($result)):
					?>
					<option value="<?php echo $producto[0] ?>"><?php echo $producto[1] ?></option>
				<?php endwhile; ?>
			</select>
			<label>Descripcion</label>
			<textarea readonly="" id="descripcionV" name="descripcionV" class="form-control input-sm"></textarea>
			<label>Cantidad Disponible</label>
			<input readonly="" type="text" class="form-control input-sm" id="cantidadDisponible" name="cantidadDisponible">
			<label>Cantidad a Vender</label>
			<input type="number" class="form-control input-sm" id="cantidadV" name="cantidadV" min="1" value="1">
			<label>Precio Unitario</label>
			<input readonly="" type="text" class="form-control input-sm" id="precioUnitario" name="precioUnitario">
			<label>Precio Total</label>
			<input readonly="" type="text" class="form-control input-sm" id="precioV" name="precioV">
			<p></p>
			<span class="btn btn-primary" id="btnAgregaVenta">Agregar</span>
			<span class="btn btn-danger" id="btnVaciarVentas">Vaciar ventas</span>
		</form>
	</div>
	<div class="col-sm-3">
		<div id="imgProducto"></div>
	</div>
	<div class="col-sm-4">
		<div id="tablaVentasTempLoad"></div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){

		$('#tablaVentasTempLoad').load("ventas/tablaVentasTemp.php");

		$('#productoVenta').change(function(){
			$.ajax({
				type:"POST",
				data:"idproducto=" + $('#productoVenta').val(),
				url:"../procesos/ventas/llenarFormProducto.php",
				success:function(r){
					dato=jQuery.parseJSON(r);

					$('#descripcionV').val(dato['descripcion']);
					$('#cantidadDisponible').val(dato['cantidad']);
					$('#precioUnitario').val(dato['precio']);
					
					// Calcular precio total inicial
					calcularPrecioTotal();

					$('#imgProducto').html('<img class="img-thumbnail" id="imgp" src="' + dato['ruta'] + '" />');
				}
			});
		});

		// Calcular precio total cuando cambie la cantidad a vender
		$('#cantidadV').on('input', function(){
			calcularPrecioTotal();
		});

		$('#btnAgregaVenta').click(function(){
			vacios=validarFormVacio('frmVentasProductos');

			if(vacios > 0){
				alertify.alert("Debes llenar todos los campos!!");
				return false;
			}

			// Validar que la cantidad a vender no sea mayor que la disponible
			var cantidadDisponible = parseInt($('#cantidadDisponible').val());
			var cantidadVender = parseInt($('#cantidadV').val());

			if(cantidadVender > cantidadDisponible){
				alertify.alert("La cantidad a vender no puede ser mayor que la cantidad disponible!");
				return false;
			}

			if(cantidadVender <= 0){
				alertify.alert("La cantidad a vender debe ser mayor que 0!");
				return false;
			}

			datos=$('#frmVentasProductos').serialize();
			$.ajax({
				type:"POST",
				data:datos,
				url:"../procesos/ventas/agregaProductoTemp.php",
				success:function(r){
					$('#tablaVentasTempLoad').load("ventas/tablaVentasTemp.php");
					// Limpiar campos después de agregar
					$('#cantidadV').val(1);
					calcularPrecioTotal();
				}
			});
		});

		$('#btnVaciarVentas').click(function(){

		$.ajax({
			url:"../procesos/ventas/vaciarTemp.php",
			success:function(r){
				$('#tablaVentasTempLoad').load("ventas/tablaVentasTemp.php");
			}
		});
	});

	});

	// Función para calcular el precio total
	function calcularPrecioTotal(){
		var precioUnitario = parseFloat($('#precioUnitario').val()) || 0;
		var cantidad = parseInt($('#cantidadV').val()) || 1;
		var precioTotal = precioUnitario * cantidad;
		$('#precioV').val(precioTotal.toFixed(2));
	}
</script>

<script type="text/javascript">
	function quitarP(index){
		$.ajax({
			type:"POST",
			data:"ind=" + index,
			url:"../procesos/ventas/quitarproducto.php",
			success:function(r){
				$('#tablaVentasTempLoad').load("ventas/tablaVentasTemp.php");
				alertify.success("Se quito el producto :D");
			}
		});
	}

	function crearVenta(){
		$.ajax({
			url:"../procesos/ventas/crearVenta.php",
			success:function(r){
				if(r > 0){
					$('#tablaVentasTempLoad').load("ventas/tablaVentasTemp.php");
					$('#frmVentasProductos')[0].reset();
					$('#imgProducto').empty();
					alertify.alert("Venta creada con exito, consulte la informacion de esta en ventas hechas :D");
				}else if(r==0){
					alertify.alert("No hay lista de venta!!");
				}else{
					alertify.error("No se pudo crear la venta");
				}
			}
		});
	}
</script>

<script type="text/javascript">
	$(document).ready(function(){
		$('#clienteVenta').select2();
		$('#productoVenta').select2();

	});
</script>