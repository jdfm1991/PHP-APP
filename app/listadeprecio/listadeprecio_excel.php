<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require('../vendor/autoload.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("listadeprecio_modelo.php");

//INSTANCIAMOS EL MODELO
$precios = new Listadeprecio();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$i = 0;
//funcion recursiva creada para reporte Excel que evalua los numeros > 0
// y asigna la letra desde la A....hasta la Z y AA, AB, AC.....AZ
function getExcelCol($num) {
    $numero = $num % 26;
    $letra = chr(65 + $numero);
    $num2 = intval($num / 26);
    $GLOBALS['i'] = $GLOBALS['i'] +1;
    if ($num2 > 0) {
        return getExcelCol($num2 - 1) . $letra;
    } else {
        return $letra;
    }
}

$depos = $_GET['depos'];
$marcas = $_GET['marcas'];
$orden = $_GET['orden'];
$exis = $_GET['exis'];
$iva = $_GET['iva'];
$cubi = $_GET['cubi'];
$p1 = str_replace("1","1",$_GET['p1']);
$p2 = str_replace("1","2",$_GET['p2']);
$p3 = str_replace("1","3",$_GET['p3']);
$sumap = $_GET['p1'] + $_GET['p2'] + $_GET['p3'];
$sumap2 = $p1 + $p2 + $p3;
$pAux = '';


//creamos la cabecera de la tabla
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
$sheet->setCellValue(getExcelCol($i).'1', 'Codigo');
$sheet->setCellValue(getExcelCol($i).'1', 'Descripción');
$sheet->setCellValue(getExcelCol($i).'1', 'Marca');
//BULTOS
$sheet->setCellValue(getExcelCol($i).'1', 'Bultos');
switch ($sumap) {
    case 1:
        $sheet->setCellValue(getExcelCol($i).'1', 'Pre '.$sumap2.' Bul');
        break;
    case 2:
        if($p1 == 1){ $pAux = $p1; }else{ $pAux = $p2;}
        $sheet->setCellValue(getExcelCol($i).'1', 'Pre '.$pAux.' Bul');
        if ($p3 == 3){ $pAux = $p3; }else{ $pAux = $p2;}
        $sheet->setCellValue(getExcelCol($i).'1', 'Pre '.$pAux.' Bul');
        $pAux = '';
        break;
    default: /** 0 || 3**/
        $sheet->setCellValue(getExcelCol($i).'1', 'Pre 1 Bul');
        $sheet->setCellValue(getExcelCol($i).'1', 'Pre 2 Bul');
        $sheet->setCellValue(getExcelCol($i).'1', 'Pre 3 Bul');
}
//PAQUETES
$sheet->setCellValue(getExcelCol($i).'1', 'Paquete');
switch ($sumap) {
    case 1:
        $sheet->setCellValue(getExcelCol($i).'1', 'Pre '.$sumap2.' Paq');
        break;
    case 2:
        if($p1 == 1){ $pAux = $p1; }else{ $pAux = $p2;}
        $sheet->setCellValue(getExcelCol($i).'1', 'Pre '.$pAux.' Paq');
        if ($p3 == 3){ $pAux = $p3; }else{ $pAux = $p2;}
        $sheet->setCellValue(getExcelCol($i).'1', 'Pre '.$pAux.' Paq');
        $pAux = '';
        break;
    default: /** 0 || 3**/
        $sheet->setCellValue(getExcelCol($i).'1', 'Pre 1 Paq');
        $sheet->setCellValue(getExcelCol($i).'1', 'Pre 2 Paq');
        $sheet->setCellValue(getExcelCol($i).'1', 'Pre 3 Paq');
}
if ($cubi == 1) {
    $sheet->setCellValue(getExcelCol($i).'1', 'Cubicaje');
}
//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++){ $spreadsheet->getActiveSheet()->getColumnDimension(getExcelCol($n))->setAutoSize(true); }
//pinta la cabecera de amarillo
$spreadsheet->getActiveSheet()->getStyle('A1:'.getExcelCol($aux).'1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');


//a partir de aqui comienza a llenarse la tabla
$datos = $precios->getListadeprecios($marcas, $depos, $exis, $orden);
$num = count($datos);
$row = 2;
foreach ($datos as $x) {
    $i = 0;
    if ($x['esexento']) {
        $precio1 = $x['precio1'] * $iva;
        $precio2 = $x['precio2'] * $iva;
        $precio3 = $x['precio3'] * $iva;
        $preciou1 = $x['preciou1'] * $iva;
        $preciou2 = $x['preciou2'] * $iva;
        $preciou3 = $x['preciou3'] * $iva;
    } else {
        $precio1 = $x['precio1'];
        $precio2 = $x['precio2'];
        $precio3 = $x['precio3'];
        $preciou1 = $x['preciou1'];
        $preciou2 = $x['preciou2'];
        $preciou3 = $x['preciou3'];
    }
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue(getExcelCol($i) . $row, $x['codprod']);
    $sheet->setCellValue(getExcelCol($i) . $row, $x['descrip']);
    $sheet->setCellValue(getExcelCol($i) . $row, $x['marca']);
    //BULTOS
    $sheet->setCellValue(getExcelCol($i) . $row, $x['existen']);
    switch ($sumap) {
        case 1:
            if ($x['esexento'] == 0) {
                $pAux = number_format($x['precio' . $sumap2] * $iva, 2, ",", ".");
            } else {
                $pAux = number_format($x['precio' . $sumap2], 2, ",", ".");
            }
            $sheet->setCellValue(getExcelCol($i) . $row, $pAux);
            break;
        case 2:
            if ($p1 == 1) {
                $pAux = number_format($precio1, 2, ",", ".");
            } else {
                $pAux = number_format($precio2, 2, ",", ".");
            }
            $sheet->setCellValue(getExcelCol($i) . $row, $pAux);
            if ($p3 == 3) {
                $pAux = number_format($precio3, 2, ",", ".");
            } else {
                $pAux = number_format($precio2, 2, ",", ".");
            }
            $sheet->setCellValue(getExcelCol($i) . $row, $pAux);
            break;
        default:
            /** 0 || 3**/
            $sheet->setCellValue(getExcelCol($i) . $row, number_format($precio1, 2, ",", "."));
            $sheet->setCellValue(getExcelCol($i) . $row, number_format($precio2, 2, ",", "."));
            $sheet->setCellValue(getExcelCol($i) . $row, number_format($precio3, 2, ",", "."));
    }
    $pAux = '';
    //PAQUETES
    $sheet->setCellValue(getExcelCol($i) . $row, round($x['exunidad']));
    switch ($sumap) {
        case 1:
            if ($x['esexento'] == 0) {
                $pAux = number_format($x['preciou' . $sumap2] * $iva, 2, ",", ".");
            } else {
                $pAux = number_format($x['preciou' . $sumap2], 2, ",", ".");
            }
            $sheet->setCellValue(getExcelCol($i) . $row, $pAux);
            break;
        case 2:
            if ($p1 == 1) {
                $pAux = number_format($preciou1, 2, ",", ".");
            } else {
                $pAux = number_format($preciou2, 2, ",", ".");
            }
            $sheet->setCellValue(getExcelCol($i) . $row, $pAux);
            if ($p3 == 3) {
                $pAux = number_format($preciou3, 2, ",", ".");
            } else {
                $pAux = number_format($preciou2, 2, ",", ".");
            }
            $sheet->setCellValue(getExcelCol($i) . $row, $pAux);
            break;
        default:
            /** 0 || 3**/
            $sheet->setCellValue(getExcelCol($i) . $row, number_format($preciou1, 2, ",", "."));
            $sheet->setCellValue(getExcelCol($i) . $row, number_format($preciou2, 2, ",", "."));
            $sheet->setCellValue(getExcelCol($i) . $row, number_format($preciou3, 2, ",", "."));
    }
    $pAux = '';
    if ($cubi == 1) {
        $sheet->setCellValue(getExcelCol($i) . $row, $x['cubicaje']);
    }
    $row++;
}
// por ultimo se escribe la cantidad de productos
$sheet->setCellValue('B' . ($row + 3), 'Total de Productos:  ' . $num);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Listado_de_precios_e_inventario_' . date('d/m/Y') . '.xls"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
ob_end_clean();
ob_start();
$writer->save('php://output');
