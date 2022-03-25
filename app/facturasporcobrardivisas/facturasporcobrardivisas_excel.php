<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
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
require_once("facturasporcobrardivisas_modelo.php");

//INSTANCIAMOS EL MODELO
$facturas = new facturasporcobrardivisas();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
foreach(range('A','G') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}

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
$objDrawing->setCoordinates('E1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFont()->setSize(25);


 $sheet->setCellValue('A1', 'Facturas Pendientes por Cobrar Divisas');


$sheet->setCellValue('A5', 'fecha tope:  '. date('d-m-Y'));

$spreadsheet->getActiveSheet()->mergeCells('A1:C1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue('A7', utf8_decode(Strings::titleFromJson('tipo_transaccion')))
    ->setCellValue('B7', Strings::titleFromJson('numerod'))
    ->setCellValue('C7', Strings::titleFromJson('codclie'))
    ->setCellValue('D7', Strings::titleFromJson('cliente'))
    ->setCellValue('E7', Strings::titleFromJson('fecha_emision'))
    ->setCellValue('F7', Strings::titleFromJson('fecha_despacho'))
    ->setCellValue('G7', Strings::titleFromJson('dias_transcurridos'))
    ->setCellValue('H7', Strings::titleFromJson('0_a_7'))
    ->setCellValue('I7', Strings::titleFromJson('8_a_14'))
    ->setCellValue('J7', Strings::titleFromJson('15_a_21'))
    ->setCellValue('K7', Strings::titleFromJson('22_a_31'))
    ->setCellValue('L7', Strings::titleFromJson('31_dias'))
    ->setCellValue('M7', Strings::titleFromJson('saldo_pendiente'))
    ->setCellValue('N7', Strings::titleFromJson('ruta'))
    ->setCellValue('O7', Strings::titleFromJson('supervisor'));

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);


//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:O7');

$query = $facturas->getfacturasporcobrardivisas( $fechai, $fechaf);

$row = 8;
foreach ($query as $i) {

        $De_0_a_7_Dias = number_format($i["De_0_a_7_Dias"], 2, ',', '.');
        $De_8_a_14_Dias = number_format($i["De_8_a_14_Dias"], 2, ',', '.');
        $De_15_a_21_Dias = number_format($i["De_15_a_21_Dias"], 2, ',', '.');
        $De_22_a_31_Dias = number_format($i["De_22_a_31_Dias"], 2, ',', '.');
        $Mas_31_Dias = number_format($i["Mas_31_Dias"], 2, ',', '.');

        $total = number_format($i["SaldoPend"], 2, ',', '.');

        $fecha_E = date('d/m/Y', strtotime($i["FechaEmi"]));
        $fecha_D = date('d/m/Y', strtotime($i["FechaDesp"]));

    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A' . $row, $i['TipoOpe']);
    $sheet->setCellValue('B' . $row, $i['NroDoc']);
    $sheet->setCellValue('C' . $row, $i['CodClie']);
    $sheet->setCellValue('D' . $row, $i['Cliente']);
    $sheet->setCellValue('E' . $row, $fecha_E);
    $sheet->setCellValue('F' . $row, $fecha_D);
    $sheet->setCellValue('G' . $row, $i['DiasTransHoy']);
    $sheet->setCellValue('H' . $row, $De_0_a_7_Dias);
    $sheet->setCellValue('I' . $row, $De_8_a_14_Dias);
    $sheet->setCellValue('J' . $row, $De_15_a_21_Dias);
    $sheet->setCellValue('K' . $row, $De_22_a_31_Dias);
    $sheet->setCellValue('L' . $row, $Mas_31_Dias); 
    $sheet->setCellValue('M' . $row, $total);
    $sheet->setCellValue('N' . $row, $i['Ruta']);
    $sheet->setCellValue('O' . $row, $i['Supervisor']);

    /** centrar las celdas **/
    $spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('E'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('F'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('G'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('H'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('I'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('J'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('K'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('L'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('M'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('N'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('O'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $row++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header('Content-Disposition: attachment;filename="Facturas Pendientes por Cobrar Divisas del '.$fechai.' hasta '.$fechaf.'.xlsx"');

header('Cache-Control: max-age=0');


$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');
