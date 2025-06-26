<?php 
require_once "../../clases/Conexion.php";

$obj = new conectar();
$conexion = $obj->conexion();

$sql = "SELECT id_mesa, nombre_mesa, ocupada FROM mesas ORDER BY nombre_mesa";
$result = mysqli_query($conexion, $sql);
?>

<div class="table-responsive">
    <table class="table table-hover table-condensed table-bordered" style="text-align: center;">
        <caption><label>Mesas del Restaurante</label></caption>
        <tr>
            <td>Nombre Mesa</td>
            <td>Estado</td>
            <td>Ocupada</td>
            <td>Editar</td>
            <td>Eliminar</td>
        </tr>

        <?php while($ver = mysqli_fetch_row($result)): ?>
        <tr>
            <td><?php echo $ver[1]; ?></td>
            <td>
                <?php if($ver[2] == 1): ?>
                    <span class="label label-danger">OCUPADA</span>
                <?php else: ?>
                    <span class="label label-success">LIBRE</span>
                <?php endif; ?>
            </td>
            <td>
                <input type="checkbox" <?php echo ($ver[2] == 1) ? 'checked' : ''; ?> disabled>
            </td>
            <td>
                <span class="btn btn-warning btn-xs" data-toggle="modal" data-target="#abremodalMesasUpdate" onclick="agregaDatosMesa('<?php echo $ver[0]; ?>')">
                    <span class="glyphicon glyphicon-pencil"></span>
                </span>
            </td>
            <td>
                <span class="btn btn-danger btn-xs" onclick="eliminarMesa('<?php echo $ver[0]; ?>')">
                    <span class="glyphicon glyphicon-remove"></span>
                </span>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>