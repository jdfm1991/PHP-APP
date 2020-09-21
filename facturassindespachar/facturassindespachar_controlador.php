<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("facturassindespachar_modelo.php");
require_once("../relacionclientes/relacionclientes_modelo.php");
require_once("../despachos/despachos_modelo.php");

//INSTANCIAMOS EL MODELO
$factsindes = new FacturaSinDes();
$relacion = new RelacionClientes();
$despachos = new Despachos();

// Da igual el formato de las fechas (dd-mm-aaaa o aaaa-mm-dd),
function diasEntreFechas($fechainicio, $fechafin){
    return ((strtotime($fechafin)-strtotime($fechainicio))/86400);
}

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar":
        $fechai = $_POST['fechai'];
        $fechaf = $_POST['fechaf'];
        $convend = $_POST['vendedores'];
        $tipo = $_POST['tipo'];
        $check = hash_equals("true", $_POST['check']);
        $hoy = date("d-m-Y");

        $datos = $factsindes->getFacturas($tipo, $fechai, $fechaf, $convend, $check);
        $num = count($datos);
        $suma_bulto = 0;
        $suma_paq = 0;
        $suma_monto = 0;
        $porcent = 0;

        /** TITULO DE LAS COLUMNAS DE LA TABLA **/
        $thead = Array();
        $thead[] = "Documento";
        $thead[] = "Fecha Emisión";
        if($check) {
            $thead[] = "Fecha Despacho";
            $thead[] = "Dias Transcurridos";
        }
        $thead[] = "Código";
        $thead[] = "Cliente";
        $thead[] = "Días Transcurridos Hasta Hoy";
        $thead[] = "Cantidad Bultos";
        $thead[] = "Cantidad Paquetes";
        $thead[] = "Monto Bs";
        $thead[] = "EDV";
        if($check) {
            $thead[] = "Tiempo Promedio Estimado";
            $thead[] = "%Oportunidad";
        }


        /** CONTENIDO DE LA TABLA **/
        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            if($check) {
                $calcula = 0;
                if (round(diasEntreFechas(date("d-m-Y", strtotime($row["FechaE"])),date("d-m-Y", strtotime($row["fechad"])))) != 0)
                    $calcula = (2 / round(diasEntreFechas(date("d-m-Y", strtotime($row["FechaE"])),date("d-m-Y", strtotime($row["fechad"])))))*100;

                if ($calcula > 100)
                    $calcula = 100;

                $porcent += $calcula;
            }

            $sub_array[] = '<div class="col text-center"><a id="numerod" data-toggle="modal" onclick="mostrarModalDetalleFactura(\''.$row['NumeroD'].'\', \''.$row['TipoFac'].'\')" data-target="#detallefactura" href="#"> '.$row['NumeroD'].'</div>';

            $sub_array[] = date("d/m/Y", strtotime($row["FechaE"]));
            if ($check) {
                $sub_array[] = date("d/m/Y", strtotime($row["fechad"]));
                $sub_array[] = round(diasEntreFechas(date("d-m-Y", strtotime($row["FechaE"])),date("d-m-Y", strtotime($row["fechad"]))));
            }
            $sub_array[] = $row["CodClie"];
            $sub_array[] = $row["Descrip"];
            $sub_array[] = round(diasEntreFechas(date("d-m-Y", strtotime($row["FechaE"])), $hoy));
            $sub_array[] = round($row['Bult']);
            $sub_array[] = round($row['Paq']);
            $sub_array[] = number_format($row["Monto"], 1, ",", "."); $suma_monto += $row["Monto"];
            $sub_array[] = $row['CodVend'];
            if ($check) {
                $sub_array[] = 2;
                $sub_array[] = number_format($calcula, 1, ",", ".") . "%";
            }

            $data[] = $sub_array;
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "columns" => $thead,
            "aaData" => $data);
        echo json_encode($output);

        break;

    case "listar_canales":
        $output['lista_canales'] = $factsindes->getCanales();
        echo json_encode($output);
        break;

    case "detalle_de_factura":
        $numerod = $_POST["numerod"];
        $tipofac = $_POST["tipofac"];

        switch ($tipofac) {
            case "10":
            case "A":
                $texto_tipofact = "FACTURA ";
                break;
            case "B":
                $texto_tipofact = "DEVOLUCION ";
                break;
            case "C":
                $texto_tipofact = "Nota de Entrega ";
                break;
            case "20":
                $texto_tipofact = "N/D ";
                break;
        }
        $output["tipo_documento"] = $texto_tipofact;

        $cabecera_factura = $factsindes->get_cabecera_factura_por_id($numerod, $tipofac);
        if (is_array($cabecera_factura) == true and count($cabecera_factura) > 0) {
            $output["descrip"] = $cabecera_factura[0]['cliente'];
            $output["codusua"] = $cabecera_factura[0]['codusua'];
            $output["fechae"] = date('d/m/Y h:iA', strtotime($cabecera_factura[0]['fechaemi']));
            $output["codvend"] = $cabecera_factura[0]['vendedor'];
        }

        $detalle_factura = $relacion->get_detalle_factura_por_id($numerod, $tipofac);
        if (is_array($detalle_factura) == true and count($detalle_factura) > 0) {
            //debido a que el detalle de una factura es de multiples items(productos),
            //se procede a crear un array que almacene cada registro
            $array_detalle = Array();

            //se itera cada items
            $paquetes = 0;
            $bultos = 0;
            $kg = 0;
            foreach ($detalle_factura as $detalle){
                //se crea otro array adicional que es quien almacena las columnas para 1 registro
                $sub_array = array();

                $cantidad = 0;
                $tipounid = "";

                $cantidad = $detalle['cantidad'];

                if ($detalle['esunid'] == 1) {
                    $tipounid = "PAQ";
                    $paquetes += intval($cantidad);
                } else {
                    $tipounid = "BUL";
                    $bultos += intval($cantidad);
                }

                $kg += $detalle['peso'];

                //asignamos al array adicional las columnas con arrays asociativo
                $sub_array["coditem"] = $detalle['coditem'];
                $sub_array["descrip1"] = $detalle['descrip1'];
                $sub_array["cantidad"] = number_format($cantidad, 0, ".", ",");
                $sub_array["tipounid"] = $tipounid;
                $sub_array["peso"] = number_format($detalle['peso'], 2, ".", ",");
                $sub_array["totalitem"] = number_format($detalle['totalitem'], 2, ",", ".");

                //asignamos el array adicional al array de registros
                $array_detalle[] = $sub_array;
            }
            //una vez culminado las iteraciones, el array de registros, se asigna a una variable de salida
            $output["detalle_factura"] = $array_detalle;
            //y devolvemos tambien los la cantidad de productos, kg, paquetes y bultos totales
            $output["productos"] = count($detalle_factura);
            $output["paquetes"]  = $paquetes;
            $output["bultos"]    = $bultos;
            $output["kg"] = number_format($kg, 2, ".", ",");
        }

        $totales_factura = $relacion->get_totales_factura_por_id($numerod, $tipofac);
        if (is_array($totales_factura) == true and count($totales_factura) > 0) {
            $output["subtotal"] = number_format($totales_factura[0]['subtotal'], 2, ",", ".");
            $output["descuento"] = number_format($totales_factura[0]['descuento'], 2, ",", ".");
            $output["exento"] = number_format($totales_factura[0]['exento'], 2, ",", ".");
            $output["base"] = number_format($totales_factura[0]['base'], 2, ",", ".");
            $output["iva"] = number_format($totales_factura[0]['iva'], 0, ",", ".");
            $output["impuesto"] = number_format($totales_factura[0]['impuesto'], 2, ",", ".");
            $output["total"] = number_format($totales_factura[0]['total'], 2, ",", ".");
        }

        $factura_despachada = $despachos->get_existe_factura_despachada_por_id($numerod);
        if (is_array($factura_despachada) == true and count($factura_despachada) > 0) {
            $output["factura_despachada"] = "Factura Despachada: " . date("d/m/Y", strtotime($factura_despachada[0]['fechad'])) .
                '</br> Por:'. $factura_despachada[0]['nomper'] .
                '</br>En el Despacho nro: '. str_pad($factura_despachada[0]['correlativo'], 8, 0, STR_PAD_LEFT);
        } else {
            $output["factura_despachada"] = "Factura Sin Despachar";
        }


        echo json_encode($output);

        break;

}
?>
