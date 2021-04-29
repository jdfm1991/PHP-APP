<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("clientesnuevos_modelo.php");

//INSTANCIAMOS EL MODELO
$clientesnuevos = new ClientesNuevos();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_clientesnuevos":

    $datos = $clientesnuevos->getClientesNuevos($_POST["fechai"], $_POST["fechaf"]);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();


    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();
        /*$sub_array[] = date("d-m-Y",strtotime($row["fechauv"]));*/



        $sub_array[] = $row["codclie"];
        $sub_array[] = $row["descrip"];
        $sub_array[] = $row["id3"];
        $sub_array[] = date("d-m-Y", strtotime($row["fechae"]));
        $sub_array[] = $row["codvend"];

        /*$sub_array[] = number_format($row["total"], 2, ",", ".");*/


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

  /*  case "mostrar":
    $datos = $clientesnuevos->getTotalClientesnuevos($_POST["fechai"], $_POST["fechaf"]);

    foreach ($datos as $row) {

        $output["cuenta"] = "Clientes Nuevos: " . $row["cuenta"];

    }

    echo json_encode($output);
    break;*/

}