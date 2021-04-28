<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require ('../vendor/autoload.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("clientesnuevos_modelo.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//INSTANCIAMOS EL MODELO
$clientesnuevos = new ClientesNuevos();

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getStyle('A1:E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');
$sheet->setCellValue('A1', 'Cod CLiente')->setCellValue('B1', 'Cliente')->setCellValue('C1', 'Rif')->setCellValue('D1', 'Fecha')->setCellValue('E1', 'Ruta');

$query = $clientesnuevos->getClientesNuevos($fechai, $fechaf);
$num = count($query);
$row = 2;
foreach ($query as $i) {
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A' . $row, $i['codclie']);
    $sheet->setCellValue('B' . $row, $i['descrip']);
    $sheet->setCellValue('C' . $row, $i['id3']);
    $sheet->setCellValue('D' . $row, date("d/m/Y", strtotime($i['fechae'])));
    $sheet->setCellValue('E' . $row, $i['codvend']);
    $row++;
}
$sheet->setCellValue('B' . ($row+3), 'Total de Clientes Nuevos:  '.$num);


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Clientes_nuevos__del_'.$fechai.'_al_'.$fechaf.'.xls"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
ob_end_clean();
ob_start();
$writer->save('php://output');

