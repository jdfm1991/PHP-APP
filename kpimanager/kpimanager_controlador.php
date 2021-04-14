<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("kpimanager_modelo.php");

//INSTANCIAMOS EL MODELO
$kpiManager = new KpiManager();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar":

        $edv = $_POST['edv'];
        $datos = $kpiManager->get_datos_edv($edv);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $row){

            $sub_array = array();

            //ESTADO
            $est = '';
            $atrib = "btn btn-success btn-sm estado";
            switch ($row["activo"]){
                case 0:
                    $est = 'INACTIVO';
                    $atrib = "btn btn-warning btn-sm estado";
                    break;
                case 1:
                    $est = 'ACTIVO';
                    break;
            }

            $coordinador = ($row["activo"]==1 and empty($row["Coordinador"])) ? '<br><span class="right badge badge-primary">Coordinador no asignado</span>' : '';

            $sub_array[] = $row["CodVend"];
            $sub_array[] = $row["Descrip"] . $coordinador;
            $sub_array[] = $row["clase"];
            $sub_array[] = !empty($row["ubicacion"]) ? $row["ubicacion"] : "-";
            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="cambiarEstado(\''.$row["CodVend"].'\',\''.$row["activo"].'\');" name="estado" id="' . $row["CodVend"] . '" class="' . $atrib . '">' . $est . '</button>' . " " . '
                                <button type="button" onClick="mostrar(' . $row["CodVend"] . ');"  id="' . $row["CodVend"] . '" class="btn btn-info btn-sm update">Editar</button>
                            </div>';

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
