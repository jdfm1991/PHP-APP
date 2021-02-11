<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");
require_once("../acceso/funciones.php");

require('../vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Chart\Layout;

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("indicadoresdespacho_modelo.php");
require_once("../choferes/choferes_modelo.php");

//INSTANCIAMOS EL MODELO
$indicadores = new InidicadoresDespachos();
$choferes = new Choferes();

$tipoPeriodo = $_GET['tipoPeriodo'];
$periodo   = $_GET['periodo'];
$chofer_id = $_GET['chofer'];
$causa = $_GET['causa'];

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

$cant_ordenes_despacho_max = 22;
$cant_fact_sinliquidar_max = 26;
$ancho_tabla_max = 19;
$row = 0;

$i = 0;
//funcion recursiva creada para reporte Excel que evalua los numeros > 0
// y asigna la letra desde la A....hasta la Z y AA, AB, AC.....AZ
function getExcelCol($num, $letra_temp = false) {
    $numero = $num % 26;
    $letra = chr(65 + $numero);
    $num2 = intval($num / 26);
    if(!$letra_temp)
        $GLOBALS['i'] = $GLOBALS['i'] +1;

    if ($num2 > 0) {
        return getExcelCol($num2 - 1) . $letra;
    } else {
        return $letra;
    }
}

/************************************* */
/** CONFIGURAMOS EL TIPO DE DOCUMENTO **/
/************************************* */
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(21);
foreach(range('B','U') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setWidth(10);
}


/********************** */
/** SE INSERTA EL LOGO **/
/********************** */
$gdImage = imagecreatefrompng('../public/build/images/logo.png');
$objDrawing = new MemoryDrawing();
$objDrawing->setName('Sample image');
$objDrawing->setDescription('TEST');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight(118);
$objDrawing->setWidth(118);
$objDrawing->setCoordinates('B3');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());


/********************** */
/** TITULO DEL REPORTE **/
/********************** */
$row = 2;
$spreadsheet->getActiveSheet()->getStyle('J3:L3')->getFont()->setSize(25);
$sheet->setCellValue('K3', 'RECHAZO DE LOS CLIENTES');
$sheet->setCellValue('R2', 'Codigo: FOR-TRA-09-R0');
$sheet->setCellValue('R4', 'Fecha: 25/08/14');
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=0).':T'.($row))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=1).':T'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=1).':T'.($row))->applyFromArray(array('borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));



/************************************************************************************************ */
/** INICIO DE LOS PROCESOS DE OBTENCION DE DATOS Y PROCESAMIENTO PARA UTILIZARLOS POSTERIORMENTE **/
/************************************************************************************************ */

$query = $indicadores->get_causasrechazo_por_chofer($fechai, $fechaf, $chofer_id, $causa);

if($chofer_id!="-") {
    $chofer = $choferes->get_chofer_por_id($chofer_id);
    $chofer = (count($chofer) > 0) ? $chofer[0]['cedula'].' - '.$chofer[0]['descripcion'] : "";
} else {
    $chofer = "Todos los Choferes";
}
$formato_fecha = $tipoPeriodo=="Anual" ? 'm-Y' : 'd-m-Y';
$ordenes_despacho_string = "";
$totaldespacho = 0;
$total_ped_devueltos = 0;
$fecha_entrega = Array();
$nombre_mes = Array();
$cant_documentos = Array();
$porc = Array();
$ordenes_despacho = Array();
$correlativo = Array();
$observacion = Array();
$valoresParaGrafico = Array();


//almacenamos el total de despachos para calcular la efectividad posteriormente
foreach ($query as $item)
    $totaldespacho += intval($item['cant_documentos']);

foreach ($query as $key => $item)
{
    /** obtener los despachos realizados para imprimirlos en un string ordenado al final **/
    if(count($ordenes_despacho)>0 and $item['correlativo'] == $ordenes_despacho[count($ordenes_despacho)-1]["correlativo"]) {
        $ordenes_despacho[count($ordenes_despacho)-1]['cant_documentos'] += intval($item['cant_documentos']);
    } else {
        $ordenes_despacho[] = array(
            "correlativo"     => $item['correlativo'],
            "cant_documentos" => $item['cant_documentos']
        );
    }

    $porcentaje = number_format(($item['cant_documentos'] / $totaldespacho) * 100, 1);

    /** causas de rechazo **/
    if(($item['tipo_pago'] =='N/C' or $item['tipo_pago'] =='N/C/P') and $key>0 )
    {
        //consultamos si la de la iteracion actual tiene fecha igual a la insertada en la interacion anterior
        if(count($fecha_entrega)>0 and  ($item['fecha_entre'] != null
                and date_format(date_create($item['fecha_entre']), $formato_fecha) == $fecha_entrega[count($fecha_entrega)-1]) ||
            ($item['fecha_entre']==null and "sin fecha de entrega"==$fecha_entrega[count($fecha_entrega)-1])
        ) {
            $cant_documentos[count($cant_documentos)-1] += intval($item['cant_documentos']);
            $porc[count($porc)-1] += floatval($porcentaje);
            $correlativo[count($correlativo)-1] .= (", " . $item['correlativo']);
        }
        //si no es igual, solo inserta un nuevo registro al array
        elseif($item['fecha_entre']==null or Funciones::check_in_range($fechai, $fechaf, $item['fecha_entre'])){
            $fecha_ent = ($item['fecha_entre'] != null and strlen($item['fecha_entre'])>0)
                ? date_format(date_create($item['fecha_entre']), $formato_fecha) : "sin fecha de entrega";
            $nombreMes = ($item['fecha_entre'] != null and strlen($item['fecha_entre'])>0)
                ? Funciones::convertir(date_format(date_create($item['fecha_entre']), 'm'), true) : "sin f. entreg.";

            $fecha_entrega[] = $fecha_ent;
            $nombre_mes[] = $nombreMes;
            $cant_documentos[] = intval($item['cant_documentos']);
            $porc[] = floatval($porcentaje);
            $correlativo[] = $item['correlativo'];
            $observacion[] = $item['observacion'];
        }
    }
}

/** los despachos realizados obtenidos se agregan a un string **/
foreach ($ordenes_despacho as $arr){
    $ordenes_despacho_string .= ($arr['correlativo'] . "(" . Funciones::addCero($arr['cant_documentos']) . "), ");
}

/** calcular los pedidos devueltos **/
foreach ($cant_documentos as $arr){
    $total_ped_devueltos += intval($arr);
}


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
$row+=2;
$sheet->setCellValue('A'.($row), 'CHOFER:'); $sheet->setCellValue('B'.($row), $chofer);
$sheet->setCellValue('N'.($row), 'DESDE: ' . date_format(date_create($fechai), "d-m-Y"));
$sheet->setCellValue('R'.($row), 'HASTA: ' . date_format(date_create($fechaf), "d-m-Y"));
$spreadsheet->getActiveSheet()->mergeCells('N'.($row).':O'.($row));
$spreadsheet->getActiveSheet()->mergeCells('R'.($row).':S'.($row));
$spreadsheet->getActiveSheet()->getStyle('A'.($row).':T'.($row))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=1).':T'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));

