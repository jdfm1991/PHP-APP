<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

require('../public/fpdf/fpdf.php');

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once ("despachosrelacion_modelo.php");
require_once("../despachos/despachos_modelo.php");
require_once("../choferes/choferes_modelo.php");
require_once("../vehiculos/vehiculos_modelo.php");

//INSTANCIAMOS EL MODELO
$relacion  = new DespachosRelacion();
$despachos  = new Despachos();
$choferes = new Choferes();
$vehiculos = new Vehiculos();

$correlativo = $_GET['correlativo'];

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $despachos  = new Despachos();
        $empresa = $despachos->getDatosEmpresa();

        // Logo
        $this->Image('../public/build/images/logo.png', 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 11);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(30, 10, $empresa[0]['Descrip'], 0, 0, 'C');
        $this->Ln();

    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw =& $this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}


function rdecimal($valor) {
    //$float_redondeado=round($valor * 10) / 10;
    $float_redondeado = number_format($valor, 2, ",", ".");
    return $float_redondeado;
}


$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);

    /*******************************************/
    /*              CABECERA                   */
    /*******************************************/
$cabeceraDespacho = $despachos->getCabeceraDespacho($correlativo);
$chofer = $choferes->get_chofer_por_id($cabeceraDespacho[0]['ID_Chofer']);
$vehiculo = $vehiculos->get_vehiculo_por_id($cabeceraDespacho[0]['ID_Vehiculo']);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(90,7,'Nro de Despacho: '.str_pad($correlativo, 8, 0, STR_PAD_LEFT),0,0,'C');
$pdf->Ln();
$pdf->SetFont ('Arial','',7);
$pdf->Cell(90,7,'Fecha Despacho: '.date("d/m/Y", strtotime($cabeceraDespacho[0]['fechad'])),0,0,'L');
$pdf->Cell(90,7,'Vehiculo de Carga: : '.$vehiculo[0]['Placa'].'  '.$vehiculo[0]['Modelo'].'  '.$vehiculo[0]['Capacidad'].'Kg',0,0,'L');
$pdf->Ln();

$pdf->Cell(150,7,'Destino : '.$cabeceraDespacho[0]['Destino']." - ".$chofer[0]['Nomper'],0,0,'L');
$pdf->Ln();



    /*****************************************************************/
    /*               TABLA DE FACTURAS DEL DESPACHO                  */
    /*****************************************************************/
$pdf->SetFont ('Arial','',8);
$pdf->Cell(62,7,'Listado de Facturas Seleccionadas',0,0,'C');
$pdf->Ln();
$pdf->SetFillColor(200,220,255);
// titulo de columnas
$pdf->SetFont ('Arial','B',8);
$pdf->Cell(20,7,'Nro Fact',1,0,'C',true);
$pdf->Cell(30,7,'Fecha E',1,0,'C',true);
$pdf->Cell(10,7,'Ruta',1,0,'C',true);
$pdf->Cell(30,7,'CodCliente',1,0,'C',true);
$pdf->Cell(75,7,'Cliente',1,0,'C',true);
$pdf->Cell(20,7,'Peso(Kg)',1,0,'C',true);
$pdf->SetFont ('Arial','',7);
$pdf->Ln();


$facturas_en_despacho = $relacion->get_factura_de_un_despacho_por_correlativo($correlativo);

$pdf->SetWidths(array(20,30,10,30,75,20));
foreach ($facturas_en_despacho AS $item){
    $pdf->Row(
        array(
            $item["NumeroD"],
            date("d M Y   h:m A", strtotime($item["FechaE"])),
            $item["CodVend"],
            $item["CodClie"],
            $item["Descrip"],
            rdecimal($item["Peso"])
        )
    );
}
$pdf->Cell(62,7,'Total de Facturas Emitidas: '.count($facturas_en_despacho),0,0,'C');
$pdf->Ln();
$pdf->Ln();



    /*****************************************************************/
    /*              TABLA DE PRODUCTOS DEL DESPACHO                  */
    /*****************************************************************/
