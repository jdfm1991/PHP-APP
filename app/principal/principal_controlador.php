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
        $fechai = $aniod . "-01-01";

        $fechaf_ant = ($dato[0]-1).'-'.$dato[1].'-'.$dato[2];
        $fechai_ant = ($dato[0]-1)."-01-01";

        //datos del año anterior
        $ventas_nt_anterior = $principal->get_ventas_por_mes_nota($fechai_ant, $fechaf_ant);
        //datos del año en curso
        $ventas_nt = $principal->get_ventas_por_mes_nota($fechai, $fechaf);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $output = Array();
        if (is_array($ventas_nt)==true and count($ventas_nt)>0)
        {
            $valor_mas_alto = 0;
            $cant_meses = $dato[1];
            $output['anio'] = $ventas_nt[0]['anio'];

            $data=array();
            foreach ($ventas_nt as $row) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array["num_mes"] = intval($row["mes"]);
                $sub_array["mes"] = Dates::month_name(Strings::addCero($row["mes"]), true);
                $sub_array["valor"] = number_format($row["total"], 2, ".", "");

                //aqui obtenemos el valor mas alto
                if($valor_mas_alto<floatval($row['total'])) {
                    $valor_mas_alto = floatval($row['total']);
                }

                $data['ventas_ano_actual'][] = $sub_array;
            }

            $data1=array();
            foreach ($ventas_nt_anterior as $row) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array["num_mes"] = intval($row["mes"]);
                $sub_array["mes"] = Dates::month_name(Strings::addCero($row["mes"]), true);
                $sub_array["valor"] = number_format($row["total"], 2, ".", "");

                //aqui obtenemos el valor mas alto
                if($valor_mas_alto<floatval($row['total'])) {
                    $valor_mas_alto = floatval($row['total']);
                }

                $data1['ventas_ano_anterior'][] = $sub_array;
            }

            $output['cantidad_meses_evaluar'] = intval($cant_meses);
            $output['valor_mas_alto'] = number_format($valor_mas_alto, 2, ".", "");
            $output['datos']=array(); array_push($output['datos'], $data, $data1);
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        echo json_encode($output);
        break;

    case "listar_inventario_valorizado":

        $depos = [
            '01',
//            '13',
        ];
//        $depos =  array_map(function($val) { return $val['codubi']; }, Almacen::todos());

        $datos = $principal->get_inventario_valorizado($depos);

        //DECLARAMOS ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $key => $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            //ASIGNAMOS EN EL SUB_ARRAY LOS DATOS PROCESADOS
            $sub_array['almacen']   = $row["almacen"];
            $sub_array['total']     = Strings::rdecimal(floatval($row["total_b"]) + floatval($row["total_p"]),2);
            $sub_array['acciones']  = '<a href="#" class="text-muted">
                                         <i class="fas fa-search"></i>
                                       </a>';

            //AGREGAMOS AL ARRAY DE CONTENIDO DE LA TABLA
            $data[] = $sub_array;
        }

        //al terminar, se almacena en una variable de salida el array.
        $output['contenido_tabla'] = $data;

        echo json_encode($output);
        break;

    case "buscar_clientes_naturales":

        $output["cant_naturales"] = Strings::rdecimal(count($principal->get_clientes_por_tipo(1)),0);
        echo json_encode($output);
        break;

    case "buscar_clientes_juridicos":

        $output["cant_juridico"] = Strings::rdecimal(count($principal->get_clientes_por_tipo(0)),0);
        echo json_encode($output);
        break;

    case "buscar_tasa_dolar":

        $output["tasa"] = Strings::rdecimal($principal->get_tasa_dolar()['tasa'],2);
        echo json_encode($output);
        break;

    case "buscar_devoluciones_sin_motivo":

        $data = array(
            'sin_despacho_fact' => Numbers::avoidNull(count($principal->get_devoluciones_sin_motivo_Factura('0'))),
            'con_despacho_fact' => Numbers::avoidNull(count($principal->get_devoluciones_sin_motivo_Factura('1'))),
            'sin_despacho_nota' => Numbers::avoidNull(count($principal->get_devoluciones_sin_motivo_NotadeEntrega('0'))),
            'con_despacho_nota' => Numbers::avoidNull(count($principal->get_devoluciones_sin_motivo_NotadeEntrega('1'))),
        );

        $sumatoria_devoluciones_fact = (intval($data['sin_despacho_fact']) + intval($data['con_despacho_fact']));
        $sumatoria_devoluciones_nota = (intval($data['sin_despacho_nota']) + intval($data['con_despacho_nota']));
        $total = ($sumatoria_devoluciones_fact + $sumatoria_devoluciones_nota);

        $output["devoluciones_sin_motivo"] = Strings::rdecimal($total,0);
        echo json_encode($output);
        break;

}
