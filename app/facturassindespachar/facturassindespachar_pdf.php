<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

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
        $this->Image(PATH_LIBRARY.'build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', '', 12);
        // Movernos a la derecha
        $this->Cell(140);
        // Título
        $this->Cell(40, 10, $titulo, 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(24 + ($anchoAdicional*0.05)), 6, 'Documento', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(30 + ($anchoAdicional*0.05)), 6, utf8_decode('Fecha Emisión'), 1, 0, 'C', true);
        if($GLOBALS['check']) {
            $this->Cell(addWidthInArray(34), 6, 'Fecha Despacho', 1, 0, 'C', true);
            $this->Cell(addWidthInArray(21), 6, utf8_decode('DíasTrans'), 1, 0, 'C', true);
        }
        $this->Cell(addWidthInArray(24 + ($anchoAdicional*0.10)), 6, utf8_decode('Código'), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(38 + ($anchoAdicional*0.40)), 6, 'Cliente', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(26 + ($anchoAdicional*0.05)), 6, utf8_decode('DíasHastHoy'), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(19 + ($anchoAdicional*0.05)), 6, 'Cant Bult', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(19 + ($anchoAdicional*0.05)), 6, 'Cant Paq', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(30 + ($anchoAdicional*0.20)), 6, 'Monto Bs', 1, 0, 'C', true);
        $this->Cell(addWidthInArray(14 + ($anchoAdicional*0.05)), 6, 'EDV', 1, ($GLOBALS['check']) ? 0 : 1, 'C', true);
        if($GLOBALS['check']) {
            $this->Cell(addWidthInArray(23), 6, 'TPromEsti', 1, 0, 'C', true);
            $this->Cell(addWidthInArray(28), 6, '%Oportunidad', 1, 1, 'C', true);
        }
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation, $GLOBALS['documentsize']);
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
        if (round(Dates::daysEnterDates(date("d-m-Y", strtotime($x["FechaE"])),date("d-m-Y", strtotime($x["fechad"])))) != 0)
            $calcula = (2 / round(Dates::daysEnterDates(date("d-m-Y", strtotime($x["FechaE"])),date("d-m-Y", strtotime($x["fechad"])))))*100;

        if ($calcula > 100)
            $calcula = 100;

        $porcent += $calcula;
    }

    addInfoInArray($x['NumeroD']);
    addInfoInArray(date("d/m/Y", strtotime($x["FechaE"])));
    if ($check) {
        addInfoInArray(date("d/m/Y", strtotime($x["fechad"])));
        addInfoInArray(round(Dates::daysEnterDates(date("d-m-Y", strtotime($x["FechaE"])),date("d-m-Y", strtotime($x["fechad"])))));
    }
    addInfoInArray($x['CodClie']);
    addInfoInArray(utf8_decode($x['Descrip']));
    addInfoInArray(round(Dates::daysEnterDates(date("d-m-Y", strtotime($x["FechaE"])), $hoy)));
    addInfoInArray(round($x['Bult']));
    addInfoInArray(round($x['Paq']));
    addInfoInArray(Strings::rdecimal($x["Monto"], 1)); $suma_monto += $x["Monto"];
    addInfoInArray($x['CodVend']);
    if ($check) {
        addInfoInArray(2);
        addInfoInArray(Strings::rdecimal($calcula, 1) . "%");
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
addInfoInArray('Monto Total: ' . Strings::rdecimal($suma_monto, 2));
addInfoInArray('');
if ($check) {
    addInfoInArray('');
    addInfoInArray('% Oportunidad Total: ' . Strings::rdecimal(($porcent / count($query)), 2) . ' %');
}
$pdf->Row($info);

$pdf->Output();
