<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO
require_once("despachos_modelo.php");

//INSTANCIAMOS EL MODELO
$despachos  = new Despachos();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "obtener_pesomaxvehiculo":

        $peso_max_vehiculo = Vehiculo::getById($_POST["id"]);

        if (is_array($peso_max_vehiculo)==true and count($peso_max_vehiculo)) {
            $output["capacidad"] = $peso_max_vehiculo[0]["capacidad"];
            $output["cubicajeMax"] = $peso_max_vehiculo[0]["volumen"];
        } else {
            $output["capacidad"] = 0;
            $output["cubicajeMax"] = 0;
        }

        echo json_encode($output);
        break;

    case "obtener_pesoporfactura":

        $peso_acum = str_replace(",", ".", $_POST["peso_acum_facturas"]);
        $peso_max  = str_replace(",", ".", $_POST["peso_max_vehiculo"]);
        $cubicaje_acum  = str_replace(",", ".", $_POST["cubicaje_acum_facturas"]);
        $cubicaje_max  = str_replace(",", ".", $_POST["cubicaje_max_vehiculo"]);

        isset($_POST["eliminarPeso"]) ? $eliminarPeso = true : $eliminarPeso = false;

        $datos = $despachos->getPesoTotalporFactura($_POST["numero_fact"]);

        $peso = 0;
        $cubicaje = 0;
        if (count($datos) != 0){
            foreach ($datos as $dato) {
                if($dato['tipofac'] == "A") {
                    ($dato['unidad'] == 0) ? ($peso += ($dato['peso'] * $dato['cantidad'])) : ($peso += (($dato['peso'] / $dato['paquetes']) * $dato['cantidad'])) ;
                }
                $cubicaje += $dato['cubicaje'];
            }
        }
        //agrega formato a el peso y al cubicaje
        $peso = Strings::rdecimal($peso, 2, ".", "");
        $cubicaje = Strings::rdecimal($cubicaje, 2, ".", "");
        $porcentajePeso = "1";

        //consulta si deseamos eliminar el peso de la factura actual
        if($eliminarPeso){
            //calcula el porcenaje del peso tras eliminar
            $porcentajePeso = strval(( (floatval($peso_acum) - floatval($peso)) * 100) / floatval($peso_max) );

            //calcula el porcentaje del cubicaje tras eliminar
            $porcentajeCubicaje = strval(( (floatval($cubicaje_acum) - floatval($cubicaje)) * 100) / floatval($cubicaje_max) );

            //asigna el peso y cubicaje acumulado eliminandole el peso y volumen de una factura especifica
            $output["pesoNuevoAcum"] = strval(floatval($peso_acum) - floatval($peso));
            $output["cubicajeNuevoAcum"] = strval(floatval($cubicaje_acum) - floatval($cubicaje));

        }
        //sino, consulta si el peso nuevo + el peso acumulado es < que el peso total del camion
        else if( (floatval($peso) + floatval($peso_acum) ) < floatval($peso_max) ){

            //calcula el porcentaje del peso a agregar
            $porcentajePeso = strval(((floatval($peso) + floatval($peso_acum)) * 100) / floatval($peso_max) );

            //calcula el porcentaje de cubicaje a agregar
            $porcentajeCubicaje = strval(( (floatval($cubicaje_acum) + floatval($cubicaje)) * 100) / floatval($cubicaje_max) );

           //asigna el peso y cubicaje nuevo + el acumulado
            $output["pesoNuevoAcum"] = strval(floatval($peso) + floatval($peso_acum));
            $output["cubicajeNuevoAcum"] = strval(floatval($cubicaje_acum) + floatval($cubicaje));
            $output["pesoDeFactura"] = floatval($peso);
            $output["cond"] = "true";
        }
        //sino, solo devuelve el acumulado anterior y avisa que el acumulado supera al maximo de carga con la cond
        else {
            $porcentajePeso = strval((floatval($peso_acum) * 100) / floatval($peso_max) );
            $porcentajeCubicaje = strval((floatval($cubicaje_acum) * 100) / floatval($cubicaje_max) );
            $output["pesoNuevoAcum"] = $peso_acum;
            $output["cubicajeNuevoAcum"] = $cubicaje_acum;
            $output["cond"] = "false";
        }

        //evaluacion del color de la barra de progreso del peso acumulado
        $bgProgress = "";
        if(floatval($porcentajePeso) >= 0 && floatval($porcentajePeso) <70){
            $bgProgress = "bg-success";
        } elseif(floatval($porcentajePeso) >= 70 && floatval($porcentajePeso) <90){
            $bgProgress = "bg-warning";
        }elseif (floatval($porcentajePeso) >= 90 && floatval($porcentajePeso) <=100){
            $bgProgress = "bg-danger";
        }
        $output["porcentajePeso"] = $porcentajePeso;
        $output["porcentajeCubicaje"] = $porcentajeCubicaje;
        $output["bgProgreso"] = $bgProgress;

        echo json_encode($output);
        break;

    case "obtener_facturasporcargardespacho":

        $array = explode(";", substr($_POST["registros_por_despachar"], 0, -1));

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($array as $row){

            $datos = $despachos->getFactura($row);
            $pesodefact = $despachos->getPesoTotalporFactura($row);

            $peso = 0;
            $cubicaje = 0;
            if (count($pesodefact) != 0){
                foreach ($pesodefact as $dato) {
                    if($dato['tipofac'] == "A") {
                        ($dato['unidad'] == 0) ? ($peso += ($dato['peso'] * $dato['cantidad'])) : ($peso += (($dato['peso'] / $dato['paquetes']) * $dato['cantidad']));
                    }
                    $cubicaje += $dato['cubicaje'];
                }
            }

            $sub_array = array();

            $sub_array[] = $datos[0]["numerod"];
            $sub_array[] = date(FORMAT_DATE, strtotime($datos[0]["fechae"]));
            $sub_array[] = $datos[0]["descrip"];
            $sub_array[] = $datos[0]["direc2"];
            $sub_array[] = $datos[0]["codvend"];
            $sub_array[] = Strings::rdecimal($datos[0]["mtototal"], 2);
            $sub_array[] = Strings::rdecimal($peso, 2);
            $sub_array[] = Strings::rdecimal($cubicaje, 2);
            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="eliminar(\''.$datos[0]["numerod"].'\');" name="eliminar" id="eliminar" class="btn btn-danger btn-sm eliminar">Eliminar</button>
                            </div>';

            $data[] = $sub_array;
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);
        echo json_encode($results);
        break;

    case "buscar_facturaEnDespachos_modal":

        $datos = $despachos->getFacturaEnDespachos($_POST['nrfactb']);

        $output = array();
        //verificamos que exista datos de la consulta
        if(is_array($datos) == true && count($datos) > 0) {
            //creamos un array para almacenar los datos procesados
            $data = Array();
            $data['nrfactb'] = $_POST['nrfactb'];
            $data['Correlativo'] = str_pad($datos[0]['Correlativo'], 8, 0, STR_PAD_LEFT);
            $data['fechae'] = date(FORMAT_DATETIME2, strtotime($datos[0]['fechae']));
            $data['Destino'] = $datos[0]["Destino"]." - ".$datos[0]["NomperChofer"];

            //al terminar, se almacena en una variable de salida el array.
            $output['factura_en_despacho'] = $data;

            //verificamos si la consulta tiene registro de pagos
            if (isset($datos[0]['fecha_liqui']) AND isset($datos[0]['monto_cancelado'])) {
                //creamos un array para almacenar los datos procesados
                $data1 = array();
                $data1['fecha_liqui'] = date(FORMAT_DATE, strtotime($datos[0]['fecha_liqui']));
                $data1['monto_cancelado'] = Strings::rdecimal($datos[0]['monto_cancelado'], 1) . " BsS";

                //al terminar, se almacena en una variable de salida el array.
                $output['datos_pago'] = $data1;
            }
        }
        echo json_encode($output);
        break;

    case "buscar_facturaendespacho":

        $datos = $despachos->getExisteFacturaEnDespachos($_POST["numero_fact"]);

        if(is_array($datos) == true && count($datos) > 0) {
            ($output["mensaje"] = "El Numero de Factura: ". $datos[0]['Numerod'] . " Ya Fue Agregado en Otro Despacho");
        } else {
            ($output["mensaje"] = "");
        }

        echo json_encode($output);
        break;

    case "listar_chofer_vehiculo":

        $output["lista_choferes"] = Choferes::todos();
        $output["lista_vehiculos"] = Vehiculo::todos();

        echo json_encode($output);
        break;

    case "buscar_existefactura":

        $aux = 0;

        //consultamos si la factura existe en la bd
        $datos = $despachos->getFactura($_POST["numero_fact"]);

        //consultamos si la factura existe en el despacho por crear
        if(isset($_POST["registros_por_despachar"])){

            $array = explode(";", substr($_POST["registros_por_despachar"], 0, -1));

            for ($x = 0; $x < count($array); $x++) {
                if ($array[$x] === $_POST["numero_fact"]) {
                    $aux++;
                }
            }
        }


        if(count($datos) == 0){
            $output["mensaje"] = "El Numero de Factura " . $_POST["numero_fact"] . " No Existe en Sistema";
        }
        else if ($aux !== 0){
            $output["mensaje"] = "El Numero de Factura: " . $_POST["numero_fact"] . ", Ya fue Agregado";
        }
        else {
            $output["mensaje"] = "";
        }

        echo json_encode($output);
        break;

    case "registrar_despacho":

        $creacionDespacho = false;
        $creacionDetalleDespacho = true;

        if(isset($_POST["documentos"])) {
            $array = explode(";", substr($_POST["documentos"], 0, -1));
        }

        $values = array(
            'fechad'   => $_POST["fechad"],
            'chofer'   => $_POST["chofer"],
            'vehiculo' => $_POST["vehiculo"],
            'destino'  => $_POST["destino"],
            'usuario'  => $_POST["usuario"],
        );

        $creacionDespacho = $despachos->insertarDespacho($values);

        if ($creacionDespacho != -1) {
            foreach ($array AS $item) {
                $insertDet = $despachos->insertarDetalleDespacho($creacionDespacho, $item, 'A');
                if (!$insertDet) {
                    $creacionDetalleDespacho = false;
                }
            }
        }

        # datos
        $vehiculo = ArraysHelpers::validateWithPos(Vehiculo::getById($values['vehiculo']), 0);
        $chofer   = ArraysHelpers::validateWithPos(Choferes::getByDni($values['chofer']), 0);

        if ($creacionDespacho && $creacionDetalleDespacho)
        {
            /**  enviar correo: despachos_visual **/
            # preparamos los datos a enviar
            $dataEmail = EmailData::DataCreacionDeDespacho(
                array(
                    'usuario' => $_SESSION['login'],
                    'correl_despacho' => $correlativo,
                    'vehiculo' => $vehiculo['placa']." ".$vehiculo['modelo']." ".$vehiculo['capacidad']."Kg",
                    'destino'  => $values['destino'],
                    'chofer'   => $chofer['Nomper'],
                    'fechad'   => $values['fechad'],
                )
            );

            # enviar correo
            $status_send = Email::send_email(
                $dataEmail['title'],
                $dataEmail['body'],
                $dataEmail['recipients'],
            );

            /*$cad_cero = "";
            for($i=0; $i<(8-intval($creacionDespacho)); $i++)
                $cad_cero.='0';*/

//            $output["mensaje"] = "SE HA CREADO UN NUEVO DESPACHO NRO: " . ($cad_cero.$creacionDespacho);
            $output["mensaje"] = "SE HA CREADO UN NUEVO DESPACHO NRO: " . (str_pad($creacionDespacho, 8, 0, STR_PAD_LEFT));
            $output["icono"] = "success";
            $output["correl"] = $creacionDespacho;
        } else {
            $output["mensaje"] = "ERROR AL CREAR ESTE DESPACHO";
            $output["icono"] = "error";
            $output["correl"] = 0;
        }

        echo json_encode($output);
        break;

    case "listar_despacho":

        //correlativo
        $correlativo = $_POST["correlativo"];

        //obtenemos los registros de los productos en dichos documentos
        $datos = $despachos->getProductosDespachoCreado($correlativo);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        $total_bultos = 0;
        $total_paq = 0;
        foreach ($datos as $row) {

            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            //REALIZAMOS PROCESOS DE CALCULO
            $bultos = 0;
            $paq = 0;
            if ($row["BULTOS"] > 0){
                $bultos = $row["BULTOS"];
            }
            if ($row["PAQUETES"] > 0){
                $paq = $row["PAQUETES"];
            }

            if ($row["EsEmpaque"] != 0){
                if ($row["PAQUETES"] > $row["CantEmpaq"]){

                    if ($row["CantEmpaq"] != 0) {
                        $bultos_total = $row["PAQUETES"] / $row["CantEmpaq"];
                    }else{
                        $bultos_total = 0;
                    }
                    $decimales = explode(".",$bultos_total);
                    $bultos_deci = $bultos_total - $decimales[0];
                    $paq = $bultos_deci * $row["CantEmpaq"];
                    $bultos = $decimales[0] + $bultos;
                }
            }
            $total_bultos += $bultos;
            $total_paq += $paq;

            //agregamos al sub array
            $sub_array[] = $row["CodItem"];
            $sub_array[] = $row["Descrip"];
            $sub_array[] = round($bultos);
            $sub_array[] = round($paq);

            //agregamos un registro al array principal
            $data[] = $sub_array;
        }

        $totales = array(
            "total_bultos"  => $total_bultos,
            "total_paq"     => $total_paq,
        );

        //al terminar, se almacena en una variable de salida el array.
        $output['contenido_tabla'] = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);

        //de igual forma, se almacena en una variable de salida el array de totales.
        $output['totales_tabla'] = $totales;

        echo json_encode($output);
        break;

    case "listar_mercancia_por_despachar":

        //verificamos si existe al menos 1 deposito selecionado
        $depos = $_POST['depo'] ?? array();

        $fechaf = date('Y-m-d');
        $dato = explode("-", $fechaf); //Hasta
        $aniod = $dato[0]; //año
        $mesd = $dato[1]; //mes
        $diad = "01"; //dia
        $fechai = $aniod . "-01-01";


        $coditem = $cantidad = $tipo = array();
        $t = 0;

        $devolucionesDeFactura = Factura::getInvoiceReturns($fechai, $fechaf, $depos);
        if(count($devolucionesDeFactura) > 0) {
            foreach ($devolucionesDeFactura as $devol) {
                $coditem[] = $devol['coditem'];
                $cantidad[] = $devol['cantidad'];
                $tipo[] = $devol['esunid'];
                $t += 1;
            }
        }

        $datos = $despachos->getMercanciaSinDespachar($fechai, $fechaf, $depos);
        $tbulto = $tpaq = $tbultoinv = $tpaqinv = 0;
        $cant_paq = 0;
        $cant_bul = 0;
        $i=0;
        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        $totales = Array();

        foreach ($datos as $key => $row) {

            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            if($t > 0) {
                for($e = 0; $e < $t; $e++)
                {
                    if($coditem[$e] == $row['CodProd']) {
                        switch ($tipo[$e]) {
                            case '0':
                                $cant_bul = $row['todob'] - $cantidad[$e];
                                break;
                            case '1':
                                $cant_paq = $row['todop'] - $cantidad[$e];
                                break;
                        }
//                        $e = $t + 2;
                        break;
                    }else{
                        $cant_bul = $row['todob'];
                        $cant_paq = $row['todop'];
                    }
                }
            } else {
                $cant_bul = $row['todob'];
                $cant_paq = $row['todop'];
            }
            ////conversión de bultos a paquetes
            $cantemp = $row['CantEmpaq'];
            $invbut  = $row['exis'];
            $invpaq  = $row['exunid'];

            $i++;
            if($cant_paq >= $cantemp){
                $conv = floor($cant_paq / $cantemp);
                $cant_paq -= ($conv * $cantemp);
                $cant_bul += $conv;
            }
            if($invpaq >= $cantemp){
                $conv = floor($invpaq / $cantemp);
                $invpaq -= ($conv * $cantemp);
                $invbut += $conv;
            }
            $tinvbult = $invbut + $cant_bul;
            $tinvpaq = $invpaq + $cant_paq;

            if($tinvpaq >= $cantemp){
                $conv1 = floor($tinvpaq / $cantemp);
                $tinvpaq -= ($conv1 * $cantemp);
                $tinvbult += $conv1;
            }

            //ASIGNAMOS EN EL SUB_ARRAY LOS DATOS PROCESADOS
//            $sub_array[] = $key;
            $sub_array['codprod'] = $row["CodProd"];
            $sub_array['descrip'] = $row["Descrip"];
            $sub_array['cant_bul'] = Strings::rdecimal($cant_bul,0);
            $sub_array['cant_paq'] = Strings::rdecimal($cant_paq,0);
            $sub_array['tinvbult'] = Strings::rdecimal($tinvbult,0);
            $sub_array['tinvpaq'] = Strings::rdecimal($tinvpaq, 0);

            //ACUMULAMOS LOS TOTALES
            $tbulto     += $cant_bul;
            $tpaq       += $cant_paq;
            $tbultoinv  += $tinvbult;
            $tpaqinv    += $tinvpaq;

            $data[] = $sub_array;
        }

        //CREAMOS UN SUB_ARRAY PARA ALMACENAR LOS DATOS ACUMULADOS
        $totales = array();
        $totales['tbulto']     = Strings::rdecimal($tbulto,0);
        $totales['tpaq']       = Strings::rdecimal($tpaq,0);
        $totales['tbultoinv']  = Strings::rdecimal($tbultoinv,0);
        $totales['tpaqinv']    = Strings::rdecimal($tpaqinv,0);
        $totales['facturas_sin_despachar'] = count($devolucionesDeFactura);


        //al terminar, se almacena en una variable de salida el array.
        $output['contenido_tabla'] = $data;

        //de igual forma, se almacena en una variable de salida el array de totales.
        $output['totales_tabla'] = $totales;

        echo json_encode($output);
        break;

    case "listar_depositos":

        $output['lista_depositos'] = Almacen::todos();

        echo json_encode($output);
        break;
}
