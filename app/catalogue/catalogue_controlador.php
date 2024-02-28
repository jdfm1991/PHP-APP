<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR_C0NF1M4N14');
session_start();
//LLAMAMOS A LA CONEXION BASE DE DATOS.

require_once("../../config/conexion.php");
require_once("../../public/pdf/autoload.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("catalogue_modelo.php");

//INSTANCIAMOS EL MODELO
$download = new Download();

$stamps  = (isset($_GET['stamps'])) ? $_GET['stamps'] : '';
$price   = (isset($_GET['price'])) ? $_GET['price'] : '';
$existe  = (isset($_GET['existe'])) ? $_GET['existe'] : ''; 
$checkimage  = (isset($_POST['checkimage'])) ? $_POST['checkimage'] : 'false';

$query = '';

    if ($price == 1) {
        $query .= 'SELECT p.CodProd, p.Descrip,p.Refere, I.Descrip AS stamp, p.Activo, ISNULL(pp.Precio1_P, 0) AS pprice, ISNULL(pp.Precio1_B, 0) AS bprice, e.Existen, p.CantEmpaq, e.ExUnidad, pp.ImagenC FROM SAPROD AS P';
    }elseif ($price == 2) {
        $query .='SELECT p.CodProd, p.Descrip,p.Refere, I.Descrip AS stamp, p.Activo, ISNULL(pp.Precio2_P, 0) AS pprice, ISNULL(pp.Precio2_B, 0) AS bprice, e.Existen, p.CantEmpaq, e.ExUnidad, pp.ImagenC FROM SAPROD AS P';
    }elseif ($price == 3) {
        $query .= 'SELECT p.CodProd, p.Descrip,p.Refere, I.Descrip AS stamp, p.Activo, ISNULL(pp.Precio3_P, 0) AS pprice, ISNULL(pp.Precio3_B, 0) AS bprice, e.Existen, p.CantEmpaq, e.ExUnidad, pp.ImagenC FROM SAPROD AS P';
    }else {
        $query .= 'SELECT p.CodProd, p.Descrip,p.Refere, I.Descrip AS stamp, p.Activo, ISNULL(pp.Precio1_P, 0) AS pprice, ISNULL(pp.Precio1_B, 0) AS bprice, e.Existen, p.CantEmpaq, e.ExUnidad, pp.ImagenC FROM SAPROD AS P';
    }

    $query .= '
    INNER JOIN SAEXIS AS E ON P.CodProd = E.CodProd 
    INNER JOIN SAINSTA AS I ON p.CodInst = I.CodInst
    INNER JOIN SAINSTA_01 AS I1 ON I1.CodInst = I.CodInst
    INNER JOIN saprod_02 AS pp ON p.codprod = pp.codprod 
    WHERE (e.codubic = 1) AND p.Activo != 0';

    if ($stamps == 'All' || empty($stamps )) {
        $query .= '';
    } else {
        $query .= " AND I.CodInst IN ($stamps)";
    }
    
    if ($existe == 'false') {
        $query .= '';
    } else {
        $query .= " AND (e.existen > 0 or e.ExUnidad > 0)  ";
    }

    if ($checkimage == 'false') {
        $query .= ' AND pp.ImagenC IS NOT NULL';
      } else {
        $query .= " AND pp.ImagenC IS NULL";
      }

    $query .= " ORDER BY I1.CodInst, P.Descrip ASC ";

$name = $download->subsidiaryname();
$stylesheet = file_get_contents('../../public/style.css');
$logo = 'logo2.png';

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case 'substamps':

        $data = $download->substamps();
        echo json_encode($data, JSON_UNESCAPED_UNICODE); 
        break;

    case 'enlist':

        $data = $download->searchcontent($query);
        echo json_encode($data, JSON_UNESCAPED_UNICODE); 
        break;
    
    case 'catalogue':
        
        $data = $download->searchcontent($query);

        $body = "";
        foreach ($data as $row) {
            $body .='
        <div class="box">
            <center>
                <img src="../../public/img/gallery/'.$row['ImagenC'].'" class="img">
            </center>
            <div class="text">
                <h4>'.$row['Descrip'].'</h4>
                <p>'.$row['Refere'].'</p>';
               

                    $body .= '<span> '.$row['stamp'].'</span><br>';

                
                if($name=='DISTRIBUCIONES AJ, C.A.'){
                    $body .= '<strong><span>Precio Paq.: '.number_format($row['bprice'],2).' $</span></strong>';
                }
                else{
                   $body .= '<strong><span>Precio Paq.: '.$row['pprice'].' $ Precio Bul.: '.$row['bprice'].' $</span></strong>';
                }
        $body .= '        
            </div>
        </div>';
        }
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'orientation' => 'L',
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch',
        ]);

        $mpdf->SetHeader('<img src="../../public/build/images/'.$logo.'" style="width: 100px;">|<h1>'.$name.'</h1>Pag.: {PAGENO}, Consulta del: {DATE j-m-Y}');
        $mpdf->SetFooter('&copy; Copyright <strong><span>Catalogo Digital</span></strong>. All Rights Reserved <strong><br> Diseño por </strong><b> Innovación Tecnológica <strong><span>(INTEC)</span></strong>, C.A. </b>|Precio: '.$price.',  Numero de Pagina: {PAGENO}| Fecha de Consulta: {DATE j-m-Y}');
        $mpdf->defaultheaderfontsize=10;
        $mpdf->defaultheaderfontstyle='B';
        $mpdf->defaultheaderline=0;
        $mpdf->defaultfooterfontsize=10;
        $mpdf->defaultfooterfontstyle='BI';
        $mpdf->defaultfooterline=0;

        $mpdf->SetWatermarkImage('../../public/build/images/'.$logo.'');
        $mpdf->showWatermarkImage = true;
        $mpdf->watermarkImageAlpha = 0.1;
        /*
        $mpdf->SetWatermarkText($name);
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;
        */
        $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($body,\Mpdf\HTMLParserMode::HTML_BODY);
        $mpdf->Output('Catalogo '.$name.' Precio '.$price.'.pdf',\Mpdf\Output\Destination::INLINE);
        
        break;

    case 'list':

        $data = $download->searchcontent($query);

        $body = "";
        $body.='
        <table style="text-align:center;">
            <thead>
                <tr>
                    <th style="border: black 5px solid;">Producto</th>
                    <th>Referencia</th>
                    <th>Marca</th>';
                    if($name=='DISTRIBUCIONES AJ, C.A.'){
                    $body.=     '<th>Precio Paq $</th>';
                    }
                    else{
                    $body.=     '   <th>Precio Paq $</th>
                        <th>Precio Bul $</th>';
                    }
                    $body.=     '<th>Imagen</th>
                </tr>
            </thead>
            <tbody>';
            foreach ($data as $row) {
                $body.='   
                <tr>
                    <td>'.$row['Descrip'].'</td>
                    <td>'.$row['Refere'].'</td>                       
                    <td>'.$row['stamp'].'</td>';
                    if ($name=='DISTRIBUCIONES AJ, C.A.') {
                        $body.='<td>'.number_format($row['bprice'],2).' $</td>';
                    } else {
                        $body.='
                        <td>'.number_format($row['pprice'],2).' $</td>
                        <td>'.number_format($row['bprice'],2).' $</td>';
                    }
                $body.='                                    
                    <td><img src="../../public/img/gallery/'.$row['ImagenC'].'" style="width: 100px;"<></td>
                </tr>';
            }
            $body.='
            </tbody>
        </table>';


        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'orientation' => 'P',
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch',
        ]);

        $mpdf->SetHeader('<img src="../../public/build/images/'.$logo.'" style="width: 100px;">|<h1>'.$name.'</h1>Pag.: {PAGENO}, Consulta del: {DATE j-m-Y}');
        $mpdf->SetFooter('&copy; Copyright <strong><span>Catalogo Digital</span></strong>. All Rights Reserved <strong>, Diseño por </strong><b> Innovación Tecnológica <strong><span>(INTEC)</span></strong>, C.A. </b>|Precio: '.$price.',  Numero de Pagina: {PAGENO}| Fecha de Consulta: {DATE j-m-Y}');
        $mpdf->defaultheaderfontsize=10;
        $mpdf->defaultheaderfontstyle='B';
        $mpdf->defaultheaderline=0;
        $mpdf->defaultfooterfontsize=10;
        $mpdf->defaultfooterfontstyle='BI';
        $mpdf->defaultfooterline=0;

        $mpdf->SetWatermarkImage('../../public/build/images/'.$logo.'');
        $mpdf->showWatermarkImage = true;
        $mpdf->watermarkImageAlpha = 0.1;
        /*
        $mpdf->SetWatermarkText($name);
        $mpdf->showWatermarkText = true;
        $mpdf->watermarkTextAlpha = 0.1;
        */
        $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($body,\Mpdf\HTMLParserMode::HTML_BODY);
        $mpdf->Output('Lista '.$name.' Precio '.$price.'.pdf',\Mpdf\Output\Destination::INLINE);
        break;

    case 'save':
        $idCommodity      = (isset($_POST['idCommodity'])) ? $_POST['idCommodity'] : '';
        $descripCommodity = (isset($_POST['descripCommodity'])) ? $_POST['descripCommodity'] : '';
    
        $destino = "../../public/img/gallery/"; 
        //Parámetros optimización, resolución máxima permitida
        $max_ancho = 300;
        $max_alto  = 300;
    
        $nombre_img = $_FILES['image']['name'];
                
        $data = $download->updateDataGeneral($idCommodity,$nombre_img);
        


        if($data){
            
            $medidasimagen= getimagesize($_FILES['image']['tmp_name']);

            if($medidasimagen[0] < 500 && $_FILES['image']['size'] < 10000){
                
                $nombre_img = $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], $destino.'/'.$nombre_img);
            }else {
                $nombre_img = $_FILES['image']['name'];

                //Redimensionar
                $rtOriginal=$_FILES['image']['tmp_name'];

                if($_FILES['image']['type']=='image/jpeg'){
                    $original = imagecreatefromjpeg($rtOriginal);
                }else if($_FILES['image']['type']=='image/png'){
                    $original = imagecreatefrompng($rtOriginal);
                }else if($_FILES['image']['type']=='image/gif'){
                    $original = imagecreatefromgif($rtOriginal);
                }

                list($ancho,$alto)=getimagesize($rtOriginal);

                $x_ratio = $max_ancho / $ancho;
                $y_ratio = $max_alto / $alto;

                if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){
                    $ancho_final = $ancho;
                    $alto_final = $alto;
                }
                elseif (($x_ratio * $alto) < $max_alto){
                    $alto_final = ceil($x_ratio * $alto);
                    $ancho_final = $max_ancho;
                }
                else{
                    $ancho_final = ceil($y_ratio * $ancho);
                    $alto_final = $max_alto;
                }

                $lienzo=imagecreatetruecolor($ancho_final,$alto_final); 

                imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
                
                //imagedestroy($original);
                
                $cal=8;

                if($_FILES['image']['type']=='image/jpeg'){
                    imagejpeg($lienzo,$destino."/".$nombre_img);
                }else if($_FILES['image']['type']=='image/png'){
                    imagepng($lienzo,$destino."/".$nombre_img);
                }
                else if($_FILES['image']['type']=='image/gif'){
                imagegif($lienzo,$destino."/".$nombre_img);
                }
            }

            $output = [
                "mensaje" => "Guardado con Exito!",
                "icono"   => "success"
            ];
        } else {
            $output = [
                "mensaje" => "Ocurrió un error al Guardar!",
                "icono"   => "error"
            ];
        }

        echo json_encode($output);
        break;


        

}