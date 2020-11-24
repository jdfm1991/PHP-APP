<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("indicadoresdespacho_modelo.php");
require_once("../choferes/choferes_modelo.php");

//INSTANCIAMOS EL MODELO
$indicadores = new InidicadoresDespachos();
$choferes = new Choferes();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_choferes":

        $output["lista_choferes"] = $choferes->get_choferes();

        echo json_encode($output);

        break;

    /*case "listar_causas_rechazo":

        $output["lista_choferes"] = $choferes->get_choferes();

        echo json_encode($output);

        break;*/

    case "listar_entregas_efectivas":
        $fechai = $_POST['fechai'];
        $fechaf = $_POST['fechaf'];
        $chofer_id = $_POST['chofer'];

        $datos = $indicadores->get_entregasefectivas_por_chofer($fechai, $fechaf, $chofer_id);
        $num = count($datos);

        //inicializamos la variables
        $chofer = (!empty($datos[0]['chofer'])) ? $datos[0]['chofer'] : "";
        $ordenes_despacho_string = "";
        $fact_sinliquidar_string = "";
        $totaldespacho = 0;
        $total_ped_entregados = 0;
        $total_ped_porliquidar = 0;
        $promedio_diario_despacho = 0;

        /** AGREGAR TOTAL DE DESPACHOS **/

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        if(is_array($datos) and count($datos) > 0) {

            foreach ($datos as $key => $row) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $porcentaje = number_format(($row['cant_documentos'] / $totaldespacho) * 100, 1);

                //consultamos si la de la iteracion actual tiene fecha igual a la insertada en la interacion anterior
                if($key>0 and is_array($data) and $row['fecha_entre'] == $data[count($data)]['fecha_entrega'])
                {
                    $data[count($data)]['ped_despachados'] += intval($row['entreg']);
                    $data[count($data)]['ordenes_despacho'] += floatval($porcentaje);
                    $data[count($data)]['ordenes_despacho'] .= (", " . $row['correlativo']);
                }
                //si no es igual, solo inserta un nuevo registro al array
                else {
                    $sub_array['fecha_entrega'] = date_format(date_create($row['fecha_entre']), 'd-m-Y');
                    $sub_array['ped_despachados'] = intval($row['cant_documentos']);
                    $sub_array['porc_efectividad'] = floatval($porcentaje);
                    $sub_array['ordenes_despacho'] = $row['correlativo'];

                    $data[] = $sub_array;
                }

            }
        }
        /** calcular los pedidos entregados **/
        foreach ($data as $arr){
            $total_ped_entregados += intval($arr['ped_despachados']);
        }

        /** calcular los pedidos sin liquidar **/
        $total_ped_porliquidar = $totaldespacho - $total_ped_entregados;

        /** calcular el promedio diario de despachos **/
        $promedio_diario_despacho = (count($data) > 0) ? $total_ped_entregados / count($data) : 0;

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "chofer" => $chofer,
            "ordenes_despacho" => $ordenes_despacho_string,
            "fact_sinliquidar" => $fact_sinliquidar_string,
            "totaldespacho" => $totaldespacho,
            "total_ped_entregados" => $total_ped_entregados,
            "total_ped_porliquidar" => $total_ped_porliquidar,
            "promedio_diario_despacho" => number_format($promedio_diario_despacho,2, ",", "."),
            "tabla" => $data
        );
        echo json_encode($output);

        break;

    case "listar_entregas_efectivas_old":
        $fechai = $_POST['fechai'];
        $fechaf = $_POST['fechaf'];
        $chofer_id = $_POST['chofer'];

        $datos = $indicadores->get_correlativos_entregasefectivas_por_chofer($fechai, $fechaf, $chofer_id);
        $num = count($datos);

        $data = Array();
        //inicializamos la variables
        $chofer = (!empty($datos[0]['chofer'])) ? $datos[0]['chofer'] : "";
        $ordenes_despacho_string = "";
        $fact_sinliquidar_string = "";
        $totaldespacho = 0;
        $total_ped_entregados = 0;
        $total_ped_porliquidar = 0;
        $promedio_diario_despacho = 0;

        $x = 0; //varible de control, tamaño del array principal
        $w = 0; //variable de control, numero de iteracion
        if(is_array($datos) and count($datos) > 0)
        {
            //almacenamos el total de despachos para calcular la efectividad posteriormente
            foreach ($datos as $row)
                $totaldespacho += intval($row['totaldespacho']);

            //carga de la data
            foreach ($datos as $row) {

                $sub_array = array();// '16395823'

                $ordenes_despacho_string .= ($row['correlativo'] . "(" . $row['totaldespacho'] . "), ");

                /** agregar dias de entrega de esta orden de despacho **/
                $diasentrega = $indicadores->get_dias_entrega_por_orden_despacho($row['correlativo']);
                if (count($diasentrega) > 0)
                {
                    foreach ($diasentrega as $dias)
                    {
                        /*buscamos si en el array principal existe algun correlativo de despacho
                          con la misma fecha de entrega que el correlativo de la iteracion actual

                            considerando que el array principal $data:
                                $data[$i][fecha_entrega]    = fecha de entrega
                                $data[$i][ped_despachados]  = pedidos despachados
                                $data[$i][porc_efectividad] = porcentaje de efectividad
                                $data[$i][ordenes_despacho] = correlativo de despacho
                        */
                        for ($i = 0; $i <= $w; $i++) {

                            if ($i<count($data) and $data[$i]['fecha_entrega'] == $dias['fecha_entre']) //evalua la fecha si esta coincide
                            {
                                /*en caso que si coincida:
                                    *se suma a la cantidad existente, las "cantidad de pedidos" de este correlativo de pedido en iteracion actual
                                    *se concatena el correlativo de pedido a la ya existente separada por coma( , ), agrupandolas */
                                $data[$i]['ped_despachados'] += $dias['entreg'];
                                $data[$i]['ordenes_despacho'] .= (", " . $row['correlativo']);
                                $i = $w + 3; // se le agrega ($w+3) a $i para su siguiente iteracion se vuelva ($w+4) y se detenga el for.
                            }
                        }

                        /* luego evalua si la ultima iteracion del for es igual ($w + 4) con el fin de determinar si se agrupo el correlativo de pedido
                           con un correlativo anterior en el array principal, dado el caso que no, entra en esta condicion */
                        if ($i != ($w + 4)) {

                            $porcentaje = number_format(($dias['entreg'] / $totaldespacho) * 100, 1);

//                            $sub_array['num'] = $x+1;
                            $sub_array['fecha_entrega'] = date_format( date_create($dias['fecha_entre']), 'd-m-Y');
                            $sub_array['ped_despachados'] = $dias['entreg'];
                            $sub_array['porc_efectividad'] = $porcentaje. "%";
                            $sub_array['ordenes_despacho'] = $row['correlativo'];

                            $x++;
                        }
                        $w++;
                    }

                }

                /** facturas sin liquidar **/
                $sinliquidar = $indicadores->get_facturas_sin_liquidar_por_orden_despacho($row['correlativo']);
                if (count($sinliquidar) > 0) {
                    foreach ($sinliquidar as $item)
                        $fact_sinliquidar_string .= ($item['numerod'] . ", ");
                }

                if(count($sub_array)>0)
                    $data[] = $sub_array;
            }
        }
        /** calcular los pedidos entregados **/
        foreach ($data as $arr){
            $total_ped_entregados += intval($arr['ped_despachados']);
        }

        /** calcular los pedidos sin liquidar **/
        $total_ped_porliquidar = $totaldespacho - $total_ped_entregados;

        /** calcular el promedio diario de despachos **/
        $promedio_diario_despacho = ($x > 0) ? $total_ped_entregados / $x : 0;



        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "chofer" => $chofer,
            "ordenes_despacho" => $ordenes_despacho_string,
            "fact_sinliquidar" => $fact_sinliquidar_string,
            "totaldespacho" => $totaldespacho,
            "total_ped_entregados" => $total_ped_entregados,
            "total_ped_porliquidar" => $total_ped_porliquidar,
            "promedio_diario_despacho" => number_format($promedio_diario_despacho,2, ",", "."),
            "tabla" => $data
        );
        echo json_encode($output);

        break;
}
