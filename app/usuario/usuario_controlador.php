<?php

//llamar a la conexion de la base de datos
require_once("../../config/conexion.php");

//llamar a el modelo Usuarios
require_once("Usuario_modelo.php");

$usuarios = new Usuario();

switch ($_GET["op"])
{
    case "guardaryeditar":
        $usuario = true;

        $id_usuario = $_POST['id_usuario'];

        $data = array(
            'id_usuario'=> $id_usuario,
            'cedula'    => $_POST['cedula'],
            'login'     => $_POST['login'],
            'nomper'    => $_POST['nomper'],
            'email'     => $_POST['email'],
            'clave'     => $_POST['clave'],
            'rol'       => $_POST['rol'],
            'estado'    => $_POST['estado'],
        );

        /*si el id no existe entonces lo registra
        importante: se debe poner el $_POST sino no funciona*/
        if ( !Strings::avoidNullOrEmpty($id_usuario) ) {

            /*verificamos si existe la cedula y correo en la base de datos, si ya existe un registro con la cedula o correo entonces no se registra el usuario*/
            $datos = $usuarios->get_cedula_correo_del_usuario($data["cedula"], $data["email"]);

            if (is_array($datos) == true and count($datos) == 0) {
                //no existe el usuario por lo tanto hacemos el registros
                $usuario = $usuarios->registrar_usuario($data);

            } else {
                /*   $errors[]="La cédula o el correo ya existe";*/
            }

        } else {
            /*si ya existe entonces editamos el usuario*/
            $usuario = $usuarios->editar_usuario($data);
        }

        //mensaje
        if($usuario){
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
        $output=array();

        $output['lista_roles'] = $usuarios->get_roles();

        if($_POST["id_usuario"] != -1){
            //el parametro id_usuario se envia por AJAX cuando se edita el usuario
            $datos = Usuarios::byDni($_POST["id_usuario"]);

            foreach ($datos as $row) {
                $output["cedula"] = $row["cedula"];
                $output["login"] = $row["login"];
                $output["nomper"] = $row["nomper"];
                $output["email"] = $row["email"];
//                $output["clave"] = $row["clave"];
                $output["estado"] = $row["estado"];
                $output["rol"] = $row["id_rol"];
            }
        }

        echo json_encode($output);
        break;

    case "activarydesactivar":
        $id = $_POST["id"];
        $activo  = $_POST["est"];
        //los parametros id_usuario y est vienen por via ajax
        $datos = Usuarios::byDni($id);
        //valida el id del usuario
        if (is_array($datos) == true and count($datos) > 0) {
            //si esta activo(1) lo situamos cero(0), y viceversa
            ($activo == "0") ? $activo = 1 : $activo = 0;
            //edita el estado
            $usuarios->editar_estado($id, $activo);
            //evalua que se realizara el query
            ($estado) ? $output["mensaje"] = "Actualizacion realizada Exitosamente" : $output["mensaje"] = "Error al Actualizar";
        }

        echo json_encode($output);
        break;

    case "listar":
        $datos = Usuarios::todos();

        //declaramos el array
        $data = array();

        foreach ($datos as $row) {
            $sub_array = array();

            //ESTADO
            $est = '';
            $atrib = "btn btn-success btn-sm estado";
            if ($row["estado"] == 0) {
                $est = 'INACTIVO';
                $atrib = "btn btn-warning btn-sm estado";
            } else {
                if ($row["estado"] == 1) {
                    $est = 'ACTIVO';
                }
            }
            //nivel del rol asignado

            if ($row["id_rol"] == 1) {

                $nivel = "SUPER ADMINISTRADOR";

            } elseif ($row["id_rol"] == 2) {

                $nivel = "ADMINISTRADOR";

            } elseif ($row["id_rol"] == 3) {

                $nivel = "DIRECTIVA";

            } elseif ($row["id_rol"] == 4) {

                $nivel = "GERENTE";

            } elseif ($row["id_rol"] == 5) {

                $nivel = "JEFE ADMINISTRATIVO";

            } elseif ($row["id_rol"] == 6) {

                $nivel = "SUPERVISOR";

            } elseif ($row["id_rol"] == 7) {

                $nivel = "ANALISTA";

            } elseif ($row["id_rol"] == 8) {

                $nivel = "COBRANZAS";

            } elseif ($row["id_rol"] == 9) {

                $nivel = "DESPACHOS";

            } elseif ($row["id_rol"] == 10) {

                $nivel = "REPORTES";

            } elseif ($row["id_rol"] == 11) {

                $nivel = "EDV";

            } else {

                $nivel = "IT";

            }

            $Fecha_Registro = date('d/m/Y', strtotime($row['fecha_registro']));
            $Fecha_Ult_Ingreso = date('d/m/Y', strtotime($row['fecha_ult_ingreso']));

            $sub_array[] = $row["cedula"];
            $sub_array[] = $row["login"];
            $sub_array[] = $row["nomper"];
            $sub_array[] = $row["email"];
            $sub_array[] = $nivel;

            # el parametro t es el tipo:
            #        0 el tipo es rol
            #        1 el tipo es usuario (este caso)
            $sub_array[] = '<div align="center form-check-inline p-t-30">
								<a href="../permiso/permiso.php?&t='. 1 .'&i='. $row["cedula"] .'">Ver Permisos</a>
							</div>';
            $sub_array[] = $Fecha_Ult_Ingreso;
            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="cambiarEstado(\'' . $row["cedula"] . '\',\'' . $row["estado"] . '\');" name="estado" id="' . $row["cedula"] . '" class="' . $atrib . '">' . $est . '</button>' . " " . '
                                <button type="button" onClick="mostrar(\'' . $row["cedula"] . '\');"  id="' . $row["cedula"] . '" class="btn btn-info btn-sm update">Editar</button>' . " " . '
                                <button type="button" onClick="eliminar(\'' . $row["cedula"] . '\',\''. $row["login"] . '\');"  id="' . $row["cedula"] . '" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                            </div>';

            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data
        );

        echo json_encode($results);
        break;

    case "eliminar":
        $eliminar = false;
        $cedula = $_POST["cedula"];

        $usuario = Usuarios::byDni($cedula);
        if(is_array($usuario) == true and count($usuario) > 0) {
            $eliminar = $usuarios->eliminar_usuario($cedula);
        }

        //mensaje
        if($eliminar){
            $output = [
                "mensaje" => "Se eliminó exitosamente!",
                "icono"   => "success"
            ];
        } else {
            $output = [
                "mensaje" => "Ocurrió un error al eliminar!",
                "icono"   => "error"
            ];
        }

        echo json_encode($output);
        break;
}

?>
