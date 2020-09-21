<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require('../public/fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("facturassindespachar_modelo.php");

//INSTANCIAMOS EL MODELO
$factsindes = new FacturaSinDes();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$convend = $_GET['vendedores'];
$tipo = $_GET['tipo'];
$check = hash_equals("true", $_GET['check']);
$hoy = date("d-m-Y");

$i = 0;
$j = 0;
$documentsize = 'Legal';
$width = array();
$info = array();

// Da igual el formato de las fechas (dd-mm-aaaa o aaaa-mm-dd),
function diasEntreFechas($fechainicio, $fechafin){
    return ((strtotime($fechafin)-strtotime($fechainicio))/86400);
}

function addWidthInArray($num){
    $GLOBALS['width'][$GLOBALS['i']] = $num;
    $GLOBALS['i'] = $GLOBALS['i'] + 1;
    return $num;
}

function addInfoInArray($info){
    $GLOBALS['info'][$GLOBALS['j']] = $info;
    $GLOBALS['j'] = $GLOBALS['j'] + 1;
}

class PDF extends FPDF
{
    var $widths;
    var $aligns;

    // Cabecera de página
    function Header()
    {
        /*calculo del ancho adicional para mantener el orden de las celdas de acuerdo a su seleccion segun las siguientes premisas:
                * para el ancho de las celdas que son dinamicas son Fecha Despacho=34, DíasTrans=21, TPromEsti=23 y %Oportunidad=28
                * si esta el check de ver despachadas = false, se suma el ancho de las celdas mencionadas
                * en caso contrario, si esta el check en true, no se suma

         la suma de ancho adicional se distribuira de la siguiente forma:
                * Documento = 5%
                * Fecha Emisión = 5%
                * Código    = 10%
                * Cliente   = 40%
                * DíasHastHoy = 5%
                * Cant Bult = 5%
                * Cant Paq  = 5%
                * Monto Bs  = 20%
                * EDV       = 5%
            TOTAL 100%
        */
        $anchoAdicional = 0;
        switch ($GLOBALS['check']) {
            case true:
                $titulo = 'REPORTE DE FACTURAS DESPACHADAS DEL ' . $GLOBALS['fechai'] . ' AL ' . $GLOBALS['fechaf'];
                $anchoAdicional += (0);// +0
                break;
            case false:
                $titulo = 'REPORTE DE FACTURAS SIN DESPACHAR DEL ' . $GLOBALS['fechai'] . ' AL ' . $GLOBALS['fechaf'];
                $anchoAdicional += (106);// +34+21+23+28
                break;
        }

        // Logo
        $this->Image('../public/build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', '', 12);
        // Movernos a la derecha
        $this->Cell(140);
        // Título
        $this->Cell(40, 10, $titulo, 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        // titulo de columnas
        $this->Cell(addWidthInArray(24 + ($anchoAdicional*0.05)), 6, 'Documento', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(30 + ($anchoAdicional*0.05)), 6, utf8_decode('Fecha Emisión'), 1, 0, 'C', 0);
        if($GLOBALS['check']) {
            $this->Cell(addWidthInArray(34), 6, 'Fecha Despacho', 1, 0, 'C', 0);
            $this->Cell(addWidthInArray(21), 6, utf8_decode('DíasTrans'), 1, 0, 'C', 0);
        }
        $this->Cell(addWidthInArray(24 + ($anchoAdicional*0.10)), 6, utf8_decode('Código'), 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(38 + ($anchoAdicional*0.40)), 6, 'Cliente', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(26 + ($anchoAdicional*0.05)), 6, utf8_decode('DíasHastHoy'), 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(19 + ($anchoAdicional*0.05)), 6, 'Cant Bult', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(19 + ($anchoAdicional*0.05)), 6, 'Cant Paq', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(30 + ($anchoAdicional*0.20)), 6, 'Monto Bs', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(14 + ($anchoAdicional*0.05)), 6, 'EDV', 1, ($GLOBALS['check']) ? 0 : 1, 'C', 0);
        if($GLOBALS['check']) {
            $this->Cell(addWidthInArray(23), 6, 'TPromEsti', 1, 0, 'C', 0);
            $this->Cell(addWidthInArray(28), 6, '%Oportunidad', 1, 1, 'C', 0);
        }
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
$pdf->SetFont('Arial', '', 8);

$pdf->SetWidths($width);

$query = $factsindes->getFacturas($tipo, $fechai, $fechaf, $convend, $check);
$num = count($query);
$suma_bulto = 0;
$suma_paq = 0;
$suma_monto = 0;
$porcent = 0;

foreach ($query as $x) {
    $j = 0;

    if($check) {
        $calcula = 0;
        if (round(diasEntreFechas(date("d-m-Y", strtotime($x["FechaE"])),date("d-m-Y", strtotime($x["fechad"])))) != 0)
            $calcula = (2 / round(diasEntreFechas(date("d-m-Y", strtotime($x["FechaE"])),date("d-m-Y", strtotime($x["fechad"])))))*100;

        if ($calcula > 100)
            $calcula = 100;

        $porcent += $calcula;
    }

    addInfoInArray($x['NumeroD']);
    addInfoInArray(date("d/m/Y", strtotime($x["FechaE"])));
    if ($check) {
        addInfoInArray(date("d/m/Y", strtotime($x["fechad"])));
        addInfoInArray(round(diasEntreFechas(date("d-m-Y", strtotime($x["FechaE"])),date("d-m-Y", strtotime($x["fechad"])))));
    }
    addInfoInArray($x['CodClie']);
    addInfoInArray(utf8_decode($x['Descrip']));
    addInfoInArray(round(diasEntreFechas(date("d-m-Y", strtotime($x["FechaE"])), $hoy)));
    addInfoInArray(round($x['Bult']));
    addInfoInArray(round($x['Paq']));
    addInfoInArray(number_format($x["Monto"], 1, ",", ".")); $suma_monto += $x["Monto"];
    addInfoInArray($x['CodVend']);
    if ($check) {
        addInfoInArray(2);
        addInfoInArray(number_format($calcula, 1, ",", ".") . "%");
    }
    $pdf->Row($info);
}

$j = 0;
$pdf->SetFont('Arial', 'B', ($check) ? 9 : 10);
addInfoInArray('');
addInfoInArray('');
if ($check) {
    addInfoInArray('');
    addInfoInArray('');
}
addInfoInArray('');
addInfoInArray('Total de Documentos:  '. $num);
addInfoInArray('');
addInfoInArray('');
addInfoInArray('');
addInfoInArray('Monto Total: ' . number_format($suma_monto, 2, ",", "."));
addInfoInArray('');
if ($check) {
    addInfoInArray('');
    addInfoInArray('% Oportunidad Total: ' . number_format(($porcent / count($query)), 2, ",", ".") . ' %');
}
$pdf->Row($info);

$pdf->Output();
