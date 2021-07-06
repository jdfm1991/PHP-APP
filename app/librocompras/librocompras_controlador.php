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
        $data = $totales = Array();

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

                $sub_array['fechacompra']  = date(FORMAT_DATE, strtotime($row["fechacompra"]));
                $sub_array['id3ex']        = $row["id3ex"];
                $sub_array['descripex']    = utf8_encode($row["descripex"]);
                $sub_array['tipodoc']      = $row["tipodoc"];
                $sub_array['nroretencion'] = Strings::avoidNull($row["nroretencion"]);
                $sub_array['numerodoc']    = $row["numerodoc"];
                $sub_array['nroctrol']     = Strings::avoidNull($row["nroctrol"]);
                $sub_array['tiporeg']      = $row["tiporeg"];
                $sub_array['docafectado']  = Strings::avoidNull($row["docafectado"]);
                $sub_array['totalcompraconiva'] = Strings::rdecimal($row["totalcompraconiva"], 2);
                $sub_array['mtoexento']    = Strings::rdecimal($row["mtoexento"], 2);
                $sub_array['totalcompra']  = Strings::rdecimal($row["totalcompra"], 2);
                $sub_array['alicuota_iva'] = Strings::rdecimal($row["alicuota_iva"], 0);
                $sub_array['monto_iva']    = Strings::rdecimal($row["monto_iva"], 2);
                $sub_array['retencioniva'] = Strings::rdecimal($row["retencioniva"], 2);
                $sub_array['porctreten']   = Strings::rdecimal($row["porctreten"], 0);

                $data[] = $sub_array;
            }

            $totales = array(
                "tcci" => $tcci,
                "mtoex" => $mtoex,
                "totcom" => $totcom,
                "mtoiva" => $mtoiva,
                "retiva" => $retiva
            );

        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
            "data" => $data,
            "totales" => $totales
        );

        echo json_encode($results);
        break;
}
