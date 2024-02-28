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

//LLAMAMOS AL MODELO
//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("recepcion_compras_modelo.php");

//INSTANCIAMOS EL MODELO
$tabladinamica = new Tabladinamica();

$i = 0;
//funcion recursiva creada para reporte Excel que evalua los numeros > 0
// y asigna la letra desde la A....hasta la Z y AA, AB, AC.....AZ
function getExcelCol($num, $letra_temp = false) {
    $numero = $num % 26;
    $letra = chr(65 + $numero);
    $num2 = intval($num / 26);
    if(!$letra_temp)
        $GLOBALS['i'] = $GLOBALS['i'] +1;

    if ($num2 > 0) {
        return getExcelCol($num2 - 1, true) . $letra;
    } else {
        return $letra;
    }
}

$data = array(
    'fechai' => $_GET['fechai'],
    'fechaf' => $_GET['fechaf'],
);

# creamos la cabecera de la tabla
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
foreach(range('B','R') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}

# Logo
$gdImage = imagecreatefrompng(PATH_LIBRARY.'build/images/logo.png');
$objDrawing = new MemoryDrawing();
$objDrawing->setName('Sample image');
$objDrawing->setDescription('TEST');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
$objDrawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
$objDrawing->setHeight(108);
$objDrawing->setWidth(128);
$objDrawing->setCoordinates('H1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$sheet->setCellValue('A1', 'LA CONFIMANIA.COM, C.A' /*Empresa::getName()*/);
$spreadsheet->getActiveSheet()->mergeCells('A1:G1');
$spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray(array('font' => array('bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$temp='';
switch ($_GET['t']) {
    case 'f': $temp = Strings::titleFromJson('factura'); break;
    case 'n': $temp = Strings::titleFromJson('nota_de_entrega'); break;
}
$spreadsheet->getActiveSheet()->getStyle('A2:F2')->getFont()->setSize(18);
$sheet->setCellValue('A2', 'Recepción de Compras ('.$temp.')');
$spreadsheet->getActiveSheet()->mergeCells('A2:K2');

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);


$sheet->setCellValue('C4', 'Desde:');
$sheet->setCellValue('D4', date(FORMAT_DATE, strtotime($_GET['fechai'])));
$spreadsheet->getActiveSheet()->mergeCells('D4:E4');
$spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('D4:E4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$sheet->setCellValue('G4', 'Hasta:');
$sheet->setCellValue('H4', date(FORMAT_DATE, strtotime($_GET['fechaf'])));
$spreadsheet->getActiveSheet()->mergeCells('H4:I4');
$spreadsheet->getActiveSheet()->getStyle('G4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('H4:I4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

foreach(range('A','Y') as $columnID) {
    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
}


$row = 8;
$i = 0;
$sheet->setCellValue(getExcelCol($i).$row, Strings::titleFromJson('#'));
$sheet->setCellValue(getExcelCol($i).$row, "Código Proveedor");
$sheet->setCellValue(getExcelCol($i).$row, "Descripción Proveedor");
$sheet->setCellValue(getExcelCol($i).$row, "Documento");
$sheet->setCellValue(getExcelCol($i).$row, "Código Producto");
$sheet->setCellValue(getExcelCol($i).$row, "Descripción Producto");
$sheet->setCellValue(getExcelCol($i).$row, "Costo Unitario");
$sheet->setCellValue(getExcelCol($i).$row, "Cantidad");
$sheet->setCellValue(getExcelCol($i).$row, "Costo Total");
$sheet->setCellValue(getExcelCol($i).$row, "Fecha");
$sheet->setCellValue(getExcelCol($i).$row, "Tasa");

//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++)
    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($n, true).$row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE),'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
$spreadsheet->getActiveSheet()->getStyle( 'A'.$row.':'.getExcelCol($aux, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'c8dcff'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),));




$datos = array();
switch ($_GET['t']) {
    case 'f': $datos = $tabladinamica->getTabladinamicaFactura($data); break;
    case 'n': $datos = $tabladinamica->getTabladinamicaNotaDeEntrega($data); break;
}

//DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
$arr_data = Array();

$Costo = $Cantidad = $TotalItem = 0;

if (is_array($datos)==true and count($datos)>0)
{
    foreach ($datos as $key => $row)
    {
        //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        $montod = $montobs = $descuento = 0;

        if($row['tipo']=='I' or $row['tipo']=='K'){
            $multiplicador = -1;
        }else{
            $multiplicador = 1;
        }

                 $sub_array['num']  = $key+1;
                $sub_array['CodProv']       = $row["CodProv"];
                $sub_array['Descrip']      = utf8_encode($row["Descrip"]);
                $sub_array['NumeroD']     = $row["NumeroD"];
                $sub_array['CodItem']          = $row["CodItem"];
                $sub_array['Descrip1']       = utf8_encode($row["Descrip1"]);
                $sub_array['Costo']       = Strings::rdecimal($row["Costo"],2);
                $sub_array['Cantidad']       = Strings::rdecimal($row["Cantidad"]);
                $sub_array['TotalItem']     = Strings::rdecimal($row["TotalItem"],2);
                $sub_array['fechae']        = date(FORMAT_DATE, strtotime($row["FechaE"]));
                $sub_array['tasa']           =  Strings::rdecimal($row['tasa'],2);

                $Costo  += $row["Costo"] * $multiplicador;
                $Cantidad  += $row["Cantidad"] * $multiplicador;
                $TotalItem  += $row["TotalItem"]  * $multiplicador;

        $arr_data[] = $sub_array;
    }
}


$row = 9;
if (is_array($arr_data)==true and count($arr_data)>0) {
    foreach ($arr_data as $x) {
        $i = 0;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue(getExcelCol($i) . $row, $x['num']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['CodProv']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['Descrip']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['NumeroD']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['CodItem']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['Descrip1']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['Costo']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['Cantidad']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['TotalItem']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['fechae']);
        $sheet->setCellValue(getExcelCol($i) . $row, $x['tasa']);

        $i = 0;
        /** centrarlas las celdas **/
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
      
        $row++;
    }
}

$i = 0;
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue(getExcelCol($i) . $row,'Totales');

if($Costo>=1000){
    $sheet->setCellValue(getExcelCol($i+=5) . $row, $Costo);
}else{
    $sheet->setCellValue(getExcelCol($i+=5) . $row, number_format($Costo, 2));
}

if(($Costo*$Cantidad)>=1000){
    $sheet->setCellValue(getExcelCol($i) . $row, ($Costo*$Cantidad));
}else{
    $sheet->setCellValue(getExcelCol($i) . $row, number_format(($Costo*$Cantidad), 2));
}

if($TotalItem>=1000){
    $sheet->setCellValue(getExcelCol($i) . $row, $TotalItem);
}else{
    $sheet->setCellValue(getExcelCol($i) . $row, number_format($TotalItem, 2));
}

$spreadsheet->getActiveSheet()->mergeCells('A'.$row.':F'.$row);
$i = 0;
/** centrarlas las celdas **/
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i+=5) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle(getExcelCol($i) . $row)->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));


$spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(80);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte_Recepcion_Compras'.date(FORMAT_DATE, strtotime($_GET['fechai'])).'_al_'.date(FORMAT_DATE, strtotime($_GET['fechaf'])).'_'.$_GET['marca'].'_'.$_GET['vendedor'].'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');