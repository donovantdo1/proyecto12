<?php 
require_once "../../clases/Conexion.php";
require_once "../../clases/Articulos.php";

// Verificar que se recibió el parámetro
if(!isset($_POST['idarticulo'])) {
    echo 0;
    exit;
}

$idart = $_POST['idarticulo'];

// Validar que el ID no esté vacío
if(empty($idart)) {
    echo 0;
    exit;
}

$obj = new articulos();

// Usar el método simplificado
$resultado = $obj->eliminaArticulo($idart);

echo $resultado;
?>
