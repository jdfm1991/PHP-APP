<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("facturasporcobrardivisas_modelo.php");

//INSTANCIAMOS EL MODELO
$facturas = new facturasporcobrardivisas();
//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_facturasporcobrar":

    $datos = $facturas->getfacturasporcobrardivisas( $_POST["fechai"], $_POST["fechaf"]);

    //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
    $data = Array();

    foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        $sub_array = array();

        $fecha_E = date('d/m/Y', strtotime($row["FechaEmi"]));
        $fecha_D = date('d/m/Y', strtotime($row["FechaDesp"]));

        $tasa=$row["tasa"];

        $De_0_a_7_Dias = $row["De_0_a_7_Dias"];
        $De_8_a_14_Dias = $row["De_8_a_14_Dias"];
        $De_15_a_21_Dias = $row["De_15_a_21_Dias"];
        $De_22_a_31_Dias = $row["De_22_a_31_Dias"];
        $Mas_31_Dias = $row["Mas_31_Dias"];
        
        

        $sub_array[] = $row["TipoOpe"];
        $sub_array[] = $row["NroDoc"];
        $sub_array[] = $row["CodClie"];
        $sub_array[] = $row["Cliente"];
        $sub_array[] = $fecha_E;
        $sub_array[] = $row["FechaDesp"];
        $sub_array[] = $row["DiasTrans"];
        $sub_array[] = $row["DiasTransHoy"];
        if($tasa>=1){
            $sub_array[] = number_format(($De_0_a_7_Dias / $tasa), 2, ',', '.');
            $sub_array[] = number_format(($De_8_a_14_Dias / $tasa), 2, ',', '.');
            $sub_array[] = number_format(($De_15_a_21_Dias / $tasa), 2, ',', '.');
            $sub_array[] = number_format(($De_22_a_31_Dias / $tasa), 2, ',', '.');
            $sub_array[] = number_format(($Mas_31_Dias / $tasa), 2, ',', '.');
            $Montonew = ($De_0_a_7_Dias / $tasa) + ($De_8_a_14_Dias / $tasa) + ($De_15_a_21_Dias / $tasa) + ($De_22_a_31_Dias / $tasa) + ($Mas_31_Dias / $tasa);
            $sub_array[] = number_format( $Montonew, 2, ',', '.') ;
        }else{
             $sub_array[] = 0;
             $sub_array[] = 0;
             $sub_array[] = 0;
             $sub_array[] = 0;
             $sub_array[] = 0;
             $sub_array[] = 0;
        }
        
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