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
        $chofer = $_POST['chofer'];

        $datos = $indicadores->get_correlativos_entregasefectivas_por_chofer($fechai, $fechaf, $chofer);
        $num = count($datos);

        $data = Array();
        $cabecera = array();
        $cabecera['chofer'] = $datos[0]['chofer'];
        $cabecera['ordenes_despacho'] = "";
        $cabecera['fact_sinliquidar'] = "";
        $cabecera['totaldespacho'] = 0;
        $cabecera['promedio'] = 0;

        $x = 0; //varible de control, tamaño del array principal
        $w = 0; //variable de control, numero de iteracion
        if(is_array($datos) and count($datos) > 0)
        {
            //almacenamos el total de despachos para calcular la efectividad posteriormente
            foreach ($datos as $row)
                $cabecera['totaldespacho'] += intval($row['totaldespacho']);

            //carga de la data
            foreach ($datos as $row) {

                $sub_array = array();

                $cabecera['ordenes_despacho'] .= ($row['correlativo'] . "(" . $row['totaldespacho'] . "),");

                /** agregar dias de entrega de esta orden de despacho **/
                $diasentrega = $indicadores->get_dias_entrega_por_orden_despacho($row['correlativo']);
                if (count($diasentrega) > 0)
                {
                    foreach ($diasentrega as $dias)
                    {
                        /*buscamos si en el array principal existe algun correlativo de despacho
                          con la misma fecha de entrega que el correlativo de la iteracion actual

                            considerando que el array principal $data:
                                $data[$i][0] = fecha de entrega
                                $data[$i][1] = pedidos despachados
                                $data[$i][2] = porcentaje de efectividad
                                $data[$i][3] = correlativo de despacho
                        */
                        for ($i = 0; $i <= $w; $i++) {

                            if ($i<count($data) and $data[$i][0] == $dias['fecha_entre']) //evalua la fecha si esta coincide
                            {
                                /*en caso que si coincida:
                                    *se suma a la cantidad existente, las "cantidad de pedidos" de este correlativo de pedido en iteracion actual
                                    *se concatena el correlativo de pedido a la ya existente separada por coma( , ), agrupandolas */
                                $data[$i][1] += $dias['entreg'];
                                $data[$i][3] .= (", " . $row['correlativo']);
                                $i = $w + 3; // se le agrega ($w+3) a $i para su siguiente iteracion se vuelva ($w+4) y se detenga el for.
                            }
                        }

                        /* luego evalua si la ultima iteracion del for es igual ($w + 4) con el fin de determinar si se agrupo el correlativo de pedido
                           con un correlativo anterior en el array principal, dado el caso que no, entra en esta condicion */
                        if ($i != ($w + 4)) {

                            $porcentaje = number_format(($dias['entreg'] / $cabecera['totaldespacho']) * 100, 1);

                            $sub_array[] = $x+1;
                            $sub_array[] = date_format( date_create($dias['fecha_entre']), 'd-m');
                            $sub_array[] = $dias['entreg'];
                            $sub_array[] = $porcentaje. "%";
                            $sub_array[] = $row['correlativo'];

                            $cabecera['promedio'] += floatval($porcentaje);
                            $x++;
                        }
                        $w++;
                    }
                }

                /** facturas sin liquidar **/
                $sinliquidar = $indicadores->get_facturas_sin_liquidar_por_orden_despacho($row['correlativo']);
                if (count($sinliquidar) > 0) {
                    foreach ($sinliquidar as $item)
                        $cabecera['fact_sinliquidar'] .= ($item['numerod'] . ",");
                }

                if(count($sub_array)>0)
                    $data[] = $sub_array;
            }
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
//            "cabecera" => $cabecera,
            "aaData" => $data);
        echo json_encode($output);

        break;
}
