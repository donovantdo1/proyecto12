<?php 
	session_start();
	require_once "../../clases/Conexion.php";

	$c= new conectar();
	$conexion=$c->conexion();

	$idcliente=$_POST['clienteVenta'];
	$idproducto=$_POST['productoVenta'];
	$descripcion=$_POST['descripcionV'];
	$cantidad=$_POST['cantidadV'];
	$precioTotal=$_POST['precioV']; // Este ya viene calculado desde el frontend

	$sql="SELECT nombre,apellido 
			from clientes 
			where id_cliente='$idcliente'";
	$result=mysqli_query($conexion,$sql);

	$c=mysqli_fetch_row($result);

	$ncliente=$c[1]." ".$c[0];

	$sql="SELECT nombre 
			from articulos 
			where id_producto='$idproducto'";
	$result=mysqli_query($conexion,$sql);

	$nombreproducto=mysqli_fetch_row($result)[0];

	// NUEVO: Verificar si el producto ya existe en la tabla temporal
	$productoExiste = false;
	$indiceExistente = -1;

	if(isset($_SESSION['tablaComprasTemp'])) {
		for($i = 0; $i < count($_SESSION['tablaComprasTemp']); $i++) {
			$datosExistentes = explode("||", $_SESSION['tablaComprasTemp'][$i]);
			
			// Verificar si es el mismo producto y mismo cliente
			if($datosExistentes[0] == $idproducto && $datosExistentes[5] == $idcliente) {
				$productoExiste = true;
				$indiceExistente = $i;
				break;
			}
		}
	}

	if($productoExiste) {
		// ACTUALIZAR: Sumar cantidad y recalcular precio
		$datosExistentes = explode("||", $_SESSION['tablaComprasTemp'][$indiceExistente]);
		
		$cantidadAnterior = $datosExistentes[6];
		$nuevaCantidad = $cantidadAnterior + $cantidad;
		
		// Obtener precio unitario
		$sqlPrecio = "SELECT precio FROM articulos WHERE id_producto='$idproducto'";
		$resultPrecio = mysqli_query($conexion, $sqlPrecio);
		$precioUnitario = mysqli_fetch_row($resultPrecio)[0];
		
		$nuevoPrecioTotal = $precioUnitario * $nuevaCantidad;
		
		// Actualizar el artículo existente
		$articuloActualizado = $idproducto."||".
		                      $nombreproducto."||".
		                      $descripcion."||".
		                      $nuevoPrecioTotal."||".
		                      $ncliente."||".
		                      $idcliente."||".
		                      $nuevaCantidad;
		
		$_SESSION['tablaComprasTemp'][$indiceExistente] = $articuloActualizado;
		
	} else {
		// AGREGAR: Nuevo producto
		$articulo=$idproducto."||".
		          $nombreproducto."||".
		          $descripcion."||".
		          $precioTotal."||".
		          $ncliente."||".
		          $idcliente."||".
		          $cantidad;
		          
		$_SESSION['tablaComprasTemp'][]=$articulo;
	}

 ?>