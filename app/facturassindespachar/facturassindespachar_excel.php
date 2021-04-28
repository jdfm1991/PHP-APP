<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require('../vendor/autoload.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("facturassindespachar_modelo.php");

//INSTANCIAMOS EL MODELO
$factsindes = new FacturaSinDes();

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

$fechai = $_GET['fechai'];
$fechaf = $_GET['fechaf'];
$convend = $_GET['vendedores'];
$tipo = $_GET['tipo'];
$check = hash_equals("true", $_GET['check']);
$hoy = date("d-m-Y");

// Da igual el formato de las fechas (dd-mm-aaaa o aaaa-mm-dd),
function diasEntreFechas($fechainicio, $fechafin){
    return ((strtotime($fechafin)-strtotime($fechainicio))/86400);
}


//creamos la cabecera de la tabla
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
$sheet->setCellValue(getExcelCol($i).'1', 'Documento');
$sheet->setCellValue(getExcelCol($i).'1', 'Fecha Emisión');
if($check) {
    $sheet->setCellValue(getExcelCol($i).'1', 'Fecha Despacho');
    $sheet->setCellValue(getExcelCol($i).'1', 'DíasTrans');
}
$sheet->setCellValue(getExcelCol($i).'1', 'Código');
$sheet->setCellValue(getExcelCol($i).'1', 'Cliente');
$sheet->setCellValue(getExcelCol($i).'1', 'DíasHastHoy');
$sheet->setCellValue(getExcelCol($i).'1', 'Cant Bult');
$sheet->setCellValue(getExcelCol($i).'1', 'Cant Paq');
$sheet->setCellValue(getExcelCol($i).'1', 'Monto Bs');
$sheet->setCellValue(getExcelCol($i).'1', 'EDV');
if($check) {
    $sheet->setCellValue(getExcelCol($i).'1', 'TPromEsti');
    $sheet->setCellValue(getExcelCol($i).'1', '%Oportunidad');
}

//obtenemos el ultimo valor de la celda y la guardamos en una variable auxiliar
$aux = $i-1;
//se itera la cantidad de celdas almacenadas en la variable axiliar y se situan AutoSize
for($n=0; $n <= $aux; $n++){ $spreadsheet->getActiveSheet()->getColumnDimension(getExcelCol($n))->setAutoSize(true); }
//pinta la cabecera de amarillo
$spreadsheet->getActiveSheet()->getStyle('A1:'.getExcelCol($aux).'1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');


//a partir de aqui comienza a llenarse la tabla
$query = $factsindes->getFacturas($tipo, $fechai, $fechaf, $convend, $check);
$num = count($query);
$suma_bulto = 0;
$suma_paq = 0;
$suma_monto = 0;
$porcent = 0;
$row = 2;
foreach ($query as $x) {
    $i = 0;

    if($check) {
        $calcula = 0;
        if (round(diasEntreFechas(date("d-m-Y", strtotime($x["FechaE"])),date("d-m-Y", strtotime($x["fechad"])))) != 0)
            $calcula = (2 / round(diasEntreFechas(date("d-m-Y", strtotime($x["FechaE"])),date("d-m-Y", strtotime($x["fechad"])))))*100;

        if ($calcula > 100)
            $calcula = 100;

        $porcent += $calcula;
    }

    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue(getExcelCol($i) . $row, $x['NumeroD']);
    $sheet->setCellValue(getExcelCol($i) . $row, date("d/m/Y", strtotime($x["FechaE"])));
    if ($check) {
        $sheet->setCellValue(getExcelCol($i) . $row, date("d/m/Y", strtotime($x["fechad"])));
        $sheet->setCellValue(getExcelCol($i) . $row, round(diasEntreFechas(date("d-m-Y", strtotime($x["FechaE"])),date("d-m-Y", strtotime($x["fechad"])))));
    }
    $sheet->setCellValue(getExcelCol($i) . $row, $x['CodClie']);
    $sheet->setCellValue(getExcelCol($i) . $row, utf8_decode($x['Descrip']));
    $sheet->setCellValue(getExcelCol($i) . $row, round(diasEntreFechas(date("d-m-Y", strtotime($x["FechaE"])), $hoy)));
    $sheet->setCellValue(getExcelCol($i) . $row, number_format($x["Bult"], 0, ",", "."));
    $sheet->setCellValue(getExcelCol($i) . $row, number_format($x["Paq"], 0, ",", "."));
    $sheet->setCellValue(getExcelCol($i) . $row, number_format($x["Monto"], 1, ",", ".")); $suma_monto += $x["Monto"];
    $sheet->setCellValue(getExcelCol($i) . $row, $x['CodVend']);
    if ($check) {
        $sheet->setCellValue(getExcelCol($i) . $row, 2);
        $sheet->setCellValue(getExcelCol($i) . $row, number_format($calcula, 1, ",", ".") . "%");
    }
    $row++;
}
$i = 0;
// por ultimo se escribe los totales
$sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
$sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
if ($check) {
    $sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
    $sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
}
$sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
$sheet->setCellValue(getExcelCol($i) . ($row + 3), 'Total de Documentos:  ' . $num);
$sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
$sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
$sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
$sheet->setCellValue(getExcelCol($i) . ($row + 3), 'Monto Total: ' . number_format($suma_monto, 2, ",", "."));
$sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
if ($check) {
    $sheet->setCellValue(getExcelCol($i) . ($row + 3), '');
    $sheet->setCellValue(getExcelCol($i) . ($row + 3), '% Oportunidad Total: ' . number_format(($porcent / count($query)), 2, ",", ".") . ' %');
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Listado_de_precios_e_inventario_' . date('d/m/Y') . '.xls"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
ob_end_clean();
ob_start();
$writer->save('php://output');
