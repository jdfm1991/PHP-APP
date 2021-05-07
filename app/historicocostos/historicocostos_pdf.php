<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("historicocostos_modelo.php");

//INSTANCIAMOS EL MODELO
$historico = new Historicocostos();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];

$j = 0;
$width = array();
function addWidthInArray($num){
    $GLOBALS['width'][$GLOBALS['j']] = $num;
    $GLOBALS['j'] = $GLOBALS['j'] + 1;
    return $num;
}

class PDF extends FPDF
{
    var $widths;
    var $aligns;

    // Cabecera de página
    function Header()
    {
        // Logo
        $this->Image(PATH_LIBRARY.'build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', '', 11);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(40, 10, 'HISTORICO COSTOS DEL ' . $GLOBALS['fechai'] . " AL " . $GLOBALS['fechaf'], 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(25), 6, 'Codprod', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(53), 6, utf8_decode('Descripción'), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(33), 6, 'Marca', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(30), 6, 'Fecha', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(21), 6, 'Costos', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(24), 6, 'Cantidad', 1, 1, 'C', true);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 7);

$pdf->SetWidths($width);

$datos = $historico->get_historicocostos_por_rango($fechai, $fechaf);
$num = count($datos);

foreach ($datos as $i) {

    $pdf->Row(
        array(
            $i['codprod'],
            utf8_decode($i['descrip']),
            $i['marca'],
            date("d/m/Y", strtotime($i['fechae'])),
            Strings::rdecimal($i['costo'], 2),
            $i['cantidad']
        )
    );
}
$pdf->Output();

?>