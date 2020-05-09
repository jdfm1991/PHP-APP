<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO
require_once("../vehiculos/vehiculos_modelo.php");
require_once("despachos_modelo.php");

//INSTANCIAMOS EL MODELO
$despachos  = new Despachos();
$vehiculo = new Vehiculos();


//ARRAY PARA CARGAR EN MEMORIA LISTA DE FACTURAS ANTES DE INSERTAR EN DB
$registros_por_despachar = Array();


//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "obtener_pesomaxvehiculo":

        $peso_max_vehiculo = $vehiculo->get_vehiculo_por_id($_POST["id"]);

        $output["capacidad"] = $peso_max_vehiculo[0]["Capacidad"];

        echo json_encode($output);

        break;


    case "obtener_pesoporfactura":

        $datos = $despachos->getPesoTotalporFactura($_POST["numero_fact"]);

        $peso = 0;
        if (count($datos) != 0){
            foreach ($datos as $dato) {
                if($dato['tipofac'] == "A") {
                    ($dato['unidad'] == 0) ? ($peso += ($dato['peso'] * $dato['cantidad'])) : ($peso += (($dato['peso'] / $dato['paquetes']) * $dato['cantidad'])) ;
                }
            }
        }
        $output["peso"] = number_format($peso, 2, ",", ".");
        echo json_encode($output);

        break;


    case "obtener_facturasporcargardespacho":

        $array = explode(";", substr($_POST["registros_por_despachar"], 0, -1));

        $eliminar = "eliminar";

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($array as $row){

            $datos = $despachos->getFactura($row);
            $pesodefact = $despachos->getPesoTotalporFactura($row);

            $peso = 0;
            if (count($pesodefact) != 0){
                foreach ($pesodefact as $dato) {
                    if($dato['tipofac'] == "A") {
                        ($dato['unidad'] == 0) ? ($peso += ($dato['peso'] * $dato['cantidad'])) : ($peso += (($dato['peso'] / $dato['paquetes']) * $dato['cantidad'])) ;
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
            $sub_array[] = '<div class="col text-center"><button type="button" onClick="gestionDeDocumentos('.$datos[0]["numerod"].', '.$eliminar.');"  id="'.$datos[0]["numerod"].'" class="btn btn-danger btn-sm eliminar">Eliminar</button></div>';

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


    case "listar_despacho": //no esta listo

        $_POST["correlativo"];

        $datos = $despachos->lista_busca_activacionclientes($_POST["fecha_final"]);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();


        foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();
            $sub_array[] = date("d-m-Y", strtotime($row["fechauv"]));
            $sub_array[] = $row["codclie"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = $row["id3"];
            $sub_array[] = $row["codvend"];
            $sub_array[] = number_format($row["total"], 2, ",", ".");


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
}
