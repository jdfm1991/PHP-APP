<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO
require_once("kpi_modelo.php");

//INSTANCIAMOS EL MODELO
$kpi = new Kpi();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_kpi":
        $fechai     = $_POST['fechai'];
        $fechaf     = $_POST['fechaf'];
        $d_habiles  = $_POST['d_habiles'];
        $d_trans    = $_POST['d_trans'];

        $coordinadores = $kpi->get_coordinadores();
        if (is_array($coordinadores) == true and count($coordinadores) > 0)
        {
            $marcasKpi = $kpi->get_marcas_kpi();

            //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
            $data = Array();
            foreach ($coordinadores as $coord)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array['coordinador'] = $coord["coordinador"];

                $vendedores = $kpi->get_rutasPorCoordinador($coord["coordinador"]);
                if (is_array($vendedores) == true and count($vendedores) > 0) {
                    foreach ($vendedores as $vend)
                    {

                    }
                }
                $data[] = $sub_array;
            }
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "marcas" => $marcasKpi,
            "tabla" => $data
        );

        echo json_encode($output);
        break;
}