<?php

//llamar a la conexion de la base de datos
require_once("../../config/conexion.php");

//llamar a el modelo
require_once("chofer_modelo.php");

$chofer = new Chofer();

/*$id_chofer = isset($_POST["id_chofer"]);
$cedula = isset($_POST["cedula"]);
$nomper = isset($_POST["nomper"]);
$estado = isset($_POST["estado"]);*/
/*$fecha_registro = date("Y-m-d h:i:s");
$fecha_ult_ingreso = date("Y-m-d h:i:s");*/


switch ($_GET["op"]) {

    case "activarydesactivar":
        $id = $_POST["id"];
        $activo  = $_POST["est"];
        //los parametros id_chofer y est vienen por via ajax
        $datos = Choferes::getByDni($id);
        //valida el id del usuario
        if (is_array($datos) == true and count($datos) > 0) {
            //si esta activo(1) lo situamos cero(0), y viceversa
            ($activo == "0") ? $activo = 1 : $activo = 0;
            //edita el estado
            $repuesta = $chofer->editar_estado($id, $activo);
            //evalua que se realizara el query
            ($estado) ? $output["mensaje"] = "Actualizacion realizada Exitosamente" : $output["mensaje"] = "Error al Actualizar";
        }

        echo json_encode($output);
        break;

    case "listar":

        $datos = Choferes::todos();
        //declaramos el array
        $data = array();
        foreach ($datos as $row) {

            $sub_array = array();

            //ESTADO
            $est = '';
            $atrib = "btn btn-success btn-sm estado";
            switch ($row["Estado"]){
                case 0:
                    $est = 'INACTIVO';
                    $atrib = "btn btn-warning btn-sm estado";
                    break;
                case 1:
                    $est = 'ACTIVO';
                    break;
            }

            $Fecha_Registro = date('d/m/Y', strtotime($row['Fecha_Registro']));

            $sub_array[] = $row["Cedula"];
            $sub_array[] = $row["Nomper"];
            $sub_array[] = $Fecha_Registro;
            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="cambiarEstado(\'' . $row["Cedula"] . '\',\'' . $row["Estado"] . '\');" name="estado" id="' . $row["Cedula"] . '" class="' . $atrib . '">' . $est . '</button>' . " " . '
                                <button type="button" onClick="mostrar(\'' . $row["Cedula"] . '\');"  id="' . $row["Cedula"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                            </div>';

            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data);
        echo json_encode($results);
        break;

    case "guardaryeditar":
        $chofer_estatus = false;

        $id_chofer = $_POST["id_chofer"];

        $data = array(
            'cedula'        => $id_chofer,
            'nomper'        => $_POST['nomper'],
            'fecha_ingreso' => date('Y-m-d H:i:s'),
            'estado'        => $_POST['estado'],
        );

        /* consulta si la variable id_chofer llego vacia para realizar
           operaciones de creacion o actualizacion  */
        if (empty($id_chofer)) {
            /*verificamos si existe un registro en la base de datos, si ya existe un registro entonces no se registra*/
            $datos = Choferes::getByDni($id_chofer);

            if (is_array($datos) == true and count($datos) == 0) {
                //no existe, por lo tanto hacemos el registro
                $chofer_estatus = $chofer->registrar_chofer($data);

            } else {


            }

        }  else {
            /*si ya existe entonces actualizamos */
            $chofer_estatus = $chofer->editar_chofer($data);
        }

        //mensaje
        if($chofer_estatus){
            $output = [
                "mensaje" => "Guardado con Exito!",
                "icono"   => "success"
            ];
        } else {
            $output = [
                "mensaje" => "Ocurrió un error al Guardar!",
                "icono"   => "error"
            ];
        }

        echo json_encode($output);
        break;

    case "mostrar":
        $output = array();
        $id_chofer = $_POST["id_chofer"];

        $datos = Choferes::getByDni($id_chofer);

        if (is_array($datos) == true and count($datos) > 0) {
            $output["cedula"] = $datos[0]["Cedula"];
            $output["nomper"] = $datos[0]["Nomper"];
            $output["fecha_ingreso"] = $datos[0]["Fecha_Registro"];
            $output["estado"] = $datos[0]["Estado"];
        }

        echo json_encode($output);
        break;

    case "listar_choferes":

        $output["lista_choferes"] = Choferes::todos();

        echo json_encode($output);

        break;

}