$sheet->setCellValue('A'.($row+=1), 'ORDENES DE DESPACHO');
$ordenes_despacho_arr = explode(",", $ordenes_despacho_string);
//como la longitud en horizontal puede sobrepasar el cuadro
//evalua la cantidad de ordenes para su procesamiento
if(count($ordenes_despacho_arr) > $cant_ordenes_despacho_max)
{
    //dividimos las ordenes de despacho, en string de (cantidad maxima de ordenes) despachos
    // y los anexa a un array para posterior utlizacion
    $temp_string = "";
    foreach($ordenes_despacho_arr as $index => $arr)
    {
        //axena cada (cantidad maxima de ordenes) o si llego al final del arr
        if( ($index>0 && ($index % $cant_ordenes_despacho_max)==0) || ($index>0 && $index==count($ordenes_despacho_arr)) ) {
            $sheet->setCellValue('B'.($row), $temp_string);
            $spreadsheet->getActiveSheet()->mergeCells('B'.($row).':T'.($row));
            $spreadsheet->getActiveSheet()->getStyle('A'.($row).':T'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));
            $temp_string = ($arr . ", ");
            $row+=1;
        } else {
            //concadena cada despacho
            $temp_string .= ($arr . ", ");
        }
    }
}
else {
    //sino no es necesario procesar la ordenes de despachos sino imprimirlas
    $sheet->setCellValue('B'.($row), $ordenes_despacho_string);
    $spreadsheet->getActiveSheet()->mergeCells('B'.($row).':T'.($row));
    $spreadsheet->getActiveSheet()->getStyle('A'.($row).':T'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));
    $row+=1;
}
$spreadsheet->getActiveSheet()->getStyle('A'.($row).':T'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));

