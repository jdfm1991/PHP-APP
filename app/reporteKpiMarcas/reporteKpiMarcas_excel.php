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
require_once("Kpi_Marcas_dos_modelo.php");

//INSTANCIAMOS EL MODELO
$modelos = new Kpi_Marcas_dos();

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

$mes = $_GET['fecha'];

# creamos la cabecera de la tabla
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

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
$objDrawing->setCoordinates('P1');
$objDrawing->setWorksheet($spreadsheet->getActiveSheet());

/** DATOS DEL REPORTE **/
$spreadsheet->getActiveSheet()->getStyle('A1:F1')->getFont()->setSize(25);
$sheet->setCellValue('A1', Empresa::getName());
$spreadsheet->getActiveSheet()->mergeCells('A1:N1');
$spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray(array('font' => array('bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_JUSTIFY, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

$spreadsheet->getActiveSheet()->getStyle('A2:F2')->getFont()->setSize(18);
$sheet->setCellValue('A2', 'REPORTE KPI (NEW)');
$spreadsheet->getActiveSheet()->mergeCells('A2:K2');

$style_title = new Style();
$style_title->applyFromArray(
    Excel::styleHeadTable()
);


 if($mes=='01'){

                                    $string='ENERO';

                                }else{

                                        if($mes=='02'){
                                            $string='FEBRERO';
                                        }else{

                                            if($mes=='03'){
                                                $string='MARZO';      
                                            }else{

                                                if($mes=='04'){
                                                    $string='ABRIL';
                                                }else{

                                                    if($mes=='05'){
                                                        $string='MAYO';
                                                    }else{

                                                        if($mes=='06'){
                                                            $string='JUNIO';
                                                        }else{

                                                            if($mes=='07'){
                                                                $string='JULIO';
                                                            }else{

                                                                if($mes=='08'){
                                                                    $string='AGOSTO';
                                                                }else{

                                                                    if($mes=='09'){
                                                                        $string='SEPTIEMBRE';
                                                                    }else{

                                                                        if($mes=='10'){
                                                                            $string='OCTUBRE';
                                                                        }else{

                                                                            if($mes=='11'){
                                                                                $string='NOVIEMBRE';
                                                                            }else{

                                                                                if($mes=='12'){
                                                                                    $string='DICIEMBRE';
                                                                                }else{
                                                                                        $string='';
                                                                                }   
                                                                                    
                                                                            }
                                                                            
                                                                        }
                                                                        
                                                                    }
                                                                    
                                                                }
                                                                
                                                            }
                                                            
                                                        }
                                                            
                                                    }
                                                                            
                                                }
                                                
                                            }      
                                            
                                        }

                                }


$sheet->setCellValue('C4', 'Fecha de Consulta:');
$sheet->setCellValue('D4', $string);
$spreadsheet->getActiveSheet()->mergeCells('D4:E4');
$spreadsheet->getActiveSheet()->getStyle('C4')->applyFromArray(array('font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')),'alignment' => array('horizontal'=> Alignment::HORIZONTAL_RIGHT, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
$spreadsheet->getActiveSheet()->getStyle('D4:E4')->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'DCDCDC'],), 'borders' => array('bottom' => ['borderStyle' => Border::BORDER_THIN],), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

//estableceer el estilo de la cabecera de la tabla
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, 'A8:ZZ8');

$row = 8;
$i = 0;
$sheet->setCellValue(getExcelCol($i).$row, 'Marcas');
$sheet->setCellValue(getExcelCol($i).$row, 'Data Entry');

 $modelos = new Kpi_Marcas_dos();
 $contadorCabecera=2;

$datos = $modelos->consultaSQL("SELECT DataEntry_Vendedores.CodVend, Valor, Clase FROM DataEntry_Vendedores INNER JOIN SAVEND ON SAVEND.CodVend=DataEntry_Vendedores.CodVend   WHERE valor>0  ORDER BY DataEntry_Vendedores.CodVend desc");
$DataEntryPorcentual=$ValorVende=$CodVend='';
    foreach ($datos as $heard) {
                                    
        $CodVend=($heard["CodVend"]);
        $ValorVende=number_format($heard["Valor"],0);
        $sheet->setCellValue(getExcelCol($i).$row,  $CodVend.' - '.$ValorVende.'%');
        $sheet->setCellValue(getExcelCol($i).$row, 'ALCANZADO');
        $sheet->setCellValue(getExcelCol($i).$row, '%');
        $sheet->setCellValue(getExcelCol($i).$row, 'CLIENTES ACTIVADOS');
        $contadorCabecera= $contadorCabecera+4; 
    } 


    $diasAc= date('d');
     $anno= date('Y');
     $mesAc = date('m');
                                $diai='01';
                                $diaf='30';

                                if($mes=='01'){

                                            $diaf='31';

                                                        }else{

                                                                if($mes=='02'){

                                                                    $diaf='28';
                                                                    
                                                                }else{

                                                                    if($mes=='03'){

                                                                        $diaf='31';
                                                                        
                                                                    }else{

                                                                        if($mes=='05'){
                                                                            $diaf='31';
                                                                        
                                                                        }else{

                                                                            if($mes=='07'){
                                                                                $diaf='31';
                                                                            
                                                                            }else{

                                                                                if($mes=='08'){
                                                                                    $diaf='31';
                                                                                
                                                                                }else{

                                                                                    if($mes=='10'){
                                                                                        $diaf='31';
                                                                                    
                                                                                    }else{

                                                                                        if($mes=='12'){
                                                                                            $diaf='31';
                                                                                        
                                                                                        }else{
                                                                                            
                                                                                        }
                                                                                        
                                                                                    }
                                                                                    
                                                                                }
                                                                                    
                                                                            }
                                                                                                    
                                                                        }
                                                                        
                                                                    }      
                                                                    
                                                                }

                                                        }

                                                        $fechai = $anno . '-' . $mes . '-' . $diai;
                                                        if($mes == $mesAc){
                                                            $fechaf = $anno . '-' . $mes . '-' . $diasAc;   
                                                        }else{
                                                           $fechaf = $anno . '-' . $mes . '-' . $diaf;
                                                        }

                                $datos = $modelos->consultaSQL("SELECT count(CodMarca) contador , CodMarca as marca , Valor , CodInst FROM DataEntry_Marcas inner join SAPROD on SAPROD.Marca = DataEntry_Marcas.CodMarca WHERE valor>0  GROUP BY CodInst, CodMarca,Valor");
                                $contadorMarcas =0;
                                 $marca_array = array();
                                 $Valormarca_array = array();

                                    $CodInst_array = array();
                                    $CalculosAux_array = array();

                            
                                foreach ($datos as $h) {

                                    $contadorMarcas+=1;//$h["contador"];
                                    array_push( $marca_array,$h["marca"]);
                                    array_push( $Valormarca_array,$h["Valor"]);
                                    array_push($CodInst_array, $h["CodInst"]);
                                    
                                }


                            $columnas = $contadorCabecera;
                            $filas = $contadorMarcas; // OK?
                            $row = 9;

                                $Calculos_array = array();
                                $repetidor = 0;


                                    for ($k = 0; $k < $columnas; ++$k)
                                    {
    
                                        $Calculos_array[0]="TOTAL GENERAL";
                                        $Calculos_array[$k]=0;

                                        $CalculosAux_array[0] = "TOTAL";
                                        $CalculosAux_array[$k] = 0;
                                    }


                            for ($x = 0; $x < $filas; ++$x)
                            {

                                $i = 0;

                                $Data_array = array();
                                $DataNumerico_array = array();

                                    $CodMarca=$marca_array[$x];
                                    array_push( $Data_array,$CodMarca);

                                    $Valor=$Valormarca_array[$x];

                                    if($Valor>=1000){
                                        array_push( $Data_array,($Valor));
                                    }else{
                                        array_push( $Data_array,number_format($Valor,2));
                                     }

                                    array_push( $DataNumerico_array,$Valor);

                                $datos = $modelos->consultaSQL("SELECT DataEntry_Vendedores.CodVend, Valor, Clase FROM DataEntry_Vendedores INNER JOIN SAVEND ON SAVEND.CodVend=DataEntry_Vendedores.CodVend   WHERE valor>0  ORDER BY DataEntry_Vendedores.CodVend desc");
                                $Vendedores_array = array();

                                foreach ($datos as $g) {
                                    $DataEntryPorcentual=0;

                                    $CodVend = $g["CodVend"];
                                     $ValorVende = $g["Valor"];

                                    $DataEntryPorcentual= ($Valor*$ValorVende)/100;

                                    if($DataEntryPorcentual>=1000){
                                        array_push( $Data_array,($DataEntryPorcentual));
                                    }else{
                                        array_push( $Data_array,number_format($DataEntryPorcentual,2));
                                     }

                                    array_push( $DataNumerico_array,$DataEntryPorcentual);

                                    //$datosAlcanzados= $modelos->consultaSQL("SELECT TipoFac, CodItem, Cantidad, TotalItem, Tasai, CodVend from SAITEMFAC inner join SAPROD on SAPROD.CodProd = SAITEMFAC.CodItem where SAPROD.Marca LIKE '$CodMarca' and CodVend='$CodVend' and  FechaE between '$fechai' and '$fechaf' and TipoFac in ('A','B','C','D')");
                                       

                                $datosAlcanzadosFact = $modelos->consultaSQL("SELECT
                                SAITEMFAC.TipoFac AS TipoFac,
                                SAITEMFAC.CodItem,
                                SAITEMFAC.Cantidad,
                                SAITEMFAC.TotalItem as TotalItem,
                                SAITEMFAC.Descto as descuento,
                                (SELECT codvend FROM savend WHERE savend.codvend = SAITEMFAC.codvend) AS codvend,
                                SAITEMFAC.Tasai
                                --(SELECT tasa FROM SAFACT WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS Tasai
                                 FROM SAITEMFAC INNER JOIN saprod ON SAITEMFAC.coditem = saprod.codprod
                                 INNER JOIN SAFACT ON SAITEMFAC.numerod = SAFACT.numerod AND SAITEMFAC.tipofac = SAFACT.tipofac WHERE
                                 DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMFAC.FechaE)) between '$fechai' and '$fechaf'  AND saprod.marca LIKE '$CodMarca' AND  SAFACT.codvend LIKE '$CodVend' AND (SAITEMFAC.tipofac = 'A' OR SAITEMFAC.Tipofac = 'B')");

                                
                                $alcanzadoFact = $alcanzadoNe =$alcanzado = 0;
                                foreach ($datosAlcanzadosFact as $row3) {

                                    if ($row3['TipoFac'] == 'B' or $row3['TipoFac'] == 'D') {
                                        $multiplicador = -1;
                                    } else {
                                        $multiplicador = 1;
                                    }

                                    $alcanzadoFact += ((($row3['TotalItem']* $multiplicador) ) / $row3['Tasai']) ;

                                }


                                $datosAlcanzadosNe = $modelos->consultaSQL("SELECT
                                saitemnota.tipofac AS TipoFac,
                                SAITEMNOTA.CodItem,
                                SAITEMNOTA.Cantidad,
                                (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.total ELSE SAITEMNOTA.total / 1.16 END) AS TotalItem,
                               (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.descuento ELSE SAITEMNOTA.descuento / 1.16 END) AS descuento,
                                (SELECT codvend FROM savend WHERE savend.codvend = SAITEMNOTA.codvend) AS CodVend
                                FROM SAITEMNOTA INNER JOIN saprod ON SAITEMNOTA.coditem = saprod.codprod
                                INNER JOIN sanota ON saitemnota.numerod = sanota.numerod AND saitemnota.tipofac = sanota.tipofac WHERE
                                DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) between '$fechai' and '$fechaf' AND saprod.marca LIKE '$CodMarca' and sanota.codvend LIKE '$CodVend'  AND (SAITEMNOTA.tipofac = 'C' OR SAITEMNOTA.Tipofac = 'D') AND  
                               SANOTA.numerof =(SELECT numerof FROM sanota WHERE sanota.numerod = SAITEMNOTA.numerod AND sanota.tipofac = SAITEMNOTA.tipofac AND sanota.numerof = 0) ");

          
                                foreach ($datosAlcanzadosNe as $row3) {

                                    if ($row3['TipoFac'] == 'B' or $row3['TipoFac'] == 'D') {
                                        $multiplicador = -1;
                                    } else {
                                        $multiplicador = 1;
                                    }

                                    $alcanzadoNe += ((($row3['TotalItem']* $multiplicador) )) ;

                                }

                                $alcanzado=$alcanzadoFact+$alcanzadoNe;


                                        if($alcanzado>=1000){
                                                array_push( $Data_array,($alcanzado));
                                        }else{
                                                array_push( $Data_array,number_format($alcanzado,2));
                                        }


                                     array_push( $DataNumerico_array,$alcanzado);

                                     $alcanzadoPorcentual = number_format(($alcanzado/$DataEntryPorcentual)*100,1);

                                      if($alcanzadoPorcentual>=0 and $alcanzadoPorcentual<=50){

                                                    $validador="bg-danger color-palette";

                                              }else{

                                                    if($alcanzadoPorcentual>=51 and $alcanzadoPorcentual<=80){

                                                         $validador="bg-warning color-palette";

                                                    }else{
                                                       
                                                        if($alcanzadoPorcentual>=81 and $alcanzadoPorcentual<=100){
                                                            $validador="bg-success color-palette";
                                                        }
                                                    }

                                              }

                                              $validadorValor=/*'<div class='.$validador.' ><span> '.*/$alcanzadoPorcentual/*.' </span></div>'*/;

                                              if($validadorValor>=1000){
                                                array_push( $Data_array,($validadorValor));
                                              }else{
                                                  array_push( $Data_array,number_format($validadorValor,2));
                                               }


                                              array_push( $DataNumerico_array,(($alcanzado/$DataEntryPorcentual)*100));

                                        $datosActivaciones= $modelos->consultaSQL("SELECT COUNT(saclie.CodClie) contador
                                                                                    FROM saclie inner join SAFACT on SAFACT.CodClie = SACLIE.CodClie inner join SAITEMFAC on 
                                                                                    SAITEMFAC.NumeroD = SAFACT.NumeroD inner join SAPROD on SAPROD.CodProd = SAITEMFAC.CodItem 
                                                                                    WHERE (saclie.fechauv between '$fechai' and '$fechaf') AND saclie.activo > 0 and SAITEMFAC.CodVend='$CodVend' and Marca LIKE '$CodMarca' ");
                                            $activaciones=0;
                                            foreach ($datosActivaciones as $row4){
                                               $activaciones=$row4['contador'];
                                            
                                            }
                                            

                                            array_push( $Data_array,$activaciones);

                                            array_push( $DataNumerico_array,$activaciones);

                                     }


                                        /*CAMBIO 5*/
                                        $masCuatro = 2;
                                        $masCinco = 4;
                                        if ($CodInst_array[$x] != 809 and $repetidor == 0) {
                                            for ($y = 0; $y < $columnas; ++$y) {
                                                // ESCRIBE LA FILA DE PEPSICO
                                                /*
                                                $ValorNumerico = 0;

                                                if ($y == 0) {
                                                    $sheet->setCellValue(getExcelCol($i) . $row, "TOTAL PEPSICO");
                                                    $spreadsheet->getActiveSheet()->getStyle($row)->applyFromArray(array('font' => array('bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_JUSTIFY, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
                                                } else {

                                                        if ($Calculos_array[$y] >= 1000) {
                                                            $ValorNumerico = ($Calculos_array[$y]);
                                                        } else {
                                                            $ValorNumerico = number_format($Calculos_array[$y], 2);
                                                        }



                                                        if ($y == $masCinco) {
                                                            if ($Calculos_array[$y - 2] <= 0) {
                                                                $ValorNumerico = number_format(0, 2);
                                                            } else {
                                                                $calculo = ($Calculos_array[$y - 1] / $Calculos_array[$y - 2]) * 100;
                                                                $ValorNumerico = number_format($calculo, 2);

                                                                if ( $ValorNumerico <= 50) {

                                                                    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true) . $row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'], ), 'font' => array('name' => 'Arial', 'bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], ), ));

                                                                } else {

                                                                    if ($ValorNumerico >= 51 and $ValorNumerico <= 80) {

                                                                        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true) . $row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ffc107'], ), 'font' => array('name' => 'Arial', 'bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], ), ));

                                                                    } else {

                                                                        if ($ValorNumerico >= 81) {
                                                                            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true) . $row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '8028a745'], ), 'font' => array('name' => 'Arial', 'bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], ), ));
                                                                        }
                                                                    }

                                                                }

                                                            }
                                                            $masCinco = $masCinco + 4;
                                                        }

                                                        $sheet->setCellValue(getExcelCol($i) . $row, $ValorNumerico);


                                                }*/

                                                $repetidor += 1;
                                            }
                                                $i=0;
                                                $row++;
                                        }
                                        /* FIN CAMBIO 5*/

                                    
                                   
                                    $masCinco=3;
                                    for ($y = 0; $y < $columnas; ++$y)
                                    {
  
                                        // rutinas....
                                        $sheet->setCellValue(getExcelCol($i).$row, $Data_array[$y]);
                                        if($y==$masCinco){ 
                                            $ValorNumerico=$DataNumerico_array[$y];
                                            if( $ValorNumerico<=50){
                                                $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                                            }else{
                                                  if($ValorNumerico>=51 and $ValorNumerico<=80){
                                                          $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ffc107'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                                                  }else{
                                                        if($ValorNumerico>=81 ){
                                                               $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '8028a745'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                                                         }
                                                  }

                                            }
                                            $masCinco=$masCinco+4;
                                        }
                                        

                                         if($y > 0){
                                            $Calculos_array[$y] += $DataNumerico_array[$y-1];
                                                if ($CodInst_array[$x] != 809) {
                                                    $CalculosAux_array[$y] += $DataNumerico_array[$y - 1];
                                                }
                                         }
                                       
                                        
                                    }

                                    $row++;
                            }


/*TOTAL*/
$i = 0;
$sheet = $spreadsheet->getActiveSheet();
$masCinco = 4;
for ($y = 0; $y < $columnas; ++$y) {
    if ($y == 0) {
        $sheet->setCellValue(getExcelCol($i) . $row, $CalculosAux_array[$y]);
    } else {

        if ($CalculosAux_array[$y] >= 1000) {
            $ValorNumerico = ($CalculosAux_array[$y]);
        } else {
            $ValorNumerico = number_format($CalculosAux_array[$y], 2);
        }



        if ($y == $masCinco) {
            if ($CalculosAux_array[$y - 2] <= 0) {
                $ValorNumerico = number_format(0, 2);
            } else {
                $calculo = ($CalculosAux_array[$y - 1] / $CalculosAux_array[$y - 2]) * 100;
                $ValorNumerico = number_format($calculo, 2);

                if ( $ValorNumerico <= 50) {

                    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true) . $row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'], ), 'font' => array('name' => 'Arial', 'bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], ), ));

                } else {

                    if ($ValorNumerico >= 51 and $ValorNumerico <= 80) {

                        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true) . $row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ffc107'], ), 'font' => array('name' => 'Arial', 'bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], ), ));

                    } else {

                        if ($ValorNumerico >= 81) {
                            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true) . $row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '8028a745'], ), 'font' => array('name' => 'Arial', 'bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], ), ));
                        }
                    }

                }

            }
            $masCinco = $masCinco + 4;
        }

        $sheet->setCellValue(getExcelCol($i) . $row, $ValorNumerico);
    }
    $spreadsheet->getActiveSheet()->getStyle($row)->applyFromArray(array('font' => array('bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_JUSTIFY, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
}


$row++;

 /*TOTAL GENERAL*/
$i = 0;
$sheet = $spreadsheet->getActiveSheet();
 $masCinco=4;
 for ($y = 0; $y < $columnas; ++$y)
 {
    if($y==0){
        $sheet->setCellValue(getExcelCol($i).$row, $Calculos_array[$y]);
    }else{

         if($Calculos_array[$y]>=1000){
           $ValorNumerico = ($Calculos_array[$y]);
         }else{
             $ValorNumerico = number_format($Calculos_array[$y],2);
          }

       

          if($y==$masCinco){
                 if($Calculos_array[$y-2]<=0){
                            $ValorNumerico = number_format(0,2);
                 }else{
                            $calculo=($Calculos_array[$y-1]/$Calculos_array[$y-2])*100;
                            $ValorNumerico = number_format($calculo,2);

                             if( $ValorNumerico<=50){

                                      $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));

                             }else{

                                       if($ValorNumerico>=51 and $ValorNumerico<=80){

                                                    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ffc107'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));

                                        }else{
                                                       
                                                if($ValorNumerico>=81 ){
                                                           $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '8028a745'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                                              }
                                        }

                            }

                }
              $masCinco=$masCinco+4;
          }

        $sheet->setCellValue(getExcelCol($i).$row, $ValorNumerico);
    }
    $spreadsheet->getActiveSheet()->getStyle($row)->applyFromArray(array('font' => array('bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_JUSTIFY, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
 }


 $row+=4;

$variableLinea = 'A'.$row.':ZZ'.$row;
$spreadsheet->getActiveSheet()->duplicateStyle($style_title, $variableLinea);
$i = 0;
$contadorCabecera=2;

$sheet->setCellValue(getExcelCol($i).$row, 'Marcas');
$sheet->setCellValue(getExcelCol($i).$row, 'Data Entry');
$datos = $modelos->consultaSQL("SELECT distinct Clase FROM DataEntry_Vendedores INNER JOIN SAVEND ON SAVEND.CodVend=DataEntry_Vendedores.CodVend   WHERE valor>0  ORDER BY Clase desc");
$DataEntryPorcentual=$ValorVende=$CodVend='';
    foreach ($datos as $heard) {

         $Clase=($heard["Clase"]);
        $sheet->setCellValue(getExcelCol($i).$row,  $Clase );
        $sheet->setCellValue(getExcelCol($i).$row, 'ALCANZADO');
        $sheet->setCellValue(getExcelCol($i).$row, '%');
        $sheet->setCellValue(getExcelCol($i).$row, 'CLIENTES ACTIVADOS');
        $contadorCabecera= $contadorCabecera+4; 
    } 

      $anno= date('Y');
                                $diasAc= date('d');
                                $mesAc = date('m');
                                $diai='01';
                                $diaf='30';

                                if($mes=='01'){

                                            $diaf='31';

                                                        }else{

                                                                if($mes=='02'){

                                                                    $diaf='28';
                                                                    
                                                                }else{

                                                                    if($mes=='03'){

                                                                        $diaf='31';
                                                                        
                                                                    }else{

                                                                        if($mes=='05'){
                                                                            $diaf='31';
                                                                        
                                                                        }else{

                                                                            if($mes=='07'){
                                                                                $diaf='31';
                                                                            
                                                                            }else{

                                                                                if($mes=='08'){
                                                                                    $diaf='31';
                                                                                
                                                                                }else{

                                                                                    if($mes=='10'){
                                                                                        $diaf='31';
                                                                                    
                                                                                    }else{

                                                                                        if($mes=='12'){
                                                                                            $diaf='31';
                                                                                        
                                                                                        }else{
                                                                                            
                                                                                        }
                                                                                        
                                                                                    }
                                                                                    
                                                                                }
                                                                                    
                                                                            }
                                                                                                    
                                                                        }
                                                                        
                                                                    }      
                                                                    
                                                                }

                                                        }

                                                        $fechai = $anno . '-' . $mes . '-' . $diai;
                                                        if($mes == $mesAc){
                                                            $fechaf = $anno . '-' . $mes . '-' . $diasAc;   
                                                        }else{
                                                           $fechaf = $anno . '-' . $mes . '-' . $diaf;
                                                        }

                                $datos = $modelos->consultaSQL("SELECT count(CodMarca) contador , CodMarca as marca , Valor , CodInst FROM DataEntry_Marcas inner join SAPROD on SAPROD.Marca = DataEntry_Marcas.CodMarca WHERE valor>0  GROUP BY CodInst, CodMarca,Valor");
                                $contadorMarcas =0;
                                 $marca_array = array();
                                 $Valormarca_array = array();

                                 $CodInst_array = array();
                                 $CalculosAux_array = array();


                            
                                foreach ($datos as $hh) {

                                    $contadorMarcas+=1;//$hh["contador"];
                                    array_push( $marca_array,$hh["marca"]);
                                    array_push( $Valormarca_array,$hh["Valor"]);
                                    array_push($CodInst_array, $hh["CodInst"]);
                                    
                                }


                            $columnas = $contadorCabecera;
                            $filas = $contadorMarcas; // OK?
                            $acumuladorActivados=$acumuladorPorcentual=$acumuladorAlcandado=$acumuladorVendedor=0;

                            
                                $validadorValorMayor= $validadorValorOT=$validadorValorDTA=$validadorValorDTS ='';
                           $validadorValorMayornumerio= $validadorValorOTnumerio=$validadorValorDTAnumerio=$validadorValorDTSnumerio =0;
                            $validadorValorDTS ='';
                            $row++;

                            $Calculos_array = array();

                            $repetidor = 0;


                                    for ($k = 0; $k < $columnas; ++$k)
                                    {
    
                                        $Calculos_array[0]="TOTAL GENERAL";
                                        $Calculos_array[$k]=0;

                                        $CalculosAux_array[0] = "TOTAL";
                                        $CalculosAux_array[$k] = 0;
                                    }


                            for ($x = 0; $x < $filas; ++$x)
                            {
                                 $i = 0;

                                $acumuladorActivadosDTS=$acumuladorPorcentualDTS=$acumuladorAlcandadoDTS=$acumuladorVendedorDTS=0;
                            $acumuladorActivadosMayor=$acumuladorPorcentualMayor=$acumuladorAlcandadoMayor=$acumuladorVendedorMayor=0;
                            $acumuladorActivadosOT=$acumuladorPorcentualOT=$acumuladorAlcandadoOT=$acumuladorVendedorOT=0;
                            $acumuladorActivadosDTA=$acumuladorPorcentualDTA=$acumuladorAlcandadoDTA=$acumuladorVendedorDTA=0;

                                $Data_array = array();
                                $DataNumerico_array = array();

                                    $CodMarca=$marca_array[$x];
                                    array_push( $Data_array,$CodMarca);

                                    $Valor=$Valormarca_array[$x];

                                     if($Valor>=1000){
                                        array_push( $Data_array,($Valor));
                                    }else{
                                        array_push( $Data_array,number_format($Valor,2));
                                     }

                                    array_push( $DataNumerico_array,$Valor);

                                $datos = $modelos->consultaSQL("SELECT DataEntry_Vendedores.CodVend, Valor, Clase FROM DataEntry_Vendedores INNER JOIN SAVEND ON SAVEND.CodVend=DataEntry_Vendedores.CodVend   WHERE valor>0  ORDER BY Clase desc");
                                $Vendedores_array = array();

                                foreach ($datos as $df) {
                                    $DataEntryPorcentual=0;
                                    $alcanzadoPorcentual =0;

                                    $CodVend = $df["CodVend"];
                                     $ValorVende = $df["Valor"];
                                     $Clase = $df["Clase"];

                                    $DataEntryPorcentual= ($Valor*$ValorVende)/100;

                                    if($Clase =='DTS'){

                                        $acumuladorVendedorDTS +=$DataEntryPorcentual;

                                    }else{
                                        if($Clase =='OT'){
                                            $acumuladorVendedorOT +=$DataEntryPorcentual;

                                        }else{

                                            if($Clase =='MAYOR'){
                                                $acumuladorVendedorMayor +=$DataEntryPorcentual;

                                            }else{

                                                if($Clase =='DISTRIBUID'){
                                                    $acumuladorVendedorDTA +=$DataEntryPorcentual;

                                                }

                                            }

                                        }
                                    }

                                        //$datosAlcanzados= $modelos->consultaSQL("SELECT TipoFac, CodItem, Cantidad, TotalItem, Tasai, CodVend from SAITEMFAC inner join SAPROD on SAPROD.CodProd = SAITEMFAC.CodItem where SAPROD.Marca LIKE '$CodMarca' and CodVend='$CodVend' and  FechaE between '$fechai' and '$fechaf' and TipoFac in ('A','B','C','D')");
                        

                                $datosAlcanzadosFact = $modelos->consultaSQL("SELECT
                                SAITEMFAC.TipoFac AS TipoFac,
                                SAITEMFAC.CodItem,
                                SAITEMFAC.Cantidad,
                                SAITEMFAC.TotalItem as TotalItem,
                                SAITEMFAC.Descto as descuento,
                                (SELECT codvend FROM savend WHERE savend.codvend = SAITEMFAC.codvend) AS codvend,
                                SAITEMFAC.Tasai
                                --(SELECT tasa FROM SAFACT WHERE SAFACT.numerod = SAITEMFAC.numerod AND SAFACT.tipofac = SAITEMFAC.tipofac) AS Tasai
                                 FROM SAITEMFAC INNER JOIN saprod ON SAITEMFAC.coditem = saprod.codprod
                                 INNER JOIN SAFACT ON SAITEMFAC.numerod = SAFACT.numerod AND SAITEMFAC.tipofac = SAFACT.tipofac WHERE
                                 DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMFAC.FechaE)) between '$fechai' and '$fechaf'  AND saprod.marca LIKE '$CodMarca' AND  SAFACT.codvend LIKE '$CodVend' AND (SAITEMFAC.tipofac = 'A' OR SAITEMFAC.Tipofac = 'B')");

                                
                                $alcanzadoFact = $alcanzadoNe =$alcanzado = 0;
                                foreach ($datosAlcanzadosFact as $row3) {

                                    if ($row3['TipoFac'] == 'B' or $row3['TipoFac'] == 'D') {
                                        $multiplicador = -1;
                                    } else {
                                        $multiplicador = 1;
                                    }

                                    $alcanzadoFact += ((($row3['TotalItem']* $multiplicador) ) / $row3['Tasai']) ;

                                }


                                $datosAlcanzadosNe = $modelos->consultaSQL("SELECT
                                saitemnota.tipofac AS TipoFac,
                                SAITEMNOTA.CodItem,
                                SAITEMNOTA.Cantidad,
                                (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.total ELSE SAITEMNOTA.total / 1.16 END) AS TotalItem,
                               (CASE SAITEMNOTA.esexento WHEN 1  then SAITEMNOTA.descuento ELSE SAITEMNOTA.descuento / 1.16 END) AS descuento,
                                (SELECT codvend FROM savend WHERE savend.codvend = SAITEMNOTA.codvend) AS CodVend
                                FROM SAITEMNOTA INNER JOIN saprod ON SAITEMNOTA.coditem = saprod.codprod
                                INNER JOIN sanota ON saitemnota.numerod = sanota.numerod AND saitemnota.tipofac = sanota.tipofac WHERE
                                DATEADD(dd, 0, DATEDIFF(dd, 0, SAITEMNOTA.FechaE)) between '$fechai' and '$fechaf' AND saprod.marca LIKE '$CodMarca' and sanota.codvend LIKE '$CodVend'  AND (SAITEMNOTA.tipofac = 'C' OR SAITEMNOTA.Tipofac = 'D') AND  
                               SANOTA.numerof =(SELECT numerof FROM sanota WHERE sanota.numerod = SAITEMNOTA.numerod AND sanota.tipofac = SAITEMNOTA.tipofac AND sanota.numerof = 0) ");

          
                                foreach ($datosAlcanzadosNe as $row3) {

                                    if ($row3['TipoFac'] == 'B' or $row3['TipoFac'] == 'D') {
                                        $multiplicador = -1;
                                    } else {
                                        $multiplicador = 1;
                                    }

                                    $alcanzadoNe += ((($row3['TotalItem']* $multiplicador) )) ;

                                }

                                $alcanzado=$alcanzadoFact+$alcanzadoNe;



                                        if($Clase =='DTS'){

                                            $acumuladorAlcandadoDTS +=$alcanzado;
    
                                        }else{
                                            if($Clase =='OT'){
                                                $acumuladorAlcandadoOT +=$alcanzado;
    
                                            }else{
    
                                                if($Clase =='MAYOR'){
                                                    $acumuladorAlcandadoMayor +=$alcanzado;
    
                                                }else{
    
                                                    if($Clase =='DISTRIBUID'){
                                                        $acumuladorAlcandadoDTA +=$alcanzado;
    
                                                    }
    
                                                }
    
                                            }
                                        }


                                     if($Clase =='DTS'){

                                        $PorcentualDTS =number_format((($acumuladorAlcandadoDTS/$acumuladorVendedorDTS)*100),1);

                                        if($PorcentualDTS>=0 and $PorcentualDTS<=50){
                                            $validador="bg-danger color-palette";
                                        }else{
                                                if($PorcentualDTS>=51 and $PorcentualDTS<=80){
                                                    $validador="bg-warning color-palette";
                                                }else{
                                                    if($PorcentualDTS>=81 ){
                                                        $validador="bg-success color-palette";
                                                    }
                                                }
                                        }

                                        $validadorValorDTS=/*'<div class='.$validador.' ><span> '.*/$PorcentualDTS/*.' </span></div>'*/;

                                        $validadorValorDTSnumerio=(($acumuladorAlcandadoDTS/$acumuladorVendedorDTS)*100);

                                    }else{
                                        if($Clase =='OT'){
                                            $PorcentualOT =number_format((($acumuladorAlcandadoOT/$acumuladorVendedorOT)*100),1);

                                            if($PorcentualOT>=0 and $PorcentualOT<=50){
                                                $validador="bg-danger color-palette";
                                            }else{
                                                    if($PorcentualOT>=51 and $PorcentualOT<=80){
                                                        $validador="bg-warning color-palette";
                                                    }else{
                                                        if($PorcentualOT>=81 ){
                                                            $validador="bg-success color-palette";
                                                        }
                                                    }
                                            }
    
                                            $validadorValorOT=/*'<div class='.$validador.' ><span> '.*/$PorcentualOT/*.' </span></div>'*/;

                                             $validadorValorOTnumerio=((($acumuladorAlcandadoOT/$acumuladorVendedorOT)*100));

                                        }else{

                                            if($Clase =='MAYOR'){
                                                $PorcentualMayor =number_format((($acumuladorAlcandadoMayor/$acumuladorVendedorMayor)*100),1);

                                                if($PorcentualMayor>=0 and $PorcentualMayor<=50){
                                                    $validador="bg-danger color-palette";
                                                }else{
                                                        if($PorcentualMayor>=51 and $PorcentualMayor<=80){
                                                            $validador="bg-warning color-palette";
                                                        }else{
                                                            if($PorcentualMayor>=81 ){
                                                                $validador="bg-success color-palette";
                                                            }
                                                        }
                                                }
        
                                                $validadorValorMayor=/*'<div class='.$validador.' ><span> '.*/$PorcentualMayor/*.' </span></div>'*/;

                                                $validadorValorMayornumerio=((($acumuladorAlcandadoMayor/$acumuladorVendedorMayor)*100));

                                            }else{

                                                if($Clase =='DISTRIBUID'){
                                                    $PorcentualDTA =number_format((($acumuladorAlcandadoDTA/$acumuladorVendedorDTA)*100),1);

                                                    if($PorcentualDTA>=0 and $PorcentualDTA<=50){
                                                        $validador="bg-danger color-palette";
                                                    }else{
                                                            if($PorcentualDTA>=51 and $PorcentualDTA<=80){
                                                                $validador="bg-warning color-palette";
                                                            }else{
                                                                if($PorcentualDTA>=81 ){
                                                                    $validador="bg-success color-palette";
                                                                }
                                                            }
                                                    }

                                                    $validadorValorDTA=/*'<div class='.$validador.' ><span> '.*/$PorcentualDTA/*.' </span></div>'*/;

                                                    $validadorValorDTAnumerio=((($acumuladorAlcandadoDTA/$acumuladorVendedorDTA)*100));

                                                }

                                            }

                                        }
                                    }

                                    
                                      if($alcanzadoPorcentual>=0 and $alcanzadoPorcentual<=50){

                                                    $validador="bg-danger color-palette";

                                              }else{

                                                    if($alcanzadoPorcentual>=51 and $alcanzadoPorcentual<=80){

                                                         $validador="bg-warning color-palette";

                                                    }else{
                                                       
                                                        if($alcanzadoPorcentual>=81 ){
                                                            $validador="bg-success color-palette";
                                                        }
                                                    }

                                              }

                                              $validadorValor=/*'<div class='.$validador.' ><span> '.*/$alcanzadoPorcentual/*.' </span></div>'*/;


                                        $datosActivaciones= $modelos->consultaSQL("SELECT COUNT(saclie.CodClie) contador
                                                                                    FROM saclie inner join SAFACT on SAFACT.CodClie = SACLIE.CodClie inner join SAITEMFAC on 
                                                                                    SAITEMFAC.NumeroD = SAFACT.NumeroD inner join SAPROD on SAPROD.CodProd = SAITEMFAC.CodItem 
                                                                                    WHERE (saclie.fechauv between '$fechai' and '$fechaf') AND saclie.activo > 0 and SAITEMFAC.CodVend='$CodVend' and Marca LIKE '$CodMarca' ");
                                            $activaciones=0;
                                            foreach ($datosActivaciones as $row4){
                                               $activaciones=$row4['contador'];
                                            
                                            }

                                            if($Clase =='DTS'){

                                                $acumuladorActivadosDTS +=$activaciones;
        
                                            }else{
                                                if($Clase =='OT'){
                                                    $acumuladorActivadosOT +=$activaciones;
        
                                                }else{
        
                                                    if($Clase =='MAYOR'){
                                                        $acumuladorActivadosMayor +=$activaciones;
        
                                                    }else{
        
                                                        if($Clase =='DISTRIBUID'){
                                                            $acumuladorActivadosDTA +=$activaciones;
        
                                                        }
        
                                                    }
        
                                                }
                                            }


                                     }

                                     /*ARRAY OT*/
                                      if($acumuladorVendedorOT>=1000){
                                           array_push( $Data_array,($acumuladorVendedorOT));
                                        }else{
                                           array_push( $Data_array,number_format($acumuladorVendedorOT,2));
                                        }

                                        if($acumuladorAlcandadoOT>=1000){
                                            array_push( $Data_array,($acumuladorAlcandadoOT));
                                        }else{
                                           array_push( $Data_array,number_format($acumuladorAlcandadoOT,2));
                                        }

                                        array_push( $Data_array,($validadorValorOT));

                                        if($acumuladorActivadosOT>=1000){
                                           array_push( $Data_array,($acumuladorActivadosOT));
                                        }else{
                                           array_push( $Data_array,number_format($acumuladorActivadosOT,2));
                                        }

                                     array_push( $DataNumerico_array,$acumuladorVendedorOT);
                                     array_push( $DataNumerico_array,$acumuladorAlcandadoOT);
                                     array_push( $DataNumerico_array,$validadorValorOTnumerio);
                                     array_push( $DataNumerico_array,$acumuladorActivadosOT);



                                            /*ARRAY DTS*/
                                            if ($acumuladorVendedorDTS >= 1000) {
                                                array_push($Data_array, ($acumuladorVendedorDTS));
                                            } else {
                                                array_push($Data_array, number_format($acumuladorVendedorDTS, 2));
                                            }

                                            if ($acumuladorAlcandadoDTS >= 1000) {
                                                array_push($Data_array, ($acumuladorAlcandadoDTS));
                                            } else {
                                                array_push($Data_array, number_format($acumuladorAlcandadoDTS, 2));
                                            }

                                            array_push($Data_array, ($validadorValorDTS));

                                            if ($acumuladorActivadosDTS >= 1000) {
                                                array_push($Data_array, ($acumuladorActivadosDTS));
                                            } else {
                                                array_push($Data_array, number_format($acumuladorActivadosDTS, 2));
                                            }

                                            array_push($DataNumerico_array, $acumuladorVendedorDTS);
                                            array_push($DataNumerico_array, $acumuladorAlcandadoDTS);
                                            array_push($DataNumerico_array, $validadorValorDTSnumerio);
                                            array_push($DataNumerico_array, $acumuladorActivadosDTS);



                                     /*ARRAY MAYOR*/
                                      if($acumuladorVendedorMayor>=1000){
                                           array_push( $Data_array,($acumuladorVendedorMayor));
                                        }else{
                                           array_push( $Data_array,number_format($acumuladorVendedorMayor,2));
                                        }

                                        if($acumuladorAlcandadoMayor>=1000){
                                            array_push( $Data_array,($acumuladorAlcandadoMayor));
                                        }else{
                                           array_push( $Data_array,number_format($acumuladorAlcandadoMayor,2));
                                        }

                                        array_push( $Data_array,($validadorValorMayor));

                                        if($acumuladorActivadosMayor>=1000){
                                           array_push( $Data_array,($acumuladorActivadosMayor));
                                        }else{
                                           array_push( $Data_array,number_format($acumuladorActivadosMayor,2));
                                        }

                                     array_push( $DataNumerico_array,$acumuladorVendedorMayor);
                                     array_push( $DataNumerico_array,$acumuladorAlcandadoMayor);
                                     array_push( $DataNumerico_array,$validadorValorMayornumerio);
                                     array_push( $DataNumerico_array,$acumuladorActivadosMayor);


                                      /*ARRAY DISTRIBUIDORA*/
                                        if($acumuladorVendedorDTA>=1000){
                                           array_push( $Data_array,($acumuladorVendedorDTA));
                                        }else{
                                           array_push( $Data_array,number_format($acumuladorVendedorDTA,2));
                                        }

                                        if($acumuladorAlcandadoDTA>=1000){
                                            array_push( $Data_array,($acumuladorAlcandadoDTA));
                                        }else{
                                           array_push( $Data_array,number_format($acumuladorAlcandadoDTA,2));
                                        }

                                        array_push( $Data_array,($validadorValorDTA));

                                        if($acumuladorActivadosDTA>=1000){
                                           array_push( $Data_array,($acumuladorActivadosDTA));
                                        }else{
                                           array_push( $Data_array,number_format($acumuladorActivadosDTA,2));
                                        }

                                      array_push( $DataNumerico_array,$acumuladorVendedorDTA);
                                      array_push( $DataNumerico_array,$acumuladorAlcandadoDTA);
                                      array_push( $DataNumerico_array,$validadorValorDTAnumerio);
                                      array_push( $DataNumerico_array,$acumuladorActivadosDTA);
                            
                                     $Calculos_array[0]='TOTAL GENERAL';
                                     $CalculosAux_array[0] = "TOTAL";



                                        /*CAMBIO 9*/
                                        $masCuatro = 2;
                                        $masCinco = 4;
                                        if ($CodInst_array[$x] != 809 and $repetidor == 0) {
                                            for ($y = 0; $y < $columnas; ++$y) {
                                                // ESCRIBE LA FILA DE PEPSICO
                                                /*
                                                $ValorNumerico = 0;

                                                if ($y == 0) {
                                                    $sheet->setCellValue(getExcelCol($i) . $row, "TOTAL PEPSICO");
                                                    $spreadsheet->getActiveSheet()->getStyle($row)->applyFromArray(array('font' => array('bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_JUSTIFY, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
                                                } else {

                                                    if ($Calculos_array[$y] >= 1000) {
                                                        $ValorNumerico = ($Calculos_array[$y]);
                                                    } else {
                                                        $ValorNumerico = number_format($Calculos_array[$y], 2);
                                                    }



                                                    if ($y == $masCinco) {
                                                        if ($Calculos_array[$y - 2] <= 0) {
                                                            $ValorNumerico = number_format(0, 2);
                                                        } else {
                                                            $calculo = ($Calculos_array[$y - 1] / $Calculos_array[$y - 2]) * 100;
                                                            $ValorNumerico = number_format($calculo, 2);

                                                            if ( $ValorNumerico <= 50) {

                                                                $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true) . $row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'], ), 'font' => array('name' => 'Arial', 'bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], ), ));

                                                            } else {

                                                                if ($ValorNumerico >= 51 and $ValorNumerico <= 80) {

                                                                    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true) . $row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ffc107'], ), 'font' => array('name' => 'Arial', 'bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], ), ));

                                                                } else {

                                                                    if ($ValorNumerico >= 81) {
                                                                        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true) . $row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '8028a745'], ), 'font' => array('name' => 'Arial', 'bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], ), ));
                                                                    }
                                                                }

                                                            }

                                                        }
                                                        $masCinco = $masCinco + 4;
                                                    }

                                                    $sheet->setCellValue(getExcelCol($i) . $row, $ValorNumerico);


                                                }*/

                                                $repetidor += 1;
                                            }
                                            $i = 0;
                                            $row++;
                                        }
                                        /* FIN CAMBIO 9*/





                                        $masCinco=3;
                                       for ($y = 0; $y < $columnas; ++$y)
                                        {
  
                                        // rutinas....
                                        $sheet->setCellValue(getExcelCol($i).$row, $Data_array[$y]);

                                        if($y==$masCinco){ 
                                            $ValorNumerico=$DataNumerico_array[$y];
                                            if( $ValorNumerico<=50){
                                                $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                                            }else{
                                                  if($ValorNumerico>=51 and $ValorNumerico<=80){
                                                          $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ffc107'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                                                  }else{
                                                        if($ValorNumerico>=81 ){
                                                               $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '8028a745'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                                                         }
                                                  }

                                            }
                                            $masCinco=$masCinco+4;
                                            }

                                            if($y > 0){
                                                $Calculos_array[$y] += $DataNumerico_array[$y-1];
                                                    if ($CodInst_array[$x] != 809) {
                                                        $CalculosAux_array[$y] += $DataNumerico_array[$y - 1];
                                                    }
                                            }
                                       
                                        
                                    }

                                     $row++;
                            }


/*TOTAL*/
$i = 0;
$sheet = $spreadsheet->getActiveSheet();
$masCinco = 4;
for ($y = 0; $y < $columnas; ++$y) {
    if ($y == 0) {
        $sheet->setCellValue(getExcelCol($i) . $row, $CalculosAux_array[$y]);
    } else {

        if ($CalculosAux_array[$y] >= 1000) {
            $ValorNumerico = ($CalculosAux_array[$y]);
        } else {
            $ValorNumerico = number_format($CalculosAux_array[$y], 2);
        }



        if ($y == $masCinco) {
            if ($CalculosAux_array[$y - 2] <= 0) {
                $ValorNumerico = number_format(0, 2);
            } else {
                $calculo = ($CalculosAux_array[$y - 1] / $CalculosAux_array[$y - 2]) * 100;
                $ValorNumerico = number_format($calculo, 2);

                if ( $ValorNumerico <= 50) {

                    $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true) . $row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'], ), 'font' => array('name' => 'Arial', 'bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], ), ));

                } else {

                    if ($ValorNumerico >= 51 and $ValorNumerico <= 80) {

                        $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true) . $row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ffc107'], ), 'font' => array('name' => 'Arial', 'bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], ), ));

                    } else {

                        if ($ValorNumerico >= 81) {
                            $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true) . $row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '8028a745'], ), 'font' => array('name' => 'Arial', 'bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM], ), ));
                        }
                    }

                }

            }
            $masCinco = $masCinco + 4;
        }

        $sheet->setCellValue(getExcelCol($i) . $row, $ValorNumerico);
    }
    $spreadsheet->getActiveSheet()->getStyle($row)->applyFromArray(array('font' => array('bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_JUSTIFY, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));
}


$row++;


/*TOTAL GENERAL*/

    $i = 0;
$sheet = $spreadsheet->getActiveSheet();
 $masCinco=4; 
 for ($y = 0; $y < $columnas; ++$y)
 {
    if($y==0){
        $sheet->setCellValue(getExcelCol($i).$row, $Calculos_array[$y]);
    }else{

        if($Calculos_array[$y]>=1000){
            $ValorNumerico = ($Calculos_array[$y]);
         }else{
             $ValorNumerico = number_format($Calculos_array[$y],2);
         }

       

          if($y==$masCinco){
                 if($Calculos_array[$y-2]<=0){
                            $ValorNumerico = number_format(0,2);
                 }else{
                            $calculo=($Calculos_array[$y-1]/$Calculos_array[$y-2])*100;
                            $ValorNumerico = number_format($calculo,2);

                             if( $ValorNumerico<=50){

                                      $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));

                             }else{

                                       if($ValorNumerico>=51 and $ValorNumerico<=80){

                                                     $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ffc107'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));

                                        }else{
                                                       
                                                if($ValorNumerico>=81 ){
                                                           $spreadsheet->getActiveSheet()->getStyle(getExcelCol($i, true).$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '8028a745'],), 'font' => array('name' => 'Arial', 'bold'  => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
                                              }
                                        }

                            }

                }
              $masCinco=$masCinco+4;

          }

        $sheet->setCellValue(getExcelCol($i).$row, $ValorNumerico);
    }

    $spreadsheet->getActiveSheet()->getStyle($row)->applyFromArray(array('font' => array('bold' => true, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal' => Alignment::HORIZONTAL_JUSTIFY, 'vertical' => Alignment::VERTICAL_CENTER, 'wrap' => TRUE)));

 }


//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;


$spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(25);
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);

$row+=6;
$sheet->setCellValue('B'.$row, 'ROJO: 0 - 50% ');
$sheet->setCellValue('E'.$row, 'AMARILLO: 51 - 80%');
$sheet->setCellValue('J'.$row,'VERDE: 81 - 100%');
$spreadsheet->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
$spreadsheet->getActiveSheet()->mergeCells('E'.$row.':H'.$row);
$spreadsheet->getActiveSheet()->mergeCells('J'.$row.':M'.$row);
//pinta la celda en verde
$spreadsheet->getActiveSheet()->getStyle('J'.$row.':M'.$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '8028a745'],), 'font' => array('name' => 'Arial', 'bold'  => false, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
//pinta la celda en amarillo
$spreadsheet->getActiveSheet()->getStyle('E'.$row.':H'.$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ffc107'],), 'font' => array('name' => 'Arial', 'bold'  => false, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
//pinta la celda en rojo
$spreadsheet->getActiveSheet()->getStyle('B'.$row.':C'.$row)->applyFromArray(array('fill' => array('fillType' => Fill::FILL_SOLID, 'color' => ['argb' => '80ff3939'],), 'font' => array('name' => 'Arial', 'bold'  => false, 'color' => array('rgb' => '000000')), 'alignment' => array('horizontal'=> Alignment::HORIZONTAL_CENTER, 'vertical'  => Alignment::VERTICAL_CENTER, 'wrap' => TRUE), 'borders' => array('top' => ['borderStyle' => Border::BORDER_THIN], 'bottom' => ['borderStyle' => Border::BORDER_THIN], 'left' => ['borderStyle' => Border::BORDER_MEDIUM], 'right' => ['borderStyle' => Border::BORDER_MEDIUM],),));
$spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight(28);



$spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(80);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="kpi_NEW_de_'.$string.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$callStartTime = microtime(true);
ob_end_clean();
ob_start();
$writer->save('php://output');