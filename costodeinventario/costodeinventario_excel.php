<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require ('../vendor/autoload.php');

//LLAMAMOS AL MODELO
require_once("costodeinventario_modelo.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//INSTANCIAMOS EL MODELO
$costo = new CostodeInventario();

//obtenemos la marca seleccionada enviada por get
$marca = $_GET['marca'];

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
if(count($numero)>0) {
    foreach ($numero AS $i) {
        $edv .= "'" . $i . "',";
    }
}

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
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getStyle('A1:K1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');
$sheet->setCellValue('A1', 'Codigo')
    ->setCellValue('B1', 'Producto')
    ->setCellValue('C1', 'Marca')
    ->setCellValue('D1', 'Costo Bultos')
    ->setCellValue('E1', 'Costo Unidad')
    ->setCellValue('F1', 'Precio')
    ->setCellValue('G1', 'Bultos')
    ->setCellValue('H1', 'Paquetes')
    ->setCellValue('I1', 'Total Costo Bultos')
    ->setCellValue('J1', 'Total Costo Unidades')
    ->setCellValue('K1', 'Peso');

//realiza la consulta con marca y almacenes
$query = $costo->getCostosdEinventario($edv, $marca);

//inicializamos los acumuladores
$costos = 0;
$costos_p = 0;
$precios = 0;
$bultos = 0;
$paquetes = 0;
$total_costo_bultos = 0;
$total_costo_paquetes = 0;
$total_tara = 0;

$row = 2;
foreach ($query as $i) {

    if ($i['display'] == 0) {
        $cdisplay = 0;
    } else {
        $cdisplay = $i['costo'] / $i['display'];
    }

    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A' . $row, $i['codprod']);
    $sheet->setCellValue('B' . $row, $i['descrip']);
    $sheet->setCellValue('C' . $row, $i['marca']);
    $sheet->setCellValue('D' . $row, number_format($i['costo'],2, ",", "."));
    $sheet->setCellValue('E' . $row, number_format($cdisplay,2, ",", "."));
    $sheet->setCellValue('F' . $row, number_format($i['precio'],2, ",", "."));
    $sheet->setCellValue('G' . $row, number_format($i['bultos'],2, ",", "."));
    $sheet->setCellValue('H' . $row, number_format($i['paquetes'],2, ",", "."));
    $sheet->setCellValue('I' . $row, number_format($i['costo'] * $i['bultos'],2, ",", "."));
    $sheet->setCellValue('J' . $row, number_format($cdisplay * $i['paquetes'],2, ",", "."));
    $sheet->setCellValue('K' . $row, number_format($i['tara'],2, ",", "."));

    //ACUMULAMOS LOS TOTALES
    $costos += $i['costo'];
    $costos_p += $cdisplay;
    $precios += $i['precio'];
    $bultos += $i['bultos'];
    $paquetes += $i['paquetes'];
    $total_costo_bultos += ($i['costo'] * $i['bultos']);
    $total_costo_paquetes += ($cdisplay * $i['paquetes']);
    $total_tara += $i['tara'];
    $row++;
}
$spreadsheet->getActiveSheet()->getStyle('A'.$row.':K'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('17a2b8');
$sheet = $spreadsheet->getActiveSheet()->mergeCells('A'.$row.':C'.$row);
$sheet->setCellValue('A' . $row, 'Totales: ');
$sheet->setCellValue('D' . $row, number_format($costos,2, ",", "."));
$sheet->setCellValue('E' . $row, number_format($costos_p,2, ",", "."));
$sheet->setCellValue('F' . $row, number_format($precios,2, ",", "."));
$sheet->setCellValue('G' . $row, number_format($bultos,2, ",", "."));
$sheet->setCellValue('H' . $row, number_format($paquetes,2, ",", "."));
$sheet->setCellValue('I' . $row, number_format($total_costo_bultos,2, ",", "."));
$sheet->setCellValue('J' . $row, number_format($total_costo_paquetes,2, ",", "."));
$sheet->setCellValue('K' . $row, number_format($total_tara,2, ",", "."));


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Listado_costos_e_inventario.xls"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
ob_end_clean();
ob_start();
$writer->save('php://output');
