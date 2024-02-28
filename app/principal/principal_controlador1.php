<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("principal_modelo.php");

//INSTANCIAMOS EL MODELO
$principal = new Principal();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_documentos_pordespachar":

            $ffin = date('Y-m-d');
            $dato = explode("-", $ffin); //Hasta
            $aniod=$dato[0]; //año
            $mesd=$dato[1]; //mes
            $diad="01"; //dia
            $fini=$aniod."-01-01";


        $output["por_despachar"] = Strings::rdecimal(count($principal->getDocumentosSinDespachar($fini,$ffin)),0);
        echo json_encode($output);
        break;

    case "buscar_pedidos_porfacturar":

        $output["por_facturar"] = Strings::rdecimal(count($principal->getPedidosSinFacturar()),0);
        echo json_encode($output);
        break;

    case "buscar_cxc":

         $datos =$principal->get_cxc_bs_dolar();
         $suma_montodolar=0;
        foreach ($datos as $row) {

        $suma_montodolar += $row["SaldoPendolar"];

    }


        $output = array(
            "cxc_bs" => Strings::rdecimal($principal->get_cxc_bs()['saldo_bs'],2),
            "cxc_bs_dolar" => Strings::rdecimal( $suma_montodolar,2),
            "cxc_$"  => Strings::rdecimal($principal->get_cxc_dolares()['saldo_dolares'],2),
        );

        echo json_encode($output);
        break;

    case "buscar_ingresoxdia":

        $fecha = date('Y-m-d');
        $datos=$principal->get_ventas_dia($fecha,$fecha);
        $Totaldolar=$Totalbs=$dolar2=$bolivares2=$dolar=$bolivares=0;
        foreach ($datos as $row){

            $bolivares=$row['bolivares'];
            $dolar=$row['dolar'];
        }

        $datos=$principal->get_ventas_dia_dolares($fecha,$fecha);

        foreach ($datos as $row){

            $bolivares2=$row['bolivares'];
            $dolar2=$row['dolar'];
        }

        $Totalbs=$bolivares2+$bolivares;
        $Totaldolar=$dolar2+$dolar;

        $output = array(
            "ingreso_in_bs" => Strings::rdecimal($Totalbs,1),
            "ingreso_in_dolar"  => Strings::rdecimal($Totaldolar,1),
        );

        echo json_encode($output);
        break;

        case "buscar_cxp":

            $output = array(
                "cxp_$"  => Strings::rdecimal($principal->get_cxp_dolares()['saldo_dolares']),
            );
    
            echo json_encode($output);
            break;

    case 'buscar_ventasPormesdivisas':

        $fechaf = $_POST["fecha_actual"];
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $fechai = $aniod . "-01-01";

        $fechaf_ant = ($dato[0]-1).'-'.$dato[1].'-'.$dato[2];
        $fechai_ant = ($dato[0]-1)."-01-01";

         /*echo "<script>console.log('fechai: " . $fechai . "' );</script>";
         echo "<script>console.log('fechaf: " . $fechaf . "' );</script>";
         echo "<script>console.log('fechai_ant: " . $fechai_ant . "' );</script>";
         echo "<script>console.log('fechaf_ant: " . $fechaf_ant . "' );</script>";*/

        //datos del año anterior
        $ventas_fact_anterior = $principal->get_ventas_por_mes_fact($fechai_ant, $fechaf_ant);
        //$ventas_nt_anterior = $principal->get_ventas_por_mes_nota($fechai_ant, $fechaf_ant);


        //datos del año en curso
        $ventas_fact = $principal->get_ventas_por_mes_fact($fechai, $fechaf);
        //$ventas_nt = $principal->get_ventas_por_mes_nota($fechai, $fechaf);
  

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $output = Array();

        if ((is_array($ventas_fact)==true and count($ventas_fact)>0) /*or
            (is_array($ventas_nt)==true and count($ventas_nt)>0)*/)
        {

            $valor_mas_alto = 0;
            $cant_meses = $dato[1];
            $output['anio'] = $aniod;

            //primero obtenemos todas los meses y las inicializamos en cero en una variable temporal
            $temp = array();
            foreach (array($ventas_fact/*, $ventas_nt*/) as $datos) {
                foreach ($datos as $row)
                    $temp[$row['mes']] = 0;
            }

            $ventas_actual = $temp;
            $ventas_anterior = $temp;

            //ahora acumulamos los total ventas de año actual en sus meses
            foreach (array($ventas_fact/*, $ventas_nt*/) as $datos) {
                foreach ($datos as $row) {
                    if (array_key_exists($row['mes'], $ventas_actual)) {

                     
                        if($row['tipo']=='A' or $row['tipo']=='C'){
                            $multiplicador = 1;
                        }else{
                            $multiplicador = -1;
                        }

                        if($row['tipo']=='A' or $row['tipo']=='B'){
                            if($row['factor']!=0){
                                 $ventas_actual[$row['mes']] += (($row['total']*$multiplicador)-$row['descuento'])/$row['factor'];
                            }else{
                                 $ventas_actual[$row['mes']] +=0;
                            }
                           

                        }else{
                            $ventas_actual[$row['mes']] += ($row['total']-$row['descuento'])*$multiplicador;
                        }

                        

                    }
                }

            }


            //asi mismo, acumulamos los total ventas del año anterior en sus meses
            foreach (array($ventas_fact_anterior/*, $ventas_nt_anterior*/) as $datos) {
                foreach ($datos as $row) {
                    if (array_key_exists($row['mes'], $ventas_anterior)) {
                     

                        if($row['tipo']=='A' or $row['tipo']=='C'){
                            $multiplicador = 1;
                        }else{
                            $multiplicador = -1;
                        }

                        if($row['tipo']=='A' or $row['tipo']=='B'){
                            if($row['factor']!=0){
                                $ventas_anterior[$row['mes']] += (($row['total']*$multiplicador)-$row['descuento'])/$row['factor'];
                            }else{
                                 $ventas_anterior[$row['mes']] +=0;
                            }
                            
                        }else{
                          $ventas_anterior[$row['mes']] += ($row['total']-$row['descuento'])*$multiplicador;
                        }
                    }
                }
            }


            $data=array();
            foreach ($ventas_actual as $mes => $total) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array["num_mes"] = intval($mes);
                $sub_array["mes"] = Dates::month_name(Strings::addCero($mes), true);
                $sub_array["valor"] = number_format($total, 2, ".", "");

                //aqui obtenemos el valor mas alto
                if($valor_mas_alto<floatval($total)) {
                    $valor_mas_alto = floatval($total);
                }

                $data['ventas_ano_actual'][] = $sub_array;
            }

            $data1=array();
            foreach ($ventas_anterior as $mes => $total) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array["num_mes"] = intval($mes);
                $sub_array["mes"] = Dates::month_name(Strings::addCero($mes), true);
                $sub_array["valor"] = number_format($total, 2, ".", "");

                //aqui obtenemos el valor mas alto
                if($valor_mas_alto<floatval($total)) {
                    $valor_mas_alto = floatval($total);
                }

                $data1['ventas_ano_anterior'][] = $sub_array;
            }

            $output['cantidad_meses_evaluar'] = intval($cant_meses);
            $output['valor_mas_alto'] = number_format($valor_mas_alto, 2, ".", "");
            $output['datos']=array(); array_push($output['datos'], $data, $data1);
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        echo json_encode($output);
        break;















