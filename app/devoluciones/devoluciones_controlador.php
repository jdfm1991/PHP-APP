<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("devoluciones_modelo.php");

//INSTANCIAMOS EL MODELO
$devoluciones = new devolucionesdata();
//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_devoluciones":

    $datos = $devoluciones->getdevoluciones( $_POST["fechai"], $_POST["fechaf"],$_POST["ruta"],$_POST["tipo"]);

    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();

    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        if($row["tipofac"]=='B'){
            $tipo='DEVOLUCION FACT';
        }else{
            $tipo='DEVOLUCION N/E';
        }

        $fecha_E = date('d/m/Y', strtotime($row["fecha_fact"]));
        $total = number_format($row["Monto"], 2, ',', '.');

        $sub_array[] = $tipo;
        $sub_array[] = $row["code_vendedor"];
        $sub_array[] = $row["numerod"];
        $sub_array[] = $fecha_E;
        $sub_array[] = $row["cod_clie"];
        $sub_array[] = $row["cliente"];
        $sub_array[] = $row["chofer"];
        $sub_array[] = $total;

        $data[] = $sub_array;

    }

    //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
    $results = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);

    echo json_encode($results);
    break;

    case "listar_vendedores":

        $output['lista_vendedores'] = vendedores::todos();

        echo json_encode($output);
        break;

    
}