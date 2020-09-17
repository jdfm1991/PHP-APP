<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("facturassindespachar_modelo.php");

//INSTANCIAMOS EL MODELO
$factsindes = new FacturaSinDes();

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

            $sub_array[] = $row["NumeroD"];
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

}
?>
