<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO
require_once("notadeentrega_modelo.php");

//INSTANCIAMOS EL MODELO
$nota = new NotaDeEntrega();
$numerod = $_GET['nrodocumento'];

//array of space in cells
$s = 0;
$j = 0;
$width = array();
$info = array();

function addWidthInArray($num){
    $GLOBALS['width'][$GLOBALS['s']] = $num;
    $GLOBALS['s'] = $GLOBALS['s'] + 1;
    return $num;
}

function addInfoInArray($info){
    $GLOBALS['info'][$GLOBALS['j']] = $info;
    $GLOBALS['j'] = $GLOBALS['j'] + 1;
}

class PDF extends FPDF
{
    var $widths;
    var $aligns;

    // Cabecera de página
    function Header()
    {
        // Logo
        $this->Image(PATH_LIBRARY.'build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', '', 11);
        // Movernos a la derecha
        $this->Cell(75);
        // Título
        $empresa = Empresa::getInfo();
//        $this->Cell(40, 10, 'REPORTE DE MAESTRO DE CLIENTES POR RUTA', 0, 0, 'C');
        $this->Cell(30,4, $empresa["descrip"],0,1,'L');
        $this->SetFont('');
        $this->Cell(38);
        $this->Cell(68,4, $empresa["rif"],0,1,'L');
        $this->Cell(38);
        $this->Cell(68,4, $empresa["direc1"],0,1,'L');
        $this->Cell(38);
        $this->Cell(68,5, $empresa["telef"],0,1,'L');

        //linea
        $this->Line(8, 30, 200, 30);
        $this->Ln(3);

        //datos del cliente
        $numerod = $_GET['nrodocumento'];
        $cabecera = NotasDeEntrega::getHeaderById($numerod);
        $this->SetFont('Arial','B',8);
        $this->Cell(19,8, "Cod. Cliente: ",0,0,'L');
        $this->SetFont('');
        $this->Cell(40,8, $cabecera["codclie"],0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(6,8, "Rif: ",0,0,'L');
        $this->SetFont('');
        $this->Cell(40,8, $cabecera["rif"],0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(15,8, "Vendedor: ",0,0,'L');
        $this->SetFont('');
        $this->Cell(10,8, $cabecera["codvend"],0,1,'L');

        $this->SetFont('Arial','B',8);
        $this->Cell(20,8, "Razon Social: ",0,0,'L');
        $this->SetFont('');
        $this->Cell(79,8, $cabecera["rsocial"],0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(14,8, "Telefono: ",0,0,'L');
        $this->SetFont('');
        $this->Cell(33,8, $cabecera["telefono"],0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->Cell(10,8, "Fecha: ",0,0,'L');
        $this->SetFont('');
        $this->Cell(20,8, Date(FORMAT_DATE, strtotime($cabecera['fechae'])),0,1,'L');

        $this->SetFont('Arial','B',8);
        $this->Cell(23,8, "Direccion Fiscal: ",0,0,'L');
        $this->SetFont('');
        $this->Cell(80,8, mssql_result($cabeceranota,0,"direccion"),0,1,'L');
        $this->Cell(80,8, mssql_result($cabeceranota,0,"direccion2"),0,1,'L');

        //Nota de entrega
        $this->SetFont('Arial','B',14);
        $this->Cell(77);
        $this->Cell(30,10,'NOTA DE ENTREGA',0,1,'C');

        //numero de documento
        $this->SetFont('');
        $this->SetTextColor(255 , 0, 0);
        $this->Cell(185,8,'# '.$numeront,0,1,'R');
        $this->SetTextColor(0 , 0, 0);

        //linea
        $this->Line(8, 73, 200, 73);
        $this->Ln(3);

        // Salto de línea
        $this->Ln(15);
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(22), 6, utf8_decode(Strings::titleFromJson('codclie')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(57), 6, utf8_decode(Strings::titleFromJson('razon_social')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(14), 6, utf8_decode(Strings::titleFromJson('estatus')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(22), 6, utf8_decode(Strings::titleFromJson('ruta_principal')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(27), 6, utf8_decode(Strings::titleFromJson('ruta_alternativa_1')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(27), 6, utf8_decode(Strings::titleFromJson('ruta_alternativa_2')), 1, 0, 'C', true);
        $this->Cell(addWidthInArray(21), 6, utf8_decode(Strings::titleFromJson('dia_visita')), 1, 1, 'C', true);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 7);

/*$pdf->SetWidths($width);

$query =  $maestro->getMaestro($codvend);

foreach ($query as $i) {

    $pdf->Row(
        array(
            $i['codclie'],
            utf8_decode($i['descrip']),
            ($i['activo'] == 1) ? "Activo" : "Inactivo",
            $i['codvend'],
            $i["Ruta_Alternativa"],
            $i["Ruta_Alternativa_2"],
            strtoupper($i["DiasVisita"]),
        )
    );
}*/

$pdf->Output();

?>