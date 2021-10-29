<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("ventaskgedvporcategoria_modelo.php");

//INSTANCIAMOS EL MODELO
$ventaskg = new VentasKgEdvPorCategoria();


//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar":

        $data = array(
            'fechai'    => $_POST['fechai'],
            'fechaf'    => $_POST['fechaf'],
            'vendedor'  => $_POST['vendedor'],
            'marca'     => $_POST['marca'],
        );

        $instancias_data = $ventaskg->getinstancias($data);

        if (ArraysHelpers::validate($instancias_data)) {

        }






        $datos = $factsindes->getFacturas($tipo, $fechai, $fechaf, $convend, $check);
        $num = count($datos);
        $suma_bulto = 0;
        $suma_paq = 0;
        $suma_monto = 0;
        $porcent = 0;

        /** TITULO DE LAS COLUMNAS DE LA TABLA **/
        $thead = Array();
        $thead[] = Strings::titleFromJson('numerod');
        $thead[] = Strings::titleFromJson('fecha_emision');
        if($check) {
            $thead[] = Strings::titleFromJson('fecha_despacho');
            $thead[] = Strings::titleFromJson('dias_transcurridos');
        }
        $thead[] = Strings::titleFromJson('codigo');
        $thead[] = Strings::titleFromJson('cliente');
        $thead[] = Strings::titleFromJson('dias_transcurridos_hoy');
        $thead[] = Strings::titleFromJson('cantidad_bultos');
        $thead[] = Strings::titleFromJson('cantidad_paquetes');
        $thead[] = Strings::titleFromJson('monto');
        $thead[] = Strings::titleFromJson('descrip_vend');
        if($check) {
            $thead[] = Strings::titleFromJson('tiempo_prom_estimado');
            $thead[] = Strings::titleFromJson('porcentaje_oportunidad');
        }


        /** CONTENIDO DE LA TABLA **/
        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            if($check) {
                $calcula = 0;
                if (round(Dates::daysEnterDates(date(FORMAT_DATE, strtotime($row["FechaE"])),date(FORMAT_DATE, strtotime($row["fechad"])))) != 0)
                    $calcula = (2 / round(Dates::daysEnterDates(date(FORMAT_DATE, strtotime($row["FechaE"])),date(FORMAT_DATE, strtotime($row["fechad"])))))*100;

                if ($calcula > 100)
                    $calcula = 100;

                $porcent += $calcula;
            }

            $sub_array[] = '<div class="col text-center"><a id="numerod" data-toggle="modal" onclick="mostrarModalDetalleFactura(\''.$row['NumeroD'].'\', \''.$row['TipoFac'].'\')" data-target="#detallefactura" href="#"> '.$row['NumeroD'].'</div>';

            $sub_array[] = date(FORMAT_DATE, strtotime($row["FechaE"]));
            if ($check) {
                $sub_array[] = date(FORMAT_DATE, strtotime($row["fechad"]));
                $sub_array[] = round(Dates::daysEnterDates(date(FORMAT_DATE, strtotime($row["FechaE"])),date(FORMAT_DATE, strtotime($row["fechad"]))));
            }
            $sub_array[] = $row["CodClie"];
            $sub_array[] = $row["Descrip"];
            $sub_array[] = round(Dates::daysEnterDates(date(FORMAT_DATE, strtotime($row["FechaE"])), $hoy));
            $sub_array[] = round($row['Bult']);
            $sub_array[] = round($row['Paq']);
            $sub_array[] = Strings::rdecimal($row["Monto"], 1); $suma_monto += $row["Monto"];
            $sub_array[] = $row['CodVend'];
            if ($check) {
                $sub_array[] = 2;
                $sub_array[] = Strings::rdecimal($calcula, 1) . "%";
            }

            $data[] = $sub_array;
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "columns" => $thead,
            'totalDoc' => $num,
            'Mtototal' => Strings::rdecimal($suma_monto, 2),
            'oportunidad' => ($check and count($datos)>0) ? Strings::rdecimal(($porcent / count($datos)), 2) . ' %' : '',
            "aaData" => $data);
        echo json_encode($output);

        break;

    case "listar_vendedores":

        $output['lista_vendedores'] = Vendedores::todos();

        echo json_encode($output);
        break;

    case "listar_marcas":

        $output["lista_marcas"] = Marcas::todos();

        echo json_encode($output);
        break;
}
?>
