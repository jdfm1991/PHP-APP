<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("sellin_modelo.php");

//INSTANCIAMOS EL MODELO
$sellin = new sellin();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$marca = $_GET['marca'];

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
        $this->Cell(40, 10, 'REPORTE DE SELL IN COMPRAS DE ' . date("d/m/Y", strtotime($GLOBALS['fechai'])) . ' AL ' . date("d/m/Y", strtotime($GLOBALS['fechaf'])), 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(20), 6, 'CODPROD', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(60), 6, 'PRODUCTO', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(20), 6, 'COMPRA', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(30), 6, 'DEVOLCOMP', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(30), 6, 'TOTAL', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(30), 6, 'MARCA', 1, 1, 'C', true);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 7);

$pdf->SetWidths($width);

$query =  $sellin->getsellin($fechai, $fechaf, $marca);

foreach ($query as $i) {

    $pdf->Row(
        array(
            $i['coditem'],
            utf8_encode($i['producto']),
            Strings::rdecimal($i['compras'], 2),
            Strings::rdecimal($i['devol'], 2),
            Strings::rdecimal($i['total'],2),
            $i['marca']
        )
    );
}
$pdf->Output();


?>