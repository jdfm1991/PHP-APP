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

        $datos = $sellin->getsellin($_POST["fechai"], $_POST["fechaf"], $_POST["marca"]);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            $sub_array[] = $row["coditem"];
            $sub_array[] = $row["producto"];
            $sub_array[] = $row["marca"];
            $sub_array[] = Strings::rdecimal($row["compras"],2);
            $sub_array[] = Strings::rdecimal($row["devol"],2);
            $sub_array[] = Strings::rdecimal($row["total"],2);

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
