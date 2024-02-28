<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require_once ( PATH_LIBRARY.'jpgraph4.3.4/src/jpgraph.php' );
require_once ( PATH_LIBRARY.'jpgraph4.3.4/src/jpgraph_bar.php' );
require_once ( PATH_LIBRARY.'jpgraph4.3.4/src/jpgraph_line.php' );

require (PATH_VENDOR.'autoload.php');
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
require_once("listadepreciodivisas_modelo.php");

//INSTANCIAMOS EL MODELO
$precios = new Listadepreciodivisas();

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
        return getExcelCol($num2 - 1, true) . $letra;
    } else {
        return $letra;
    }
}

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


//creamos la cabecera de la tabla
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

// Logo
$gdImage = imagecreatefrompng(PATH_LIBRARY.'build/images/logo.png');
$objDrawing = new MemoryDrawing();
$objDrawing->setName('Sample image');
$objDrawing->setDescription('TEST');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight(108);
$objDrawing->setWidth(128);
$objDrawing->setCoordinates('G1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$sheet->setCellValue('A1', 'REPORTE DE LISTADO DE PRECIOS DIVISAS E INVENTARIO');


$spreadsheet->getActiveSheet()->mergeCells('A1:E1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('codigo_prod'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('descrip_prod'));
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('marca_prod'));
//BULTOS
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('bultos'));
switch ($sumap) {
    case 1:
        $sheet->setCellValue(getExcelCol($i).'7', 'Precio '.$sumap2.' Bulto $');
        break;
    case 2:
        if($p1 == 1){ $pAux = $p1; }else{ $pAux = $p2;}
        $sheet->setCellValue(getExcelCol($i).'7', 'Precio '.$pAux.' Bulto $');
        if ($p3 == 3){ $pAux = $p3; }else{ $pAux = $p2;}
        $sheet->setCellValue(getExcelCol($i).'7', 'Precio '.$pAux.' Bulto $');
        $pAux = '';
        break;
    default: /** 0 || 3**/
        $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('precio1d_bulto'));
        $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('precio2d_bulto'));
        $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('precio3d_bulto'));
}
//PAQUETES
$sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('paquetes'));
switch ($sumap) {
    case 1:
        $sheet->setCellValue(getExcelCol($i).'7', 'Precio '.$sumap2.' Paquete $');
        break;
    case 2:
        if($p1 == 1){ $pAux = $p1; }else{ $pAux = $p2;}
        $sheet->setCellValue(getExcelCol($i).'7', 'Precio '.$pAux.' Paquete $');
        if ($p3 == 3){ $pAux = $p3; }else{ $pAux = $p2;}
        $sheet->setCellValue(getExcelCol($i).'7', 'Precio '.$pAux.' Paquete $');
        $pAux = '';
        break;
    default: /** 0 || 3**/
        $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('precio1d_paquete'));
        $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('precio2d_paquete'));
        $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('precio3d_paquete'));
}
if ($cubi == 1) {
    $sheet->setCellValue(getExcelCol($i).'7', Strings::titleFromJson('cubicaje'));
}
//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++){ $spreadsheet->getActiveSheet()->getColumnDimension(getExcelCol($n))->setAutoSize(true); }


$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:'.getExcelCol($aux).'7');


//a partir de aqui comienza a llenarse la tabla
$datos = $precios->getListadeprecios($marcas, $depos, $exis, $orden);
$num = count($datos);
$row = 8;
foreach ($datos as $x) {
    $i = 0;
    if ($x['esexento']) {
        $precio1 = $x['preciou1'] * $iva;
        $precio2 = $x['preciou2'] * $iva;
        $precio3 = $x['preciou3'] * $iva;
        $preciou1 = $x['precio1'] * $iva;
        $preciou2 = $x['precio2'] * $iva;
        $preciou3 = $x['precio3'] * $iva;
    } else {
        $precio1 = $x['preciou1'];
        $precio2 = $x['preciou2'];
        $precio3 = $x['preciou3'];
        $preciou1 = $x['precio1'];
        $preciou2 = $x['precio2'];
        $preciou3 = $x['precio3'];
    }
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue(getExcelCol($i, true) . $row, $x['codprod']);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $row, $x['descrip']);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    $sheet->setCellValue(getExcelCol($i, true) . $row, $x['marca']);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    //BULTOS
    $sheet->setCellValue(getExcelCol($i, true) . $row, $x['existen']);
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    switch ($sumap) {
        case 1:
            if ($x['esexento'] == 0) {
                $pAux = ($x['preciou' . $sumap2] * $iva);
            } else {
                $pAux = ($x['preciou' . $sumap2]);
            }
            $sheet->setCellValue(getExcelCol($i, true) . $row, $pAux);
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            break;
        case 2:
            if ($p1 == 1) {
                $pAux = ($precio1);
            } else {
                $pAux = ($precio2);
            }
            $sheet->setCellValue(getExcelCol($i, true) . $row, $pAux);
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            if ($p3 == 3) {
                $pAux = ($precio3);
            } else {
                $pAux = ($precio2);
            }
            $sheet->setCellValue(getExcelCol($i, true) . $row, $pAux);
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            break;
        default:
            /** 0 || 3**/
            $sheet->setCellValue(getExcelCol($i, true) . $row, ($precio1));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

            $sheet->setCellValue(getExcelCol($i, true) . $row, ($precio2));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

            $sheet->setCellValue(getExcelCol($i, true) . $row, ($precio3));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    }
    $pAux = '';
    //PAQUETES
    $sheet->setCellValue(getExcelCol($i, true) . $row, round($x['exunidad']));
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    switch ($sumap) {
        case 1:
            if ($x['esexento'] == 0) {
                $pAux = ($x['precio' . $sumap2] * $iva);
            } else {
                $pAux = ($x['precio' . $sumap2]);
            }
            $sheet->setCellValue(getExcelCol($i, true) . $row, $pAux);
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            break;
        case 2:
            if ($p1 == 1) {
                $pAux = ($preciou1);
            } else {
                $pAux = ($preciou2);
            }
            $sheet->setCellValue(getExcelCol($i, true) . $row, $pAux);
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            if ($p3 == 3) {
                $pAux = ($preciou3);
            } else {
                $pAux = ($preciou2);
            }
            $sheet->setCellValue(getExcelCol($i, true) . $row, $pAux);
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            break;
        default:
            /** 0 || 3**/
            $sheet->setCellValue(getExcelCol($i, true) . $row, ($preciou1));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

            $sheet->setCellValue(getExcelCol($i, true) . $row, ($preciou2));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

            $sheet->setCellValue(getExcelCol($i, true) . $row, ($preciou3));
            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    }
    $pAux = '';
    if ($cubi == 1) {
        $sheet->setCellValue(getExcelCol($i, true) . $row, $x['cubicaje']);
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i).$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    }
    $row++;
}
// por ultimo se escribe la cantidad de productos
$sheet->setCellValue('B' . ($row + 3), 'Total de Productos:  ' . $num);


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Listado_de_precios_divisas_e_inventario_' . date('d/m/Y') . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');
