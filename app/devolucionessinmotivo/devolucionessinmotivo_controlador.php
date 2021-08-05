<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("devolucionessinmotivo_modelo.php");

//INSTANCIAMOS EL MODELO
$sinmotivo = new Devolucionessinmotivo();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_devolucionessinmotivo":

        $data = array(
            'fechai'        => $_POST["fechai"],
            'fechaf'        => $_POST["fechaf"],
            'tipodespacho'  => $_POST["tipodespacho"],
            'tipodoc'       => $_POST["tipodoc"],
        );

        $datos = $sinmotivo->getDevoluciones($data);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        $suma_monto = $suma_peso = $tipo = 0;

        foreach ($datos as $key => $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            $op=0;

            $columna_3 = ($row['numeror'] != null)
                ? $row['numeror']
                : '';

            $sub_array[] = $row["code_vendedor"];
            $sub_array[] = $row["numerod"];
            $sub_array[] = $columna_3;
            $sub_array[] = $row["tipofac"];
            $sub_array[] = date(FORMAT_DATE, strtotime($row['fecha_fact']));
            $sub_array[] = $row["cod_clie"];
            $sub_array[] = $row["cliente"];
            $sub_array[] = '<div align="text-center">
								<div id="causa'.$key.'_div" class="input-group">
									<select id="causa'.$key.'" name="causa'.$key.'" class="form-control custom-select" onchange="guardarCausaSeleccionada(\''. $row["id"] .'\',\''. $key .'\')">
										'.Functions::selectListCausasRechazos().'
									</select>
								</div>
							</div>';
            $sub_array[] = Strings::rdecimal($row["Monto"],2);

            $suma_monto += $row['Monto'];

            $data[] = $sub_array;
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data
        );

        echo json_encode($results);
        break;

    case "listar_tipodespacho":

        $arr = array();

        array_push($arr, array('id' => 0, 'desrip' => 'Sin Despacho'));
        array_push($arr, array('id' => 1, 'desrip' => 'Con Despacho'));
        $output["lista_tipodespacho"] = $arr;

        echo json_encode($output);
        break;

    case "listar_tipodoc":

        $arr = array();

        array_push($arr, array('id' => 2, 'desrip' => 'Devolución Factura'));
        array_push($arr, array('id' => 3, 'desrip' => 'Devolución Nota de Entrega'));
        $output["lista_tipodoc"] = $arr;

        echo json_encode($output);
        break;

}
