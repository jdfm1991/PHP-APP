<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require ('../vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;

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
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getStyle('A7:T7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');
$spreadsheet->getActiveSheet()->getStyle('A8:T8')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');

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

$sheet->setCellValue('A7', '#')
    ->setCellValue('B7', 'Codigo')
    ->setCellValue('C7', 'Descripcion')
    ->setCellValue('D7', 'Display x Bulto')
    ->setCellValue('E7', 'Último precio de compra')
    ->setCellValue('E8', 'Display')
    ->setCellValue('F8', 'Bulto')
    ->setCellValue('G7', '% Rent')
    ->setCellValue('H7', 'Penultima compra')
    ->setCellValue('H8', 'Fecha')
    ->setCellValue('I8', 'Bultos')
    ->setCellValue('J7', 'Ultima compra')
    ->setCellValue('J8', 'Fecha')
    ->setCellValue('K8', 'Bultos')
    ->setCellValue('L7', 'Ventas mes anterior')
    ->setCellValue('L8', '1')
    ->setCellValue('M8', '2')
    ->setCellValue('N8', '3')
    ->setCellValue('O8', '4')
    ->setCellValue('P7', 'total mes anterior')
    ->setCellValue('Q7', 'Existencia Actual Bultos')
    ->setCellValue('R7', 'Dias Inv')
    ->setCellValue('S7', 'Sugerido')
    ->setCellValue('T7', 'Pedido');


$style_title = new Style();
$style_title->applyFromArray(
    array(
        'font' => array(
            'name' => 'Arial',
            'bold'  => true,
            'color' => array('rgb' => '000000')
        ),
        'fill' => array(
            'fillType' => Fill::FILL_SOLID,
            'color' => ['argb' => 'F2F2F2'],
        ),
        'borders' => array(
            'top' => ['borderStyle' => Border::BORDER_THIN],
            'bottom' => ['borderStyle' => Border::BORDER_THIN],
            'right' => ['borderStyle' => Border::BORDER_MEDIUM],
        ),
        'alignment' => array(
            'horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrap' => TRUE
        )
    )
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
        $sheet->setCellValue('D' . $i, number_format($row[0]["displaybultos"], 0, ",", "."));
        $sheet->setCellValue('E' . $i, number_format($row[0]["costodisplay"], 2, ",", "."));
        $sheet->setCellValue('F' . $i, number_format($row[0]["costobultos"], 2, ",", "."));
        $sheet->setCellValue('G' . $i, number_format($row[0]["rentabilidad"], 1, ",", ".") . "  %");
        $sheet->setCellValue('H' . $i, (count($compra) > 0) ? date("d/m/Y",strtotime($compra[0]["fechapenultimacompra"])) : 0);
        $sheet->setCellValue('I' . $i, (count($compra) > 0) ? number_format($compra[0]["bultospenultimacompra"], 0, ",", ".") : 0);
        $sheet->setCellValue('J' . $i, (count($compra) > 0) ? date("d/m/Y",strtotime($compra[0]["fechaultimacompra"])) : 0);
        $sheet->setCellValue('K' . $i, (count($compra) > 0) ? number_format($compra[0]["bultosultimacompra"], 0, ",", ".") : 0);
        $sheet->setCellValue('L' . $i, number_format($row[0]["semana1"], 0, ",", "."));
        $sheet->setCellValue('M' . $i, number_format($row[0]["semana2"], 0, ",", "."));
        $sheet->setCellValue('N' . $i, number_format($row[0]["semana3"], 0, ",", "."));
        $sheet->setCellValue('O' . $i, number_format($row[0]["semana4"], 0, ",", "."));
        $sheet->setCellValue('P' . $i, number_format($row[0]["totalventasmesanterior"], 0, ",", "."));
        $sheet->setCellValue('Q' . $i, number_format($row[0]["bultosexistentes"], 1, ",", "."));
        $sheet->setCellValue('R' . $i, number_format($row[0]["diasdeinventario"], 0, ",", "."));
        $sheet->setCellValue('S' . $i, number_format($row[0]["sugerido"], 1, ",", "."));
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

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="REPORTE_COMPRAS_'.strtoupper($meses[intval($mes)])."_".$ano.'.xls"');
header('Cache-Control: max-age=0');


$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
ob_end_clean();
ob_start();
$writer->save('php://output');

