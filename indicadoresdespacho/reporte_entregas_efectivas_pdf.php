<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require_once ('../public/fpdf/fpdf.php');
/* require_once ( '../public/jpgraph/src/jpgraph.php' ); 
require_once ( '../public/jpgraph/src/jpgraph_bar.php' ); 
require_once ( '../public/jpgraph/src/jpgraph_line.php' ); 
 */

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("indicadoresdespacho_modelo.php");
require_once("../choferes/choferes_modelo.php");

//INSTANCIAMOS EL MODELO
$indicadores = new InidicadoresDespachos();
$choferes = new Choferes();

$tipoPeriodo = $_GET['tipoPeriodo'];
$periodo   = $_GET['periodo'];
$chofer_id = $_GET['chofer'];

switch($tipoPeriodo) {
    case "Anual":
        $fechai = $periodo."-01-01";
        $fechaf = $periodo."-12-31";
        break;
    case "Mensual":
        $fechai = $periodo."-01";
        $fechaf = date("Y-m-t", strtotime($periodo));
        break;  
}

$documentsize = 'Legal';
$formato_fecha = "d-m-Y";
$cant_ordenes_despacho_max = 22;
$cant_fact_sinliquidar_max = 26;
$ancho_tabla_max = 19;
$row = 0;

$j = 0;
$width = array();
function addWidthInArray($num){
    $GLOBALS['width'][$GLOBALS['j']] = $num;
    $GLOBALS['j'] = $GLOBALS['j'] + 1;
    return $num;
}


/* function graficar($contar, $contarf,$promedio,$nombreGrafico = NULL,$ubicacionTamamo = array(),$titulo = NULL){
    // Create the graph. These two calls are always required 
    $graph = new Graph ( 850 , 850 , 'auto' ); 
    $graph -> SetScale ( "textlin",0,50 );  

    $graph -> SetMargin (50 , 50 , 80 , 100 ); 

    $graph -> yaxis -> SetTickPositions (array( 0,10,20,30,40,50,60,70,80,90,100 ), array( 5,15,25,35,45,55,65,75,80,85,95 ));  
    $graph -> SetBox ( false ); 
    $graph->yaxis-> title->SetFont (FF_VERDANA, FS_NORMAL); 
    $graph->xaxis-> title->SetFont (FF_VERDANA, FS_NORMAL);
    $graph -> xaxis -> title->Set("Fecha","left");
    $graph -> yaxis -> title->Set("Despachos","middle");
    $graph->xaxis->SetTitleMargin(25);
    $graph->yaxis->SetTitleMargin(35);


    $graph -> ygrid -> SetFill ( false ); 
    //$graph -> xaxis -> SetTickLabels (array( 'A' , 'B' , 'C' , 'D' )); 
    $graph -> yaxis -> HideLine ( false ); 
    $graph -> yaxis -> HideTicks ( false , false ); 
    // Setup month as labels on the X-axis 
    $graph -> xaxis -> SetTickLabels ( $contarf ); 

    // Create the bar plots 
    $b1plot = new BarPlot ( $contar ); 
    $b1plot->value->show();
    $lplot = new LinePlot ( $promedio ); 

    // ...and add it to the graPH 
    $graph -> Add ( $b1plot ); 
    $graph -> Add ( $lplot ); 

    $b1plot -> SetColor ( "white" ); 
    $b1plot -> SetFillGradient ( "#000066" , "white" , GRAD_LEFT_REFLECTION ); 
    $b1plot -> SetWidth ( 25 );  
    $b1plot -> SetLegend ( "Despachos" );  

    $lplot -> SetBarCenter (); 
    $lplot -> SetColor ( "red" ); 
    $lplot -> SetLegend ( "Promedio ".$promedio[0]); 
    $lplot -> mark -> SetWidth ( 15 ); 
    $lplot -> mark -> setColor ( "red" ); 
    $lplot -> mark -> setFillColor ( "red" ); 

    $graph -> legend -> SetFrameWeight ( 1 ); 
    $graph -> legend -> SetColumns ( 6 ); 
    $graph -> legend -> SetColor ( '#4E4E4E' , '#00A78A' ); 

    $graph -> title -> Set ( "Entregas Efectivas" );

    // Display the graph 
    $x = $ubicacionTamamo[0];
     $y = $ubicacionTamamo[1]; 
     $ancho = $ubicacionTamamo[2];  
     $altura = $ubicacionTamamo[3];  
       
    // Display the graph 
    $graph->Stroke("$nombreGrafico.png"); 
    $this->Image("$nombreGrafico.png",$x,$y,$ancho,$altura); 
} */


class PDF extends FPDF
{
    var $widths;
    var $aligns;

    // Cabecera de página
    function Header()
    {
        $this->SetFont('Arial','B',12);
        // Logo
        $this->Image('../public/build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial','BU',12);
		$this->Cell(330,13,utf8_decode('ENTREGAS EFECTIVAS'),'TLRB',0,'C');
		$this->SetFont('Arial','',8);
		$this->Cell(-13,4,utf8_decode('Codigo: FOR-TRA-09-R0'),'',1,'R');
		$this->Cell(225,10,utf8_decode('Fecha: 25/08/14'),'',1,'R');
		$this->SetAligns(array('L','L','R','L','R','L'));
        /* $this->SetBorders(array('LT','T','T','T','T','TR')); 
        $this->Rect(40,30,165,60);*/
        // Salto de línea
        $this->Ln(20);
        // titulo de columnas
        $this->Cell(addWidthInArray(25), 6, 'Codprod', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(53), 6, utf8_decode('Descripción'), 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(33), 6, 'Marca', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(30), 6, 'Fecha', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(21), 6, 'Costos', 1, 0, 'C', 0);
        $this->Cell(addWidthInArray(24), 6, 'Cantidad', 1, 1, 'C', 0);
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

/* $datos = $historico->get_historicocostos_por_rango($fechai, $fechaf);
$num = count($datos);

foreach ($datos as $i) {

    $pdf->Row(
        array(
            $i['codprod'],
            utf8_decode($i['descrip']),
            $i['marca'],
            date("d/m/Y", strtotime($i['fechae'])),
            number_format($i['costo'], 2, ",", "."),
            $i['cantidad']
        )
    );
} */
$pdf->Output();

?>