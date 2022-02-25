<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("facturasporcobrar_modelo.php");

//INSTANCIAMOS EL MODELO
$facturas = new facturasporcobrar();
//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_facturasporcobrar":

    $datos = $facturas->getfacturasporcobrar( $_POST["fechai"], $_POST["fechaf"]);

    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();

    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        $fecha_E = date('d/m/Y', strtotime($row["FechaEmi"]));
        $fecha_D = date('d/m/Y', strtotime($row["FechaDesp"]));

        $Montonew = number_format($row["SaldoPend"], 2, ',', '.');
        
        $De_0_a_7_Dias = number_format($row["De_0_a_7_Dias"], 2, ',', '.');
        $De_8_a_14_Dias = number_format($row["De_8_a_14_Dias"], 2, ',', '.');
        $De_15_a_21_Dias = number_format($row["De_15_a_21_Dias"], 2, ',', '.');
        $De_22_a_31_Dias = number_format($row["De_22_a_31_Dias"], 2, ',', '.');
        $Mas_31_Dias = number_format($row["Mas_31_Dias"], 2, ',', '.');

        $sub_array[] = $row["TipoOpe"];
        $sub_array[] = $row["NroDoc"];
        $sub_array[] = $row["CodClie"];
        $sub_array[] = $row["Cliente"];
        $sub_array[] = $fecha_E;
        $sub_array[] = $row["FechaDesp"];
        $sub_array[] = $row["DiasTrans"];
        $sub_array[] = $row["DiasTransHoy"];
        $sub_array[] = $De_0_a_7_Dias;
        $sub_array[] = $De_8_a_14_Dias;
        $sub_array[] = $De_15_a_21_Dias;
        $sub_array[] = $De_22_a_31_Dias;
        $sub_array[] = $Mas_31_Dias;
        $sub_array[] = $Montonew;
        $sub_array[] = $row["Ruta"];
        $sub_array[] = $row["Supervisor"];

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
}