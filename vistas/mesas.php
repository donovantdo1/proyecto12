<?php 
session_start();
if(isset($_SESSION['usuario'])){
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mesas</title>
    <?php require_once "menu.php"; ?>
</head>
<body>
    <div class="container">
        <h1>Gestión de Mesas</h1>
        <div class="row">
            <div class="col-sm-4">
                <form id="frmMesas">
                    <label>Nombre de Mesa</label>
                    <input type="text" class="form-control input-sm" id="nombre_mesa" name="nombre_mesa" placeholder="Ej: Mesa 1, VIP, Terraza A">
                    <p></p>
                    <span class="btn btn-primary" id="btnAgregarMesa">Agregar Mesa</span>
                </form>
            </div>
            <div class="col-sm-8">
                <div id="tablaMesasLoad"></div>
            </div>
        </div>
    </div>

    <!-- Modal para actualizar mesa -->
    <div class="modal fade" id="abremodalMesasUpdate" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Actualizar Mesa</h4>
                </div>
                <div class="modal-body">
                    <form id="frmMesasU">
                        <input type="text" hidden="" id="idmesaU" name="idmesaU">
                        <label>Nombre de Mesa</label>
                        <input type="text" class="form-control input-sm" id="nombre_mesaU" name="nombre_mesaU">
                        <label>
                            <input type="checkbox" id="ocupadaU" name="ocupadaU" value="1"> Mesa Ocupada
                        </label>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="btnAgregarMesaU" type="button" class="btn btn-primary" data-dismiss="modal">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

<script type="text/javascript">
    function agregaDatosMesa(idmesa){
        $.ajax({
            type:"POST",
            data:"idmesa=" + idmesa,
            url:"../procesos/mesas/obtenDatosMesa.php",
            success:function(r){
                dato=jQuery.parseJSON(r);
                $('#idmesaU').val(dato['id_mesa']);
                $('#nombre_mesaU').val(dato['nombre_mesa']);
                $('#ocupadaU').prop('checked', dato['ocupada'] == 1);
            }
        });
    }

    function eliminarMesa(idmesa){
        alertify.confirm('¿Desea eliminar esta mesa?', function(){ 
            $.ajax({
                type:"POST",
                data:"idmesa=" + idmesa,
                url:"../procesos/mesas/eliminarMesa.php",
                success:function(r){
                    if(r==1){
                        $('#tablaMesasLoad').load("mesas/tablaMesas.php");
                        alertify.success("Mesa eliminada con éxito!!");
                    }else{
                        alertify.error("No se pudo eliminar la mesa");
                    }
                }
            });
        }, function(){ 
            alertify.error('Cancelado!')
        });
    }

    $(document).ready(function(){
        $('#tablaMesasLoad').load("mesas/tablaMesas.php");

        $('#btnAgregarMesa').click(function(){
            vacios=validarFormVacio('frmMesas');

            if(vacios > 0){
                alertify.alert("Debes llenar el nombre de la mesa!!");
                return false;
            }

            datos=$('#frmMesas').serialize();

            $.ajax({
                type:"POST",
                data:datos,
                url:"../procesos/mesas/agregaMesa.php",
                success:function(r){
                    if(r==1){
                        $('#frmMesas')[0].reset();
                        $('#tablaMesasLoad').load("mesas/tablaMesas.php");
                        alertify.success("Mesa agregada con éxito :D");
                    }else{
                        alertify.error("No se pudo agregar la mesa");
                    }
                }
            });
        });

        $('#btnAgregarMesaU').click(function(){
            datos=$('#frmMesasU').serialize();

            $.ajax({
                type:"POST",
                data:datos,
                url:"../procesos/mesas/actualizaMesa.php",
                success:function(r){
                    if(r==1){
                        $('#tablaMesasLoad').load("mesas/tablaMesas.php");
                        alertify.success("Mesa actualizada con éxito :D");
                    }else{
                        alertify.error("No se pudo actualizar la mesa");
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