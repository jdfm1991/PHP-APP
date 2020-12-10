<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require('../vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("indicadoresdespacho_modelo.php");
require_once("../choferes/choferes_modelo.php");

//INSTANCIAMOS EL MODELO
$indicadores = new InidicadoresDespachos();
$choferes = new Choferes();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$chofer_id = $_GET['chofer'];

$formato_fecha = "d-m";
$cant_ordenes_despacho_max = 22;
$cant_fact_sinliquidar_max = 26;
$ancho_tabla_max = 20;
$row = 0;

$i = 0;
//funcion recursiva creada para reporte Excel que evalua los numeros > 0
// y asigna la letra desde la A....hasta la Z y AA, AB, AC.....AZ
function getExcelCol($num) {
    $numero = $num % 26;
    $letra = chr(65 + $numero);
    $num2 = intval($num / 26);
    $GLOBALS['i'] = $GLOBALS['i'] +1;
    if ($num2 > 0) {
        return getExcelCol($num2 - 1) . $letra;
    } else {
        return $letra;
    }
}

function addCero($num) {
    if(intval($num)<=9)
        return "0".$num;
    return $num;
}

/************************************* */
/** CONFIGURAMOS EL TIPO DE DOCUMENTO **/
/************************************* */
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);


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
$sheet->setCellValue('K3', 'ENTREGAS EFECTIVAS');
$sheet->setCellValue('R2', 'Codigo: FOR-TRA-09-R0');
$sheet->setCellValue('R4', 'Fecha: 25/08/14');
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=0).':U'.($row))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=1).':U'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=1).':U'.($row))->applyFromArray(array('borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));



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
$porc = Array();
$ordenes_despacho = Array();

//almacenamos el total de despachos para calcular la efectividad posteriormente
foreach ($query as $item)
    $totaldespacho += intval($item['cant_documentos']);

foreach ($query as $key => $item)
{
    $ordenes_despacho_string .= ($item['correlativo'] . "(" . addCero($item['cant_documentos']) . "),");

    $porcentaje = number_format(($item['cant_documentos'] / $totaldespacho) * 100, 1);

    /** entregas efectivas **/
    if($item['tipo_pago'] !='N/C' and $item['fecha_entre'] != null and $key>0 )
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

        }
    }

    /** facturas sin liquidar **/
    if(strlen($item['fact_sin_liquidar'])>0)
    {
        $fact_sinliquidar_string .= ($item['fact_sin_liquidar'].",");
        $array = explode(",", $fact_sinliquidar_string);
        $array = array_unique($array);
        sort($array, SORT_ASC);
        $fact_sinliquidar_string = implode($array,",");
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
$row+=2;
$sheet->setCellValue('A'.($row), 'CHOFER:'); $sheet->setCellValue('B'.($row), $chofer);
$sheet->setCellValue('N'.($row), 'DESDE: ' . date_format(date_create($fechai), "d-m-Y"));
$sheet->setCellValue('R'.($row), 'HASTA: ' . date_format(date_create($fechaf), "d-m-Y"));
$spreadsheet->getActiveSheet()->mergeCells('N'.($row).':O'.($row));
$spreadsheet->getActiveSheet()->mergeCells('R'.($row).':S'.($row));
$spreadsheet->getActiveSheet()->getStyle('A'.($row).':U'.($row))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('A'.($row+=1).':U'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));

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
            $spreadsheet->getActiveSheet()->mergeCells('B'.($row).':U'.($row));
            $spreadsheet->getActiveSheet()->getStyle('A'.($row).':U'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));
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
    $sheet->setCellValue('C'.($row), $ordenes_despacho_string);
    $spreadsheet->getActiveSheet()->mergeCells('C'.($row).':U'.($row));
    $spreadsheet->getActiveSheet()->getStyle('A'.($row).':U'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));
    $row+=1;
}
$spreadsheet->getActiveSheet()->getStyle('A'.($row).':U'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));

