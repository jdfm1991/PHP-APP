<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require ('../vendor/autoload.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("clientescodnestle_modelo.php");

//INSTANCIAMOS EL MODELO
$clientescodnestle  = new ClientesCodNestle();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$opc = $_GET['opc'];
$ruta = $_GET['vendedor'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');
$sheet->setCellValue('A1', 'Ruta')->setCellValue('B1', 'Codigo Cliente')->setCellValue('C1', 'Nombre del Cliente')->setCellValue('D1', 'Rif')->setCellValue('E1', 'Cliente Desde')->setCellValue('F1', 'Día de Visita')->setCellValue('G1', 'Codigo Nestle');

$query = $clientescodnestle ->getClientes_cnestle($opc, $ruta);
$row = 2;
foreach ($query as $i) {
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A'.$row, $i['codvend']);
    $sheet->setCellValue('B'.$row, $i['codclie']);
    $sheet->setCellValue('C'.$row, utf8_decode($i['descrip']));
    $sheet->setCellValue('D'.$row, $i['rif']);
    $sheet->setCellValue('E'.$row, date('d/m/Y',strtotime($i['fecha'])));
    $sheet->setCellValue('F'.$row, $i['dvisita']);
    $sheet->setCellValue('G'.$row, $i['codnestle']);
    $row++;
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Clientes_con_Cod_Nestle.xls"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
ob_end_clean();
ob_start();
$writer->save('php://output');
