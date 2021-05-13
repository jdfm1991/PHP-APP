<?php

//llamar a la conexion de la base de datos
require_once("../../config/conexion.php");
//llamar a el modelo vehiculo
require_once("vehiculos_modelo.php");
$vehiculo = new Vehiculos();

$id_vehiculo = isset($_POST["id_vehiculo"]);
$placa = isset($_POST["placa"]);
$modelo = isset($_POST["modelo"]);
$capacidad = isset($_POST["capacidad"]);
$volumen = isset($_POST["volumen"]);
$estado = isset($_POST["estado"]);
/*$fecha_registro = date("Y-m-d h:i:s");
$fecha_ult_ingreso = date("Y-m-d h:i:s");*/


switch ($_GET["op"]) {

    case "guardaryeditar":

        /*si el id no existe entonces lo registra
        importante: se debe poner el $_POST sino no funciona*/

        if (empty($_POST["id_vehiculo"])) {

            /*verificamos si existe la placa y correo en la base de datos, si ya existe un registro con la placa o correo entonces no se registra el usuario*/

            $datos = $vehiculo->get_placa_del_vehiculo($_POST["placa"]);

            if (is_array($datos) == true and count($datos) == 0) {

                //no existe el usuario por lo tanto hacemos el registros

                $vehiculo->registrar_vehiculo($placa, $modelo, $capacidad, $volumen, $estado, $id_vehiculo);

                /*si ya exista el correo y la placa entonces aparece el mensaje*/

            } else {


            }

        } /*cierre de la validacion empty  */ else {

            /*si ya existe entonces editamos el usuario*/

            $vehiculo->editar_vehiculo($modelo, $capacidad, $volumen, $estado, $id_vehiculo);


        }


        break;

    case "mostrar":

        //selecciona el id del usuario

        //el parametro id_vehiculo se envia por AJAX cuando se edita el usuario

        $datos = $vehiculo->get_vehiculo_por_id($_POST["id_vehiculo"]);

        foreach ($datos as $row) {

            $output["id_vehiculo"] = $row["ID"];
            $output["placa"] = $row["Placa"];
            $output["modelo"] = $row["Modelo"];
            $output["capacidad"] = $row["Capacidad"];
            $output["volumen"] = $row["Volumen"];
            $output["estado"] = $row["Estado"];

        }

        echo json_encode($output);
        break;

    case "activarydesactivar":
        //los parametros id_vehiculo y est vienen por via ajax
        $datos = $vehiculo->get_vehiculo_por_id($_POST["id"]);
        //valida el id del usuario
        if (is_array($datos) == true and count($datos) > 0) {
            //edita el estado del usuario
            $vehiculo->editar_estado($_POST["id"], $_POST["est"]);
        }
        break;
    case "listar":
        $datos = $vehiculo->get_vehiculos();
        //declaramos el array
        $data = Array();
        foreach ($datos as $row) {
            $sub_array = array();
            //ESTADO
            $est = '';
            $atrib = "btn btn-success btn-sm estado";
            if ($row["Estado"] == 0) {
                $est = 'INACTIVO';
                $atrib = "btn btn-warning btn-sm estado";
            } else {
                if ($row["Estado"] == 1) {
                    $est = 'ACTIVO';
                }
            }


            $Fecha_Registro = date('d/m/Y', strtotime($row['Fecha_Registro']));

            $sub_array[] = $row["Placa"];
            $sub_array[] = $row["Modelo"];
            $sub_array[] = $row["Capacidad"];
            $sub_array[] = $row["Volumen"];
            $sub_array[] = $Fecha_Registro;
            $sub_array[] = '<div class="col text-center"><button type="button" onClick="cambiarEstado(' . $row["ID"] . ',' . $row["Estado"] . ');" name="estado" id="' . $row["ID"] . '" class="' . $atrib . '">' . $est . '</button>' . " " . '<button type="button" onClick="mostrar(' . $row["ID"] . ');"  id="' . $row["ID"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '<button type="button" onClick="eliminar(' . $row["ID"] . ');"  id="' . $row["ID"] . '" class="btn btn-danger btn-sm eliminar">Eliminar</button></div>';

            $data[] = $sub_array;

        }

        $results = array(

            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data);
        echo json_encode($results);

        break;

}

?>
