<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("permiso_modelo.php");

//INSTANCIAMOS EL MODELO
$permisos = new Permiso();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_permisos_por_rol":
        $output = array();
        $rol_id = $_POST['rol_id'];
        $output = Functions::organigramaMenusWithModules(-1, $rol_id);

        echo json_encode($output);
        break;

    case 'guardar_permisos_por_rol':
            $output = array();
            $data = array(
                'modulo_id' => $_POST['modulo_id'],
                'rol_id'	=> $_POST['rol_id']
            );
            $state = $_POST['state'];

            if ($state=="true") {
                $mensajeError = 'registrar';
                $mensajeSuccess = 'registró';
                $rolMod = $permisos->registrar_rolmod($data);
            } else {
                $mensajeError = 'eliminar';
                $mensajeSuccess = 'eliminó';
                $rolMod = $permisos->borrar_rolmod($data);
            }

            if ($rolMod) {
                $output["mensaje"] = "Se $mensajeSuccess correctamente";
                $output["icono"] = "success";
            } else {
                $output["mensaje"] = "Error al $mensajeError";
                $output["icono"] = "error";
            }

            echo json_encode($output);
            break;
}
