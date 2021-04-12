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
            $ttl_marcas = new Kpimarca($marcasKpi);

            # inicializacion variables ttl
            $ttl_clientes           = 0;
            $ttl_clientes_activos   = 0;
            $ttl_clientes_noactivos = 0;
            $ttl_activacionBultos   = array();
            $ttl_porc_activacion    = 0;
            $ttl_obj_documentos_mensual  = 0;
            $ttl_facturas_realizadas     = 0;
            $ttl_notas_realizadas        = 0;
            $ttl_devoluciones_realizadas = 0;
            $ttl_montoendivisa_devoluciones = 0;
            $ttl_efec_alcanzada_fecha       = 0;
            $ttl_objetivo_bulto             = 0;
            $ttl_logro_bulto                = 0;
            $ttl_porc_alcanzado_bulto       = 0;
            $ttl_objetivo_kg                = 0;
            $ttl_logro_kg                   = 0;
            $ttl_porc_alcanzado_kg          = 0;
            $ttl_objetivo_ventas_divisas    = 0;
            $ttl_logro_ventas_divisas       = 0;
            $ttl_real_dz_dolares            = 0;
            $ttl_logro_ventas_divisas_pepsico        = 0;
            $ttl_porcentaje_ventas_divisas_pepsico   = 0;
            $ttl_logro_ventas_divisas_complementaria = 0;
            $ttl_porcentaje_ventas_divisas_complementaria = 0;
            $ttl_cobranzasRebajadas = 0;

            //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
            $data = Array();
            foreach ($coordinadores as $coord)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();
                $sub_array['coordinador'] = $coord["coordinador"];
                $subttl_marcas = new Kpimarca($marcasKpi);

                # inicializacion variables subttl
                $subttl_clientes           = 0;
                $subttl_clientes_activos   = 0;
                $subttl_clientes_noactivos = 0;
                $subttl_activacionBultos   = array();
                $subttl_porc_activacion    = 0;
                $subttl_obj_documentos_mensual  = 0;
                $subttl_facturas_realizadas     = 0;
                $subttl_notas_realizadas        = 0;
                $subttl_devoluciones_realizadas = 0;
                $subttl_montoendivisa_devoluciones = 0;
                $subttl_efec_alcanzada_fecha       = 0;
                $subttl_objetivo_bulto             = 0;
                $subttl_logro_bulto                = 0;
                $subttl_porc_alcanzado_bulto       = 0;
                $subttl_objetivo_kg                = 0;
                $subttl_logro_kg                   = 0;
                $subttl_porc_alcanzado_kg          = 0;
                $subttl_objetivo_ventas_divisas    = 0;
                $subttl_logro_ventas_divisas       = 0;
                $subttl_real_dz_dolares            = 0;
                $subttl_logro_ventas_divisas_pepsico        = 0;
                $subttl_porcentaje_ventas_divisas_pepsico   = 0;
                $subttl_logro_ventas_divisas_complementaria = 0;
                $subttl_porcentaje_ventas_divisas_complementaria = 0;
                $subttl_cobranzasRebajadas = 0;

                $vendedores = $kpi->get_rutasPorCoordinador($coord["coordinador"]);
                if (is_array($vendedores) == true and count($vendedores) > 0) {
                    foreach ($vendedores as $vend)
                    {
                        $ruta = $vend["ID3"];
                        $clientes           = count($kpi->get_MaestroClientesPorRuta($ruta));
                        $clientes_activos   = count($kpi->get_ClientesActivosPorRuta($ruta, $fechai2, $fechaf2));
                        $clientes_noactivos = $clientes - $clientes_activos;
                        $activacionBultos   = KpiHelpers::activacionBultosPorMarcasKpi($ruta, $marcasKpi, $fechai2, $fechaf2);
                        $porc_activacion    = ($clientes!=0) ? ($clientes_activos/$clientes) * 100 : 0;
                        $frecuencia         = $kpi->get_frecuenciaVisita($ruta)[0];
                        $frecuenciaVisita   = KpiHelpers::frecuenciaVisita($frecuencia);
                        $obj_documentos_mensual = KpiHelpers::objetivoFacturasMasNotasMensual($clientes, $d_habiles, $frecuencia);
                        $facturas_realizadas    = count($kpi->get_ventasFactura($ruta, $fechai2, $fechaf2));
                        $notas_realizadas = count($kpi->get_ventasNotas($ruta, $fechai2, $fechaf2));
                        $devolucionesFact = $kpi->get_devolucionesFactura($ruta, $fechai2, $fechaf2);
                        $devolucionesNota = $kpi->get_devolucionesNotas($ruta, $fechai2, $fechaf2);
                        $devoluciones_realizadas = count($devolucionesFact) + count($devolucionesNota);
                        $montoendivisa_devoluciones_fact = floatval($kpi->get_montoDivisasDevolucionesFactura($ruta, $fechai2, $fechaf2)[0]["MontoD"]);
                        $montoendivisa_devoluciones_nt   = floatval($kpi->get_montoDivisasDevolucionesNotas($ruta, $fechai2, $fechaf2)[0]["MontoD"]);
                        $montoendivisa_devoluciones = $montoendivisa_devoluciones_fact + $montoendivisa_devoluciones_nt;
                        $efec_alcanzada_fecha = KpiHelpers::efectividadAlcanzadaAlaFecha($d_trans, $d_habiles, $obj_documentos_mensual, $facturas_realizadas, $notas_realizadas);
                        $objetivo_bulto       = KpiHelpers::obtenerObjetivo($frecuencia, 'ObjVentasBu');
                        $logro_bulto          = KpiHelpers::logroPorTipo($ruta, $fechai2, $fechaf2, 'BUL');
                        $porc_alcanzado_bulto = (($objetivo_bulto!=0) ? ($logro_bulto/$objetivo_bulto)*100 : 0) ?? 0;
                        $objetivo_kg          = KpiHelpers::obtenerObjetivo($frecuencia, 'ObjVentasKG');
                        $logro_kg             = KpiHelpers::logroPorTipo($ruta, $fechai2, $fechaf2, 'KG');
                        $porc_alcanzado_kg    = (($objetivo_kg!=0) ? ($logro_kg/$objetivo_kg)*100 : 0) ?? 0;
                        $objetivo_ventas_divisas = KpiHelpers::obtenerObjetivo($frecuencia, 'ObjVentasBs'); # realmente objetivo $$
                        $ventas_divisas_fact  = $kpi->get_ventasDivisasFactura($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                        $ventas_divisas_nt    = $kpi->get_ventasDivisasNotas($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                        $logro_ventas_divisas = floatval($ventas_divisas_fact) + floatval($ventas_divisas_nt);
                        $real_dz_dolares      = (($facturas_realizadas+$notas_realizadas) > 0) ? $logro_ventas_divisas/($facturas_realizadas+$notas_realizadas) : 0;
                        $ventas_divisas_pepsico_fact         = $kpi->get_ventasDivisasPepsicoFactura($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                        $ventas_divisas_pepsico_nt           = $kpi->get_ventasDivisasPepsicoNotas($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                        $logro_ventas_divisas_pepsico        = floatval($ventas_divisas_pepsico_fact) + floatval($ventas_divisas_pepsico_nt);
                        $porcentaje_ventas_divisas_pepsico   = ($logro_ventas_divisas > 0) ? ($logro_ventas_divisas_pepsico / $logro_ventas_divisas) * 100 : 0;
                        $ventas_divisas_complementaria_fact  = $kpi->get_ventasDivisasComplementariaFactura($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                        $ventas_divisas_complementaria_nt    = $kpi->get_ventasDivisasComplementariaNotas($ruta, $fechai2, $fechaf2)[0]["MontoD"];
                        $logro_ventas_divisas_complementaria = floatval($ventas_divisas_complementaria_fact) + floatval($ventas_divisas_complementaria_nt);
                        $porcentaje_ventas_divisas_complementaria = ($logro_ventas_divisas > 0) ? ($logro_ventas_divisas_complementaria / $logro_ventas_divisas) * 100 : 0;
                        $cobranzasRebajadas = $kpi->get_cobranzasRebajadas($ruta, $fechai2, $fechaf2)[0]["total"];

                        #llenado de los subtotals
                        $subttl_marcas->set_acumKpiMarcas($activacionBultos);
                        $subttl_clientes                                 += $clientes;
                        $subttl_clientes_activos                         += $clientes_activos;
                        $subttl_activacionBultos                         = $subttl_marcas->get_totalKpiMarcas();
                        $subttl_clientes_noactivos                       += $clientes_noactivos;
                        $subttl_porc_activacion                          += $porc_activacion;
                        $subttl_obj_documentos_mensual                   += $obj_documentos_mensual;
                        $subttl_facturas_realizadas                      += $facturas_realizadas;
                        $subttl_notas_realizadas                         += $notas_realizadas;
                        $subttl_devoluciones_realizadas                  += $devoluciones_realizadas;
                        $subttl_montoendivisa_devoluciones               += $montoendivisa_devoluciones;
                        $subttl_efec_alcanzada_fecha                     += $efec_alcanzada_fecha;
                        $subttl_objetivo_bulto                           += $objetivo_bulto;
                        $subttl_logro_bulto                              += $logro_bulto;
                        $subttl_porc_alcanzado_bulto                     += $porc_alcanzado_bulto;
                        $subttl_objetivo_kg                              += $objetivo_kg;
                        $subttl_logro_kg                                 += $logro_kg;
                        $subttl_porc_alcanzado_kg                        += $porc_alcanzado_kg;
                        $subttl_objetivo_ventas_divisas                  += $objetivo_ventas_divisas;
                        $subttl_logro_ventas_divisas                     += $logro_ventas_divisas;
                        $subttl_real_dz_dolares                          += $real_dz_dolares;
                        $subttl_logro_ventas_divisas_pepsico             += $logro_ventas_divisas_pepsico;
                        $subttl_porcentaje_ventas_divisas_pepsico        += $porcentaje_ventas_divisas_pepsico;
                        $subttl_logro_ventas_divisas_complementaria      += $logro_ventas_divisas_complementaria;
                        $subttl_porcentaje_ventas_divisas_complementaria += $porcentaje_ventas_divisas_complementaria;
                        $subttl_cobranzasRebajadas                       += $cobranzasRebajadas;


                        $sub_array1 = array(
                            'ruta'            => $ruta,
                            'maestro'         => $clientes,
                            'activos'         => $clientes_activos,
                            'marcas'          => $activacionBultos,
                            'porc_activacion' => Strings::rdecimal($porc_activacion),
                            'por_activar'     => $clientes_noactivos,
                            'visita'          => $frecuenciaVisita,
                            'obj_documentos_mensual'        => Strings::rdecimal($obj_documentos_mensual, 2),
                            'facturas_realizadas'           => $facturas_realizadas,
                            'notas_realizadas'              => $notas_realizadas,
                            'devoluciones_realizadas'       => $devoluciones_realizadas,
                            'montoendivisa_devoluciones'    => Strings::rdecimal($montoendivisa_devoluciones,2),
                            'efec_alcanzada_fecha'          => Strings::rdecimal($efec_alcanzada_fecha, 2),
                            'objetivo_bulto'                => Strings::rdecimal($objetivo_bulto, 2),
                            'logro_bulto'                   => Strings::rdecimal($logro_bulto, 2),
                            'porc_alcanzado_bulto'          => Strings::rdecimal($porc_alcanzado_bulto, 2),
                            'objetivo_kg'                   => Strings::rdecimal($objetivo_kg, 2),
                            'logro_kg'                      => Strings::rdecimal($logro_kg, 2),
                            'porc_alcanzado_kg'             => Strings::rdecimal($porc_alcanzado_kg, 2),
                            'drop_size_divisas'             => Strings::rdecimal($real_dz_dolares, 2),
                            'objetivo_ventas_divisas'       => Strings::rdecimal($objetivo_ventas_divisas, 2),
                            'logro_ventas_divisas'          => Strings::rdecimal($logro_ventas_divisas, 2),
                            'porc_alcanzado_ventas_divisas' => Strings::rdecimal($porc_alcanzado_kg, 2),
                            'logro_ventas_divisas_pepsico'                 => Strings::rdecimal($logro_ventas_divisas_pepsico, 2),
                            'porc_alcanzado_ventas_divisas_pepsico'        => Strings::rdecimal($porcentaje_ventas_divisas_pepsico, 2),
                            'logro_ventas_divisas_complementaria'          => Strings::rdecimal($logro_ventas_divisas_complementaria, 2),
                            'porc_alcanzado_ventas_divisas_complementaria' => Strings::rdecimal($porcentaje_ventas_divisas_complementaria, 2),
                            'cobranzas_rebajadas'                          => Strings::rdecimal($cobranzasRebajadas, 2),
                        );

                        $sub_array['data'][] = $sub_array1;
                    }

                    #llenado del total general
                    $ttl_marcas->set_acumKpiMarcas($subttl_marcas->get_totalKpiMarcas());
                    $ttl_clientes                                 += $subttl_clientes;
                    $ttl_clientes_activos                         += $subttl_clientes_activos;
                    $ttl_activacionBultos                         = $ttl_marcas->get_totalKpiMarcas();
                    $ttl_clientes_noactivos                       += $subttl_clientes_noactivos;
                    $ttl_porc_activacion                          += $subttl_porc_activacion;
                    $ttl_obj_documentos_mensual                   += $subttl_obj_documentos_mensual;
                    $ttl_facturas_realizadas                      += $subttl_facturas_realizadas;
                    $ttl_notas_realizadas                         += $subttl_notas_realizadas;
                    $ttl_devoluciones_realizadas                  += $subttl_devoluciones_realizadas;
                    $ttl_montoendivisa_devoluciones               += $subttl_montoendivisa_devoluciones;
                    $ttl_efec_alcanzada_fecha                     += $subttl_efec_alcanzada_fecha;
                    $ttl_objetivo_bulto                           += $subttl_objetivo_bulto;
                    $ttl_logro_bulto                              += $subttl_logro_bulto;
                    $ttl_porc_alcanzado_bulto                     += $subttl_porc_alcanzado_bulto;
                    $ttl_objetivo_kg                              += $subttl_objetivo_kg;
                    $ttl_logro_kg                                 += $subttl_logro_kg;
                    $ttl_porc_alcanzado_kg                        += $subttl_porc_alcanzado_kg;
                    $ttl_objetivo_ventas_divisas                  += $subttl_objetivo_ventas_divisas;
                    $ttl_logro_ventas_divisas                     += $subttl_logro_ventas_divisas;
                    $ttl_real_dz_dolares                          += $subttl_real_dz_dolares;
                    $ttl_logro_ventas_divisas_pepsico             += $subttl_logro_ventas_divisas_pepsico;
                    $ttl_porcentaje_ventas_divisas_pepsico        += $subttl_porcentaje_ventas_divisas_pepsico;
                    $ttl_logro_ventas_divisas_complementaria      += $subttl_logro_ventas_divisas_complementaria;
                    $ttl_porcentaje_ventas_divisas_complementaria += $subttl_porcentaje_ventas_divisas_complementaria;
                    $ttl_cobranzasRebajadas                       += $subttl_cobranzasRebajadas;

                    $subtotal = array(
                        'ruta'            => "",
                        'maestro'         => $subttl_clientes,
                        'activos'         => $subttl_clientes_activos,
                        'marcas'          => $subttl_activacionBultos,
                        'porc_activacion' => Strings::rdecimal($subttl_porc_activacion),
                        'por_activar'     => $subttl_clientes_noactivos,
                        'visita'          => "",
                        'obj_documentos_mensual'        => Strings::rdecimal($subttl_obj_documentos_mensual, 2),
                        'facturas_realizadas'           => $subttl_facturas_realizadas,
                        'notas_realizadas'              => $subttl_notas_realizadas,
                        'devoluciones_realizadas'       => $subttl_devoluciones_realizadas,
                        'montoendivisa_devoluciones'    => Strings::rdecimal($subttl_montoendivisa_devoluciones,2),
                        'efec_alcanzada_fecha'          => Strings::rdecimal($subttl_efec_alcanzada_fecha, 2),
                        'objetivo_bulto'                => Strings::rdecimal($subttl_objetivo_bulto, 2),
                        'logro_bulto'                   => Strings::rdecimal($subttl_logro_bulto, 2),
                        'porc_alcanzado_bulto'          => Strings::rdecimal($subttl_porc_alcanzado_bulto, 2),
                        'objetivo_kg'                   => Strings::rdecimal($subttl_objetivo_kg, 2),
                        'logro_kg'                      => Strings::rdecimal($subttl_logro_kg, 2),
                        'porc_alcanzado_kg'             => Strings::rdecimal($subttl_porc_alcanzado_kg, 2),
                        'drop_size_divisas'             => Strings::rdecimal($subttl_real_dz_dolares, 2),
                        'objetivo_ventas_divisas'       => Strings::rdecimal($subttl_objetivo_ventas_divisas, 2),
                        'logro_ventas_divisas'          => Strings::rdecimal($subttl_logro_ventas_divisas, 2),
                        'porc_alcanzado_ventas_divisas' => Strings::rdecimal($subttl_porc_alcanzado_kg, 2),
                        'logro_ventas_divisas_pepsico'                 => Strings::rdecimal($subttl_logro_ventas_divisas_pepsico, 2),
                        'porc_alcanzado_ventas_divisas_pepsico'        => Strings::rdecimal($subttl_porcentaje_ventas_divisas_pepsico, 2),
                        'logro_ventas_divisas_complementaria'          => Strings::rdecimal($subttl_logro_ventas_divisas_complementaria, 2),
                        'porc_alcanzado_ventas_divisas_complementaria' => Strings::rdecimal($subttl_porcentaje_ventas_divisas_complementaria, 2),
                        'cobranzas_rebajadas'                          => Strings::rdecimal($subttl_cobranzasRebajadas, 2),
                    );

                    $sub_array['subtotal'] = $subtotal;
                }
                $data[] = $sub_array;
            }

            $total_general = array(
                'ruta'            => "",
                'maestro'         => $ttl_clientes,
                'activos'         => $ttl_clientes_activos,
                'marcas'          => $ttl_activacionBultos,
                'porc_activacion' => Strings::rdecimal($ttl_porc_activacion),
                'por_activar'     => $ttl_clientes_noactivos,
                'visita'          => "",
                'obj_documentos_mensual'        => Strings::rdecimal($ttl_obj_documentos_mensual, 2),
                'facturas_realizadas'           => $ttl_facturas_realizadas,
                'notas_realizadas'              => $ttl_notas_realizadas,
                'devoluciones_realizadas'       => $ttl_devoluciones_realizadas,
                'montoendivisa_devoluciones'    => Strings::rdecimal($ttl_montoendivisa_devoluciones,2),
                'efec_alcanzada_fecha'          => Strings::rdecimal($ttl_efec_alcanzada_fecha, 2),
                'objetivo_bulto'                => Strings::rdecimal($ttl_objetivo_bulto, 2),
                'logro_bulto'                   => Strings::rdecimal($ttl_logro_bulto, 2),
                'porc_alcanzado_bulto'          => Strings::rdecimal($ttl_porc_alcanzado_bulto, 2),
                'objetivo_kg'                   => Strings::rdecimal($ttl_objetivo_kg, 2),
                'logro_kg'                      => Strings::rdecimal($ttl_logro_kg, 2),
                'porc_alcanzado_kg'             => Strings::rdecimal($ttl_porc_alcanzado_kg, 2),
                'drop_size_divisas'             => Strings::rdecimal($ttl_real_dz_dolares, 2),
                'objetivo_ventas_divisas'       => Strings::rdecimal($ttl_objetivo_ventas_divisas, 2),
                'logro_ventas_divisas'          => Strings::rdecimal($ttl_logro_ventas_divisas, 2),
                'porc_alcanzado_ventas_divisas' => Strings::rdecimal($ttl_porc_alcanzado_kg, 2),
                'logro_ventas_divisas_pepsico'                 => Strings::rdecimal($ttl_logro_ventas_divisas_pepsico, 2),
                'porc_alcanzado_ventas_divisas_pepsico'        => Strings::rdecimal($ttl_porcentaje_ventas_divisas_pepsico, 2),
                'logro_ventas_divisas_complementaria'          => Strings::rdecimal($ttl_logro_ventas_divisas_complementaria, 2),
                'porc_alcanzado_ventas_divisas_complementaria' => Strings::rdecimal($ttl_porcentaje_ventas_divisas_complementaria, 2),
                'cobranzas_rebajadas'                          => Strings::rdecimal($ttl_cobranzasRebajadas, 2),
            );
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "cant_marcas"   => count($marcasKpi),
            "tabla"         => $data,
            "total_general" => $total_general,
        );

        echo json_encode($output);
        break;


    case "listar_marcaskpi":
        $output['lista_marcaskpi'] = array_map(function ($arr) { return $arr['descripcion']; }, KpiMarcas::todos());

        echo json_encode($output);
        break;
}