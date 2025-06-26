<?php 

class ventas{
	public function obtenDatosProducto($idproducto){
		$c=new conectar();
		$conexion=$c->conexion();

		$sql = "SELECT 
				    art.nombre,
				    art.descripcion,
				    art.cantidad,
				    img.ruta,
				    art.precio
				FROM
				    articulos AS art
				        INNER JOIN
				    imagenes AS img ON art.id_imagen = img.id_imagen
				        AND art.id_producto = '$idproducto'";
		$result=mysqli_query($conexion,$sql);

		$ver=mysqli_fetch_row($result);

		$d=explode('/', $ver[3]);

		$img=$d[1].'/'.$d[2].'/'.$d[3];

		$data=array(
			'nombre' => $ver[0],
			'descripcion' => $ver[1],
			'cantidad' => $ver[2],
			'ruta' => $img,
			'precio' => $ver[4]
		);		
		return $data;
	}

	public function crearVenta(){
		$c= new conectar();
		$conexion=$c->conexion();

		$fecha=date('Y-m-d');
		$idventa=self::creaFolio();
		$datos=$_SESSION['tablaComprasTemp'];
		$idusuario=$_SESSION['iduser'];
		$r=0;

		for ($i=0; $i < count($datos) ; $i++) { 
			$d=explode("||", $datos[$i]);

			$sql="INSERT into ventas (id_venta,
                          id_cliente,
                          id_producto,
                          id_usuario,
                          precio,
                          cantidad,
                          fechaCompra)
                values ('$idventa',
                        '$d[5]',
                        '$d[0]',
                        '$idusuario',
                        '$d[3]',
                        '$d[6]',
                        '$fecha')";
			$r=$r + $result=mysqli_query($conexion,$sql);

			if($result){
    // NUEVO: Reducir la cantidad del artículo en inventario
    $sqlUpdate="UPDATE articulos SET cantidad = cantidad - '$d[6]' WHERE id_producto = '$d[0]'";
    mysqli_query($conexion,$sqlUpdate);
    $r++;
	}
		}

		return $r;
	}

	public function creaFolio(){
		$c= new conectar();
		$conexion=$c->conexion();

		// CAMBIO AQUÍ: Corregir la consulta SQL
		$sql="SELECT id_venta from ventas ORDER BY id_venta DESC LIMIT 1";

		$resul=mysqli_query($conexion,$sql);
		
		// CAMBIO AQUÍ: Verificar si hay resultados antes de acceder
		if(mysqli_num_rows($resul) > 0){
			$id=mysqli_fetch_row($resul)[0];
		} else {
			$id = 0;
		}

		if($id=="" or $id==null or $id==0){
			return 1;
		}else{
			return $id + 1;
		}
	}
	
	public function nombreCliente($idCliente){
		$c= new conectar();
		$conexion=$c->conexion();

		 $sql="SELECT apellido,nombre 
			from clientes 
			where id_cliente='$idCliente'";
		$result=mysqli_query($conexion,$sql);

		$ver=mysqli_fetch_row($result);

		return $ver[0]." ".$ver[1];
	}

	public function obtenerTotal($idventa){
		$c= new conectar();
		$conexion=$c->conexion();

		$sql="SELECT precio 
				from ventas 
				where id_venta='$idventa'";
		$result=mysqli_query($conexion,$sql);

		$total=0;

		while($ver=mysqli_fetch_row($result)){
			$total=$total + $ver[0];
		}

		return $total;
	}

	// Agregar este método a la clase ventas
public function eliminarVenta($idventa){
    $c = new conectar();
    $conexion = $c->conexion();
    
    // Primero restaurar inventario
    $sql = "SELECT id_producto, cantidad FROM ventas WHERE id_venta = '$idventa'";
    $result = mysqli_query($conexion, $sql);
    
    while($ver = mysqli_fetch_row($result)) {
        $idproducto = $ver[0];
        $cantidad = $ver[1];
        
        $sqlUpdate = "UPDATE articulos SET cantidad = cantidad + '$cantidad' WHERE id_producto = '$idproducto'";
        mysqli_query($conexion, $sqlUpdate);
    }
    
    // Eliminar venta
    $sqlDelete = "DELETE FROM ventas WHERE id_venta = '$idventa'";
    return mysqli_query($conexion, $sqlDelete);
}
}



?>