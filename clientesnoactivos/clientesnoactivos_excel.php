<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require ('../vendor/autoload.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("clientesnoactivos_modelo.php");

//INSTANCIAMOS EL MODELO
$clientesnoactivos = new ClientesNoActivos();

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
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');
$sheet->setCellValue('A1', 'Codigo Cliente')->setCellValue('B1', 'Descripcion')->setCellValue('C1', 'Rif')->setCellValue('D1', 'Direccion')->setCellValue('E1', 'Estatus')->setCellValue('F1', 'Dias Visita');

$total = $clientesnoactivos->getTotalClientesnoActivos($fechai, $fechaf,$codvend);
$query = $clientesnoactivos->getClientesNoactivos($fechai, $fechaf, $codvend);
$row = 2;
foreach ($query as $i) {
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A' . $row, $i['codclie']);
    $sheet->setCellValue('B' . $row, $i['descrip']);
    $sheet->setCellValue('C' . $row, $i['id3']);
    $sheet->setCellValue('D' . $row, utf8_encode($i['direc1'])." ".utf8_encode($i['direc2']));
    if($i['escredito'] == 1){
        $sheet->setCellValue('E' . $row, "SOLVENTE" );
    }else{
        $sheet->setCellValue('E' . $row, "BLOQUEADO: ".utf8_encode($i['observa']) );
    }
    $sheet->setCellValue('F' . $row, $i['diasvisita']);
    $row++;
}
$sheet->setCellValue('B' . ($row+3), 'Clientes NO Activados: ' . $total[0]['cuenta']);


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Clientes_no_activados_de_'.$fechai.'_al_'.$fechaf.'.xls"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
ob_end_clean();
ob_start();
$writer->save('php://output');

