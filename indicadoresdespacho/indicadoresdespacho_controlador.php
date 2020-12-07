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

    case "listar_entregas_efectivas":
        $fechai = $_POST['fechai'];
        $fechaf = $_POST['fechaf'];
        $chofer_id = $_POST['chofer'];

        $datos = $indicadores->get_entregasefectivas_por_chofer($fechai, $fechaf, $chofer_id);

        //inicializamos la variables
        $chofer = $choferes->get_chofer_por_id($chofer_id);
        $chofer = (count($chofer) > 0) ? $chofer[0]['cedula'].' - '.$chofer[0]['descripcion'] : "";
        $ordenes_despacho_string = "";
        $fact_sinliquidar_string = "";
        $totaldespacho = 0;
        $total_ped_entregados = 0;
        $total_ped_porliquidar = 0;
        $promedio_diario_despacho = 0;

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        if(is_array($datos) and count($datos) > 0)
        {
            //almacenamos el total de despachos para calcular la efectividad posteriormente
            foreach ($datos as $row)
                $totaldespacho += intval($row['cant_documentos']);

            foreach ($datos as $key => $row)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $ordenes_despacho_string .= ($row['correlativo'] . "(" . $row['cant_documentos'] . "), ");

                $porcentaje = number_format(($row['cant_documentos'] / $totaldespacho) * 100, 1);

                /** entregas efectivas **/
                if($row['tipo_pago'] !='N/C' and $row['fecha_entre'] != null and $key>0 )
                {
                    //consultamos si la de la iteracion actual tiene fecha igual a la insertada en la interacion anterior
                    if(count($data)>0 and date_format(date_create($row['fecha_entre']), 'd-m-Y') == $data[count($data)-1]['fecha_entrega'])
                    {
                        $data[count($data)-1]['cant_documentos'] += intval($row['cant_documentos']);
                        $data[count($data)-1]['porc'] += floatval($porcentaje);
                        $data[count($data)-1]['ordenes_despacho'] .= (", " . $row['correlativo']);
                    }
                    //si no es igual, solo inserta un nuevo registro al array
                    else {
                        $sub_array['fecha_entrega'] = date_format(date_create($row['fecha_entre']), 'd-m-Y');
                        $sub_array['cant_documentos'] = intval($row['cant_documentos']);
                        $sub_array['porc'] = floatval($porcentaje);
                        $sub_array['ordenes_despacho'] = $row['correlativo'];

                        $data[] = $sub_array;
                    }
                }

                /** facturas sin liquidar **/
                if(strlen($row['fact_sin_liquidar'])>0)
                {
                    $fact_sinliquidar_string .= (",".$row['fact_sin_liquidar']);
                    $array = explode(",", $fact_sinliquidar_string);
                    $array = array_unique($array);
                    sort($array, SORT_ASC);
                    $fact_sinliquidar_string = implode($array,",");
                }
            }
        }
        /** calcular los pedidos entregados **/
        foreach ($data as $arr){
            $total_ped_entregados += intval($arr['cant_documentos']);
        }

        /** calcular los pedidos sin liquidar **/
        $total_ped_porliquidar = $totaldespacho - $total_ped_entregados;

        /** calcular el promedio diario de despachos **/
        $promedio_diario_despacho = (count($data) > 0) ? $total_ped_entregados / count($data) : 0;

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "chofer" => $chofer,
            "ordenes_despacho" => $ordenes_despacho_string,
            "fact_sinliquidar" => str_ireplace(",",", ",$fact_sinliquidar_string),
            "totaldespacho" => $totaldespacho,
            "total_ped" => $total_ped_entregados,
            "total_ped_porliquidar" => $total_ped_porliquidar,
            "promedio_diario_despacho" => number_format($promedio_diario_despacho,2, ",", "."),
            "tabla" => $data
        );
        echo json_encode($output);

        break;

    case "listar_causas_rechazo":
        $fechai = $_POST['fechai'];
        $fechaf = $_POST['fechaf'];
        $chofer_id = $_POST['chofer'];
        $causa = $_POST['causa'];

        $datos = $indicadores->get_causasrechazo_por_chofer($fechai, $fechaf, $chofer_id, $causa);

        //inicializamos la variables
        if($chofer_id!="-") {
            $chofer = $choferes->get_chofer_por_id($chofer_id);
            $chofer = (count($chofer) > 0) ? $chofer[0]['cedula'].' - '.$chofer[0]['descripcion'] : "";
        } else {
            $chofer = "Todos los Choferes";
        }
        $ordenes_despacho = Array();
        $ordenes_despacho_string = "";
        $totaldespacho = 0;
        $total_ped_devueltos = 0;

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        if(is_array($datos) and count($datos) > 0)
        {
            //almacenamos el total de despachos
            foreach ($datos as $row)
                $totaldespacho += intval($row['cant_documentos']);

            foreach ($datos as $key => $row)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                /** obtener los despachos realizados para imprimirlos en un string ordenado al final **/
                if(count($ordenes_despacho)>0 and $row['correlativo'] == $ordenes_despacho[count($ordenes_despacho)-1]["correlativo"]) {
                    $ordenes_despacho[count($ordenes_despacho)-1]['cant_documentos'] += intval($row['cant_documentos']);
                } else {
                    $ordenes_despacho[] = array(
                        "correlativo"     => $row['correlativo'],
                        "cant_documentos" => $row['cant_documentos']
                    );
                }

                $porcentaje = number_format(($row['cant_documentos'] / $totaldespacho) * 100, 1);

                /** causas de rechazo **/
                if($row['tipo_pago'] =='N/C' and $key>0 )
                {
                    //consultamos si la de la iteracion actual tiene fecha igual a la insertada en la interacion anterior
                    if(count($data)>0 and  $row['fecha_entre'] != null
                        and date_format(date_create($row['fecha_entre']), 'd-m-Y') == $data[count($data)-1]['fecha_entrega']
                    ) {
                        $data[count($data)-1]['cant_documentos'] += intval($row['cant_documentos']);
                        $data[count($data)-1]['porc'] += floatval($porcentaje);
                        $data[count($data)-1]['ordenes_despacho'] .= (", " . $row['correlativo']);
                    }
                    //si no es igual, solo inserta un nuevo registro al array
                    else {
                        $fecha_entrega = ($row['fecha_entre'] != null and strlen($row['fecha_entre'])>0)
                            ? date_format(date_create($row['fecha_entre']), 'd-m-Y') : "sin fecha de entrega";

                        $sub_array['fecha_entrega'] = $fecha_entrega;
                        $sub_array['cant_documentos'] = intval($row['cant_documentos']);
                        $sub_array['porc'] = floatval($porcentaje);
                        $sub_array['ordenes_despacho'] = $row['correlativo'];
                        $sub_array['observacion'] = $row['observacion'];

                        $data[] = $sub_array;
                    }
                }
            }
        }
        /** los despachos realizados obtenidos se agregan a un string **/
        foreach ($ordenes_despacho as $arr){
            $ordenes_despacho_string .= ($arr['correlativo'] . "(" . $arr['cant_documentos'] . "), ");
        }

        /** calcular los pedidos devueltos **/
        foreach ($data as $arr){
            $total_ped_devueltos += intval($arr['cant_documentos']);
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "chofer" => $chofer,
            "ordenes_despacho" => $ordenes_despacho_string,
            "totaldespacho" => $totaldespacho,
            "total_ped" => $total_ped_devueltos,
            "tabla" => $data
        );
        echo json_encode($output);

        break;
}
