<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require('../public/fpdf/fpdf.php');

//LLAMAMOS AL MODELO
require_once("costodeinventario_modelo.php");

//INSTANCIAMOS EL MODELO
$costo = new CostodeInventario();

//obtenemos la marca seleccionada enviada por get
$marca = $_GET['marca'];

//verificamos si existe al menos 1 deposito selecionado
//y se crea el array.
if(isset($_GET['depo'])){
    $numero = $_GET['depo'];
} else {
    $numero = array();
}

//se contruye un string para listar los depositvos seleccionados
//en caso que no haya ninguno, sera vacio
$edv = "";
if(count($numero)>0) {
    foreach ($numero AS $i) {
        $edv .= "'" . $i . "',";
    }
}

$costos = 0;
$costos_p = 0;
$precios = 0;
$bultos = 0;
$paquetes = 0;
$total_costo_bultos = 0;
$total_costo_paquetes = 0;
$total_tara = 0;

//array of space in cells
$j = 0;
$width = array();
$documentsize = 'Legal';
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
        $this->Image('../public/build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', '', 11);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(40, 10, 'REPORTE DE COSTOS E INVENTARIO', 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        // titulo de columnas
        $this->Cell(addWidthInArray(20), 6, 'Codprod', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(57), 6, utf8_decode('Descripción'), 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(22), 6, 'Marca', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(29), 6, 'Costo Bultos', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(29), 6, 'Costo Unid.', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(29), 6, 'Precio', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(19), 6, 'Bultos', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(19), 6, 'Paq.', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(40), 6, 'Total Bs Costo Bultos', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(45), 6, 'Total Bs Costo Unidades', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(24), 6, 'Tara', 1, 1, 'C', 0);

    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation, $GLOBALS['documentsize']);
    }

    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw =& $this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L', $documentsize);
$pdf->SetFont('Arial', '', 7);

$pdf->SetWidths($width);

//realiza la consulta con marca y almacenes
$query = $costo->getCostosdEinventario($edv, $marca);

foreach ($query as $i) {

    if ($i['display'] == 0) {
        $cdisplay = 0;
    } else {
        $cdisplay = $i['costo'] / $i['display'];
    }

    $pdf->Row(
        array(
            $i['codprod'],
            utf8_decode($i['descrip']),
            $i['marca'],
            number_format($i['costo'],2, ",", "."),
            number_format($cdisplay,2, ",", "."),
            number_format($i['precio'],2, ",", "."),
            number_format($i['bultos'],2, ",", "."),
            number_format($i['paquetes'],2, ",", "."),
            number_format($i['costo'] * $i['bultos'],2, ",", "."),
            number_format($cdisplay * $i['paquetes'],2, ",", "."),
            number_format($i['tara'],2, ",", ".")
        )
    );

    $costos += $i['costo'];
    $costos_p += $cdisplay;
    $precios += $i['precio'];
    $bultos += $i['bultos'];
    $paquetes += $i['paquetes'];
    $total_costo_bultos += ($i['costo'] * $i['bultos']);
    $total_costo_paquetes += ($cdisplay * $i['paquetes']);
    $total_tara += $i['tara'];
}
$pdf->SetFont('Arial', '', 9);
$pdf->Row(
    array(
        '', '', 'TOTALES: ',
        number_format($costos,2, ",", "."),
        number_format($costos_p,2, ",", "."),
        number_format($precios,2, ",", "."),
        number_format($bultos,2, ",", "."),
        number_format($paquetes,2, ",", "."),
        number_format($total_costo_bultos,2, ",", "."),
        number_format($total_costo_paquetes,2, ",", "."),
        number_format($total_tara,2, ",", ".")
    )
);

$pdf->Output();

?>