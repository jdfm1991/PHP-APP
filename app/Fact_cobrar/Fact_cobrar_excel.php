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
require_once("Fact_cobrar_modelo.php");

//INSTANCIAMOS EL MODELO
$costo = new Fact_cobrar();


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
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
foreach(range('A','K') as $columnID) {
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
$sheet->setCellValue('A1', 'Facturas por Cobrar por Ruta');
//$sheet->setCellValue('A3', 'del: '. date(FORMAT_DATE, strtotime($fechai)));
//$sheet->setCellValue('A5', 'al:  '. date(FORMAT_DATE, strtotime($fechaf)));


$spreadsheet->getActiveSheet()->mergeCells('A1:E1');

/** TITULO DE LA TABLA **/
$sheet->setCellValue('A7', ('Ruta'))
    ->setCellValue('B7', ('Documento'))
    ->setCellValue('C7', ('Código Cliente'))
    ->setCellValue('D7', ('Cliente'))
    ->setCellValue('E7', ('Fecha Emisión'))
    ->setCellValue('F7', ('Fecha Despacho'))
    ->setCellValue('G7', ('Total 0 a 7 Dias'))
    ->setCellValue('H7', ('Total 8 a 15 Dias'))
    ->setCellValue('I7', ('Total 16 a 40 Dias'))
    ->setCellValue('J7', ('Total Mayor a 7 Dias'))
    ->setCellValue('K7', ('Saldo Pendiente'))
    ->setCellValue('L7', ('Saldo Pendiente $'))
    ->setCellValue('M7', ('Supervisor'));

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A7:M7');


//realiza la consulta con marca y almacenes
$query = $costo->get_facturasPorCobrar($edv);

$row = 8;
 if (is_array($query)==true and count($query)>0)
{
    $SaldoPend = 0;
    $SaldoPendolar =0; 
    $total_SaldoPend_07=0;
    $total_SaldoPend_815=0;
    $total_SaldoPend_164=0;
    $total_SaldoPend_m40=0;

    foreach ($query as $i) {

                $SaldoPend_m40=0;
                $SaldoPend_164=0;
                $SaldoPend_815=0;
                $SaldoPend_07=0;
        

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A' . $row, $i['Ruta']);
            $sheet->setCellValue('B' . $row, $i["TipoOpe"].' '.$i["NroDoc"]);
            $sheet->setCellValue('C' . $row, $i['CodClie']);
            $sheet->setCellValue('D' . $row, $i['Cliente']);
            $sheet->setCellValue('E' . $row, date('d/m/Y', strtotime($i["FechaEmi"])));
            $sheet->setCellValue('F' . $row, date('d/m/Y', strtotime($i["FechaDesp"])));




            if($i["DiasTrans"]>=0 AND $i["DiasTrans"]<=7){


                    if($i['SaldoPend']>=1000){

                        $sheet->setCellValue('G' . $row, ($i['SaldoPend']));
                        $sheet->setCellValue('H' . $row, 0);
                        $sheet->setCellValue('I' . $row, 0);
                        $sheet->setCellValue('J' . $row, 0);
                            $SaldoPend_07 += $i['SaldoPend'];

                    }else{

                        $sheet->setCellValue('G' . $row, number_format($i['SaldoPend'],2));
                        $sheet->setCellValue('H' . $row, 0);
                        $sheet->setCellValue('I' . $row, 0);
                        $sheet->setCellValue('J' . $row, 0);
                            $SaldoPend_07 += $i['SaldoPend'];

                   }


            }else{

                if($i["DiasTrans"]>=8 AND $i["DiasTrans"]<=15){

                      if($i['SaldoPend']>=1000){

                            $sheet->setCellValue('G' . $row, 0);
                            $sheet->setCellValue('H' . $row, ($i['SaldoPend']));
                            $sheet->setCellValue('I' . $row, 0);
                            $sheet->setCellValue('J' . $row, 0);
                            $SaldoPend_815 += $i['SaldoPend'];

                    }else{

                            $sheet->setCellValue('G' . $row, 0);
                            $sheet->setCellValue('H' . $row, number_format($i['SaldoPend'],2));
                            $sheet->setCellValue('I' . $row, 0);
                            $sheet->setCellValue('J' . $row, 0);
                            $SaldoPend_815 += $i['SaldoPend'];

                   }


                }else{

                    if($i["DiasTrans"]>=16 AND $i["DiasTrans"]<=40){


                          if($i['SaldoPend']>=1000){

                            $sheet->setCellValue('G' . $row, 0);
                            $sheet->setCellValue('H' . $row, 0);
                            $sheet->setCellValue('I' . $row, ($i['SaldoPend']));
                            $sheet->setCellValue('J' . $row, 0);
                            $SaldoPend_164 += $i['SaldoPend'];

                        }else{

                            $sheet->setCellValue('G' . $row, 0);
                            $sheet->setCellValue('H' . $row, 0);
                            $sheet->setCellValue('I' . $row, number_format($i['SaldoPend'],2));
                            $sheet->setCellValue('J' . $row, 0);
                            $SaldoPend_164 += $i['SaldoPend'];

                        }


                    }else{

                        if($i["DiasTrans"]>40){


                             if($i['SaldoPend']>=1000){

                                    $sheet->setCellValue('G' . $row, 0);
                                    $sheet->setCellValue('H' . $row, 0);
                                    $sheet->setCellValue('I' . $row, 0);
                                    $sheet->setCellValue('J' . $row,  ($i['SaldoPend']));
                                    $SaldoPend_m40 += $i['SaldoPend'];

                            }else{

                                    $sheet->setCellValue('G' . $row, 0);
                                    $sheet->setCellValue('H' . $row, 0);
                                    $sheet->setCellValue('I' . $row, 0);
                                    $sheet->setCellValue('J' . $row,  number_format($i['SaldoPend'],2));
                                    $SaldoPend_m40 += $i['SaldoPend'];

                            }

                        }else{


                        }


                    }


                }


            }



            $sheet->setCellValue('K' . $row, ($SaldoPend_07+$SaldoPend_815+$SaldoPend_164+$SaldoPend_m40));
            $sheet->setCellValue('L' . $row, ($i['SaldoPendolar']));
            $sheet->setCellValue('M' . $row, $i['Supervisor'] );

            /** centrarlas las celdas **/
            $spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle('E'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle('F'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle('G'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle('H'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle('I'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle('J'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle('K'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle('L'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
            $spreadsheet->getActiveSheet()->getStyle('M'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
 
            //ACUMULAMOS LOS TOTALES
             $SaldoPend += $i['SaldoPend'];
             $SaldoPendolar += $i['SaldoPendolar'];


            $total_SaldoPend_07+=$SaldoPend_07;
            $total_SaldoPend_815+=$SaldoPend_815;
            $total_SaldoPend_164+=$SaldoPend_164;
            $total_SaldoPend_m40+=$SaldoPend_m40;
            $row++;
    }


    $spreadsheet->getActiveSheet()->getStyle('A'.$row.':M'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('17a2b8');
    $sheet = $spreadsheet->getActiveSheet()->mergeCells('A'.$row.':C'.$row);
    $sheet->setCellValue('F' . $row, 'Totales: ');
    $sheet->setCellValue('G' . $row, number_format($total_SaldoPend_07,2));
    $sheet->setCellValue('H' . $row, number_format($total_SaldoPend_815,2));
    $sheet->setCellValue('I' . $row, number_format($total_SaldoPend_164,2));
    $sheet->setCellValue('J' . $row, number_format($total_SaldoPend_m40,2));
    $sheet->setCellValue('K' . $row, number_format($SaldoPend,2));
    $sheet->setCellValue('L' . $row, number_format($SaldoPendolar,2));
    $sheet->setCellValue('M' . $row, '');
    /** centrarlas las celdas **/
    $spreadsheet->getActiveSheet()->getStyle('A'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('B'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('C'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('D'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('E'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('F'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('G'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('H'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('I'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('J'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('K'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('L'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
    $spreadsheet->getActiveSheet()->getStyle('M'.$row)->applyFromArray(array('alignment' => array('horizontal'=> \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
 

}



header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="facturas_por_Cobrar_por_Ruta.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');
