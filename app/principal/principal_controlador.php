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
        $ventas_fact_anterior = $principal->get_ventas_por_mes_fact($fechai_ant, $fechaf_ant);
        $ventas_nt_anterior = $principal->get_ventas_por_mes_nota($fechai_ant, $fechaf_ant);
        //datos del año en curso
        $ventas_fact = $principal->get_ventas_por_mes_fact($fechai, $fechaf);
        $ventas_nt = $principal->get_ventas_por_mes_nota($fechai, $fechaf);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $output = Array();
        if ((is_array($ventas_fact)==true and count($ventas_fact)>0) and
            (is_array($ventas_nt)==true and count($ventas_nt)>0))
        {
            $valor_mas_alto = 0;
            $cant_meses = $dato[1];
            $output['anio'] = $aniod;

            //primero obtenemos todas los meses y las inicializamos en cero en una variable temporal
            $temp = array();
            foreach (array($ventas_fact, $ventas_nt) as $datos) {
                foreach ($datos as $row)
                    $temp[$row['mes']] = 0;
            }

            $ventas_actual = $temp;
            $ventas_anterior = $temp;

            //ahora acumulamos los total ventas de año actual en sus meses
            foreach (array($ventas_fact, $ventas_nt) as $datos) {
                foreach ($datos as $row) {
                    if (array_key_exists($row['mes'], $ventas_actual)) {
                        $ventas_actual[$row['mes']] += $row['total'];
                    }
                }

            }


            //asi mismo, acumulamos los total ventas del año anterior en sus meses
            foreach (array($ventas_fact_anterior, $ventas_nt_anterior) as $datos) {
                foreach ($datos as $row) {
                    if (array_key_exists($row['mes'], $ventas_anterior)) {
                        $ventas_anterior[$row['mes']] += $row['total'];
                    }
                }
            }


            $data=array();
            foreach ($ventas_actual as $mes => $total) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array["num_mes"] = intval($mes);
                $sub_array["mes"] = Dates::month_name(Strings::addCero($mes), true);
                $sub_array["valor"] = number_format($total, 2, ".", "");

                //aqui obtenemos el valor mas alto
                if($valor_mas_alto<floatval($total)) {
                    $valor_mas_alto = floatval($total);
                }

                $data['ventas_ano_actual'][] = $sub_array;
            }

            $data1=array();
            foreach ($ventas_anterior as $mes => $total) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array["num_mes"] = intval($mes);
                $sub_array["mes"] = Dates::month_name(Strings::addCero($mes), true);
                $sub_array["valor"] = number_format($total, 2, ".", "");

                //aqui obtenemos el valor mas alto
                if($valor_mas_alto<floatval($total)) {
                    $valor_mas_alto = floatval($total);
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

    case "buscar_clientes":

        #tipo 1 = naturales
        #tipo 0 = juridicos
        $output["cant_naturales"] = Strings::rdecimal(count($principal->get_clientes_por_tipo(1)),0);
        $output["cant_juridico"]  = Strings::rdecimal(count($principal->get_clientes_por_tipo(0)),0);
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

    case 'listar_ventas_por_marca':

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $fechai = $aniod . "-" .$mesd. "-01";

        //datos del año en curso
        $datos_fact = $principal->get_ventas_por_marca_fact($fechai, $fechaf);
        $datos_nota = $principal->get_ventas_por_marca_nota($fechai, $fechaf);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $output = Array();
        if (is_array($datos_fact)==true and count($datos_fact)>0
            and
            is_array($datos_nota)==true and count($datos_nota)>0)
        {
            //primero obtenemos todas las marcas y las inicializamos en cero
            $marcas = array();
            foreach (array($datos_fact, $datos_nota) as $datos) {
                foreach ($datos as $row)
                    $marcas[$row['marca']] = 0;

            }

            //ahora acumulamos los montod en sus marcas con el objetivo de hacer top 10 marcas mas vendidas
            foreach (array($datos_fact, $datos_nota) as $datos) {
                foreach ($datos as $row)
                    $marcas[$row['marca']] += $row['montod'];
            }
            array_multisort($marcas, SORT_DESC); //ordena descendientemente por el monto
            $marcas = array_slice($marcas, 0, 10); //trunca el array con los primero 10

            $output['fecha'] = Dates::month_name($mesd)." ".$aniod;
            $output['marcas'] = $marcas;
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        echo json_encode($output);
        break;

    case 'listar_ventas_por_clientes':

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $fechai = $aniod . "-" .$mesd. "-01";

        //datos del año en curso
        $datos_fact = $principal->get_ventas_clientes_fact($fechai, $fechaf);
        $datos_nota = $principal->get_ventas_cliente_nota($fechai, $fechaf);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $output = Array();
        if (is_array($datos_fact)==true and count($datos_fact)>0
            and
            is_array($datos_nota)==true and count($datos_nota)>0)
        {
            //primero obtenemos todas las marcas y las inicializamos en cero
            $clientes = $temp = array();
            foreach (array($datos_fact, $datos_nota) as $datos) {
                foreach ($datos as $row)
                    $temp[$row['codclie']] = 0;
            }

            //ahora acumulamos los montod en sus marcas con el objetivo de hacer top 10 marcas mas vendidas
            foreach (array($datos_fact, $datos_nota) as $datos) {
                foreach ($datos as $row)
                    $temp[$row['codclie']] += $row['montod'];
            }
            array_multisort($temp, SORT_DESC); //ordena descendientemente por el monto
            $temp = array_slice($temp, 0, 10); //trunca el array con los primero 10

            # hasta este punto el array $temp posee un array asociativo array(codclie => montod), como nos falta obtener la descripcion
            # se reprocesara la data para obtenerlo en el orden del top 10

            # recorremos todo el top10
            foreach ($temp as $codclie => $montod) {
                #creamos una variable de bandera(flag) que funcionara para frenar
                #la iteracion de los 2 foreach a continuacion cuando coincida los codclie
                $flag_break = false;
                foreach (array($datos_fact, $datos_nota) as $datos) {
                    foreach ($datos as $row) {
                        # condicion para optener el registro
                        if ($row['codclie'] == $codclie) {
                            #en caso que lo encuentre se situa en true
                            $flag_break = true;

                            # vamos creando el nuevo array con la data de $temp
                            # conservando el orden del top10
                            $clientes[$codclie] = array(
                                'descrip' => $row['Descrip'],
                                'montod'  => $montod,
                            );
                        }
                        if ($flag_break) break;
                    }
                    if ($flag_break) break;
                }
            }


            $output['fecha'] = Dates::month_name($mesd)." ".$aniod;
            $output['clientes'] = $clientes;
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        echo json_encode($output);
        break;

    case "buscar_total_ventas_mes_encurso":

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $fechai = $aniod . "-" .$mesd. "-01";

        $output['fecha'] = ucwords(strtolower(Dates::month_name($mesd)));
        $output["total"] = Strings::rdecimal($principal->get_total_ventas($fechai, $fechaf)[0]['montod'],2);
        echo json_encode($output);
        break;
}
