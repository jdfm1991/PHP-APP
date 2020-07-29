<?php

//llamar a la conexion de la base de datos
require_once("../acceso/conexion.php");
//llamar a el modelo Usuarios
require_once("Usuarios_modelo.php");
$usuarios = new Usuarios();

$id_usuario = isset($_POST["id_usuario"]);
$cedula = isset($_POST["cedula"]);
$login = isset($_POST["login"]);
$nomper = isset($_POST["nomper"]);
$email = isset($_POST["email"]);
$clave = isset($_POST["clave"]);
$rol = isset($_POST["rol"]);
$estado = isset($_POST["estado"]);
/*$fecha_registro = date("Y-m-d h:i:s");
$fecha_ult_ingreso = date("Y-m-d h:i:s");*/


switch ($_GET["op"]) {


    case "guardaryeditar":


        /*si el id no existe entonces lo registra
        importante: se debe poner el $_POST sino no funciona*/

        if (empty($_POST["id_usuario"])) {

            /*verificamos si existe la cedula y correo en la base de datos, si ya existe un registro con la cedula o correo entonces no se registra el usuario*/

            $datos = $usuarios->get_cedula_correo_del_usuario($_POST["cedula"], $_POST["email"]);

            if (is_array($datos) == true and count($datos) == 0) {

//no existe el usuario por lo tanto hacemos el registros

                $usuarios->registrar_usuario($cedula, $login, $nomper, $email, $clave, $rol, $estado, $id_usuario);

                /*si ya exista el correo y la cedula entonces aparece el mensaje*/

            } else {

                /*   $errors[]="La cédula o el correo ya existe";*/

            }

        } /*cierre de la validacion empty  */ else {

            /*si ya existe entonces editamos el usuario*/

            $usuarios->editar_usuario($login, $nomper, $email, $clave, $rol, $estado, $id_usuario);


        }

        break;

    case "mostrar":

        $output['lista_roles'] = $usuarios->get_roles();

        if($_POST["id_usuario"] != -1){
            //el parametro id_usuario se envia por AJAX cuando se edita el usuario
            $datos = $usuarios->get_usuario_por_id($_POST["id_usuario"]);

            foreach ($datos as $row) {

                $output["cedula"] = $row["Cedula"];
                $output["login"] = $row["Login"];
                $output["nomper"] = $row["Nomper"];
                $output["email"] = $row["Email"];
                $output["clave"] = $row["Clave"];
                $output["estado"] = $row["Estado"];
                $output["rol"] = $row["ID_Rol"];
                
            }
        }

        echo json_encode($output);
        break;

    case "activarydesactivar":
//los parametros id_usuario y est vienen por via ajax
        $datos = $usuarios->get_usuario_por_id($_POST["id"]);
//valida el id del usuario
        if (is_array($datos) == true and count($datos) > 0) {
//edita el estado del usuario
            $usuarios->editar_estado($_POST["id"], $_POST["est"]);
        }
        break;
    case "listar":
        $datos = $usuarios->get_usuarios();
//declaramos el array
        $data = array();
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
//nivel del rol asignado

            if ($row["ID_Rol"] == 1) {

                $nivel = "SUPER ADMINISTRADOR";

            } elseif ($row["ID_Rol"] == 2) {

                $nivel = "ADMINISTRADOR";

            } elseif ($row["ID_Rol"] == 3) {

                $nivel = "DIRECTIVA";

            } elseif ($row["ID_Rol"] == 4) {

                $nivel = "GERENTE";

            } elseif ($row["ID_Rol"] == 5) {

                $nivel = "JEFE ADMINISTRATIVO";

            } elseif ($row["ID_Rol"] == 6) {

                $nivel = "SUPERVISOR";

            } elseif ($row["ID_Rol"] == 7) {

                $nivel = "ANALISTA";

            } elseif ($row["ID_Rol"] == 8) {

                $nivel = "COBRANZAS";

            } elseif ($row["ID_Rol"] == 9) {

                $nivel = "DESPACHOS";

            } elseif ($row["ID_Rol"] == 10) {

                $nivel = "REPORTES";

            } elseif ($row["ID_Rol"] == 11) {

                $nivel = "EDV";

            } else {

                $nivel = "IT";

            }

            $Fecha_Registro = date('d/m/Y', strtotime($row['Fecha_Registro']));
            $Fecha_Ult_Ingreso = date('d/m/Y', strtotime($row['Fecha_Ult_Ingreso']));

            $sub_array[] = $row["Cedula"];
            $sub_array[] = $row["Login"];
            $sub_array[] = $row["Nomper"];
            $sub_array[] = $row["Email"];
            $sub_array[] = $nivel;
            $sub_array[] = $Fecha_Registro;
            $sub_array[] = $Fecha_Ult_Ingreso;
            $sub_array[] = '<div class="col text-center"><button type="button" onClick="cambiarEstado(' . $row["Cedula"] . ',' . $row["Estado"] . ');" name="estado" id="' . $row["Cedula"] . '" class="' . $atrib . '">' . $est . '</button>' . " " . '<button type="button" onClick="mostrar(' . $row["Cedula"] . ');"  id="' . $row["Cedula"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '<button type="button" onClick="eliminar(' . $row["Cedula"] . ');"  id="' . $row["Cedula"] . '" class="btn btn-danger btn-sm eliminar">Eliminar</button></div>';

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
