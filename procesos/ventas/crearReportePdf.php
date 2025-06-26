<?php
// Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
require_once '../../librerias/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$id=$_GET['idventa'];

// SOLUCIÓN: Usar output buffering para capturar el HTML generado
ob_start();
$idventa = $id; // Variable que necesita el archivo incluido
include("../../vistas/ventas/rerpoteVentaPdf.php");
$html = ob_get_clean();

// Instanciamos un objeto de la clase DOMPDF.
$pdf = new DOMPDF();
 
// Definimos el tamaño y orientación del papel que queremos.
$pdf->set_paper("letter", "portrait");
 
// Cargamos el contenido HTML.
$pdf->load_html($html);
 
// Renderizamos el documento PDF.
$pdf->render();
 
// Enviamos el fichero PDF al navegador.
$pdf->stream('reporteVenta.pdf');
?>