<?php
session_name('S1sTem@@PpWebGruP0C0nF1SuR');
session_start();
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("auth_modelo.php");

//INSTANCIAMOS EL MODELO
$auth = new Auth();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "login_in":
        $login = $_POST["login"];
        $clave = md5($_POST["clave"]);

        if (!empty($login) and !empty($clave))
        {
            $resultado = $auth->login($login, $clave);

            if (is_array($resultado) and count($resultado) > 0)
            {
                include_once (PATH_HELPERS_PHP . "php/Session.php");
                Session::create($resultado);

                /*IMPORTANTE: la session guarda los valores de los campos de la tabla de la bd*/
                $output = array(
                    'status'  => true,
                    'message' => 'ok',
                    'data'    => $resultado
                );

            } else {
                $output = array(
                    'status'  => false,
                    'message' => 'El correo y/o password es incorrecto o no tienes permiso!',
                    'data'    => array()
                );
            }
        } else {
            $output = array(
                'status'  => false,
                'message' => 'Los campos estan vacios.',
                'data'    => array()
            );
        }

        echo json_encode($output);
        break;

    case 'exists_user':

        $user = $_POST["user"];

        if (!empty($user)) {
            $resultado = Usuarios::byUserName($user);

            if (is_array($resultado) and count($resultado) > 0)
            {
                "El codigo de Seguridad es: <b>12345</b> 
                <p>Desarrollado Por equipo de IT. </b>Grupo Confisur IT -> The Innovation is our's priority..</p>";
                //enviar correo

                $output = array(
                    'status'  => true,
                    'message' => 'ok'
                );

            } else {
                $output = array(
                    'status'  => false,
                    'message' => 'El usuario no existe !'
                );
            }
        } else {
            $output = array(
                'status'  => false,
                'message' => 'campo vacios.'
            );
        }

        echo json_encode($output);
        break;

}