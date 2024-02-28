<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

require(PATH_LIBRARY.'fpdf/fpdf.php');

//LLAMAMOS AL MODELO
require_once("notadeentrega_modelo.php");

//INSTANCIAMOS EL MODELO
$nota = new NotaDeEntrega();
$numerod = $_GET['nrodocumento'];

$cabecera = NotasDeEntrega::getHeaderById($numerod);
$descuentoitem  = Numbers::avoidNull( $nota->get_descuento($numerod, 'C')['descuento'] );

$observacion = Strings::avoidNull($cabecera['notas1']);
$Represent = Strings::avoidNull($cabecera['Represent']);
$subtotal = Strings::rdecimal($cabecera['subtotal'],2);
$descuentototal = Strings::rdecimal($cabecera['descuento'],2);
$totalnota = Strings::rdecimal($cabecera['total'],2);

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
        $this->Cell(30,4, $empresa["descrip"],0,1,'L');
   
       
        //linea
         $this->Line(8, 19, 200, 19);
        $this->Ln(5);

        //datos del cliente
        $numerod = $_GET['nrodocumento'];
        $cabecera = $GLOBALS['cabecera'];
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
        $this->Cell(20,1, "Razon Social: ",0,0,'L');
        $this->SetFont('');
        $this->Cell(70,1, $cabecera["rsocial"],0,0,'L');

        $this->SetFont('Arial','B',8);
        $this->Cell(22,1, "Representante: ",0,0,'L');
        $this->SetFont('');
        $this->Cell(35,1, $cabecera["Represent"],0,0,'L');

        $this->SetFont('Arial','B',8);
        $this->Cell(14,1, "Telefono: ",0,0,'L');
        $this->SetFont('');
        $this->Cell(28,1, $cabecera["telefono"],0,1,'L');

        $this->SetFont('Arial','B',8);
        $this->Cell(23,8, "Direccion Fiscal: ",0,0,'L');
        $this->SetFont('');
        $this->Cell(80,8, $cabecera["direccion"],0,1,'L');
        $this->Cell(140,1, $cabecera["direccion2"],0,0,'L');

        $this->SetFont('Arial','B',8);
        $this->Cell(10,1, "Fecha: ",0,0,'L');
        $this->SetFont('');
        $this->Cell(20,1, Date(FORMAT_DATE, strtotime($cabecera['fechae'])),0,1,'L');

        //Nota de entrega
        $this->SetFont('Arial','B',14);
        $this->Cell(77);
        $this->Cell(30,10,'NOTA DE ENTREGA',0,1,'C');

        //numero de documento
        $this->SetFont('');
        $this->SetTextColor(255 , 0, 0);
        $this->Cell(185,-4,'# '.$numerod,0,1,'R');
        $this->SetTextColor(0 , 0, 0);

    

        $descuentoitem  = $GLOBALS['descuentoitem'];

        $anchoAdicional = 0;
        if ($descuentoitem > 0) {
            $anchoAdicional += 0;// +0
        } else {
            $anchoAdicional += (20+18);// +38
        }

        // titulo de la tabla
        $this->Ln(5);
        $this->SetFont('Arial','B',9);
        $this->SetFillColor(200,220,255);
        // titulo de columnas
        $this->Cell(addWidthInArray(15 + ($anchoAdicional*0.20)),6,'Codigo',1,0,'C',true);
        $this->Cell(addWidthInArray(60 + ($anchoAdicional*0.40)),6,utf8_decode('Descripción'),1,0,'C',true);
        $this->Cell(addWidthInArray(16 + ($anchoAdicional*0.16)),6,'Cantidad',1,0,'C',true);
        $this->Cell(addWidthInArray(15 + ($anchoAdicional*0.12)),6,'Unidad',1,0,'C',true);
        $this->Cell(addWidthInArray(26),6,'Precio Unitario',1,0,'C',true);
        if ($descuentoitem > 0) {
            $this->Cell(addWidthInArray(20),6,'Sub Total',1,0,'C',true);
            $this->Cell(addWidthInArray(18),6,'Descuento',1,0,'C',true);
        }
        $this->Cell(addWidthInArray(20 + ($anchoAdicional*0.12)),6,'Total',1,1,'C',true);
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',8);

