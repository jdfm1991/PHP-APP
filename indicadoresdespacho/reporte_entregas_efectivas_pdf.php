<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");
require_once("../acceso/const.php");

require_once ('../public/fpdf/fpdf.php');
require_once ( '../public/jpgraph4.3.4/src/jpgraph.php' ); 
require_once ( '../public/jpgraph4.3.4/src/jpgraph_bar.php' ); 
require_once ( '../public/jpgraph4.3.4/src/jpgraph_line.php' ); 


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
//$formato_fecha = "d-m-Y";
$formato_fecha = $tipoPeriodo=="Anual" ? 'm-Y' : 'd-m-Y';
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

function addCero($num) {
    if(intval($num)<=9)
        return "0".$num;
    return $num;
}

function graficar($contar, $contarf,$promedio,$nombreGrafico = NULL,$ubicacionTamamo = array(),$titulo = NULL, $pdf){
    // Create the graph. These two calls are always required 
    $graph = new Graph ( 850 , 850 , 'auto' ); 
    $graph->SetScale("textlin",0,50);  

    $graph->SetMargin(50 , 50 , 80 , 100); 

    $graph->yaxis->SetTickPositions(array( 0,10,20,30,40,50,60,70,80,90,100 ), array( 5,15,25,35,45,55,65,75,80,85,95 ));  
    $graph->SetBox(false); 
    $graph->yaxis->title->SetFont(FF_VERDANA, FS_NORMAL); 
    $graph->xaxis->title->SetFont(FF_VERDANA, FS_NORMAL);
    $graph->xaxis->title->Set("Fecha","left");
    $graph->yaxis->title->Set("Despachos","middle");
    $graph->xaxis->SetTitleMargin(25);
    $graph->yaxis->SetTitleMargin(35);


    $graph->ygrid->SetFill(false); 
    //$graph -> xaxis -> SetTickLabels (array( 'A' , 'B' , 'C' , 'D' )); 
    $graph->yaxis->HideLine(false); 
    $graph->yaxis->HideTicks(false , false); 
    // Setup month as labels on the X-axis 
    $graph->xaxis->SetTickLabels($contarf); 

    // Create the bar plots 
    $b1plot = new BarPlot($contar); 
    $b1plot->value->show();
    $lplot = new LinePlot($promedio); 

    // ...and add it to the graPH 
    $graph->Add($b1plot); 
    $graph->Add($lplot); 

    $b1plot->SetColor("white"); 
    $b1plot->SetFillGradient("#000066" , "white" , GRAD_LEFT_REFLECTION); 
    $b1plot->SetWidth(25);  
    $b1plot->SetLegend("Despachos");  

    $lplot->SetBarCenter(); 
    $lplot->SetColor("red"); 
    $lplot->SetLegend("Promedio ".$promedio[0]); 
    $lplot->mark->SetWidth(15); 
    $lplot->mark->setColor("red"); 
    $lplot->mark->setFillColor("red"); 

    $graph->legend->SetFrameWeight(1); 
    $graph->legend->SetColumns(6); 
    $graph->legend->SetColor('#4E4E4E' , '#00A78A'); 

    $graph->title->Set($titulo);

    // Display the graph 
    $x = $ubicacionTamamo[0];
     $y = $ubicacionTamamo[1]; 
     $ancho = $ubicacionTamamo[2];  
     $altura = $ubicacionTamamo[3];  
       
    // Display the graph 
    $graph->Stroke("entrega_efectiva.png"); 
    $pdf->Image("entrega_efectiva.png",$x,$y,$ancho,$altura); 
    unlink("entrega_efectiva.png");
}

class PDF extends FPDF
{
    var $widths;
    var $aligns;

    // Cabecera de página
    function Header()
    {
        $this->SetFont('Arial','B',12);
        /********************** */
        /** SE INSERTA EL LOGO **/
        /********************** */
        $this->Ln(-3);
        $this->Image('../public/build/images/logo.png', 10, 8, 33);
        
        /********************** */
        /** TITULO DEL REPORTE **/
        /********************** */
        $this->SetFont('Arial','BU',12);
		$this->Cell(330,13,utf8_decode('ENTREGAS EFECTIVAS'),'TLRB',0,'C');
		$this->SetFont('Arial','',8);
		$this->Cell(-13,4,utf8_decode('Codigo: FOR-TRA-09-R0'),'',1,'R');
		$this->Cell(305,10,utf8_decode('Fecha: 25/08/14'),'',1,'R');
        $this->SetAligns(array('L','L','R','L','R','L'));
    }

    // Pie de página
    function Footer()
    {
        $this->SetFont('Arial','B',6);
        $this->SetY(190);
        $this->SetX(60);
		$this->SetAligns(array('L','L','L','L','L','L','L'));
		$this->SetBorders(array('','LTBR','TLBR','TLBR','TLBR','TLBR','TRBL'));
		$this->SetWidths(array(40,20,80,15,25,15,25));
		$this->Row(array('','Aprobado por:', "",'C.I:',"", 'Firma:',""));
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation, $GLOBALS['documentsize']);
    }
}