case 'buscar_ventasPormesbultos':

        $fechaf = $_POST["fecha_actual"];
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $fechai = $aniod . "-01-01";

        $fechaf_ant = ($dato[0]-1).'-'.$dato[1].'-'.$dato[2];
        $fechai_ant = ($dato[0]-1)."-01-01";

        //datos del año anterior
        $ventas_fact_anterior = $principal->get_bultos_por_mes_fact($fechai_ant, $fechaf_ant);
        $ventas_nt_anterior = $principal->get_bultos_por_mes_nota($fechai_ant, $fechaf_ant);
        //datos del año en curso
        $ventas_fact = $principal->get_bultos_por_mes_fact($fechai, $fechaf);
        $ventas_nt = $principal->get_bultos_por_mes_nota($fechai, $fechaf);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $output = Array();
        if ((is_array($ventas_fact)==true and count($ventas_fact)>0) or
            (is_array($ventas_nt)==true and count($ventas_nt)>0))
        {
            $valor_mas_alto = 0;
            $cant_meses = $dato[1];
            $output['anio'] = $aniod;

            //primero obtenemos todas los meses y las inicializamos en cero en una variable temporal
            $temp = array();
            foreach (array($ventas_fact, $ventas_nt) as $datos) {
                foreach ($datos as $row)
                    $temp[$row['mes']] = 0;
            }

            $ventas_actual = $temp;
            $ventas_anterior = $temp;

            //ahora acumulamos los total ventas de año actual en sus meses
            foreach (array($ventas_fact, $ventas_nt) as $datos) {
                foreach ($datos as $row) {
                    if (array_key_exists($row['mes'], $ventas_actual)) {

                        
                        if($row['tipo']=='B' or $row['tipo']=='D'){
                            $multiplicador = -1;
                        }else{
                            $multiplicador = 1;
                        }
                        $ventas_actual[$row['mes']] +=$row['bul']*$multiplicador;
                       
                    }
                }

            }


            //asi mismo, acumulamos los total ventas del año anterior en sus meses
            foreach (array($ventas_fact_anterior, $ventas_nt_anterior) as $datos) {
                foreach ($datos as $row) {
                    if (array_key_exists($row['mes'], $ventas_anterior)) {

                        if($row['tipo']=='B' or $row['tipo']=='D'){
                            $multiplicador = -1;
                        }else{
                            $multiplicador = 1;
                        }
                         $ventas_anterior[$row['mes']] +=$row['bul']* $multiplicador;
                       
                    }
                }
            }


            $data=array();
            foreach ($ventas_actual as $mes => $total) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array["num_mes"] = intval($mes);
                $sub_array["mes"] = Dates::month_name(Strings::addCero($mes), true);
                $sub_array["valor"] = number_format($total, 2, ".", "");

                //aqui obtenemos el valor mas alto
                if($valor_mas_alto<floatval($total)) {
                    $valor_mas_alto = floatval($total);
                }

                $data['ventas_ano_actual'][] = $sub_array;
            }

            $data1=array();
            foreach ($ventas_anterior as $mes => $total) {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $sub_array["num_mes"] = intval($mes);
                $sub_array["mes"] = Dates::month_name(Strings::addCero($mes), true);
                $sub_array["valor"] = number_format($total, 2, ".", "");

                //aqui obtenemos el valor mas alto
                if($valor_mas_alto<floatval($total)) {
                    $valor_mas_alto = floatval($total);
                }

                $data1['ventas_ano_anterior'][] = $sub_array;
            }

            $output['cantidad_meses_evaluar'] = intval($cant_meses);
            $output['valor_mas_alto'] = number_format($valor_mas_alto, 2, ".", "");
            $output['datos']=array(); array_push($output['datos'], $data, $data1);
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        echo json_encode($output);
        break;


















    case "listar_inventario_valorizado":

        $depos = [
            '01',
            '02',
            '03',
            '07',
            '08',
            '14',
            '100',
        ];
