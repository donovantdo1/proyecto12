<?php 
require_once "../../clases/Conexion.php";

$c = new conectar();
$conexion = $c->conexion();

$sql = "SELECT id_mesa, nombre_mesa FROM mesas WHERE ocupada=0 ORDER BY nombre_mesa";
$result = mysqli_query($conexion, $sql);

echo '<option value="A">Selecciona Mesa</option>';

while($ver = mysqli_fetch_row($result)){
    echo '<option value="'.$ver[0].'">'.$ver[1].'</option>';
}
?>