$sheet->setCellValue('A'.($row+=1), 'CAUSA DEL RECHAZO');
$sheet->setCellValue('B'.($row), $causa);
$spreadsheet->getActiveSheet()->mergeCells('B'.($row).':T'.($row));
$spreadsheet->getActiveSheet()->getStyle('A'.($row).':T'.($row))->applyFromArray(array('borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));



/************************************* */
/** CONTENIDO DE LA TABLA DE LA TABLA **/
/************************************* */

$titulo_tabla = [
    'fecha_devolucion' => 'F. Devolución',
    'ped_devueltos'    => 'Devoluciones',
    'rechazo'          => '% Rechazos',
    'orden_despacho'   => 'Orden(es) Despacho',
];

//estilo de la cabecera de la tabla
$style_title = new Style();
$style_title->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D5D5F6'],), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$style_subtitle = new Style();
$style_subtitle->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'B6B6F7'],), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$row-=3;
$temp_letra = $ult_letra = "";
for ($j=0; $j<count($cant_documentos); $j++) {
    $sheet = $spreadsheet->getActiveSheet();
    //evalua con la intencion de saber si se va a hacer un salto de la
    //tabla para agregarle estilo a la cabecera de la tabla
    if (($j % $ancho_tabla_max) == 0 || $j + 1 == count($cant_documentos)) {

        if ($temp_letra != "") {
            $ult_letra = !($j + 1 == count($cant_documentos)) ? $temp_letra : getExcelCol($i, true);
            $spreadsheet->getActiveSheet()->mergeCells('A' . ($row) . ':' . $ult_letra . ($row));
            $spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A' . ($row) . ':' . $ult_letra . ($row));
        }

        if ($j > 0 && ($j % $ancho_tabla_max) == 0 || $j + 1 == count($cant_documentos)) {
            $valoresParaGrafico[] = array(
                'fecha_entrega' => array('B', ($row + 1), $ult_letra, ($row + 1)),
                'despachos' => array('B', ($row + 2), $ult_letra, ($row + 2)),
            );
        }
    }

    //esta evalua si la iteracion va a ser superior al ancho maximo para generar
    //el salgo de la tabla y agregarle los titulos
    if (($j % $ancho_tabla_max) == 0) {
        $i = 0;
        $row += 6;
        $temp_letra = getExcelCol($i);
        $sheet->setCellValue($temp_letra . ($row + 0), 'Rechazos de los Clientes');
        $sheet->setCellValue($temp_letra . ($row + 1), $titulo_tabla['fecha_devolucion']);
        $sheet->setCellValue($temp_letra . ($row + 2), $titulo_tabla['ped_devueltos']);
        $sheet->setCellValue($temp_letra . ($row + 3), $titulo_tabla['rechazo']);
        if ($tipoPeriodo != "Anual") {
            $sheet->setCellValue($temp_letra . ($row + 4), $titulo_tabla['orden_despacho']);
        }
        $spreadsheet->getActiveSheet()->duplicateStyle($style_subtitle, $temp_letra . ($row + 1));
        $spreadsheet->getActiveSheet()->duplicateStyle($style_title, $temp_letra . ($row + 2));
        $spreadsheet->getActiveSheet()->duplicateStyle($style_title, $temp_letra . ($row + 3));
        if ($tipoPeriodo != "Anual") {
            $spreadsheet->getActiveSheet()->duplicateStyle($style_title, $temp_letra . ($row + 4));
        }
    }

    $temp_letra = getExcelCol($i);
    $sheet->setCellValue($temp_letra . ($row + 1), $tipoPeriodo!="Anual" ? $fecha_entrega[$j] : $nombre_mes[$j]);
    $sheet->setCellValue($temp_letra . ($row + 2), $cant_documentos[$j]);
    $sheet->setCellValue($temp_letra . ($row + 3), $porc[$j] . ' %');
    if ($tipoPeriodo != "Anual") {
        $sheet->setCellValue($temp_letra . ($row + 4), $correlativo[$j]);
    }

    /** centrarlas las celdas **/
    $spreadsheet->getActiveSheet()->duplicateStyle($style_subtitle, $temp_letra . ($row + 1));
    $spreadsheet->getActiveSheet()->getStyle($temp_letra . ($row + 2))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle($temp_letra . ($row + 3))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    if ($tipoPeriodo != "Anual") {
        $spreadsheet->getActiveSheet()->getStyle($temp_letra . ($row + 4))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    }
}

