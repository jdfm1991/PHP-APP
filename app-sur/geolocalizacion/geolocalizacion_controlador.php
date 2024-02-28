<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("geolocalizacion_modelo.php");

//INSTANCIAMOS EL MODELO
$geo = new Geolocalizacion();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_rutas":

        $output['listar_rutas'] = Vendedores::todos_rutas();

        echo json_encode($output);
        break;

       
}