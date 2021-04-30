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
                    'data'    => $resultado,
                    'sesion'  => $_SESSION
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

}