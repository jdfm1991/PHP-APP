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

        $fechai2 = str_replace('/','-',$fechai); $fechai2 = date('Y-m-d', strtotime($fechai2));
        $fechaf2 = str_replace('/','-',$fechaf); $fechaf2 = date('Y-m-d', strtotime($fechaf2));

        $coordinadores = $kpi->get_coordinadores();
        if (is_array($coordinadores) == true and count($coordinadores) > 0)
        {
            $marcasKpi = array_map(function ($arr) { return $arr['descripcion']; }, KpiMarcas::todos());

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
                        $ruta = $vend["ID3"];
                        $clientes         = $kpi->get_MaestroClientesPorRuta($ruta);
                        $clientes_activos = $kpi->get_ClientesActivosPorRuta($ruta, $fechai2, $fechaf2);
                        $frecuencia       = $kpi->get_frecuenciaVisita($ruta);
                        $devolucionesFact = $kpi->get_devolucionesFactura($ruta, $fechai2, $fechaf2);
                        $devolucionesNota = $kpi->get_devolucionesNotas($ruta, $fechai2, $fechaf2);
                        $montoendivisa_devoluciones_fact = $kpi->get_montoDivisasDevolucionesFactura($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                        $montoendivisa_devoluciones_nt   = $kpi->get_montoDivisasDevolucionesNotas($ruta, $fechai2, $fechaf2)[0]["MontoD"];


                        $sub_array1 = array(
                            'ruta'            => $ruta,
                            'maestro'         => count($clientes),
                            'activos'         => count($clientes_activos),
                            'marcas'          => KpiHelpers::activacionBultosPorMarcasKpi($ruta, $marcasKpi, $kpi, $fechai2, $fechaf2),
                            'porc_activacion' => (count($clientes)!=0) ? Strings::rdecimal((count($clientes_activos)/count($clientes))*100) : 0,
                            'por_activar'     => count($clientes) - count($clientes_activos),
                            'visita'          => KpiHelpers::frecuenciaVisita($frecuencia),
                            'obj_documentos_mensual' => Strings::rdecimal(KpiHelpers::objetivoFacturasMasNotasMensual(count($clientes), $d_habiles, $frecuencia), 2),
                            'facturas_realizadas'    => count($kpi->get_ventasFactura($ruta, $fechai2, $fechaf2)),
                            'notas_realizadas'       => count($kpi->get_ventasNotas($ruta, $fechai2, $fechaf2)),
                            'devoluciones_realizadas'    => count($devolucionesFact) + count($devolucionesNota),
                            'montoendivisa_devoluciones' => Strings::rdecimal(floatval($montoendivisa_devoluciones_fact) + floatval($montoendivisa_devoluciones_nt),2),
                        );


                        $sub_array[] = $sub_array1;
                    }
                }
                $data[] = $sub_array;
            }
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "tabla" => $data
        );

        echo json_encode($output);
        break;
}