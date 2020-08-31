<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require('../public/fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("listadeprecio_modelo.php");

//INSTANCIAMOS EL MODELO
$precios = new Listadeprecio();

$depos = $_GET['depos'];
$marcas = $_GET['marcas'];
$orden = $_GET['orden'];
$exis = $_GET['exis'];
$iva = $_GET['iva'];
$cubi = $_GET['cubi'];
$p1 = str_replace("1","1",$_GET['p1']);
$p2 = str_replace("1","2",$_GET['p2']);
$p3 = str_replace("1","3",$_GET['p3']);
$sumap = $_GET['p1'] + $_GET['p2'] + $_GET['p3'];
$sumap2 = $p1 + $p2 + $p3;
$pAux = '';
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

class PDF extends FPDF
{
    var $widths;
    var $aligns;

    // Cabecera de página
    function Header()
    {
        switch ($GLOBALS['cubi']){
            case 0:
                $aux = 1;
                break;
            case 1:
                $aux = 0;
                break;
            default:
                $aux = 0;
        }

        /*calculo del ancho adicional para mantener el orden de las celdas de acuerdo a su seleccion segun las siguientes premisas:
                * para el ancho de las celdas que son dinamicas son p1=25, p2=25, p3=25 y cubi=24
                * si solo aparece visualmente un precio, se suma el ancho de las otras dos mas el cubi
                * si aparece visualmente dos precios, se suma el ancho de una mas el cubi
                * si aparece los 3 precios, solo se suba el cubi
                * si aparecen los 3 precios y el cubi, no se suma nada.

         la suma de ancho adicional se distribuira de la siguiente forma:
                * codigo  = 20%
                * descripcion = 40%
                * marca   = 20%
                * bulto   = 10%
                * paquete = 10%
            TOTAL 100%
        */
        $anchoAdicional = 0;
        switch ($GLOBALS['sumap']) {
            case 1:
                $anchoAdicional += (50*2);// +25+25
                break;
            case 2:
                $anchoAdicional += (25*2);// +25
                break;
            default: /** 0 || 3**/
                $anchoAdicional += 0;// +0
        }
        if ($GLOBALS['cubi'] == 0) {
            $anchoAdicional += 24;
        }

        // Logo
        $this->Image('../public/build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', '', 12);
        // Movernos a la derecha
        $this->Cell(140);
        // Título
        $this->Cell(40, 10, 'REPORTE DE LISTADO DE PRECIOS E INVENTARIO', 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        // titulo de columnas
        $this->Cell(addWidthInArray(18 + ($anchoAdicional*0.20)), 6, 'Codigo', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(65 + ($anchoAdicional*0.40)), 6, utf8_decode('Descripción'), 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(30 + ($anchoAdicional*0.20)), 6, 'Marca', 1, 0, 'C', 0);
        //BULTOS
        $this->Cell(addWidthInArray(18  + ($anchoAdicional*0.10)), 6, 'Bultos', 1, 0, 'C', 0);
        switch ($GLOBALS['sumap']) {
            case 1:
                $this->Cell(addWidthInArray(25), 6, 'Pre '.$GLOBALS['sumap2'].' Bul', 1, 0, 'C', 0);
                break;
            case 2:
                if($GLOBALS['p1'] == 1){ $pAux = $GLOBALS['p1']; }else{ $pAux = $GLOBALS['p2'];}
                $this->Cell(addWidthInArray(25), 6, 'Pre '.$pAux.' Bul', 1, 0, 'C', 0);
                if ($GLOBALS['p3'] == 3){ $pAux = $GLOBALS['p3']; }else{ $pAux = $GLOBALS['p2'];}
                $this->Cell(addWidthInArray(25), 6, 'Pre '.$pAux.' Bul', 1, 0, 'C', 0);
                break;
            default: /** 0 || 3**/
                $this->Cell(addWidthInArray(25), 6, 'Pre 1 Bul', 1, 0, 'C', 0);
                $this->Cell(addWidthInArray(25), 6, 'Pre 2 Bul', 1, 0, 'C', 0);
                $this->Cell(addWidthInArray(25), 6, 'Pre 3 Bul', 1, 0, 'C', 0);
        }
        //PAQUETES
        $this->Cell(addWidthInArray(18  + ($anchoAdicional*0.10)), 6, 'Paquete', 1, 0, 'C', 0);
        switch ($GLOBALS['sumap']) {
            case 1:
                $this->Cell(addWidthInArray(25), 6, 'Pre '.$GLOBALS['sumap2'].' Paq', 1, $aux, 'C', 0);
                break;
            case 2:
                if($GLOBALS['p1'] == 1){ $pAux = $GLOBALS['p1']; }else{ $pAux = $GLOBALS['p2'];}
                $this->Cell(addWidthInArray(25), 6, 'Pre '.$pAux.' Paq', 1, 0, 'C', 0);
                if ($GLOBALS['p3'] == 3){ $pAux = $GLOBALS['p3']; }else{ $pAux = $GLOBALS['p2'];}
                $this->Cell(addWidthInArray(25), 6, 'Pre '.$pAux.' Paq', 1, $aux, 'C', 0);
                break;
            default: /** 0 || 3**/
                $this->Cell(addWidthInArray(25), 6, 'Pre 1 Paq', 1, 0, 'C', 0);
                $this->Cell(addWidthInArray(25), 6, 'Pre 2 Paq', 1, 0, 'C', 0);
                $this->Cell(addWidthInArray(25), 6, 'Pre 3 Paq', 1, $aux, 'C', 0);
        }
        if ($GLOBALS['cubi'] == 1) {
            $this->Cell(addWidthInArray(24), 6, 'Cubicaje', 1, 1, 'C', 0);
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

$query = $precios->getListadeprecios($marcas, $depos, $exis, $orden);
$num = count($query);

foreach ($query as $x) {
    $j = 0;
    if ($x['esexento']) {
        $precio1 = $x['precio1'] * $iva;
        $precio2 = $x['precio2'] * $iva;
        $precio3 = $x['precio3'] * $iva;
        $preciou1 = $x['preciou1'] * $iva;
        $preciou2 = $x['preciou2'] * $iva;
        $preciou3 = $x['preciou3'] * $iva;
    } else {
        $precio1 = $x['precio1'];
        $precio2 = $x['precio2'];
        $precio3 = $x['precio3'];
        $preciou1 = $x['preciou1'];
        $preciou2 = $x['preciou2'];
        $preciou3 = $x['preciou3'];
    }

    addInfoInArray($x['codprod']);
    addInfoInArray(utf8_decode($x['descrip']));
    addInfoInArray($x['marca']);
    //BULTOS
    addInfoInArray(round($x['existen']));
    switch ($sumap) {
        case 1:
            if ($x['esexento'] == 0) { addInfoInArray( number_format($x['precio'. $sumap2 ]* $iva, 2, ",", ".") ); } else { addInfoInArray( number_format($x['precio'. $sumap2 ], 2, ",", ".") ); }
            break;
        case 2:
            if ($p1 == 1) { addInfoInArray( number_format($precio1, 2, ",", ".") ); } else { addInfoInArray( number_format($precio2, 2, ",", ".") ); }
            if ($p3 == 3) { addInfoInArray( number_format($precio3, 2, ",", ".") ); } else { addInfoInArray( number_format($precio2, 2, ",", ".") ); }
            break;
        default: /** 0 || 3**/
            addInfoInArray(number_format($precio1, 2, ",", "."));
            addInfoInArray(number_format($precio2, 2, ",", "."));
            addInfoInArray(number_format($precio3, 2, ",", "."));
    }
    addInfoInArray(round($x['exunidad']));
    switch ($sumap) {
        case 1:
            if ($x['esexento'] == 0) { addInfoInArray( number_format($x['preciou'. $sumap2 ]* $iva, 2, ",", ".") ); } else { addInfoInArray( number_format($x['preciou'. $sumap2 ], 2, ",", "." ) ); }
            break;
        case 2:
            if ($p1 == 1) { addInfoInArray( number_format($preciou1, 2, ",", ".") ); } else { addInfoInArray( number_format($preciou2, 2, ",", ".") ); }
            if ($p3 == 3) { addInfoInArray( number_format($preciou3, 2, ",", ".") ); } else { addInfoInArray( number_format($preciou2, 2, ",", ".") ); }
            break;
        default: /** 0 || 3**/
            addInfoInArray(number_format($preciou1, 2, ",", "."));
            addInfoInArray(number_format($preciou2, 2, ",", "."));
            addInfoInArray(number_format($preciou3, 2, ",", "."));
    }
    if ($cubi == 1) {
        addInfoInArray($x['cubicaje']);
    }
    $pdf->Row($info);
}
$pdf->Ln(10);
$pdf->Cell(335, 10, 'Total de Productos:  '.$num, 0, 1, 'C');

$pdf->Output();
