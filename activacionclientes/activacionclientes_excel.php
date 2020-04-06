<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("activacionclientes_modelo.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    //INSTANCIAMOS EL MODELO
    $actclientes = new Activacionclientes();

    $fechaf = $_POST['fecha_final'];

    require '../vendor/autoload.php';

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');
    $sheet->setCellValue('A1', 'Ultima Venta')->setCellValue('B1', 'Codigo Cliente')->setCellValue('C1', 'Descripcion')->setCellValue('D1', 'Rif')->setCellValue('E1', 'CodVend')->setCellValue('F1', 'Pendiente');

    $query = $actclientes->lista_busca_activacionclientes($fechaf);
    $row = 2;
    foreach ($query as $i) {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A' . $row, date("d/m/Y", strtotime($i['fechauv'])));
        $sheet->setCellValue('B' . $row, $i['codclie']);
        $sheet->setCellValue('C' . $row, utf8_decode($i['descrip']));
        $sheet->setCellValue('D' . $row, $i['id3']);
        $sheet->setCellValue('E' . $row, $i['codvend']);
        $sheet->setCellValue('F' . $row, number_format($i['total'],2, ",", "."));
        $row++;
    }

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Clientes_no_activados_hasta_la_fecha_'.$fechaf.'.xls"');
    header('Cache-Control: max-age=0');

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
    $writer->save('php://output');


