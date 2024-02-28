<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("listadepreciodivisas_modelo.php");

//INSTANCIAMOS EL MODELO
$precios = new Listadepreciodivisas();

$depos = $_GET['depos'];
$marcas = $_GET['marcas'];
$orden = $_GET['orden'];
$exis = $_GET['exis'];

$bandera =0;
$linea =15;

$linea2 =40;

$iva =0.16;

/*
$iva = $_GET['iva'];
$cubi = $_GET['cubi'];
$p1 = str_replace("1","1",$_GET['p1']);
$p2 = str_replace("1","2",$_GET['p2']);
$p3 = str_replace("1","3",$_GET['p3']);
$sumap = $_GET['p1'] + $_GET['p2'] + $_GET['p3'];
$sumap2 = $p1 + $p2 + $p3;
$pAux = '';*/
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
$pdf->AddPage('P', $documentsize);
$pdf->SetFont('Arial', '', 8);

$pdf->SetWidths($width);

$query = $precios->getListadeprecios($marcas, $depos, $exis, $orden);
$num = count($query);


foreach ($query as $x) {
     $j = 0;
   /* if ($x['esexento']) {
        $precio1 = $x['preciou1'] * $iva;
        $precio2 = $x['preciou2'] * $iva;
        $precio3 = $x['preciou3'] * $iva;
        $preciou1 = $x['precio1'] * $iva;
        $preciou2 = $x['precio2'] * $iva;
        $preciou3 = $x['precio3'] * $iva;
    } else {*/
        $precio1 = $x['preciou1'];
        $precio2 = $x['preciou2'];
        $precio3 = $x['preciou3'];
        $preciou1 = $x['precio1'];
        $preciou2 = $x['precio2'];
        $preciou3 = $x['precio3'];
    /*}*/

if($precio1>0 and $precio2>0){

    if($bandera <1){


        $pdf->SetXY(15, $linea);
        $pdf->SetFont ('Arial','',10.5);
        $pdf->Image(PATH_LIBRARY.'build/images/logo.png', 86, $linea, 19);
        $pdf->Cell(90, 25,  utf8_decode($x['descrip']), 1, 0); //Celda
        $pdf->SetXY(45, $linea+8);
        $pdf->SetFont ('Arial','B',26);
        $pdf->Cell(90, 25, utf8_decode(number_format($precio1,2).'$'), 0, 1);
        $pdf->SetXY(15, $linea2);
        $pdf->SetFont ('Arial','B',20);
        $pdf->Cell(90, 25, utf8_decode('Más de 3 PAQ'), 1, 0);
        $pdf->SetFont ('Arial','B',32);
        $pdf->SetXY(65, $linea2);
        $pdf->Cell(90, 25, number_format($precio2,2).'$', 0, 1); //Celda

        
            $bandera = $bandera +1;

    }else{

            $pdf->SetXY(110, $linea);
            $pdf->SetFont ('Arial','',10.5);
            $pdf->Image(PATH_LIBRARY.'build/images/logo.png', 181, $linea, 19);
            $pdf->Cell(90, 25, utf8_decode($x['descrip']), 1, 0); //Celda
            $pdf->SetXY(140, $linea+8);
            $pdf->SetFont ('Arial','B',26);
            $pdf->Cell(90, 25, utf8_decode(number_format($precio1,2).'$'), 0, 1);
            $pdf->SetXY(110, $linea2);
            $pdf->SetFont ('Arial','B',20);
            $pdf->Cell(90, 25,utf8_decode('Más de 3 PAQ'), 1, 0); //Celda
            $pdf->SetFont ('Arial','B',32);
            $pdf->SetXY(160, $linea2);
            $pdf->Cell(90, 25,number_format($precio2,2).'$', 0, 1); //Celda

            $bandera =0;
            $linea+=52;
            $linea2+=52;

                if($linea>265){
                 $linea=15;
                }

                if($linea2>290){
                    $linea2=40;
                    $bandera =0;
                    $pdf->AddPage('P', $documentsize);
                }

            

    }

}

    
}
$pdf->Ln(10);

$pdf->Output();
