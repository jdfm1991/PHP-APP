<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO
require_once("../vehiculos/vehiculos_modelo.php");
require_once("despachos_modelo.php");

//INSTANCIAMOS EL MODELO
$despachos  = new Despachos();
$vehiculo = new Vehiculos();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "obtener_pesomaxvehiculo":

        $peso_max_vehiculo = $vehiculo->get_vehiculo_por_id($_POST["id"]);

        $output["capacidad"] = $peso_max_vehiculo[0]["Capacidad"];

        echo json_encode($output);

        break;


    case "obtener_pesoporfactura":

        $peso_acum = str_replace(",", ".", $_POST["peso_acum_facturas"]);
        $peso_max  = str_replace(",", ".", $_POST["peso_max_vehiculo"]);

        isset($_POST["eliminarPeso"]) ? $eliminarPeso = true : $eliminarPeso = false;

        $datos = $despachos->getPesoTotalporFactura($_POST["numero_fact"]);

        $peso = 0;
        if (count($datos) != 0){
            foreach ($datos as $dato) {
                if($dato['tipofac'] == "A") {
                    ($dato['unidad'] == 0) ? ($peso += ($dato['peso'] * $dato['cantidad'])) : ($peso += (($dato['peso'] / $dato['paquetes']) * $dato['cantidad'])) ;
                }
            }
        }
        //asigno el peso nuevo
        $peso = number_format($peso, 2, ".", "");

        //consulta si deseamos eliminar el peso de la factura actual
        if($eliminarPeso){
            $output["pesoNuevoAcum"] = strval(floatval($peso_acum) - floatval($peso));
        }
        //sino, consulta si el peso nuevo + el peso acumulado es < que el peso total del camion
        else if( (floatval($peso) + floatval($peso_acum) ) < floatval($peso_max) ){
           //asigna el peso nuevo + el acumulado
            $output["pesoNuevoAcum"] = strval(floatval($peso) + floatval($peso_acum));
            $output["pesoDeFactura"] = floatval($peso);
            $output["cond"] = "true";
        }
        //sino, solo devuelve el acumulado anterior y avisa que el acumulado supera al maximo de carga con la cond
        else {
            $output["pesoNuevoAcum"] = $peso_acum;
            $output["cond"] = "false";
        }

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
            if (count($pesodefact) != 0){
                foreach ($pesodefact as $dato) {
                    if($dato['tipofac'] == "A") {
                        ($dato['unidad'] == 0) ? ($peso += ($dato['peso'] * $dato['cantidad'])) : ($peso += (($dato['peso'] / $dato['paquetes']) * $dato['cantidad']));
                    }
                }
            }

            $sub_array = array();

            $sub_array[] = $datos[0]["numerod"];
            $sub_array[] = date("d/m/Y", strtotime($datos[0]["fechae"]));
            $sub_array[] = $datos[0]["descrip"];
            $sub_array[] = $datos[0]["direc2"];
            $sub_array[] = $datos[0]["codvend"];
            $sub_array[] = number_format($datos[0]["mtototal"], 2, ",", ".");
            $sub_array[] = number_format($peso, 2, ",", ".");

            $sub_array[] = '<div class="col text-center"><button type="button" onClick="eliminar(\''.$datos[0]["numerod"].'\');" name="eliminar" id="eliminar" class="btn btn-danger btn-sm eliminar">Eliminar</button></div>';

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


    case "buscar_facturaendespacho":

        $datos = $despachos->getExisteFacturaEnDespachos($_POST["numero_fact"]);

        (count($datos) > 0) ? ($output["mensaje"] = "El Numero de Factura: ". $datos[0]['Numerod'] . " Ya Fue Agregado en Otro Despacho") : ($output["mensaje"] = "");

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

        if(isset($_POST["documentos"])) {
            $array = explode(";", substr($_POST["documentos"], 0, -1));
        }

        $creacionDespacho = $despachos->insertarDespacho($_POST["fechad"], $_POST["chofer"], $_POST["vehiculo"], $_POST["destino"], $_POST["usuario"]);

        if($creacionDespacho){

            $correlativo = $despachos->getNuevoCorrelativo();

            (count($correlativo) > 0) ? $correl = $correlativo[0]["correl"] : $correl = 1;

            foreach ($array AS $item)
                $despachos->insertarDetalleDespacho($correl, $item, 'A');
            $output["mensaje"] = "SE HA CREADO UN NUEVO DESPACHO NRO: " . $correl;
            $output["icono"] = "success";
            $output["correl"] = $correl;
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

        /**
        //creamos el array con los numero de documento
        if(isset($_POST["documentos"])) {
            $array = explode(";", substr($_POST["documentos"], 0, -1));
        }

        //generamos un string para utilizar en el query
        $nros_documentos = "";
        if($array > 1) {
            foreach ($array AS $item)
                $nros_documentos .= "'".$item."',";
        } else {
            $nros_documentos .= "'".$array[0]."',";
        }
        //le quitamos 2 caracter para quitarle la ultima coma
        $nros_documentos = substr($nros_documentos, 1, -2);
         **/

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

        $_SESSION["total_bultos"] = $total_bultos;
        $_SESSION["total_paq"] = $total_paq;

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data);
        echo json_encode($results);

        break;

    case "listar_totales_paq_bul_despacho":

        if( isset($_SESSION["total_bultos"]) && isset($_SESSION["total_paq"]) ) {
            $output["total_bultos"] = $_SESSION["total_bultos"];
            $output["total_paq"] = $_SESSION["total_paq"];

            unset($_SESSION['total_bultos']);
            unset($_SESSION['total_paq']);
        }

        echo json_encode($output);

        break;
}
