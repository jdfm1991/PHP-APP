<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("inventarioglobal_modelo.php");
require_once("../costodeinventario/costodeinventario_modelo.php");

//INSTANCIAMOS EL MODELO
$invglobal = new InventarioGlobal();
$costo = new CostodeInventario();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_inventarioglobal":

        //verificamos si existe al menos 1 deposito selecionado
        //y se crea el array.
        if(isset($_POST['depo'])){
            $numero = $_POST['depo'];
        } else {
            $numero = array();
        }

        //se contruye un string para listar los depositvos seleccionados
        //en caso que no haya ninguno, sera vacio
        $edv = "";
        if (count($numero) > 0) {
            /*foreach ($numero as $i) {
                $edv .= "?,";
            }*/
            foreach ($numero as $i)
                $edv .= " OR CodUbic = ?";
        }

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $diad = "01"; //dia
        $fechai = $aniod . "-01-01";
        $t = 0;

        $devolucionesDeFactura = $invglobal->getDevolucionesDeFactura($edv, $fechai, $fechaf, $numero);
        foreach ($devolucionesDeFactura as $devol) {
            $coditem[] = $devol['coditem'];
            $cantidad[] = $devol['cantidad'];
            $tipo[] = $devol['esunid'];
            $t += 1;
        }

        $relacion_inventarioglobal = $invglobal->getInventarioGlobal($edv, $fechai, $fechaf, $numero);
        $tbulto = $tpaq = $tbultoinv = $tpaqinv = $tbultsaint = $tpaqsaint = 0;
        $cant_paq = 0;
        $cant_bul = 0;
        $i=0;
        //DECLARAMOS ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        $totales = Array();

        foreach ($relacion_inventarioglobal as $key=>$row) {

            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            if($t > 0) {
                for($e = 0; $e < $t; $e++)
                {
                    if($coditem[$e] == $row['CodProd']) {
                        switch ($tipo[$e]) {
                            case '0':
                                $cant_bul = $row['bultosxdesp'] - $cantidad[$e];
                                break;
                            case '1':
                                $cant_paq = $row['paqxdesp'] - $cantidad[$e];
                                break;
                        }
//                        $e = $t + 2;
                        break;
                    }else{
                        $cant_bul = $row['bultosxdesp'];
                        $cant_paq = $row['paqxdesp'];
                    }
                }
            } else {
                $cant_bul = $row['bultosxdesp'];
                $cant_paq = $row['paqxdesp'];
            }
            ////conversión de bultos a paquetes
            $cantemp = $row['CantEmpaq'];
            $invbut  = $row['exis'];
            $invpaq  = $row['exunid'];

            $i++;
            if($cant_paq >= $cantemp){
                $conv = floor($cant_paq / $cantemp);
                $cant_paq -= ($conv * $cantemp);
                $cant_bul += $conv;
            }
            if($invpaq >= $cantemp){
                $conv = floor($invpaq / $cantemp);
                $invpaq -= ($conv * $cantemp);
                $invbut += $conv;
            }
            $tinvbult = $invbut + $cant_bul;
            $tinvpaq = $invpaq + $cant_paq;

            if($tinvpaq >= $cantemp){
                $conv1 = floor($tinvpaq / $cantemp);
                $tinvpaq -= ($conv1 * $cantemp);
                $tinvbult += $conv1;
            }

            //ASIGNAMOS EN EL SUB_ARRAY LOS DATOS PROCESADOS
            $sub_array[] = $key;
            $sub_array[] = $row["CodProd"];
            $sub_array[] = $row["Descrip"];
            $sub_array[] = number_format($cant_bul,0);
            $sub_array[] = number_format($cant_paq,0);
            $sub_array[] = number_format($invbut,0);
            $sub_array[] = number_format($invpaq,0);
            $sub_array[] = number_format($tinvbult,0);
            $sub_array[] = number_format($tinvpaq, 0);

            //ACUMULAMOS LOS TOTALES
            $tbulto     += $cant_bul;
            $tpaq       += $cant_paq;
            $tbultoinv  += $tinvbult;
            $tpaqinv    += $tinvpaq;
            $tbultsaint += $invbut;
            $tpaqsaint  += $invpaq;

            //AGREGAMOS AL ARRAY DE CONTENIDO DE LA TABLA
            $data[] = $sub_array;
        }

        //CREAMOS UN SUB_ARRAY PARA ALMACENAR LOS DATOS ACUMULADOS
        $sub_array1 = array();
        $sub_array1['tbulto']     = number_format($tbulto,0,',','.');
        $sub_array1['tpaq']       = number_format($tpaq,0,',','.');
        $sub_array1['tbultsaint'] = number_format($tbultsaint,0,',','.');
        $sub_array1['tpaqsaint']  = number_format($tpaqsaint,0,',','.');
        $sub_array1['tbultoinv']  = number_format($tbultoinv,0,',','.');
        $sub_array1['tpaqinv']    = number_format($tpaqinv,0,',','.');
        $sub_array1['facturas_sin_despachar'] = count($devolucionesDeFactura);


        //al terminar, se almacena en una variable de salida el array.
        $output['contenido_tabla'] = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);

        //de igual forma, se almacena en una variable de salida el array de totales.
        $output['totales_tabla'] = $sub_array1;

        echo json_encode($output);
        break;
}