<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require ('../vendor/autoload.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("inventarioglobal_modelo.php");

//INSTANCIAMOS EL MODELO
$invglobal = new InventarioGlobal();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//verificamos si existe al menos 1 deposito selecionado
//y se crea el array.
if(isset($_GET['depo'])){
    $numero = $_GET['depo'];
} else {
    $numero = array();
}

//se contruye un string para listar los depositvos seleccionados
//en caso que no haya ninguno, sera vacio
$edv = "";
if (count($numero) > 0) {
    foreach ($numero as $i)
        $edv .= " OR CodUbic = ?";
}
$coditem = $cantidad = $tipo = array();
$fechaf = date('Y-m-d');
$dato = explode("-", $fechaf); //Hasta
$aniod = $dato[0]; //año
$mesd = $dato[1]; //mes
$diad = "01"; //dia
$fechai = $aniod . "-01-01";
$t = 0;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');
$sheet->getStyle('A:H')->getAlignment()->setHorizontal('center');
$sheet->setCellValue('A1', 'Codigo')
    ->setCellValue('B1', 'Producto')
    ->setCellValue('C1', 'Cantidad Bultos por Despachar')
    ->setCellValue('D1', 'Cantidad Paquetes por Despachar')
    ->setCellValue('E1', 'Cantidad Bultos Sistema')
    ->setCellValue('F1', 'Cantidad Paquetes Sistema')
    ->setCellValue('G1', 'Total Inventario Bultos')
    ->setCellValue('H1', 'Total Inventario Paquetes');

$devolucionesDeFactura = $invglobal->getDevolucionesDeFactura($edv, $fechai, $fechaf, $numero);
if(count($devolucionesDeFactura) > 0) {
    foreach ($devolucionesDeFactura as $devol) {
        $coditem[] = $devol['coditem'];
        $cantidad[] = $devol['cantidad'];
        $tipo[] = $devol['esunid'];
        $t += 1;
    }
}

$relacion_inventarioglobal = $invglobal->getInventarioGlobal($edv, $fechai, $fechaf, $numero);
$tbulto = $tpaq = $tbultoinv = $tpaqinv = $tbultsaint = $tpaqsaint = 0;
$cant_paq = 0;
$cant_bul = 0;

$row = 2;
foreach ($relacion_inventarioglobal as $i) {
    if($t > 0) {
        for($e = 0; $e < $t; $e++)
        {
            if($coditem[$e] == $i['CodProd']) {
                switch ($tipo[$e]) {
                    case '0':
                        $cant_bul = $i['bultosxdesp'] - $cantidad[$e];
                        break;
                    case '1':
                        $cant_paq = $i['paqxdesp'] - $cantidad[$e];
                        break;
                }
//                        $e = $t + 2;
                break;
            }else{
                $cant_bul = $i['bultosxdesp'];
                $cant_paq = $i['paqxdesp'];
            }
        }
    } else {
        $cant_bul = $i['bultosxdesp'];
        $cant_paq = $i['paqxdesp'];
    }
    ////conversión de bultos a paquetes
    $cantemp = $i['CantEmpaq'];
    $invbut  = $i['exis'];
    $invpaq  = $i['exunid'];

    if($cant_paq >= $cantemp){
        $conv = floor($cant_paq / $cantemp);
        $cant_paq -= ($conv * $cantemp);
        $cant_bul += $conv;
    }
    if($invpaq >= $cantemp){
        $conv = floor($invpaq / $cantemp);
        $invpaq -= ($conv * $cantemp);
        $invbut += $conv;
    }
    $tinvbult = $invbut + $cant_bul;
    $tinvpaq = $invpaq + $cant_paq;

    if($tinvpaq >= $cantemp){
        $conv1 = floor($tinvpaq / $cantemp);
        $tinvpaq -= ($conv1 * $cantemp);
        $tinvbult += $conv1;
    }

    //ASIGNAMOS EN EL SUB_ARRAY LOS DATOS PROCESADOS
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A' . $row, $i['CodProd']);
    $sheet->setCellValue('B' . $row, $i['Descrip']);
    $sheet->setCellValue('C' . $row, number_format($cant_bul,0));
    $sheet->setCellValue('D' . $row, number_format($cant_paq,0));
    $sheet->setCellValue('E' . $row, number_format($invbut,0));
    $sheet->setCellValue('F' . $row, number_format($invpaq,0));
    $sheet->setCellValue('G' . $row, number_format($tinvbult,0));
    $sheet->setCellValue('H' . $row, number_format($tinvpaq,0));

    //ACUMULAMOS LOS TOTALES
    $tbulto     += $cant_bul;
    $tpaq       += $cant_paq;
    $tbultoinv  += $tinvbult;
    $tpaqinv    += $tinvpaq;
    $tbultsaint += $invbut;
    $tpaqsaint  += $invpaq;
    $row++;
}
$spreadsheet->getActiveSheet()->getStyle('A'.$row.':H'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('17a2b8');
$sheet = $spreadsheet->getActiveSheet()->mergeCells('A'.$row.':B'.$row);
$sheet->setCellValue('A' . $row, 'Totales: ');
$sheet->setCellValue('C' . $row, number_format($tbulto,0, ",", "."));
$sheet->setCellValue('D' . $row, number_format($tpaq,0, ",", "."));
$sheet->setCellValue('E' . $row, number_format($tbultsaint,0, ",", "."));
$sheet->setCellValue('F' . $row, number_format($tpaqsaint,0, ",", "."));
$sheet->setCellValue('G' . $row, number_format($tbultoinv,0, ",", "."));
$sheet->setCellValue('H' . $row, number_format($tpaqinv,0, ",", "."));
$sheet->setCellValue('A' . ($row+2), 'Facturas sin despachar: '. count($devolucionesDeFactura));


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Listado_costos_e_inventario.xls"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
ob_end_clean();
ob_start();
$writer->save('php://output');
