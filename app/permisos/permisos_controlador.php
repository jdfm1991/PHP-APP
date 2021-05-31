<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("permisos_modelo.php");

//INSTANCIAMOS EL MODELO
$permisos = new Permiso();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_permisos_por_rol":
        $output = array();
        $rol_id = $_POST['rol_id'];
//        $output = Functions::organigramaMenusWithModules($rol_id);

        $menus = Menu::todos();
        if (is_array($menus) == true and count($menus) > 0) {
            foreach ($menus as $key => $menu)
            {
                $modulosMenu = array();
                $modulos = Modulos::getByMenuId($menu['id']);
                $modulosPorRol = array_map(function ($arr) { return $arr['id_modulo']; }, $permisos->getRolesGrupoPorRolID($rol_id));
                if (is_array($modulos) == true and count($modulos) > 0) {
                    foreach ($modulos as $key1 => $modulo) {
                        $modulosMenu[] = array(
                            'id' 	   => $modulo['id'],
                            'nombre'   => $modulo['nombre'],
                            'selected' => in_array($modulo['id'], $modulosPorRol)
                        );
                    }
                }

                $output[] = array(
                    'menu_id' 	  => $menu['id'],
                    'menu_nombre' => $menu['nombre'],
                    'modulos' 	  => $modulosMenu,
                );
            }
        }

        echo json_encode($output);
        break;

    case 'guardar_permisos_por_rol':
            $output = array();
            $data = array(
                'modulo_id' => $this->input->post('modulo_id'),
                'rol_id'	=> $this->input->post('rol_id')
            );
            $state = $this->input->post('state');

            if ($state=="true") {
                $mensajeError = 'registrar';
                $mensajeSuccess = 'registró';
                $rolMod = $this->permisos_model->registrar_rolmod($data);
            } else {
                $mensajeError = 'eliminar';
                $mensajeSuccess = 'eliminó';
                $rolMod = $this->permisos_model->borrar_rolmod($data);
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


    case "buscar_clientessintr":

        $datos = $clientessintr->getclientessintr($_POST["fechai"], $_POST["fechaf"], $_POST["vendedor"]);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = array();

        foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            $sub_array[] = $row["codvend"];
            $sub_array[] = $row["codclie"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = Strings::rdecimal($row["debe"], 2);

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


}