/************************************* */
/** CONFIGURAMOS EL TIPO DE DOCUMENTO **/
/************************************* */
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L', $documentsize);
$pdf->SetFont('Arial', '', 7);


/************************************************************************************************ */
/** INICIO DE LOS PROCESOS DE OBTENCION DE DATOS Y PROCESAMIENTO PARA UTILIZARLOS POSTERIORMENTE **/
/************************************************************************************************ */
$query = $indicadores->get_entregasefectivas_por_chofer($fechai, $fechaf, $chofer_id);
$chofer = $choferes->get_chofer_por_id($chofer_id);

$chofer = (count($chofer) > 0) ? $chofer[0]['cedula'].' - '.$chofer[0]['descripcion'] : "";
$ordenes_despacho_string = "";
$fact_sinliquidar_string = "";
$totaldespacho = 0;
$total_ped_entregados = 0;
$total_ped_porliquidar = 0;
$promedio_diario_despacho = 0;
$fecha_entrega = Array();
$cant_documentos = Array();
$nombre_mes = Array();
$porc = Array();
$ordenes_despacho = Array();
$valoresParaGrafico = Array();

//almacenamos el total de despachos para calcular la efectividad posteriormente
foreach ($query as $item)
    $totaldespacho += intval($item['cant_documentos']);

foreach ($query as $key => $item)
{
    $ordenes_despacho_string .= ($item['correlativo'] . "(" . addCero($item['cant_documentos']) . "), ");

    $porcentaje = number_format(($item['cant_documentos'] / $totaldespacho) * 100, 1);

    /** entregas efectivas **/
    if($item['tipo_pago'] !='N/C' and $item['tipo_pago'] !='N/C/P' and $item['fecha_entre'] != null and $key>0 )
    {
        //consultamos si la de la iteracion actual tiene fecha igual a la insertada en la interacion anterior
        if(count($fecha_entrega)>0 and date_format(date_create($item['fecha_entre']), $formato_fecha) == $fecha_entrega[count($fecha_entrega)-1])
        {
            $cant_documentos[count($cant_documentos)-1] += intval($item['cant_documentos']);
            $porc[count($porc)-1] += floatval($porcentaje);
            $ordenes_despacho[count($ordenes_despacho)-1] .= (", " . $item['correlativo']);
        }
        //si no es igual, solo inserta un nuevo registro al array
        else {
            $fecha_entrega[] = date_format(date_create($item['fecha_entre']), $formato_fecha);
            $cant_documentos[] = intval($item['cant_documentos']);
            $porc[] = floatval($porcentaje);
            $ordenes_despacho[] = $item['correlativo'];
            $nombre_mes[] = Conectar::convertir(date_format(date_create($item['fecha_entre']), 'm'), true);

        }
    }

    /** facturas sin liquidar **/
    if(strlen($item['fact_sin_liquidar'])>0)
    {
        $fact_sinliquidar_string .= ($item['fact_sin_liquidar'].", ");
        $array = explode(", ", $fact_sinliquidar_string);
        $array = array_unique($array);
        /* sort($array, SORT_ASC); */
        $fact_sinliquidar_string = implode($array,", ");
    }
}

/** calcular los pedidos entregados **/
foreach ($cant_documentos as $arr)
    $total_ped_entregados += intval($arr);

/** calcular los pedidos sin liquidar **/
$total_ped_porliquidar = $totaldespacho - $total_ped_entregados;

/** calcular el promedio diario de despachos **/
$promedio_diario_despacho = (count($cant_documentos) > 0) ? $total_ped_entregados / count($cant_documentos) : 0;


/****************************************************************************** */
/** EVALUAMOS SI LA DATA PROCESADA ES INFERIOR A 42 PARA EVITAR DESBORDAMIENTO **/
/****************************************************************************** */
if(count($cant_documentos)>42) {
    echo "<script>
                alert('Desbordamiento de informacion. Disminuya el rango de fecha para mejor visualizacion');
                window.close();
          </script>";
}


/****************************** */
/**      DATOS DEL REPORTE     **/
/****************************** */
$pdf->SetFont('Arial','',8);
$pdf->SetAligns(array('L','L','R','L','R','L'));
$pdf->SetBorders(array('LT','T','T','T','T','TR'));
$pdf->SetWidths(array(40,194,20,20,19,37));
$pdf->RowWithHeight(array(
    'CHOFER:', "$chofer", 
    'DESDE', date_format(date_create($fechai), "d-m-Y"), 
    'HASTA',date_format(date_create($fechaf), "d-m-Y")),
    5);
