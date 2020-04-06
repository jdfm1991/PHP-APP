<?php
date_default_timezone_set('America/Caracas');
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
require_once 'acceso/conection.php';
require('plugins/fpdf/fpdf.php');
if ($_SESSION['login']) {

    $codvend = $_GET['codvend'];

    class PDF extends FPDF
    {
        var $widths;
        var $aligns;

        // Cabecera de página
        function Header()
        {
            // Logo
            $this->Image('build/images/logo.png', 10, 8, 33);
            // Arial bold 15
            $this->SetFont('Arial', '', 11);
            // Movernos a la derecha
            $this->Cell(80);
            // Título
            $this->Cell(40, 10, 'REPORTE DE CLIENTES BLOQUEADOS', 0, 0, 'C');
            // Salto de línea
            $this->Ln(20);
            // titulo de columnas

            $this->Cell(18, 6, 'CodClient', 1, 0, 'C', 0);
            $this->Cell(60, 6, utf8_decode('Descripción'), 1, 0, 'C', 0);
            $this->Cell(18, 6, 'Rif', 1, 0, 'C', 0);
            $this->Cell(50, 6, 'Direccion', 1, 0, 'C', 0);
            $this->Cell(21, 6, 'Estatus', 1, 0, 'C', 0);
            $this->Cell(24, 6, 'Dias Visita', 1, 1, 'C', 0);
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

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 7);

    $pdf->SetWidths(array(18, 60, 18, 50, 21, 24));

    $total = $bd1->getTotalClientesPorCodigo($codvend);
    $query = $bd1->getClientesBloqueados($codvend);
    $num = count($query);

    foreach ($query as $i) {

        $escredito = "";
        if ($i['escredito'] == 1) {
            $escredito = "SOLVENTE";
        } else {
            $escredito = "BLOQUEADO: " . utf8_encode($i['observa']);
        }

        $pdf->Row(
            array(
                $i['codclie'],
                utf8_decode($i['descrip']),
                $i['id3'],
                utf8_encode($i['direc1']) . " " . utf8_encode($i['direc2']),
                $escredito,
                $i['diasvisita']
            )
        );
    }
    $pdf->Ln(10);
    $pdf->Cell(190, 10, 'Total de Clientes BLOQUEADOS:  '.$num.'  de  '.count($total).' Clientes.', 0, 1, 'C');
    $pdf->Output();

} else {
    header('Location: logueoerror.php');
}

?>