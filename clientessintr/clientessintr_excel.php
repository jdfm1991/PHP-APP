<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require ('../vendor/autoload.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("clientessintr_modelo.php");

//INSTANCIAMOS EL MODELO
$clientessintr = new ClientesSintr();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$codvend = $_GET['vendedor'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');
$sheet->setCellValue('A1', 'Vend')->setCellValue('B1', 'CodClie')->setCellValue('C1', 'Cliente')->setCellValue('D1', 'Saldo');

$query = $clientessintr->getclientessintr($fechai, $fechaf, $codvend);
$num = count($query);
$row = 2;
foreach ($query as $i) {
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A' . $row, $i['codvend']);
    $sheet->setCellValue('B' . $row, $i['codclie']);
    $sheet->setCellValue('C' . $row, $i['descrip']);
    $sheet->setCellValue('D' . $row, number_format($i['debe'],2, ",", "."));
    $row++;
}
$sheet->setCellValue('B' . ($row+3), 'Total de Clientes:  '.$num);


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Clientes_sinfactura_de_'.$fechai.'_al_'.$fechaf.'.xls"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
ob_end_clean();
ob_start();
$writer->save('php://output');