$pdf->SetWidths(array(40,290));
$pdf->SetBorders(array('L','R'));
$pdf->RowWithHeight(array('',''), 2);
$pdf->RowWithHeight(array('ORDENES DE DESPACHO',"$ordenes_despacho_string"), 4);
$pdf->RowWithHeight(array('',''), 2);
$pdf->SetWidths(array(40,290));
$pdf->SetBorders(array('LB','RB'));
$pdf->RowWithHeight(array('FACTURAS SIN LIQUIDAR',"$fact_sinliquidar_string"), 4);
$pdf->Cell(0,8,"",0,1,'C');


/************************************* */
/** CONTENIDO DE LA TABLA DE LA TABLA **/
/************************************* */
$x = count($cant_documentos);

$pdf->SetFont('Arial','',10);
$pdf->SetFillColor(213, 213, 246);
if($x>=21){
    $pdf->CellFitSpace((13*(22))+5,6,utf8_decode('Entregas Efectivas'),'TLRB',1,'C',true);
}else{
    $pdf->CellFitSpace((13*($x+1))+5,6,utf8_decode('Entregas Efectivas'),'TLRB',1,'C',true);
} 
$pdf->SetFont('Arial','',6.2);
$pdf->SetFillColor(182, 182, 247); 
for($i=0;$i<$x;$i++){
    if($i==0){
        $pdf->Cell(18,5,'F. Entrega','TLBR',0,'C',true);
    }
    if($i==21){
        $pdf->Cell(18,5,'F. Entrega','TLBR',0,'C',true);
        $pdf->Cell(13,5, $tipoPeriodo!="Anual" ? $fecha_entrega[$i] : $nombre_mes[$i],'TLBR',0,'C', true);
    }else{
        $pdf->Cell(13,5, $tipoPeriodo!="Anual" ? $fecha_entrega[$i] : $nombre_mes[$i],'TLBR',0,'C', true);
    }
}
$pdf->Cell(0,5,"",'',1,'C');
for($i=0;$i<$x;$i++){
    if($i==0){
        $pdf->Cell(18,5,'P. Despachados','TLBR',0,'C');
    }
    if($i==21){
        $pdf->Cell(18,5,'P. Despachados','TLBR',0,'C');
        $pdf->Cell(13,5,$cant_documentos[$i],'TLBR',0,'C');
    }else{
        $pdf->Cell(13,5,$cant_documentos[$i],'TLBR',0,'C');
    }
}
$pdf->Cell(0,5,"",'',1,'C');
for($i=0;$i<$x;$i++){
    if($i==0){
        $pdf->Cell(18,5,'% Efectividad','TLBR',0,'C');
    }
        if($i==21){
        $pdf->Cell(18,5,'% Efectividad','TLBR',0,'C');
        $pdf->Cell(13,5,$porc[$i]." %",'TLBR',0,'C');
    }else{
        $pdf->Cell(13,5,$porc[$i]." %",'TLBR',0,'C');
    }
}
if ($tipoPeriodo!="Anual") {
    $pdf->Cell(0,5,"",'',1,'C');
    for($i=0;$i<$x;$i++){
        if($i==0){
            $pdf->Cell(18,5,'Orden(es) D','TLBR',0,'C');
        }
        if($i==21){
            $pdf->Cell(18,5,'Orden(es) D','TLBR',0,'C');
            $pdf->CellFitSpace(13,5,$ordenes_despacho[$i],'TLBR',0,'C');
        }else{
            $pdf->CellFitSpace(13,5,$ordenes_despacho[$i],'TLBR',0,'C');
        }
    }
}



/************************************* */
/**      TOTALES BAJO LA TABLA        **/
/************************************* */
$pdf->SetFont('Arial','',8);
$pdf->Cell(20,6,'','',1,'C');
$pdf->Cell(50,4,utf8_decode("Total de Pedidos en el camión: ".$totaldespacho),'',0,'L');
$pdf->Cell(50,4,utf8_decode("Total de Pedidos entregados: ".$total_ped_entregados),'',1,'L');
$pdf->Cell(50,4,utf8_decode("Pedidos pendientes por liquidar: ".$total_ped_porliquidar),'',0,'L');
$pdf->Cell(50,4,utf8_decode("Promedio Diario de Despachos: ".number_format($promedio_diario_despacho,0)),'',1,'L');

$promedio_despacho = array();
for($d=0;$d<count($ordenes_despacho);$d++)
    $promedio_despacho[] = number_format($promedio_diario_despacho,0);

/************************************* */
/**             GRAFICO               **/
/************************************* */
$pdf->AddPage('L', $documentsize);
$y = $pdf->GetY();
$x = $pdf->GetX();
graficar($cant_documentos, $tipoPeriodo!="Anual" ? $fecha_entrega : $nombre_mes, $promedio_despacho,'Indicadores de Despacho',array(20,$y,320,170),'', $pdf);


$pdf->Output();

?>