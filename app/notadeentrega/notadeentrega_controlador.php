<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("notadeentrega_modelo.php");

//INSTANCIAMOS EL MODELO
$nota = new NotaDeEntrega();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "buscar_notadeentrega":
        $output = array();
        $numerod = $_POST["nrodocumento"];

        $empresa = Empresa::getInfo();
        $cabecera = NotasDeEntrega::getHeaderById($numerod);
        $detalle = NotasDeEntrega::getDetailById($numerod);

        # darle formatos y validar datos de la cabecera
        $codclie = $cabecera['codclie'];
        $cabecera['representante'] = $nota->get_datos_cliente($codclie)['represent'];
        $cabecera['fechae']    = Date(FORMAT_DATE, strtotime($cabecera['fechae']));
        $cabecera['telefono']  = Strings::avoidNull($cabecera['telefono']);
        $cabecera['notas1']    = Strings::avoidNull($cabecera['notas1']);
        $cabecera['subtotal']  = Strings::rdecimal($cabecera['subtotal']);
        $cabecera['descuento'] = Strings::rdecimal($cabecera['descuento']);
        $cabecera['total']     = Strings::rdecimal($cabecera['total']);

        # darle formatos y validar datos del detalle
        $data_detalle = array();
        foreach ($detalle as $key => $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            $sub_array['fechaemision']  = date(FORMAT_DATE, strtotime($row["fechaemision"]));

            $sub_array['coditem']     = $row["coditem"];
            $sub_array['descripcion'] = utf8_encode($row["descripcion"]);
            $sub_array['cantidad']    = $row["cantidad"];
            $sub_array['unidad']      = ($row['esunidad'] == '1') ? 'PAQ' : 'BUL';
            $sub_array['nroctrol']    = Strings::rdecimal($row["precio"], 2);

            $sub_array['tiporeg']       = $row["tiporeg"];
            $sub_array['factafectada']  = Strings::avoidNull($row["factafectada"]);
            $sub_array['nroretencion']  = count($retencion_dato)>0 ? Strings::avoidNull($retencion_dato[0]["nroretencion"]) : '';
            $sub_array['totalventasconiva'] = Strings::rdecimal($totalventasconiva, 2);
            $sub_array['mtoexento']      = Strings::rdecimal($row["mtoexento"], 2);
            $sub_array['base_imponible'] = Strings::rdecimal($base_imponible, 2);
            $sub_array['alicuota_contribuyeiva'] = Strings::rdecimal($row["alicuota_contribuyeiva"], 0);
            $sub_array['montoiva_contribuyeiva'] = Strings::rdecimal($row["montoiva_contribuyeiva"], 2);
            $sub_array['retencioniva']  = count($retencion_dato)>0 ? Strings::avoidNull($retencion_dato[0]["retencioniva"]) : '';

            $data[] = $sub_array;
        }


        $output = array(
            'empresa' => $empresa,
            'cabecera' => $cabecera,
            'detalle' => $data_detalle,
        );

        echo json_encode($output);
        break;
}
