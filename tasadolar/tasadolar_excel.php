<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require ('../vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("tasadolar_modelo.php");

//INSTANCIAMOS EL MODELO
$tasa = new TasaDolar();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
// Logo
$gdImage = imagecreatefrompng('../public/build/images/logo.png');
$objDrawing = new MemoryDrawing();
$objDrawing->setName('Sample image');
$objDrawing->setDescription('TEST');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight(108);
$objDrawing->setWidth(128);
$objDrawing->setCoordinates('E2');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$sheet->setCellValue('A1', 'HISTORICO DE TASA DOLAR COMPRA');

$spreadsheet->getActiveSheet()->mergeCells('A1:C1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue('B4', '#')
    ->setCellValue('C4', 'Fecha')
    ->setCellValue('D4', 'Tasa');

$style_title = new Style();
$style_title->applyFromArray(
    array(
        'font' => array(
            'name' => 'Arial',
            'bold'  => true,
            'color' => array('rgb' => '000000')
        ),
        'fill' => array(
            'fillType' => Fill::FILL_SOLID,
            'color' => ['argb' => 'F2F2F2'],
        ),
        'borders' => array(
            'top' => ['borderStyle' => Border::BORDER_THIN],
            'bottom' => ['borderStyle' => Border::BORDER_THIN],
            'right' => ['borderStyle' => Border::BORDER_MEDIUM],
        ),
        'alignment' => array(
            'horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrap' => TRUE
        )
    )
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'B4:D4');

$query =  $tasa->get_tasadolar();
$num = count($query);
$row = 5;
foreach ($query as $key=>$i) {
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('B' . $row, $key+1);
    $sheet->setCellValue('C' . $row, date("d/m/Y", strtotime($i["fechae"])));
    $sheet->setCellValue('D' . $row, number_format($i["tasa"], 2, ",", "."));
    $spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $row++;
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="reporte_tasa_dolar_compra.xls"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
ob_end_clean();
ob_start();
$writer->save('php://output');
