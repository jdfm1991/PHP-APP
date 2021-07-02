<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("librocompras_modelo.php");

//INSTANCIAMOS EL MODELO
$librocompra = new LibroCompra();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_librocompras":

        $fechai = Dates::normalize_date($_POST['fechai']).' 00:00:00';
        $fechaf = Dates::normalize_date($_POST['fechaf']).' 23:59:59';

        $datos = $librocompra->getLibroPorFecha($fechai, $fechaf);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        if (is_array($datos)==true and count($datos)>0)
        {
            $tcci = $mtoex = $totcom = $mtoiva = $retiva = 0;

            foreach ($datos as $i => $row) {

                $tcci += $row['totalcompraconiva'];
                $mtoex += $row['mtoexento'];
                $totcom += $row['totalcompra'];
                $mtoiva += $row['monto_iva'];
                $retiva += $row['retencioniva'];

                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array[] = date(FORMAT_DATE, $row["fechacompra"]);
                $sub_array[] = $row["id3ex"];
                $sub_array[] = utf8_encode($row["descripex"]);
                $sub_array[] = $row["tipodoc"];
                $sub_array[] = $row["nroretencion"];
                $sub_array[] = $row["numerodoc"];
                $sub_array[] = $row["nroctrol"];
                $sub_array[] = $row["tiporeg"];
                $sub_array[] = $row["docafectado"];
                $sub_array[] = Strings::rdecimal($row["totalcompraconiva"], 2);
                $sub_array[] = Strings::rdecimal($row["mtoexento"], 2);
                $sub_array[] = Strings::rdecimal($row["totalcompra"], 2);
                $sub_array[] = Strings::rdecimal($row["alicuota_iva"], 0);
                $sub_array[] = Strings::rdecimal($row["monto_iva"], 2);
                $sub_array[] = Strings::rdecimal($row["retencioniva"], 2);
                $sub_array[] = Strings::rdecimal($row["porctreten"], 0);

                $data[] = $sub_array;
            }

            //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
            $results = array(
                "sEcho" => 1, //INFORMACION PARA EL DATATABLE
                "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
                "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
                "aaData" => $data);
        }

        echo json_encode($results);
        break;
}
