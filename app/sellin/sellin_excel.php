<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require ('../vendor/autoload.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("sellin_modelo.php");

//INSTANCIAMOS EL MODELO
$sellin = new sellin();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$marca = $_GET['marca'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');
$sheet->setCellValue('A1', 'CODPROD')->setCellValue('B1', 'PRODUCTO')->setCellValue('C1', 'COMPRA')->setCellValue('D1', 'DEVOLCOMP')->setCellValue('E1', 'TOTAL')->setCellValue('F1', 'MARCA');

$query =  $sellin->getsellin($fechai, $fechaf, $marca);
$row = 2;
foreach ($query as $i) {
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A' . $row, $i['coditem']);
    $sheet->setCellValue('B' . $row, utf8_decode($i['producto']));
    $sheet->setCellValue('C' . $row, number_format($i['compras'], 1, ",", "."));
    $sheet->setCellValue('D' . $row, number_format($i['devol'], 1, ",", "."));
    $sheet->setCellValue('E' . $row, number_format($i['total'],1, ",", "."));
    $sheet->setCellValue('F' . $row, $i['marca']);
    $row++;
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="sell_in_compras_de_'.$fechai.'_al_'.$fechaf.'.xls"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
ob_end_clean();
ob_start();
$writer->save('php://output');