$lote = "";

//obtener los productos por despacho creado
$query = $despachos->getProductosDespachoCreado($correlativo);

//facturas por correlativo
$documentos = $despachos->getFacturasPorCorrelativo($correlativo);
$num = count($documentos);
foreach ($documentos AS $item)
    $lote .= " ".$item['Numerod'].",";
//le quitamos 1 caracter para quitarle la ultima coma
$lote = substr($lote, 0, -1);

$total_bultos = 0;
$total_paq = 0;
$total_peso = 0;
$total_peso_azucar = 0;
$total_peso_galleta = 0;
$total_peso_chocolote = 0;

$pdf->Cell(62,7,'Listado de Productos a Despachar',0,0,'C');
$pdf->Ln();
$pdf->SetFillColor(200,220,255);
// titulo de columnas
$pdf->SetFont ('Arial','B',8);
$pdf->Cell(20,7,'Cod Prod',1,0,'C',true);
$pdf->Cell(75,7, utf8_decode('Descripción'),1,0,'C',true);
$pdf->Cell(30,7,'Cant Bultos',1,0,'C',true);
$pdf->Cell(30,7,'Cant Paquetes',1,0,'C',true);
$pdf->Cell(30,7,'Peso',1,1,'C',true);
$pdf->SetFont ('Arial','',8);

$pdf->SetWidths(array(20,75,30,30,30));
foreach ($query as $i) {

    $bultos = 0;
    $paq = 0;

    if ($i["BULTOS"] > 0){
        $bultos = $i["BULTOS"];
    }
    if ($i["PAQUETES"] > 0){
        $paq = $i["PAQUETES"];
    }

    if ($i["EsEmpaque"] != 0){
        if ($i["PAQUETES"] >= $i["CantEmpaq"]){

            if ($i["CantEmpaq"] != 0) {
                $bultos_total = $i["PAQUETES"] / $i["CantEmpaq"];
            }else{
                $bultos_total = 0;
            }
            $decimales = explode(".",$bultos_total);
            $bultos_deci = $bultos_total - $decimales[0];
            $paq = $bultos_deci * $i["CantEmpaq"];
            $bultos = $decimales[0] + $bultos;
        }
    }
    $peso = $bultos * $i['tara'];
    if($i["CantEmpaq"] != 0) {
        $peso += ($i['tara'] * $paq) / $i['CantEmpaq'];
    }

    switch ($i["CodInst"]){
        case "80":
            $total_peso_chocolote = $total_peso_chocolote + $peso;
            break;
        case "81":
            $total_peso_azucar = $total_peso_azucar + $peso;
            break;
        case "82":
            $total_peso_galleta = $total_peso_galleta + $peso;
            break;
    }

    $total_peso += $peso;
    $total_bultos += $bultos;
    $total_paq += $paq;

    $pdf->Row(
        array(
            $i["CodItem"],
            strtoupper($i["Descrip"]),
            round($bultos),
            round($paq),
            rdecimal($peso)
        )
    );

}
$pdf->SetFillColor(200,220,255);
$pdf->SetFont ('Arial','B',8);
$pdf->Cell(95,7,'Total = ',1,0,'C');
$pdf->Cell(30,7,$total_bultos.' Bult',1,0,'C',true);
$pdf->Cell(30,7,$total_paq.' Paq',1,0,'C',true);
$pdf->Cell(30,7,rdecimal($total_peso).'Kg'.' - '.rdecimal($total_peso/1000).'TN',1,0,'C',true);
$pdf->Ln();
$pdf->Cell(62,7,'FACTURAS DESPACHADAS '.$num,0,0,'C');
$pdf->Ln();
$pdf->Cell(20,7,' ',0,0,'C');
$pdf->MultiCell(140,5,$lote);
$pdf->Ln();
$pdf->Cell(120,7,'Total Azucar (TN): '.($total_peso_azucar/1000).' Total Chocolate (TN): '.($total_peso_chocolote/100).' Total Galleta (TN): '.($total_peso_galleta/1000),0,0,'C');
$pdf->Ln();




    /***************************************************************************/
    /*              TABLA DE PRODUCTOS DEVUELTOS DEL DESPACHO                  */
    /***************************************************************************/
