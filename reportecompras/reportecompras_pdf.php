<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require('../public/fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("reportecompras_modelo.php");

//INSTANCIAMOS EL MODELO
$reporte = new ReporteCompras();

$fechai = $_GET['fechai'];
$marca = $_GET['marca'];
$n = $_GET['n'];
$v = $_GET['v'];

$hoy = date("d-m-Y");

$i = 0;
$j = 0;
$documentsize = 'Legal';
$width = array();
$info = array();

function addWidthInArray($num){
    $GLOBALS['width'][$GLOBALS['i']] = $num;
    $GLOBALS['i'] = $GLOBALS['i'] + 1;
    return $num;
}

function addInfoInArray($info){
    $GLOBALS['info'][$GLOBALS['j']] = $info;
    $GLOBALS['j'] = $GLOBALS['j'] + 1;
}

$separa = explode("-", $fechai);
$ano = $separa[0];
$mes = $separa[1];
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");


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
        $this->SetFont('Arial', 'B', 10);
        // Movernos a la derecha
        $this->Cell(140);
        // Título
        $this->Cell(40, 10, 'REPORTE DE COMPRAS DE ' . strtoupper($GLOBALS['meses'][intval($GLOBALS['mes'])]) ." " . $GLOBALS['ano'], 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        // titulo de columnas
        $this->Cell(6, 18, '#', 1, 0, 'C', 0);
        $this->Cell(20, 18, 'Codigo', 1, 0, 'C', 0);
        $this->Cell(38, 18, 'Descripcion', 1, 0, 'C', 0);
        $this->MultiCell2(20, 9, 'Display x Bulto', 1, 0, 'C', 0);
        $this->Ln(-9);
        $this->Cell(84);
        $this->MultiCell2(44, 12, 'Ultimo precio de compra', 1, 0, 'C', 0);
//        $this->Ln(-6);
        $this->Cell(128);
        $this->Cell(13, 18, '% Rent', 1, 0, 'C', 0);
        $this->MultiCell2(34, 6, 'Fecha penultima compra', 1, 0, 'C', 0);
        $this->Ln(-6);
        $this->Cell(175);
        $this->MultiCell2(34, 6, 'Fecha ultima compra', 1, 0, 'C', 0);
        $this->Ln(-6);
        $this->Cell(209);
        $this->MultiCell2(30, 6, 'Ventas mes aterior', 1, 0, 'C', 0);
        $this->Ln(-6);
        $this->Cell(239);
        $this->MultiCell2(15, 4.5, 'Venta total ultimo mes', 1, 0, 'C', 0);
        $this->Ln(-13.5);
        $this->Cell(254);
        $this->MultiCell2(20, 6, 'Existencia Actual Bultos', 1, 0, 'C', 0);
        $this->Ln(-12);
        $this->Cell(274);
        $this->MultiCell2(20, 9, 'Dias de Inventario', 1, 0, 'C', 0);
        $this->Ln(-9);
        $this->Cell(294);
        $this->Cell(20, 18, 'Sugerido', 1, 0, 'C', 0);
        $this->Cell(14, 18, 'Pedido', 1, 1, 'C', 0);

        $this->Ln(-6);
        $this->Cell(84);
        $this->Cell(22, 6, 'Display', 1, 0, 'C', 0);
        $this->Cell(22, 6, 'Bulto', 1, 0, 'C', 0);
        $this->Cell(13);
        $this->Cell(22, 6, 'Fecha', 1, 0, 'C', 0);
        $this->Cell(12, 6, 'Bultos', 1, 0, 'C', 0);
        $this->Cell(22, 6, 'Fecha', 1, 0, 'C', 0);
        $this->Cell(12, 6, 'Bultos', 1, 0, 'C', 0);
        $this->Cell(7.5, 6, '1', 1, 0, 'C', 0);
        $this->Cell(7.5, 6, '2', 1, 0, 'C', 0);
        $this->Cell(7.5, 6, '3', 1, 0, 'C', 0);
        $this->Cell(7.5, 6, '4', 1, 1, 'C', 0);
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

    function Row($data, $numberColumn = 0, $fill = false)
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
            if($i == $numberColumn){
                $this->SetFillColor(255,57,57);
                $this->MultiCell($w, 5, $data[$i], 0, $a, $fill);
            } else {
                $this->MultiCell($w, 5, $data[$i], 0, $a);
            }
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
$pdf->SetFont('Arial', '', 8);

$pdf->SetWidths(array(6, 20, 38, 20, 22, 22, 13, 22, 12, 22, 12, 7.5, 7.5, 7.5, 7.5, 15, 20, 20, 20, 14));

$num=0;
foreach ($v as $key=>$coditem)
{
    if(!hash_equals("", $n[$key] ))
    {
        $row = $reporte->get_reportecompra_por_codprod($coditem, $fechai);
        $compra = $reporte->get_ultimascompras_por_codprod($coditem);

        /** cargado de las filas **/
        $pdf->Row(
            array(
                $num+1,
                $row[0]["codproducto"],
                $row[0]["descrip"],
                number_format($row[0]["displaybultos"], 0, ",", "."),
                number_format($row[0]["costodisplay"], 2, ",", "."),
                number_format($row[0]["costobultos"], 2, ",", "."),
                number_format($row[0]["rentabilidad"], 1, ",", ".") . "  %",
                (count($compra) > 0) ? date("d/m/Y",strtotime($compra[0]["fechapenultimacompra"])) : 0,
                (count($compra) > 0) ? number_format($compra[0]["bultospenultimacompra"], 0, ",", ".") : 0,
                (count($compra) > 0) ? date("d/m/Y",strtotime($compra[0]["fechaultimacompra"])) : 0,
                (count($compra) > 0) ? number_format($compra[0]["bultosultimacompra"], 0, ",", ".") : 0,
                number_format($row[0]["semana1"], 0, ",", "."),
                number_format($row[0]["semana2"], 0, ",", "."),
                number_format($row[0]["semana3"], 0, ",", "."),
                number_format($row[0]["semana4"], 0, ",", "."),
                number_format($row[0]["totalventasmesanterior"], 0, ",", "."),
                number_format($row[0]["bultosexistentes"], 1, ",", "."),
                number_format($row[0]["diasdeinventario"], 0, ",", "."),
                number_format($row[0]["sugerido"], 1, ",", "."),
                $n[$key]
            ),
            6,
            (intval($row[0]["rentabilidad"]) > 30) ? true : false
        );
        $num++;
    }
}
$pdf->Output();
