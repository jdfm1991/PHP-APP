<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("reportecompras_modelo.php");

//INSTANCIAMOS EL MODELO
$reporte = new ReporteCompras();

$fechai = $_GET['fechai'];
$marca = $_GET['marca'];
$n = $_GET['n'];
$v = $_GET['v'];

$hoy = date(FORMAT_DATE);

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
        $this->Image(PATH_LIBRARY.'build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 12);
        // Movernos a la derecha
        $this->Cell(140);
        // Título
        $this->Cell(40, 10, 'REPORTE DE COMPRAS DE ' . strtoupper($GLOBALS['meses'][intval($GLOBALS['mes'])]) ." " . $GLOBALS['ano'], 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(6, 18, utf8_decode(Strings::titleFromJson('#')), 1, 0, 'C', true);
        $this->Cell(20, 18, substr(utf8_decode(Strings::titleFromJson('codigo_prod')), 0, 7), 1, 0, 'C', true);
        $this->Cell(38, 18, utf8_decode(Strings::titleFromJson('descrip_prod')), 1, 0, 'C', true);
        $this->MultiCell2(20, 9, utf8_decode(Strings::titleFromJson('display_por_bulto')), 1, 0, 'C', true);
        $this->Ln(-9);
        $this->Cell(84);
        $this->MultiCell2(44, 12, utf8_decode(Strings::titleFromJson('ultimo_precio_compra')), 1, 0, 'C', true);
//        $this->Ln(-6);
        $this->Cell(128);
        $this->Cell(13, 18, substr(utf8_decode(Strings::titleFromJson('porcentaje_rentabilidad')),0,5), 1, 0, 'C', true);
        $this->MultiCell2(34, 6, utf8_decode(Strings::titleFromJson('fecha_penultima_compra')), 1, 0, 'C', true);
        $this->Ln(-6);
        $this->Cell(175);
        $this->MultiCell2(34, 6, utf8_decode(Strings::titleFromJson('fecha_ultima_compra')), 1, 0, 'C', true);
        $this->Ln(-6);
        $this->Cell(209);
        $this->MultiCell2(30, 6, utf8_decode(Strings::titleFromJson('ventas_mes_anterior')), 1, 0, 'C', true);
        $this->Ln(-6);
        $this->Cell(239);
        $this->MultiCell2(15, 4.5, utf8_decode(Strings::titleFromJson('ventas_total_ult_mes')), 1, 0, 'C', true);
        $this->Ln(-13.5);
        $this->Cell(254);
        $this->MultiCell2(20, 6, utf8_decode(Strings::titleFromJson('existencia_actual_bultos')), 1, 0, 'C', true);
        $this->Ln(-12);
        $this->Cell(274);
        $this->MultiCell2(20, 9, utf8_decode(Strings::titleFromJson('dias_inventario')), 1, 0, 'C', true);
        $this->Ln(-9);
        $this->Cell(294);
        $this->Cell(20, 18, utf8_decode(Strings::titleFromJson('sugerido')), 1, 0, 'C', true);
        $this->Cell(14, 18, utf8_decode(Strings::titleFromJson('pedido')), 1, 1, 'C', true);

        $this->Ln(-6);
        $this->Cell(84);
        $this->Cell(22, 6, utf8_decode(Strings::titleFromJson('display')), 1, 0, 'C', true);
        $this->Cell(22, 6, utf8_decode(Strings::titleFromJson('bulto')), 1, 0, 'C', true);
        $this->Cell(13);
        $this->Cell(22, 6, utf8_decode(Strings::titleFromJson('fecha')), 1, 0, 'C', true);
        $this->Cell(12, 6, utf8_decode(Strings::titleFromJson('bultos')), 1, 0, 'C', true);
        $this->Cell(22, 6, utf8_decode(Strings::titleFromJson('fecha')), 1, 0, 'C', true);
        $this->Cell(12, 6, utf8_decode(Strings::titleFromJson('bultos')), 1, 0, 'C', true);
        $this->Cell(7.5, 6, '1', 1, 0, 'C', true);
        $this->Cell(7.5, 6, '2', 1, 0, 'C', true);
        $this->Cell(7.5, 6, '3', 1, 0, 'C', true);
        $this->Cell(7.5, 6, '4', 1, 1, 'C', true);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation, $GLOBALS['documentsize']);
    }

    function Row($data, $numberColumn = [], $fill = false)
    {
        //Calculate the height of the row
        $nb = 0;
        $j = 0;
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
            if($j < count($numberColumn) and $i == $numberColumn[$j]){
                $this->SetFillColor(255,57,57);
                $this->MultiCell($w, 5, $data[$i], 0, $a, $fill);
                $j++;
            } else {
                $this->MultiCell($w, 5, $data[$i], 0, $a);
            }

            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
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
                Strings::rdecimal($row[0]["displaybultos"], 0),
                Strings::rdecimal($row[0]["costodisplay"], 2),
                Strings::rdecimal($row[0]["costobultos"], 2),
                Strings::rdecimal($row[0]["rentabilidad"], 2) . "  %",
                (count($compra) > 0) ? date(FORMAT_DATE,strtotime($compra[0]["fechapenultimacompra"])) : '-',
                (count($compra) > 0) ? Strings::rdecimal($compra[0]["bultospenultimacompra"], 0) : 0,
                (count($compra) > 0) ? date(FORMAT_DATE,strtotime($compra[0]["fechaultimacompra"])) : '-',
                (count($compra) > 0) ? Strings::rdecimal($compra[0]["bultosultimacompra"], 0) : 0,
                Strings::rdecimal($row[0]["semana1"], 0),
                Strings::rdecimal($row[0]["semana2"], 0),
                Strings::rdecimal($row[0]["semana3"], 0),
                Strings::rdecimal($row[0]["semana4"], 0),
                Strings::rdecimal($row[0]["totalventasmesanterior"], 0),
                Strings::rdecimal($row[0]["bultosexistentes"], 2),
                Strings::rdecimal($row[0]["diasdeinventario"], 0),
                Strings::rdecimal($row[0]["sugerido"], 2),
                $n[$key]
            ),
            [6],
            ($row[0]["rentabilidad"] > 30) ? true : false
        );
        $num++;
    }
}
$pdf->Output();