$sheet->setCellValue('A'.($row+=1), 'FACT SIN LIQUIDAR');
$fact_sinliquidar_arr = explode(",", $fact_sinliquidar_string);
//como la longitud en horizontal puede sobrepasar el cuadro
//evalua la cantidad de facturas sin liquidar para su procesamiento
if(count($fact_sinliquidar_arr) > $cant_fact_sinliquidar_max)
{
    //dividimos las facturas, en string de (cantidad maxima de facturas sin liquidar)
    // y los anexa a un array para posterior utlizacion
    $temp_string = "";
    foreach($fact_sinliquidar_arr as $index => $arr)
    {
        //axena cada (cantidad maxima de ordenes) o si llego al final del arr
        if( ($index>0 && ($index % $cant_fact_sinliquidar_max)==0) || ($index>0 && $index==count($fact_sinliquidar_arr)) ) {
            $sheet->setCellValue('B'.($row), $temp_string);
            $spreadsheet->getActiveSheet()->mergeCells('B'.($row).':U'.($row));
            $spreadsheet->getActiveSheet()->getStyle('A'.($row).':U'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));
            $temp_string = ($arr . ", ");
            $row+=1;
        } else {
            //concadena cada despacho
            $temp_string .= ($arr . ", ");
        }
    }
}
else {
    //sino no es necesario procesar las facturas sin liquidar, se imprimen directamente
    $sheet->setCellValue('C'.($row), $fact_sinliquidar_string);
    $spreadsheet->getActiveSheet()->mergeCells('C'.($row).':U'.($row));
    $spreadsheet->getActiveSheet()->getStyle('A'.($row).':U'.($row))->applyFromArray(array('borders' => array('left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));
    $row+=1;
}
$spreadsheet->getActiveSheet()->getStyle('A'.($row).':U'.($row))->applyFromArray(array('borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('wrap' => TRUE)));


/************************************* */
/** CONTENIDO DE LA TABLA DE LA TABLA **/
/************************************* */

$titulo_tabla = [
    'fecha_entrega'   => 'F. Entrega',
    'ped_despachados' => 'P. Despachados',
    'efectividad'     => '% Efectividad',
    'orden_despacho'  => 'Orden(es) D',
];

//estilo de la cabecera de la tabla
$style_title = new Style();
$style_title->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D5D5F6'],), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$style_subtitle = new Style();
$style_subtitle->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'B6B6F7'],), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$row-=3;
$temp_letra = $ult_letra = "";
for ($j=0; $j<count($cant_documentos); $j++) {

    //evalua con la intencion de saber si se va a hacer un salto de la
    //tabla para agregarle estilo a la cabecera de la tabla
    if( ( $j % $ancho_tabla_max )==0 || $j+1==count($cant_documentos) ) {
        if($temp_letra!="") {
            $ult_letra = $temp_letra;
            $spreadsheet->getActiveSheet()->mergeCells('A'.($row).':'. $ult_letra . ($row));
            $spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A'.($row).':'. $ult_letra . ($row));
        }
    }

    //esta evalua si la iteracion va a ser superior al ancho maximo para generar
    //el salgo de la tabla y agregarle los titulos
    if( ( $j % $ancho_tabla_max )==0 ) {
        $i = 0;
        $row += 6;
        $temp_letra = getExcelCol($i);
        $sheet->setCellValue($temp_letra . ($row+0), 'Entregas Efectivas');
        $sheet->setCellValue($temp_letra . ($row+1), $titulo_tabla['fecha_entrega']);
        $sheet->setCellValue($temp_letra . ($row+2), $titulo_tabla['ped_despachados']);
        $sheet->setCellValue($temp_letra . ($row+3), $titulo_tabla['efectividad']);
        $sheet->setCellValue($temp_letra . ($row+4), $titulo_tabla['orden_despacho']);
        $spreadsheet->getActiveSheet()->duplicateStyle($style_subtitle, $temp_letra . ($row+1));
        $spreadsheet->getActiveSheet()->duplicateStyle($style_title, $temp_letra . ($row+2));
        $spreadsheet->getActiveSheet()->duplicateStyle($style_title, $temp_letra . ($row+3));
        $spreadsheet->getActiveSheet()->duplicateStyle($style_title, $temp_letra . ($row+4));
    }

    $temp_letra = getExcelCol($i);
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue($temp_letra . ($row+1), $fecha_entrega[$j]);
    $sheet->setCellValue($temp_letra . ($row+2), $cant_documentos[$j]);
    $sheet->setCellValue($temp_letra . ($row+3), $porc[$j]);
    $sheet->setCellValue($temp_letra . ($row+4), $ordenes_despacho[$j]);

    /** centrarlas las celdas **/
    $spreadsheet->getActiveSheet()->duplicateStyle($style_subtitle, $temp_letra . ($row+1));
    $spreadsheet->getActiveSheet()->getStyle($temp_letra . ($row+2))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle($temp_letra . ($row+3))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle($temp_letra . ($row+4))->applyFromArray(array('borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
}



header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="indicadores_entregas_efectivas_del'.$fechai.'_al_'.$fechaf.'.xls"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
ob_end_clean();
ob_start();
$writer->save('php://output');