//        $depos =  array_map(function($val) { return $val['codubi']; }, Almacen::todos());

        $datos = $principal->get_inventario_valorizado($depos);

        //DECLARAMOS ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $key => $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            //ASIGNAMOS EN EL SUB_ARRAY LOS DATOS PROCESADOS

            if($row["almacen"]=='01'){
                    $almacen='Principal';
            }else{
                if($row["almacen"]=='02'){
                        $almacen='Dev Proveedor';
                }else{
                    if($row["almacen"]=='03'){
                        $almacen='CONFISUR';
                    }else{
                        if($row["almacen"]=='07'){
                            $almacen='Mala Calidad Parmalat';

                        }else{
                            if($row["almacen"]=='08'){
                                $almacen='Dev Proveedor';
                            }else{
                                if($row["almacen"]=='14'){
                                $almacen='Muestra Dev';
                               } else{
                                    if($row["almacen"]=='100'){
                                    $almacen='Devolucion Proveedor';
                                   }               
                                }               
                            }              
                        }
                    }
                }
            }
                        $dato_factor = $principal->get_tasa_dolar();
                        foreach ($dato_factor as $rowa) {
                            $factor = $rowa["tasa"];
                            }

            $sub_array['almacen']   = $almacen;
            $sub_array['total']     = Strings::rdecimal(floatval($row["total_b"]/ $factor) + floatval($row["total_p"]/ $factor),2);
            $sub_array['acciones']  = '<div class="col text-center">
                                    <button type="button" onClick="modalVerDetalleAlmacen(\'' . $row["almacen"] . '\');" id="' . $row["almacen"] . '" class="btn btn-info btn-sm ver_detalles"><i class="fas fa-search"></i></button>' . " " . '
                                </div>';
            
            
            /*'<a href="#" class="text-muted">
                                         <i class="fas fa-search"></i>
                                       </a>';*/

            //AGREGAMOS AL ARRAY DE CONTENIDO DE LA TABLA
            $data[] = $sub_array;
        }

        //al terminar, se almacena en una variable de salida el array.
        $output['contenido_tabla'] = $data;

        echo json_encode($output);
        break;

    case "buscar_clientes":

        #tipo 1 = naturales
        #tipo 0 = juridicos
        $output["cant_naturales"] = Strings::rdecimal(count($principal->get_clientes_por_tipo(1)),0);
        $output["cant_juridico"]  = Strings::rdecimal(count($principal->get_clientes_por_tipo(0)),0);
        echo json_encode($output);
        break;


    case "buscar_documentos":

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mes = $dato[1]; //año
        $fechai = $aniod."-".$mes."-01";

        $fechai = $fechai.' 00:01:01.000';
        $fechaf = $fechaf.' 21:59:01.000';

        #tipo A = facturas
        #tipo C = notas
        $output["cant_factura"] = Strings::rdecimal(count($principal->get_n_documento($fechai,$fechaf,"A")),0);
        $output["cant_nentrega"]  = Strings::rdecimal(count($principal->get_n_documento($fechai,$fechaf,"C")),0);
        echo json_encode($output);
        break;

    case "buscar_tasa_dolar":

          $dato_factor = $principal->get_tasa_dolar();
                        foreach ($dato_factor as $rowa) {
                            $factor = $rowa["tasa"];
                            }

        $output["tasa"] = Strings::rdecimal( $factor,2);
        echo json_encode($output);
        break;

    case "buscar_devoluciones_sin_motivo":

        $data = array(
            'sin_despacho_fact' => Numbers::avoidNull(count($principal->get_devoluciones_sin_motivo_Factura('0'))),
            'con_despacho_fact' => Numbers::avoidNull(count($principal->get_devoluciones_sin_motivo_Factura('1'))),
            'sin_despacho_nota' => Numbers::avoidNull(count($principal->get_devoluciones_sin_motivo_NotadeEntrega('0'))),
            'con_despacho_nota' => Numbers::avoidNull(count($principal->get_devoluciones_sin_motivo_NotadeEntrega('1'))),
        );

        $sumatoria_devoluciones_fact = (intval($data['sin_despacho_fact']) + intval($data['con_despacho_fact']));
        $sumatoria_devoluciones_nota = (intval($data['sin_despacho_nota']) + intval($data['con_despacho_nota']));
        $total = ($sumatoria_devoluciones_fact + $sumatoria_devoluciones_nota);

        $output["devoluciones_sin_motivo"] = Strings::rdecimal($total,0);
        echo json_encode($output);
        break;

    case 'listar_ventas_por_marca':

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $fechai = $aniod . "-" .$mesd. "-01";
         $multiplicador =0;

         $datos_fact = $principal->get_ventas_por_marca_fact($fechai, $fechaf);

           
         $datos_nota = $principal->get_ventas_por_marca_nota($fechai, $fechaf);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $output = Array();
        if (is_array($datos_fact)==true and count($datos_fact)>0
            or
            is_array($datos_nota)==true and count($datos_nota)>0)
        {
            //primero obtenemos todas las marcas y las inicializamos en cero
            $marcas = array();
            foreach (array($datos_fact, $datos_nota) as $datos) {
                foreach ($datos as $row){

                     $marcas[$row['marca']] = 0;
                }
               /* if($row['tipo']=='B' or $row['tipo']=='D'){
                    
                }else{
                        $marcas[$row['marca']] = 0;
                }*/
                    

            }

            //ahora acumulamos los montod en sus marcas con el objetivo de hacer top 10 marcas mas vendidas
            foreach (array($datos_fact, $datos_nota) as $datos) {
                foreach ($datos as $row){

                     
                        if($row['tipo']=='B' or $row['tipo']=='D'){
                            $multiplicador = -1;
                        }else{
                            $multiplicador = 1;
                        }
                 
                    $marcas[$row['marca']] +=($row['montod']*$multiplicador);
                   

                }
               
            }
            array_multisort($marcas, SORT_DESC); //ordena descendientemente por el monto
            $marcas = array_slice($marcas, 0, 10); //trunca el array con los primero 10

            $output['fecha'] = Dates::month_name($mesd)." ".$aniod;
            $output['marcas'] = $marcas;
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        echo json_encode($output);
        break;



         case 'listar_ventas_por_productos':

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $fechai = $aniod . "-" .$mesd. "-01";

        //datos del año en curso
        $datos_fact = $principal->get_ventas_por_productos_fact($fechai, $fechaf);
        $datos_nota = $principal->get_ventas_por_productos_nota($fechai, $fechaf);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $output = Array();
        if (is_array($datos_fact)==true and count($datos_fact)>0
            or
            is_array($datos_nota)==true and count($datos_nota)>0)
        {
            //primero obtenemos todas las marcas y las inicializamos en cero
            $marcas = array();
            foreach (array($datos_fact, $datos_nota) as $datos) {
                foreach ($datos as $row)
                if($row['montod']>0){
                    $marcas[$row['marca']] = 0;
                }
            }

            //ahora acumulamos los montod en sus marcas con el objetivo de hacer top 10 marcas mas vendidas
            foreach (array($datos_fact, $datos_nota) as $datos) {
                foreach ($datos as $row)
                if($row['montod']>0){

                    $marcas[$row['marca']] += $row['montod'];

                }
                    
            }
            array_multisort($marcas, SORT_DESC); //ordena descendientemente por el monto
            $marcas = array_slice($marcas, 0, 10); //trunca el array con los primero 10

            $output['fecha'] = Dates::month_name($mesd)." ".$aniod;
            $output['marcas'] = $marcas;
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        echo json_encode($output);
        break;



    case 'listar_ventas_por_clientes':

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $fechai = $aniod . "-" .$mesd. "-01";

        //datos del año en curso
        $datos_fact = $principal->get_ventas_clientes_fact($fechai, $fechaf);
        $datos_nota = $principal->get_ventas_cliente_nota($fechai, $fechaf);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $output = Array();
        if (is_array($datos_fact)==true and count($datos_fact)>0
            or
            is_array($datos_nota)==true and count($datos_nota)>0)
        {
            //primero obtenemos todas las marcas y las inicializamos en cero
            $clientes = $temp = array();
            foreach (array($datos_fact, $datos_nota) as $datos) {
                foreach ($datos as $row)
                if($row['montod']>0){

                   $temp[$row['codclie']] = 0;

                }
                    
            }

            //ahora acumulamos los montod en sus marcas con el objetivo de hacer top 10 marcas mas vendidas
            foreach (array($datos_fact, $datos_nota) as $datos) {
                foreach ($datos as $row)
                if($row['montod']>0){

                   $temp[$row['codclie']] += ($row['montod']);

                }
                   
            }
            array_multisort($temp, SORT_DESC); //ordena descendientemente por el monto
            $temp = array_slice($temp, 0, 10); //trunca el array con los primero 10

            # hasta este punto el array $temp posee un array asociativo array(codclie => montod), como nos falta obtener la descripcion
            # se reprocesara la data para obtenerlo en el orden del top 10

            # recorremos todo el top10
            foreach ($temp as $codclie => $montod) {
                #creamos una variable de bandera(flag) que funcionara para frenar
                #la iteracion de los 2 foreach a continuacion cuando coincida los codclie
                $flag_break = false;
                foreach (array($datos_fact, $datos_nota) as $datos) {
                    foreach ($datos as $row) {
                        # condicion para optener el registro
                        if ($row['codclie'] == $codclie) {
                            #en caso que lo encuentre se situa en true
                            $flag_break = true;

                            # vamos creando el nuevo array con la data de $temp
                            # conservando el orden del top10
                            $clientes[$codclie] = array(
                                'descrip' => $row['Descrip'],
                                'montod'  => $montod,
                            );
                        }
                        if ($flag_break) break;
                    }
                    if ($flag_break) break;
                }
            }


            $output['fecha'] = Dates::month_name($mesd)." ".$aniod;
            $output['clientes'] = $clientes;
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        echo json_encode($output);
        break;

    case "buscar_total_ventas_mes_encurso":

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $fechai = $aniod . "-" .$mesd. "-01";
        $descuentos =$totalbs=$totald=$totalvendido= 0;

        $output['fecha'] = ucwords(strtolower(Dates::month_name($mesd)));
        $datos24= $principal->get_total_ventas($fechai, $fechaf);

        foreach ($datos24 as $row24) {

            $multiplicador = in_array($row24['tipo'], array('A','C'))
                    ? 1
                    : -1;
 
           $totald += ($row24["Vendido"]*$multiplicador);
        }


       $datos1= $principal->get_total_ventasbolivares($fechai, $fechaf);

        foreach ($datos1 as $row1) {

            $multiplicador = in_array($row1['tipo'], array('A','C'))
                    ? 1
                    : -1;

          $totalbs += ($row1["Vendidobs"]*$multiplicador/$row1["factor"]); 
        }

 /**echo "<script>console.log('Console: " . $totald . "' );</script>";
  echo "<script>console.log('Console: " . $totalbs . "' );</script>";*/

        $output["total"] = number_format(($totald) + ($totalbs), 2, ",", "");
        echo json_encode($output);
        break;



        case "buscar_total_dev_mes_encurso":

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $fechai = $aniod . "-" .$mesd. "-01";
        $descuentos =$totalbs=$totald=$totalvendido= 0;

        $output['fecha'] = ucwords(strtolower(Dates::month_name($mesd)));
        //$datos24= $principal->get_total_devoluciones($fechai, $fechaf);

         $datos24= $principal->get_master();

        foreach ($datos24 as $row24) {
 
        $totald += floatval($row24["Devoluciones"]/**$multiplicador*/);
        
        }

        $output["total"] = number_format(($totald), 2, ",", "");
        echo json_encode($output);
        break;




         case "buscar_total_real_mes_encurso":

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $fechai = $aniod . "-" .$mesd. "-01";
        $descuentos =$totalbs=$totald=$totalvendido= 0;

        $output['fecha'] = ucwords(strtolower(Dates::month_name($mesd)));

         $datos24= $principal->get_master();

        foreach ($datos24 as $row24) {
 
        $totald += floatval($row24["Total"]/**$multiplicador*/);
        
        }


        $output["total"] = number_format(($totald), 2, ",", "");
        echo json_encode($output);
        break;




         case "buscar_total_descuento_mes_encurso":

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $fechai = $aniod . "-" .$mesd. "-01";
        $descuentos =$totalbs=$totald=$totalvendido= 0;

        $output['fecha'] = ucwords(strtolower(Dates::month_name($mesd)));
        //$datos24= $principal->get_total_descuentos("A",$fechai, $fechaf);

        $datos24= $principal->get_master();

        foreach ($datos24 as $row24) {
 
        $totald += floatval($row24["Descuento"]);
        }


      /* $datos1= $principal->get_total_descuentos("C",$fechai, $fechaf);

        foreach ($datos1 as $row1) {

          $totalbs += ($row1["descuento"]//$row1["factor"]); 
        }*/


        $output["total"] = number_format(($totald) + ($totalbs), 2, ",", "");
        echo json_encode($output);
        break;




     case "buscar_detalles_almacenes":


        $almacen = $_POST["correlativo"];

         $datos = $principal->get_detalle_almacen($almacen);
        $data = Array();
        $total_cantidadb = $total_cantidadp= $total_b = $total_p = $total=0;

         $i=1;

         $tasa =$principal->get_tasa_dolar();

         foreach ($tasa as $tasa_factor) {
             $factor=$tasa_factor['tasa'];
         } 


         foreach ($datos as $row) {

            $sub_array = array();

             $datos72 = $principal->get_detalle_almacen_producto($almacen,$row['instancia']);

             $sub_array[]   = $i;
             $sub_array[]   = $row["instancia"];

             $cdisplay = $cantidad_b= $cantidad_p= $valor_bulto= $valor_paq= $valor_t = 0;
                
                foreach ($datos72 as $row72) {


                    if ($row72['display'] == 0) {
                        $cdisplay = 0;
                    } else {
                        $cdisplay = $row72['costo'] / $row72['display'];
                    }

                        $cantidad_b   += Strings::rdecimal((floatval($row72["cantidad_b"])),2);
                        $cantidad_p   += Strings::rdecimal((floatval($row72["cantidad_p"])),2);
                        $valor_bulto   += ((($row72['costo'] /$factor )* $row72['cantidad_b']));
                        $valor_paq   += number_format((( $cdisplay /$factor)* $row72['cantidad_p'])  ,2);
                        $valor_t   += ((($row72['costo'] /$factor )* $row72['cantidad_b']) + (( $cdisplay /$factor)* $row72['cantidad_p']));

                        

                            $total_b += floatval((($row72['costo'] /$factor )* $row72['cantidad_b'])) ;
                            $total_p += floatval((( $cdisplay/$factor)* $row72['cantidad_p']));
                            $total += floatval((($row72['costo'] /$factor )* $row72['cantidad_b']) + (( $cdisplay /$factor)* $row72['cantidad_p']));
                            $total_cantidadb += floatval($row72["cantidad_b"]) ; 
                            $total_cantidadp+= floatval($row72["cantidad_p"]) ;
                
                    } 

                        $sub_array[]   = $cantidad_b;
                        $sub_array[]   = $cantidad_p;
                        $sub_array[]   = number_format($valor_bulto,2);
                        $sub_array[]   = number_format($valor_paq,2);
                        $sub_array[]   = number_format($valor_t,2);

            
             $data[] = $sub_array;
             $i+=1;
         }   




       
        
  

        //al terminar, se almacena en una variable de salida el array.
      //  $output['contenido'] = $data;

         $output["total_b"] =  Strings::rdecimal((($total_b)),2);
         $output["total_p"] =  Strings::rdecimal((($total_p)),2);
         $output["total"] =  Strings::rdecimal((($total)),2);
         $output["cantidad_b"] =  Strings::rdecimal((($total_cantidadb)),2);
         $output["cantidad_p"] =  Strings::rdecimal((($total_cantidadp)),2);

         $output['tabla'] = array(
            "sEcho" => 1, # INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);

        echo json_encode($output);


        break;





        case "buscar_detalles_ventas":


        $anno = $_POST["i"];
        

        if($anno == date("Y")){

            $fechaf =  date("Y-n-d");
            $fechai = $anno . "-1-1";

            $f=date("n");

        }else{

            $fechaf =  $anno . "-12-31";
            $fechai = $anno . "-1-1";

            $f=12;

        }

  $total_ventas_fact=$total_ventas_notas= $total_bul =$total_u= $total_paq=$total=0;

for ($i = 1; $i <= $f; $i++) {

   
        $bulto_fact = $principal->get_bultos_detalles_fact($fechai, $fechaf, $i);
        $bulto_nota = $principal->get_bultos_detalles_nota($fechai, $fechaf, $i);

        $ventas_fact_a = $principal->get_ventas_detalle_fact($fechai, $fechaf, $i);
        $ventas_nota_a = $principal->get_ventas_detalle_nota($fechai, $fechaf, $i);

      
$cantidad =$total_ventas= $total_unidad=  $bul = $paq = $ventas_nota= $ventas_fact= $paq_nota =  $bul_nota = 0; 

         if($i==1){

            $string='ENERO';

         }else{

                if($i==2){
                    $string='FEBRERO';
                }else{


                    if($i==3){
                          $string='MARZO';      
                    }else{

                        if($i==4){
                            $string='ABRIL';
                        }else{

                            if($i==5){
                                $string='MAYO';
                            }else{

                                if($i==6){
                                    $string='JUNIO';
                                }else{

                                    if($i==7){
                                        $string='JULIO';
                                    }else{

                                        if($i==8){
                                            $string='AGOSTO';
                                        }else{

                                            if($i==9){
                                                $string='SEPTIEMBRE';
                                            }else{

                                                if($i==10){
                                                    $string='OCTUBRE';
                                                }else{

                                                    if($i==11){
                                                        $string='NOVIEMBRE';
                                                    }else{

                                                        if($i==12){
                                                            $string='DICIEMBRE';
                                                        }else{
                                                                  $string='';
                                                        }   
                                                            
                                                    }
                                                    
                                                }
                                                
                                            }
                                            
                                        }
                                        
                                    }
                                    
                                }
                                    
                            }
                                                    
                        }
                        
                    }      
                    
                }

         }

        $sub_array = array();
        foreach ($bulto_fact as $row) {

            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        

         if($row['tipo']=='B' or $row['tipo']=='D'){
                            $multiplicador = -1;
           }else{
                            $multiplicador = 1;
           }

         if($row['unid']=='PAQ'){
                        $paq += $row['bul']*$multiplicador;
           }else{
                         $bul += $row['bul']*$multiplicador;
           }

       
    }

    foreach ($bulto_nota as $row_nota) {

            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        

         if($row_nota['tipo']=='B' or $row_nota['tipo']=='D'){
                            $multiplicador = -1;
           }else{
                            $multiplicador = 1;
           }

           if($row_nota['unid']=='PAQ'){
                        $paq_nota += $row_nota['bul']*$multiplicador;
           }else{
                         $bul_nota += $row_nota['bul']*$multiplicador;
           }

        

       
    }

        foreach ($ventas_fact_a as $row_ventas_fact) {

            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        

         if($row_ventas_fact['tipo']=='B' or $row_ventas_fact['tipo']=='D'){
                            $multiplicador = -1;
           }else{
                            $multiplicador = 1;
           }

         
              $ventas_fact += $row_ventas_fact['total']*$multiplicador;
      

       
        }


         foreach ($ventas_nota_a as $row_ventas_nota) {

            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
        

         if($row_ventas_nota['tipo']=='B' or $row_ventas_nota['tipo']=='D'){
                            $multiplicador = -1;
           }else{
                            $multiplicador = 1;
           }

         
              $ventas_nota += $row_ventas_nota['total']*$multiplicador;
      

       
        }

        $total_ventas_fact+= $ventas_fact;

        $total_ventas_notas+= $ventas_nota;
    


        $total_bul += $bul+$bul_nota;
       
        $total_paq+=$paq+$paq_nota;

        $total+=$ventas_fact+$ventas_nota;

        $total_u+=$bul+$bul_nota+$paq+$paq_nota;

        if($i <10){
            $sub_array[] = '0'.$i.' '.$string;
        }else{
             $sub_array[] = $i.' '.$string;
        }

       

        $sub_array[] = number_format($ventas_fact,2,',','.');
        $sub_array[] = number_format($ventas_nota,2,',','.');

        $sub_array[] = number_format($paq+$paq_nota,2,',','.');
        $sub_array[] = number_format($bul+$bul_nota,2,',','.');

        $sub_array[] = number_format($ventas_fact+$ventas_nota,2,',','.');
         $sub_array[] = number_format($bul+$bul_nota+$paq+$paq_nota,2,',','.');
       

            $data[] = $sub_array;

}

         $output["total_ventas_fact"] =  Strings::rdecimal((($total_ventas_fact)),2);
         $output["total_ventas_notas"] =  Strings::rdecimal((($total_ventas_notas)),2);
         $output["total_bul"] =  Strings::rdecimal((($total_bul)),2);
         $output["total_paq"] =  Strings::rdecimal((($total_paq)),2);
         $output["total_u"] =  Strings::rdecimal((($total_u)),2);
         $output["total_dolar"] =  Strings::rdecimal((($total)),2);

         $output['tabla'] = array(
            "sEcho" => 1, # INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), # ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);

        echo json_encode($output);


        break;
}
