<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("disponiblealmacen_modelo.php");

//INSTANCIAMOS EL MODELO
$invglobal = new disponiblealmacen();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_marcas":

        $output['lista_marcas'] = Marcas::todos();

        echo json_encode($output);
        break;
}