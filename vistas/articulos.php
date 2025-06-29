<?php 
session_start();
if(isset($_SESSION['usuario'])){

	?>


	<!DOCTYPE html>
	<html>
	<head>
		<title>articulos</title>
		<?php require_once "menu.php"; ?>
		<?php require_once "../clases/Conexion.php"; 
		$c= new conectar();
		$conexion=$c->conexion();
		$sql="SELECT id_categoria,nombreCategoria
		from categorias";
		$result=mysqli_query($conexion,$sql);
		?>
	</head>
	<body>
		<div class="container">
			<h1>Articulos</h1>
			<div class="row">
				<div class="col-sm-4">
					<form id="frmArticulos" enctype="multipart/form-data">
						<label>Categoria</label>
						<select class="form-control input-sm" id="categoriaSelect" name="categoriaSelect">
							<option value="A">Selecciona Categoria</option>
							<?php while($ver=mysqli_fetch_row($result)): ?>
								<option value="<?php echo $ver[0] ?>"><?php echo $ver[1]; ?></option>
							<?php endwhile; ?>
						</select>
						<label>Nombre</label>
						<input type="text" class="form-control input-sm" id="nombre" name="nombre">
						<label>Descripcion</label>
						<input type="text" class="form-control input-sm" id="descripcion" name="descripcion">
						<label>Cantidad</label>
						<input type="text" class="form-control input-sm" id="cantidad" name="cantidad">
						<label>Precio</label>
						<input type="text" class="form-control input-sm" id="precio" name="precio">
						<label>Imagen</label>
						<input type="file" id="imagen" name="imagen">
						<p></p>
						<span id="btnAgregaArticulo" class="btn btn-primary">Agregar</span>
					</form>
				</div>
				<div class="col-sm-8">
					<div id="tablaArticulosLoad"></div>
				</div>
			</div>
		</div>

		<!-- Modal -->
		<div class="modal fade" id="abremodalUpdateArticulo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Actualiza Articulo</h4>
					</div>
					<div class="modal-body">
						<form id="frmArticulosU" enctype="multipart/form-data">
							<input type="text" id="idArticulo" hidden="" name="idArticulo">
							<label>Categoria</label>
							<select class="form-control input-sm" id="categoriaSelectU" name="categoriaSelectU">
								<option value="A">Selecciona Categoria</option>
								<?php 
								$sql="SELECT id_categoria,nombreCategoria
								from categorias";
								$result=mysqli_query($conexion,$sql);
								?>
								<?php while($ver=mysqli_fetch_row($result)): ?>
									<option value="<?php echo $ver[0] ?>"><?php echo $ver[1]; ?></option>
								<?php endwhile; ?>
							</select>
							<label>Nombre</label>
							<input type="text" class="form-control input-sm" id="nombreU" name="nombreU">
							<label>Descripcion</label>
							<input type="text" class="form-control input-sm" id="descripcionU" name="descripcionU">
							
							<label>Cantidad Actual</label>
							<input type="text" readonly class="form-control input-sm" id="cantidadActualU">
							
							<label>Ajustar Cantidad</label>
							<div class="row">
								<div class="col-sm-4">
									<input type="number" class="form-control input-sm" id="cantidadAjuste" min="1" value="1" placeholder="Cantidad">
								</div>
								<div class="col-sm-4">
									<button type="button" class="btn btn-success btn-sm" id="btnAumentar">
										<span class="glyphicon glyphicon-plus"></span> Aumentar
									</button>
								</div>
								<div class="col-sm-4">
									<button type="button" class="btn btn-warning btn-sm" id="btnDisminuir">
										<span class="glyphicon glyphicon-minus"></span> Disminuir
									</button>
								</div>
							</div>
							<br>
							
							<label>Precio</label>
							<input type="text" class="form-control input-sm" id="precioU" name="precioU">
							
							<input type="hidden" id="cantidadU" name="cantidadU">
						</form>
					</div>
					<div class="modal-footer">
						<button id="btnActualizaarticulo" type="button" class="btn btn-warning" data-dismiss="modal">Actualizar</button>
					</div>
				</div>
			</div>
		</div>

	</body>
	</html>

	<script type="text/javascript">
		function agregaDatosArticulo(idarticulo){
			$.ajax({
				type:"POST",
				data:"idart=" + idarticulo,
				url:"../procesos/articulos/obtenDatosArticulo.php",
				success:function(r){
					
					dato=jQuery.parseJSON(r);
					$('#idArticulo').val(dato['id_producto']);
					$('#categoriaSelectU').val(dato['id_categoria']);
					$('#nombreU').val(dato['nombre']);
					$('#descripcionU').val(dato['descripcion']);
					$('#cantidadActualU').val(dato['cantidad']);
					$('#cantidadU').val(dato['cantidad']);
					$('#precioU').val(dato['precio']);

				}
			});
		}

		function eliminaArticulo(idArticulo){
			alertify.confirm('¿Desea eliminar este articulo?', function(){ 
				$.ajax({
					type:"POST",
					data:"idarticulo=" + idArticulo,
					url:"../procesos/articulos/eliminarArticulo.php",
					success:function(r){
						if(r==1){
							$('#tablaArticulosLoad').load("articulos/tablaArticulos.php");
							alertify.success("Eliminado con exito!!");
						}else{
							alertify.error("No se pudo eliminar :(");
						}
					}
				});
			}, function(){ 
				alertify.error('Cancelo !')
			});
		}
	</script>

	<script type="text/javascript">
		$(document).ready(function(){
			
			// Aumentar cantidad
			$('#btnAumentar').click(function(){
				var idproducto = $('#idArticulo').val();
				var cantidad = $('#cantidadAjuste').val();
				
				if(cantidad <= 0){
					alertify.alert("La cantidad debe ser mayor que 0");
					return false;
				}
				
				$.ajax({
					type:"POST",
					data:"idproducto=" + idproducto + "&cantidad=" + cantidad + "&operacion=aumentar",
					url:"../procesos/articulos/ajustarCantidad.php",
					success:function(r){
						if(r == 1){
							alertify.success("Cantidad aumentada correctamente");
							// Actualizar la cantidad mostrada
							var cantidadActual = parseInt($('#cantidadActualU').val());
							var nuevaCantidad = cantidadActual + parseInt(cantidad);
							$('#cantidadActualU').val(nuevaCantidad);
							$('#cantidadU').val(nuevaCantidad);
							$('#cantidadAjuste').val(1);
							
							// Recargar tabla de artículos
							$('#tablaArticulosLoad').load("articulos/tablaArticulos.php");
						} else {
							alertify.error("Error al aumentar cantidad");
						}
					}
				});
			});
			
			// Disminuir cantidad
			$('#btnDisminuir').click(function(){
				var idproducto = $('#idArticulo').val();
				var cantidad = $('#cantidadAjuste').val();
				var cantidadActual = parseInt($('#cantidadActualU').val());
				
				if(cantidad <= 0){
					alertify.alert("La cantidad debe ser mayor que 0");
					return false;
				}
				
				if(cantidad > cantidadActual){
					alertify.alert("No puedes disminuir más cantidad de la disponible");
					return false;
				}
				
				$.ajax({
					type:"POST",
					data:"idproducto=" + idproducto + "&cantidad=" + cantidad + "&operacion=disminuir",
					url:"../procesos/articulos/ajustarCantidad.php",
					success:function(r){
						if(r == 1){
							alertify.success("Cantidad disminuida correctamente");
							// Actualizar la cantidad mostrada
							var nuevaCantidad = cantidadActual - parseInt(cantidad);
							$('#cantidadActualU').val(nuevaCantidad);
							$('#cantidadU').val(nuevaCantidad);
							$('#cantidadAjuste').val(1);
							
							// Recargar tabla de artículos
							$('#tablaArticulosLoad').load("articulos/tablaArticulos.php");
						} else if(r == -1) {
							alertify.error("No se puede disminuir más cantidad de la disponible");
						} else {
							alertify.error("Error al disminuir cantidad");
						}
					}
				});
			});

			$('#btnActualizaarticulo').click(function(){

				datos=$('#frmArticulosU').serialize();
				$.ajax({
					type:"POST",
					data:datos,
					url:"../procesos/articulos/actualizaArticulos.php",
					success:function(r){
						if(r==1){
							$('#tablaArticulosLoad').load("articulos/tablaArticulos.php");
							alertify.success("Actualizado con exito :D");
						}else{
							alertify.error("Error al actualizar :(");
						}
					}
				});
			});
		});
	</script>

	<script type="text/javascript">
		$(document).ready(function(){
			$('#tablaArticulosLoad').load("articulos/tablaArticulos.php");

			$('#btnAgregaArticulo').click(function(){

				vacios=validarFormVacio('frmArticulos');

				if(vacios > 0){
					alertify.alert("Debes llenar todos los campos!!");
					return false;
				}

				var formData = new FormData(document.getElementById("frmArticulos"));

				$.ajax({
					url: "../procesos/articulos/insertaArticulos.php",
					type: "post",
					dataType: "html",
					data: formData,
					cache: false,
					contentType: false,
					processData: false,

					success:function(r){
						
						if(r == 1){
							$('#frmArticulos')[0].reset();
							$('#tablaArticulosLoad').load("articulos/tablaArticulos.php");
							alertify.success("Agregado con exito :D");
						}else{
							alertify.error("Fallo al subir el archivo :(");
						}
					}
				});
				
			});
		});
	</script>

	<?php 
}else{
	header("location:../index.php");
}
?>