/************************************* */
/**      TOTALES BAJO LA TABLA        **/
/************************************* */
$row+=6;
$sheet->setCellValue('B' . ($row+0), 'Total de Pedidos en el camión:');
$sheet->setCellValue('B' . ($row+1), 'Total de Pedidos devueltos:');
$sheet->setCellValue('E' . ($row+0), $totaldespacho);
$sheet->setCellValue('E' . ($row+1), $total_ped_devueltos);
$spreadsheet->getActiveSheet()->mergeCells('B'.($row+0).':D'.($row+0));
$spreadsheet->getActiveSheet()->mergeCells('B'.($row+1).':D'.($row+1));
$spreadsheet->getActiveSheet()->getStyle('B'.($row+0).':D'.($row+0))->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('B'.($row+1).':D'.($row+1))->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('E'.($row+0))->applyFromArray(array('font' => array('bold'  => true), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_LEFT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('E'.($row+1))->applyFromArray(array('font' => array('bold'  => true), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_LEFT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));




/************************************* */
/**             GRAFICO               **/
/************************************* */

//agregamos los titulos de la leyenda ocultos atras del grafico
$num_temp = 0;
foreach ($observacion as $key=>$obs) {
    $num_temp = $pos = ($row + $key + 4);
    $sheet->setCellValue('B'.$pos, strtoupper($obs));

    //agregamos a su vez los valores por serie
    $i = 2;
    foreach ($cant_documentos as $key1=>$value){
        $sheet->setCellValue(getExcelCol($i).$pos, ($key==$key1) ? $value : 0);
    }
}

// tipo (Grupo) de serie de la barras
$dataSeriesLabels = [];
for($x=($row+4); $x<=$num_temp; $x++){
    $dataSeriesLabels[] = new DataSeriesValues('String', 'Worksheet!$B$'.$x, null, 1);
}

// serie EJE X del nombre de las barras (en la parte inferior)
$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$'.$valoresParaGrafico[0]['fecha_entrega'][0].'$'.$valoresParaGrafico[0]['fecha_entrega'][1].':$'.$valoresParaGrafico[0]['fecha_entrega'][2].'$'.$valoresParaGrafico[0]['fecha_entrega'][3], null, count($cant_documentos)),
];

//valores de las barras (por cada item del EJE X)
$dataSeriesValues = []; $i=2;
for($x=($row+4); $x<$num_temp+1; $x++){
    $dataSeriesValues[] = new DataSeriesValues('Number', 'Worksheet!$C$'.$x.':$'.getExcelCol($i+count($cant_documentos), true).'$'.$x, null, count($cant_documentos));
}

// Construccion de las DataSeries
$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_STANDARD, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues,  // plotCategory
    $dataSeriesValues  // plotValues
);
$series->setPlotDirection(DataSeries::DIRECTION_COL);

// Datos necesarios para la contruccion del grafico
$plotArea   = new PlotArea(null, [$series]);
$legend     = new Legend(Legend::POSITION_RIGHT, null, false);
$title      = new Title('Causas de los rechazo');
$yAxisLabel = new Title('Despachos');

// Construccion del Grafico
$chart = new Chart(
    'grafico', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    0, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel  // yAxisLabel
);
$chart->setTopLeftPosition('B' . ($row+=4))
    ->setBottomRightPosition('S' . ($row+=17));

// AGREGA EL GRAFICO AL DOCUMENTO
$spreadsheet->getActiveSheet()->addChart($chart);



/************************************* */
/**     CUADRO DE AUTORIZADO POR      **/
/************************************* */
$row+=4;
$sheet->setCellValue('B'.$row, 'Aprobado por: ');
$sheet->setCellValue('K'.$row, 'C.I:');
$sheet->setCellValue('O'.$row, 'Firma: ');
$spreadsheet->getActiveSheet()->mergeCells('B'.($row).':C'.($row));
$spreadsheet->getActiveSheet()->getStyle('B'.($row).':C'.($row))->applyFromArray(array('font' => array('bold'  => true), 'borders' => array('top' => ['borderStyle' => Border::BORDER_MEDIUM], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], 'bottom' => ['borderStyle' => Border::BORDER_MEDIUM],)));
$spreadsheet->getActiveSheet()->getStyle('D'.($row).':J'.($row))->applyFromArray(array('font' => array('bold'  => true), 'borders' => array('top' => ['borderStyle' => Border::BORDER_MEDIUM], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], 'bottom' => ['borderStyle' => Border::BORDER_MEDIUM],)));
$spreadsheet->getActiveSheet()->getStyle('K'.($row).':L'.($row))->applyFromArray(array('font' => array('bold'  => true), 'borders' => array('top' => ['borderStyle' => Border::BORDER_MEDIUM], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], 'bottom' => ['borderStyle' => Border::BORDER_MEDIUM],)));
$spreadsheet->getActiveSheet()->getStyle('M'.($row).':N'.($row))->applyFromArray(array('font' => array('bold'  => true), 'borders' => array('top' => ['borderStyle' => Border::BORDER_MEDIUM], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], 'bottom' => ['borderStyle' => Border::BORDER_MEDIUM],)));
$spreadsheet->getActiveSheet()->getStyle('O'.($row).':P'.($row))->applyFromArray(array('font' => array('bold'  => true), 'borders' => array('top' => ['borderStyle' => Border::BORDER_MEDIUM], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], 'bottom' => ['borderStyle' => Border::BORDER_MEDIUM],)));
$spreadsheet->getActiveSheet()->getStyle('Q'.($row).':R'.($row))->applyFromArray(array('font' => array('bold'  => true), 'borders' => array('top' => ['borderStyle' => Border::BORDER_MEDIUM], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], 'bottom' => ['borderStyle' => Border::BORDER_MEDIUM],)));



header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="indicadores_causas_rechazo_del_'.$fechai.'_al_'.$fechaf.'.xlsx"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->setIncludeCharts(true);
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');