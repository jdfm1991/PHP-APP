<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("auditoriadecomisiones_modelo.php");

//INSTANCIAMOS EL MODELO
$auditoriadecomisiones = new Auditoriadecomisiones();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$codvend = $_GET['vendedor'];

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
        $this->Cell(40, 10, 'REPORTE DE AUDITORIA DE CAMBIOS EN COMISIONES  ' . date(FORMAT_DATE, strtotime($GLOBALS['fechai'])) . ' AL ' . date(FORMAT_DATE, strtotime($GLOBALS['fechaf'])), 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        $this->SetFont('Arial', '', 9);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(24), 6, utf8_decode(Strings::titleFromJson('campo_mod')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(55), 6, utf8_decode(Strings::titleFromJson('antes')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(18), 6, utf8_decode(Strings::titleFromJson('despues')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(50), 6, utf8_decode(Strings::titleFromJson('direccion')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(21), 6, utf8_decode(Strings::titleFromJson('diferencia')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(24), 6, utf8_decode(Strings::titleFromJson('fecha_hora')), 1, 1, 'C', true);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 7);

$pdf->SetWidths($width);

$query = $auditoriacomisiones->getauditoriacomisiones($codvend, $fechai, $fechaf);


foreach ($datos as $row) {
    //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
    $sub_array = array();

    $campo = "";
    switch ($row["campo"]) {
        case 1:
          $campo = "Cobranza 0 a 7 días";
          break;
        case 2:
          $campo = "Comisión 0 a 7 días";
          break;
        case 3:
          $campo = "Cobranza 8 a 14 días";
          break;
        case 4:
          $campo = "Comisión 8 a 14 días";
          break;
        case 5:
          $campo = "Cobranza 15 a 21 días";
          break;
        case 6:
          $campo = "Comisión 15 a 21 días";
          break;
        case 7:
          $campo = "Cobranza mayor a 21 días";
          break;
        case 8:
          $campo = "Activación de Clintes";
          break;
        case 9:
          $campo = "Efectividad de Facturación (EVA)";
          break;
      }

    $sub_array[] = $campo;
    $sub_array[] = Strings::rdecimal($row["antes"]);
    $sub_array[] = Strings::rdecimal($row["despu"]);
    $sub_array[] = Strings::rdecimal($row["despu"]-$row["antes"], 2);
    $sub_array[] = utf8_encode($row["descrip"]);
    $sub_array[] = date(FORMAT_DATETIME2, strtotime($row["fechah"]));

    $data[] = $sub_array;

}
$pdf->Ln(10);
$pdf->Output(); 
?>