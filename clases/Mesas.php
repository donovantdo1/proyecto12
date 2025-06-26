<?php 

class mesas{

    public function agregaMesa($datos){
        $c = new conectar();
        $conexion = $c->conexion();
        $idusuario = $_SESSION['iduser'];
        $fecha = date('Y-m-d');

        $sql = "INSERT INTO mesas (id_usuario, nombre_mesa, ocupada, fecha_creacion)
                VALUES ('$idusuario', '$datos[0]', 0, '$fecha')";
        return mysqli_query($conexion, $sql);	
    }

    public function obtenDatosMesa($idmesa){
        $c = new conectar();
        $conexion = $c->conexion();

        $sql = "SELECT id_mesa, nombre_mesa, ocupada 
                FROM mesas WHERE id_mesa='$idmesa'";
        $result = mysqli_query($conexion, $sql);
        $ver = mysqli_fetch_row($result);

        $datos = array(
            'id_mesa' => $ver[0], 
            'nombre_mesa' => $ver[1],
            'ocupada' => $ver[2]
        );
        return $datos;
    }

    public function actualizaMesa($datos){
        $c = new conectar();
        $conexion = $c->conexion();
        
        $sql = "UPDATE mesas SET nombre_mesa='$datos[1]', ocupada='$datos[2]' 
                WHERE id_mesa='$datos[0]'";
        return mysqli_query($conexion, $sql);
    }

    public function eliminaMesa($idmesa){
        $c = new conectar();
        $conexion = $c->conexion();

        $sql = "DELETE FROM mesas WHERE id_mesa='$idmesa'";
        return mysqli_query($conexion, $sql);
    }

    public function ocuparMesa($idmesa){
        $c = new conectar();
        $conexion = $c->conexion();
        
        $sql = "UPDATE mesas SET ocupada=1 WHERE id_mesa='$idmesa'";
        return mysqli_query($conexion, $sql);
    }

    public function liberarMesa($idmesa){
        $c = new conectar();
        $conexion = $c->conexion();
        
        $sql = "UPDATE mesas SET ocupada=0 WHERE id_mesa='$idmesa'";
        return mysqli_query($conexion, $sql);
    }

    public function obtenerMesasLibres(){
        $c = new conectar();
        $conexion = $c->conexion();
        
        $sql = "SELECT id_mesa, nombre_mesa FROM mesas WHERE ocupada=0 ORDER BY nombre_mesa";
        return mysqli_query($conexion, $sql);
    }

    public function nombreMesa($idMesa){
        $c = new conectar();
        $conexion = $c->conexion();

        $sql = "SELECT nombre_mesa FROM mesas WHERE id_mesa='$idMesa'";
        $result = mysqli_query($conexion, $sql);
        $ver = mysqli_fetch_row($result);

        return $ver[0];
    }
}

?>