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

//LLAMAMOS AL MODELO
require_once("kpi_modelo.php");
require_once("../kpimanager/kpimanager_modelo.php");

//INSTANCIAMOS EL MODELO
$kpi = new Kpi();
$kpiManager = new KpiManager();

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

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$d_habiles = $_GET['d_habiles'];
$d_trans = $_GET['d_trans'];

//creamos la cabecera de la tabla
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

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
$objDrawing->setCoordinates('P1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$sheet->setCellValue('A1', 'REPORTE KPI (Key Performance Indicator)');
$spreadsheet->getActiveSheet()->mergeCells('A1:K1');

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

$sheet->setCellValue('C4', 'Desde:');
$sheet->setCellValue('D4', date("d/m/Y", strtotime($fechai)));
$spreadsheet->getActiveSheet()->mergeCells('D4:E4');
$spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('D4:E4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$sheet->setCellValue('G4', 'Hasta:');
$sheet->setCellValue('H4', date("d/m/Y", strtotime($fechaf)));
$spreadsheet->getActiveSheet()->mergeCells('H4:I4');
$spreadsheet->getActiveSheet()->getStyle('G4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('H4:I4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$sheet->setCellValue('L4', 'D. Habiles:');
$sheet->setCellValue('M4', $d_habiles);
$spreadsheet->getActiveSheet()->getStyle('L4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('M4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$sheet->setCellValue('O4', 'D. Transc:');
$sheet->setCellValue('P4', $d_trans);
$spreadsheet->getActiveSheet()->getStyle('O4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('P4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));




$lista_marcaskpi = array_map(function ($arr) { return $arr['descripcion']; }, KpiMarcas::todos('DESC'));


$row = 7;
$sheet->setCellValue('A'.$row, 'Rutas');
$sheet->setCellValue('B'.$row, 'Activación');
$sheet->setCellValue('F'.$row, 'Efectividad');
$sheet->setCellValue('M'.$row, 'Ventas');
$spreadsheet->getActiveSheet()->getStyle('A'.$row.':AA'.$row)->getFont()->setSize(14);

$spreadsheet->getActiveSheet()->mergeCells('B'.$row.':'.getExcelCol(count($lista_marcaskpi)+4, true).$row);

$spreadsheet->getActiveSheet()->getStyle( 'A'.$row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '7abaff'],), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle( 'B'.$row.':'.getExcelCol(count($lista_marcaskpi)+4, true).$row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '7abaff'],), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));




$row = 8;
$i = 0;
$sheet->setCellValue(getExcelCol($i).$row, 'Rutas');
$sheet->setCellValue(getExcelCol($i).$row, 'Maestro');
$sheet->setCellValue(getExcelCol($i).$row, 'Clie Activados');
#listado dinamico de las marcas
foreach ($lista_marcaskpi as $marcakpi)
    $sheet->setCellValue(getExcelCol($i).$row, $marcakpi);
#fin de listado dinamico de las marcas
$sheet->setCellValue(getExcelCol($i).$row, '%Act. Alcanzada');
$sheet->setCellValue(getExcelCol($i).$row, 'Pendientes');
$sheet->setCellValue(getExcelCol($i).$row, 'Visita');
$sheet->setCellValue(getExcelCol($i).$row, 'Obj  Facturas mas notas Mensual');
$sheet->setCellValue(getExcelCol($i).$row, 'Total Facturas Realizadas');
$sheet->setCellValue(getExcelCol($i).$row, 'Total Notas Realizadas');
$sheet->setCellValue(getExcelCol($i).$row, 'Devoluciones Realizadas (nt + fac)');
$sheet->setCellValue(getExcelCol($i).$row, 'Total Devoluciones Realizadas ($)');
$sheet->setCellValue(getExcelCol($i).$row, '% Efectividad Alcanzada a la Fecha');
$sheet->setCellValue(getExcelCol($i).$row, 'Objetivo (Bulto)');
$sheet->setCellValue(getExcelCol($i).$row, 'Logro (Bulto)');
$sheet->setCellValue(getExcelCol($i).$row, '%Alcanzado (Bulto)');
$sheet->setCellValue(getExcelCol($i).$row, 'Objetivo (Kg)');
$sheet->setCellValue(getExcelCol($i).$row, 'Logro (Kg)');
$sheet->setCellValue(getExcelCol($i).$row, '%Alcanzado (Kg)');
$sheet->setCellValue(getExcelCol($i).$row, 'Real Drop Size ($)');
$sheet->setCellValue(getExcelCol($i).$row, 'Objetivo Total Ventas ($)');
$sheet->setCellValue(getExcelCol($i).$row, 'Total Logro Ventas en ($)');
$sheet->setCellValue(getExcelCol($i).$row, '%Alcanzado ($)');
$sheet->setCellValue(getExcelCol($i).$row, 'Ventas PEPSICO ($)');
$sheet->setCellValue(getExcelCol($i).$row, '% Venta PEPSICO');
$sheet->setCellValue(getExcelCol($i).$row, 'Ventas Complementaria ($)');
$sheet->setCellValue(getExcelCol($i).$row, '% Venta Complementaria');
$sheet->setCellValue(getExcelCol($i).$row, 'Cobranza Rebajadas (Bs)');

//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n < $aux; $n++) {
    if ($n >= 3 and $n < count($lista_marcaskpi)+3) {
        $spreadsheet->getActiveSheet()->getColumnDimension(getExcelCol($n, true))->setWidth('6');
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->getAlignment()->setTextRotation(90);
    } else {
        $spreadsheet->getActiveSheet()->getColumnDimension(getExcelCol($n, true))->setWidth('13');
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->getAlignment()->setWrapText(true);
        }
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE),'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
}
$spreadsheet->getActiveSheet()->getStyle( 'A'.$row.':'.getExcelCol($aux-1, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'c8dcff'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),));











$spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(80);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Listado_de_precios_e_inventario_' . date('d/m/Y') . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');