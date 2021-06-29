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
require_once("reportecompras_modelo.php");

//INSTANCIAMOS EL MODELO
$reporte = new ReporteCompras();

$fechai = $_GET['fechai'];
$marca = $_GET['marca'];
$n = $_GET['n'];
$v = $_GET['v'];

$separa = explode("-", $fechai);
$ano = $separa[0];
$mes = $separa[1];
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
//$spreadsheet->getActiveSheet()->getStyle('A7:T7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');
//$spreadsheet->getActiveSheet()->getStyle('A8:T8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');

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
$objDrawing->setCoordinates('O1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$spreadsheet->getActiveSheet()->getStyle('A3:F3')->getFont()->setSize(18);
$spreadsheet->getActiveSheet()->getStyle('A5:F5')->getFont()->setSize(18);
$sheet->setCellValue('A1', 'REPORTE DE COMPRAS');
$sheet->setCellValue('A3', 'Proveedor: '. $marca);
$sheet->setCellValue('A5', strtoupper($meses[intval($mes)]) . " " . $ano);

/** TITULO DE LA TABLA **/
$spreadsheet->getActiveSheet()
    ->mergeCells('A1:H1')->mergeCells('A3:D3')->mergeCells('A5:G5')
    ->mergeCells('A7:A8')->mergeCells('B7:B8')->mergeCells('C7:C8')
    ->mergeCells('D7:D8')->mergeCells('E7:F7')->mergeCells('G7:G8')
    ->mergeCells('H7:I7')->mergeCells('J7:K7')->mergeCells('L7:O7')
    ->mergeCells('P7:P8')->mergeCells('Q7:Q8')->mergeCells('R7:R8')
    ->mergeCells('S7:S8')->mergeCells('T7:T8')->mergeCells('T7:T8');

$sheet->setCellValue('A7', Strings::titleFromJson('#'))
    ->setCellValue('B7', Strings::titleFromJson('codigo_prod'))
    ->setCellValue('C7', Strings::titleFromJson('descrip_prod'))
    ->setCellValue('D7', Strings::titleFromJson('display_por_bulto'))
    ->setCellValue('E7', Strings::titleFromJson('ultimo_precio_compra'))
    ->setCellValue('E8', Strings::titleFromJson('display'))
    ->setCellValue('F8', Strings::titleFromJson('bulto'))
    ->setCellValue('G7', Strings::titleFromJson('porcentaje_rentabilidad'))
    ->setCellValue('H7', Strings::titleFromJson('fecha_penultima_compra'))
    ->setCellValue('H8', Strings::titleFromJson('fecha'))
    ->setCellValue('I8', Strings::titleFromJson('bultos'))
    ->setCellValue('J7', Strings::titleFromJson('fecha_ultima_compra'))
    ->setCellValue('J8', Strings::titleFromJson('fecha'))
    ->setCellValue('K8', Strings::titleFromJson('bultos'))
    ->setCellValue('L7', Strings::titleFromJson('ventas_mes_anterior'))
    ->setCellValue('L8', '1')
    ->setCellValue('M8', '2')
    ->setCellValue('N8', '3')
    ->setCellValue('O8', '4')
    ->setCellValue('P7', Strings::titleFromJson('ventas_total_ult_mes'))
    ->setCellValue('Q7', Strings::titleFromJson('existencia_actual_bultos'))
    ->setCellValue('R7', Strings::titleFromJson('dias_inventario'))
    ->setCellValue('S7', Strings::titleFromJson('sugerido'))
    ->setCellValue('T7', Strings::titleFromJson('pedido'));


$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:T8');

$i = 9;
$num=0;
foreach ($v as $key=>$coditem) {
    if(!hash_equals("", $n[$key] ))
    {
        $row = $reporte->get_reportecompra_por_codprod($coditem, $fechai);
        $compra = $reporte->get_ultimascompras_por_codprod($coditem);

        /** cargado de las filas **/
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A' . $i, $num+1);
        $sheet->setCellValue('B' . $i, $row[0]["codproducto"]);
        $sheet->setCellValue('C' . $i, $row[0]["descrip"]);
        $sheet->setCellValue('D' . $i, number_format($row[0]["displaybultos"], 0));
        $sheet->setCellValue('E' . $i, Strings::rdecimal($row[0]["costodisplay"], 2));
        $sheet->setCellValue('F' . $i, Strings::rdecimal($row[0]["costobultos"], 2));
        $sheet->setCellValue('G' . $i, Strings::rdecimal($row[0]["rentabilidad"], 2) . "  %");
        $sheet->setCellValue('H' . $i, (count($compra) > 0) ? date(FORMAT_DATE,strtotime($compra[0]["fechapenultimacompra"])) : '------------');
        $sheet->setCellValue('I' . $i, (count($compra) > 0) ? number_format($compra[0]["bultospenultimacompra"], 0) : 0);
        $sheet->setCellValue('J' . $i, (count($compra) > 0) ? date(FORMAT_DATE,strtotime($compra[0]["fechaultimacompra"])) : '------------');
        $sheet->setCellValue('K' . $i, (count($compra) > 0) ? number_format($compra[0]["bultosultimacompra"], 0) : 0);
        $sheet->setCellValue('L' . $i, number_format($row[0]["semana1"], 0));
        $sheet->setCellValue('M' . $i, number_format($row[0]["semana2"], 0));
        $sheet->setCellValue('N' . $i, number_format($row[0]["semana3"], 0));
        $sheet->setCellValue('O' . $i, number_format($row[0]["semana4"], 0));
        $sheet->setCellValue('P' . $i, number_format($row[0]["totalventasmesanterior"], 0));
        $sheet->setCellValue('Q' . $i, Strings::rdecimal($row[0]["bultosexistentes"], 2));
        $sheet->setCellValue('R' . $i, number_format($row[0]["diasdeinventario"], 0));
        $sheet->setCellValue('S' . $i, Strings::rdecimal($row[0]["sugerido"], 2));
        $sheet->setCellValue('T' . $i, $n[$key]);


        /** centrarlas las celdas **/
        $spreadsheet->getActiveSheet()->getStyle('A'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('B'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('C'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('D'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('E'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('F'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        if($row[0]["rentabilidad"] > 30){
            //pinta la celda en rojo
            $spreadsheet->getActiveSheet()->getStyle('G'.$i)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'],), 'alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        }else {//solo lo centra
            $spreadsheet->getActiveSheet()->getStyle('G'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        }
        $spreadsheet->getActiveSheet()->getStyle('H'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('I'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('J'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('K'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('L'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('M'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('N'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('O'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('P'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('Q'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('R'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('S'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle('T'.$i)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

        $i++;
        $num++;
    }
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="REPORTE_COMPRAS_'.strtoupper($meses[intval($mes)])."_".$ano.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');