$devoluciones = $relacion->get_productos_devueltos_de_un_despacho($correlativo);

if (count($devoluciones) != 0){
    $fact_devueltas = $relacion->get_facturas_devueltas_de_un_despacho($correlativo);

    $pdf->SetFont ('Arial','',7);
    $pdf->Cell(62,7,'PRODUCTOS DEVUELTOS',0,0,'C');
    $pdf->Ln();
    $pdf->SetTextColor(255,255,255);
    $pdf->SetFillColor(255,0,0);
    $pdf->Cell(20,7,'Cod Prod',1,0,'C',true);
    $pdf->Cell(75,7,'Descripcion',1,0,'C',true);
    $pdf->Cell(30,7,'Cant Bultos',1,0,'C',true);
    $pdf->Cell(30,7,'Cant Paquetes',1,0,'C',true);
    $pdf->Cell(30,7,'Peso',1,0,'C',true);
    $pdf->Ln();
    $pdf->SetTextColor(0,0,0);
    $total_bultos = 0;
    $total_paq = 0;
    $total_peso = 0;

    $pdf->SetWidths(array(20,75,30,30,30));
    foreach ($devoluciones AS $i){

        $bultos = 0;
        $paq = 0;

        if ($i["BULTOS"] > 0){
            $bultos = $i["BULTOS"];
        }
        if ($i["PAQUETES"] > 0){
            $paq = $i["PAQUETES"];
        }

        if ($i["EsEmpaque"] != 0){
            if ($i["PAQUETES"] >= $i["CantEmpaq"]){
                $bultos_total = $i["PAQUETES"] / $i["CantEmpaq"];
                $decimales = explode(".",$bultos_total);
                $bultos_deci = $bultos_total - $decimales[0];
                $paq = $bultos_deci * $i["CantEmpaq"];
                $bultos = $decimales[0] + $bultos;
            }
        }
        $peso = $bultos * $i['tara'];
        $peso += ($i['tara'] * $paq) / $i['CantEmpaq'];

        $total_peso += $peso;
        $total_bultos += $bultos;
        $total_paq += $paq;

        $pdf->Row(
            array(
                $i["CodItem"],
                strtoupper($i["Descrip"]),
                round($bultos),
                round($paq),
                rdecimal($peso)
            )
        );
    }

    $pdf->SetFont ('Arial','B',8);
    $pdf->Cell(95,7,'Total Devuelto = ',1,0,'C');
    $pdf->SetTextColor(255,255,255);
    $pdf->Cell(30,7,$total_bultos.' Bult',1,0,'C',true);
    $pdf->Cell(30,7,$total_paq.' Paq',1,0,'C',true);
    $pdf->Cell(30,7,rdecimal($total_peso).'Kg'.' - '.rdecimal($total_peso/1000).'TN',1,0,'C',true);
    $pdf->Ln();
    $pdf->SetTextColor(0,0,0);
    $pdf->Cell(62,7,'FACTURAS AFECTADAS ('.count($fact_devueltas).') :',0,0,'C');
    $pdf->Ln();
    $notas = "";
    foreach ($fact_devueltas AS $item)
        $notas .= " ".$item['ONumero'].",";
    $notas = substr($notas, 0, -1);
    $pdf->Cell(20,7,' ',0,0,'C');
    $pdf->MultiCell(140,5,$notas);

}else{
    $pdf->Cell(20,7,' ',0,0,'C');
    $pdf->Cell(62,7,'NO SE LE REALIZARON DEVOLUCIONES A ESTE DESPACHO ',0,0,'C');
}

$pdf->Output();

?>
