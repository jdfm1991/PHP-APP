<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("principal_modelo.php");

//INSTANCIAMOS EL MODELO
$principal = new Principal();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_documentos_pordespachar":

        $output["por_despachar"] = Strings::rdecimal(count($principal->getDocumentosSinDespachar()),0);
        echo json_encode($output);
        break;

    case "buscar_pedidos_porfacturar":

        $output["por_facturar"] = Strings::rdecimal(count($principal->getPedidosSinFacturar()),0);
        echo json_encode($output);
        break;

    case "buscar_cxc":

        $output = array(
            "cxc_bs" => Strings::rdecimal($principal->get_cxc_bs()['saldo_bs'],1),
            "cxc_$"  => Strings::rdecimal($principal->get_cxc_dolares()['saldo_dolares'],1),
        );

        echo json_encode($output);
        break;

    case "buscar_cxp":

        $output = array(
            "cxp_bs" => Strings::rdecimal(/*$principal->get_cxp_bs()['saldo_bs']*/0,1),
            "cxp_$"  => Strings::rdecimal(/*$principal->get_cxp_dolares()['saldo_dolares']*/0,1),
        );

        echo json_encode($output);
        break;

    case 'buscar_ventasPormesdivisas':

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $diad = "01"; //dia
        $fechai = $aniod . "-01-01";

        $ventas_nt = $principal->get_ventas_por_mes_nota($fechai, $fechaf);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $output = Array();
        if (is_array($ventas_nt)==true and count($ventas_nt)>0)
        {
            $valor_mas_alto = 0;
            $output['anio'] = $ventas_nt[0]['anio'];

            $data=array();
            foreach ($ventas_nt as $row) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array["mes"] = Dates::month_name(Strings::addCero($row["mes"]), true);
                $sub_array["valor"] = number_format($row["total"], 2, ".", "");

                //aqui obtenemos el valor mas alto
                if($valor_mas_alto<floatval($row['total'])) {
                    $valor_mas_alto = floatval($row['total']);
                }

                $data[] = $sub_array;
            }
            $output['valor_mas_alto'] = number_format($valor_mas_alto, 2, ".", "");
            $output['datos'] = $data;
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        echo json_encode($output);
        break;

}
