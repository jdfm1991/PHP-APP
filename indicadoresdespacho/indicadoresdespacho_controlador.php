<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("indicadoresdespacho_modelo.php");
require_once("../choferes/choferes_modelo.php");

//INSTANCIAMOS EL MODELO
$indicadores = new InidicadoresDespachos();
$choferes = new Choferes();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_choferes":

        $output["lista_choferes"] = $choferes->get_choferes();

        echo json_encode($output);

        break;

    case "listar_causas_rechazo":

        $output["lista_choferes"] = $choferes->get_choferes();

        echo json_encode($output);

        break;
}