$pdf->SetWidths($width);

$detalle = NotasDeEntrega::getDetailById($numerod);

$multiplicador_linea = 0;
foreach ($detalle as $i) {
    $j = 0;
    $multiplicador_linea += 5;

    addInfoInArray($i['coditem']);
    addInfoInArray(utf8_decode($i['descripcion']));
    addInfoInArray(number_format($i['cantidad']));
    addInfoInArray(($i['esunidad'] == '1') ? "PAQ" : "BUL");
    addInfoInArray(Strings::rdecimal($i['precio'], 2));
    if($descuentoitem > 0)
    {
        addInfoInArray(Strings::rdecimal($i['totalitem'], 2));
        addInfoInArray(Strings::rdecimal($i['descuento'], 2));
    }
    addInfoInArray(Strings::rdecimal($i['total'], 2));

    $pdf->Row($info);
}
//linea
//$pdf->Line(8, $multiplicador_linea + 98, 200, $multiplicador_linea + 98);
$pdf->Ln(10);


$pdf->SetFont('Arial','B',8);
if($descuentototal >= 0) {
    $pdf->Cell(155,8, "",0,0,'L');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(15,-13, "Sub Total: ",0,0,'L');
    $pdf->SetFont('');
    $pdf->Cell(33,-13, $subtotal,0,1,'L');
    $pdf->Cell(153,-10, "",0,0,'L');
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(16,19, "Descuento: ",0,0,'L');
    $pdf->SetFont('');
    $pdf->Cell(33,19, $descuentototal,0,1,'L');
}

$pdf->Cell(23,-26, "Observaciones: ",0,0,'L');
$pdf->SetFont('');
$pdf->Cell(130,-26, $observacion,0,0,'L');
$pdf->Cell(8,8, "",0,0,'L');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(9,-13, "Total: ",0,0,'L');
$pdf->SetFont('');
$pdf->Cell(33,-13, ($totalnota),0,1,'L');

$pdf->Cell(77);
$pdf->Cell(30,10,'SIN DERECHO A CREDITO FISCAL',0,1,'C');
$pdf->Ln(-5);
$pdf->Cell(0,10,'VERIFIQUE SU MERCANCIA, NO SE ACEPTAN RECLAMOS DESPUES DE HABER FIRMADO',0,1,'C');
$pdf->Ln(-5);
$pdf->Cell(0,10,'Y SELLADO ESTA NOTA DE ENTREGA.',0,0,'C');
$pdf->Ln(5);
$pdf->Cell(0,10,'ESTIMADO CLIENTE CONFIRME SU PAGO REALIZADO A NUESTRA EMPRESA',0,1,'C');
$pdf->Ln(-5);
$pdf->Cell(0,10,'CON UN MENSAJE AL WHATSAPP +58 424-9466495',0,1,'C');
$pdf->SetFont('Arial','B',8); 
$pdf->Cell(0,5,'"GRACIAS POR PREFERIRNOS".',0,1,'C');


$pdf->Ln(30);

// lineas de firma
$pdf->Line(40, $multiplicador_linea + 100, 70, $multiplicador_linea + 100);
$pdf->Line(135, $multiplicador_linea + 100, 165, $multiplicador_linea + 100);

// texto de firmas
$pdf->SetFont('Arial','',8);
$pdf->Cell(33);
$pdf->Cell(23,-30, "Depachado por",0,0,'C');
$pdf->Cell(73);
$pdf->Cell(23,-30, "Recibido por",0,0,'C');


$pdf->Output();

?>