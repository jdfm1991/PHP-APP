<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO
require_once("relacionclientes_modelo.php");

//INSTANCIAMOS EL MODELO
$relacion = new RelacionClientes();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    /*case "obtener_pesomaxvehiculo":

        $peso_max_vehiculo = $vehiculo->get_vehiculo_por_id($_POST["id"]);

        $output["capacidad"] = $peso_max_vehiculo[0]["Capacidad"];
        $output["cubicajeMax"] = $peso_max_vehiculo[0]["Volumen"];

        echo json_encode($output);

        break;*/


    case "listar":

        $datos = $relacion->get_todos_los_clientes();

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $row){

            $sub_array = array();

            $sub_array[] = $row["codclie"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = $row["id3"];
            $sub_array[] = number_format($row['saldo'], 2, ",", ".");
            $sub_array[] = '<div class="col text-center"></button>'." ".'<button type="button" onClick="modalMostrarDocumentoEnDespacho(\''.$row["codclie"].'\');"  id="'.$row["codclie"].'" class="btn btn-info btn-sm update">Editar</button>'." ".'<button type="button" onClick="modalEliminarDocumentoEnDespacho(\''.$row["codclie"].'\');"  id="'.$row["codclie"].'" class="btn btn-info btn-sm ver_detalles">Ver Detalles</button></div>';

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