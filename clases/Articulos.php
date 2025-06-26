<?php 
class articulos{
    public function agregaImagen($datos){
        $c=new conectar();
        $conexion=$c->conexion();

        $fecha=date('Y-m-d');

        $sql="INSERT into imagenes (id_categoria,
                                    nombre,
                                    ruta,
                                    fechaSubida)
                        values ('$datos[0]',
                                '$datos[1]',
                                '$datos[2]',
                                '$fecha')";
        $result=mysqli_query($conexion,$sql);

        return mysqli_insert_id($conexion);
    }

    public function insertaArticulo($datos){
        $c=new conectar();
        $conexion=$c->conexion();

        $fecha=date('Y-m-d');

        $sql="INSERT into articulos (id_categoria,
                                    id_imagen,
                                    id_usuario,
                                    nombre,
                                    descripcion,
                                    cantidad,
                                    precio,
                                    fechaCaptura) 
                        values ('$datos[0]',
                                '$datos[1]',
                                '$datos[2]',
                                '$datos[3]',
                                '$datos[4]',
                                '$datos[5]',
                                '$datos[6]',
                                '$fecha')";
        return mysqli_query($conexion,$sql);
    }

    public function obtenDatosArticulo($idarticulo){
        $c=new conectar();
        $conexion=$c->conexion();

        $sql="SELECT id_producto, 
                    id_categoria, 
                    nombre,
                    descripcion,
                    cantidad,
                    precio 
            from articulos 
            where id_producto='$idarticulo'";
        $result=mysqli_query($conexion,$sql);

        $ver=mysqli_fetch_row($result);

        $datos=array(
                "id_producto" => $ver[0],
                "id_categoria" => $ver[1],
                "nombre" => $ver[2],
                "descripcion" => $ver[3],
                "cantidad" => $ver[4],
                "precio" => $ver[5]
                    );

        return $datos;
    }

    public function actualizaArticulo($datos){
        $c=new conectar();
        $conexion=$c->conexion();

        $sql="UPDATE articulos set id_categoria='$datos[1]', 
                                    nombre='$datos[2]',
                                    descripcion='$datos[3]',
                                    cantidad='$datos[4]',
                                    precio='$datos[5]'
                    where id_producto='$datos[0]'";

        return mysqli_query($conexion,$sql);
    }

    public function ajustarCantidad($idproducto, $cantidad, $operacion){
        $c=new conectar();
        $conexion=$c->conexion();

        if($operacion == 'aumentar'){
            $sql="UPDATE articulos SET cantidad = cantidad + '$cantidad' WHERE id_producto='$idproducto'";
        } else if($operacion == 'disminuir'){
            $sql="UPDATE articulos SET cantidad = cantidad - '$cantidad' WHERE id_producto='$idproducto'";
        }

        return mysqli_query($conexion,$sql);
    }

    // MÉTODO SIMPLIFICADO PARA ELIMINAR ARTÍCULO
    public function eliminaArticulo($idarticulo){
        $c=new conectar();
        $conexion=$c->conexion();

        // Verificar que el artículo existe
        $sql_check="SELECT id_producto FROM articulos WHERE id_producto='$idarticulo'";
        $result_check=mysqli_query($conexion,$sql_check);
        
        if(!$result_check || mysqli_num_rows($result_check) == 0){
            return 0; // Artículo no existe
        }

        // OPCIÓN 1: Eliminación simple (solo de la base de datos)
        $sql="DELETE FROM articulos WHERE id_producto='$idarticulo'";
        $result=mysqli_query($conexion,$sql);
        
        if($result){
            return 1; // Éxito
        } else {
            return 0; // Error
        }
    }

    // MÉTODO ALTERNATIVO: Eliminación completa con imágenes (más seguro)
    public function eliminaArticuloCompleto($idarticulo){
        $c=new conectar();
        $conexion=$c->conexion();

        try {
            // Obtener ID de imagen antes de eliminar el artículo
            $idimagen = $this->obtenIdImg($idarticulo);
            
            // Eliminar artículo primero
            $sql="DELETE FROM articulos WHERE id_producto='$idarticulo'";
            $result=mysqli_query($conexion,$sql);
            
            if($result){
                // Si el artículo se eliminó exitosamente, intentar eliminar imagen
                if($idimagen && $idimagen != null){
                    $ruta = $this->obtenRutaImagen($idimagen);
                    
                    // Eliminar registro de imagen
                    $sql_img="DELETE FROM imagenes WHERE id_imagen='$idimagen'";
                    mysqli_query($conexion,$sql_img);
                    
                    // Intentar eliminar archivo físico (no crítico si falla)
                    if($ruta && file_exists($ruta)){
                        @unlink($ruta); // @ suprime errores
                    }
                }
                return 1; // Éxito
            } else {
                return 0; // Error al eliminar artículo
            }
        } catch (Exception $e) {
            return 0; // Error general
        }
    }

    public function obtenIdImg($idProducto){
        $c= new conectar();
        $conexion=$c->conexion();

        $sql="SELECT id_imagen 
                from articulos 
                where id_producto='$idProducto'";
        $result=mysqli_query($conexion,$sql);

        if($result && mysqli_num_rows($result) > 0){
            return mysqli_fetch_row($result)[0];
        }
        return null;
    }

    public function obtenRutaImagen($idImg){
        $c= new conectar();
        $conexion=$c->conexion();

        $sql="SELECT ruta 
                from imagenes 
                where id_imagen='$idImg'";

        $result=mysqli_query($conexion,$sql);

        if($result && mysqli_num_rows($result) > 0){
            return mysqli_fetch_row($result)[0];
        }
        return null;
    }
}
?>
