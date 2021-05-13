<?php
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
require_once("inventarioglobal_modelo.php");

//INSTANCIAMOS EL MODELO
$invglobal = new InventarioGlobal();

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
foreach(range('A','H') as $columnID) {
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
$objDrawing->setCoordinates('G1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$sheet->setCellValue('A1', 'REPORTE DE INVENTARIO GLOBAL');
$sheet->setCellValue('A3', 'del: '. date("d/m/Y", strtotime($fechai)));
$sheet->setCellValue('A5', 'al:  '. date("d/m/Y", strtotime($fechaf)));


$spreadsheet->getActiveSheet()->mergeCells('A1:F1');
//$sheet->getStyle('A:H')->getAlignment()->setHorizontal('center');
/** TITULO DE LA TABLA **/
$sheet->setCellValue('A7', 'Codigo')
    ->setCellValue('B7', 'Producto')
    ->setCellValue('C7', 'Cant Bultos X Desp')
    ->setCellValue('D7', 'Cant Paq X Desp')
    ->setCellValue('E7', 'Cant Bultos Sistema')
    ->setCellValue('F7', 'Cant Paq Sistema')
    ->setCellValue('G7', 'Total Invent Bultos')
    ->setCellValue('H7', 'Total Invent Paq');

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:H7');


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

$row = 8;
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

    /** centrarlas las celdas **/
    $spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('E'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('F'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('G'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('H'.$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

    //ACUMULAMOS LOS TOTALES
    $tbulto     += $cant_bul;
    $tpaq       += $cant_paq;
    $tbultoinv  += $tinvbult;
    $tpaqinv    += $tinvpaq;
    $tbultsaint += $invbut;
    $tpaqsaint  += $invpaq;
    $row++;
}
$spreadsheet->getActiveSheet()->getStyle('A'.$row.':H'.$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('17a2b8');
$sheet = $spreadsheet->getActiveSheet()->mergeCells('A'.$row.':B'.$row);
$sheet->setCellValue('A' . $row, 'Totales: ');
$sheet->setCellValue('C' . $row, number_format($tbulto,0));
$sheet->setCellValue('D' . $row, number_format($tpaq,0));
$sheet->setCellValue('E' . $row, number_format($tbultsaint,0));
$sheet->setCellValue('F' . $row, number_format($tpaqsaint,0));
$sheet->setCellValue('G' . $row, number_format($tbultoinv,0));
$sheet->setCellValue('H' . $row, number_format($tpaqinv,0));

/** centrarlas las celdas **/
$spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('E'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('F'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('G'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('H'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));


$sheet->setCellValue('A' . ($row+2), 'Facturas sin despachar: '. count($devolucionesDeFactura));


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Listado_costos_e_inventario.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');
