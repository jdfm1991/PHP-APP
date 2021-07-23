<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("tabladinamica_modelo.php");

//INSTANCIAMOS EL MODELO
$tabladinamica = new Tabladinamica();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_tabladinamica":

        $data = array(
            'fechai' => $_POST['fechai'],
            'fechaf' => $_POST['fechaf'],
            'marca'  => $_POST['marca'],
            'edv'    => $_POST['edv'],
        );

        $datos = array();
        switch ($_POST['tipo']) {
            case 'f':
                $datos = $tabladinamica->getTabladinamicaFactura($data); break;
            case 'n':
                $datos = $tabladinamica->getTabladinamicaNotaDeEntrega($data); break;
        }

//        $retenciones_otros_periodos = $libroventa->getRetencionesOtrosPeriodos($fechai, $fechaf);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        $paqt = $bult = $kilo = $total = 0;

        if (is_array($datos)==true and count($datos)>0)
        {
            foreach ($datos as $key => $row)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $multiplicador = in_array($row['tipo'], array('A','C'))
                    ? 1
                    : -1;

                $montod = (is_numeric($row["factor"]) and $row["factor"]>0)
                    ? $row["montod"] / $row["factor"]
                    : '';

                $descuento = (is_numeric($row["factor"]) and $row["factor"]>0)
                    ? $row["descuento"] / $row["factor"]
                    : '';

                $sub_array['num']  = $key+1;
                $sub_array['codvend']       = $row["codvend"];
                $sub_array['vendedor']      = $row["vendedor"];
                $sub_array['clasevend']     = $row["clasevend"];
                $sub_array['tipo']          = $row["tipo"];
                $sub_array['numerod']       = $row["numerod"];
                $sub_array['codclie']       = $row["codclie"];
                $sub_array['cliente']       = utf8_encode($row["cliente"]);
                $sub_array['codnestle']     = Strings::avoidNull($row["codnestle"]);
                $sub_array['clasificacion'] = Strings::avoidNull($row["clasificacion"]);
                $sub_array['coditem']       = $row["coditem"];
                $sub_array['descripcion']   = utf8_encode($row["descripcion"]);
                $sub_array['marca']         = $row["marca"];
                $sub_array['cantidad']      = $row["cantidad"] * $multiplicador;
                $sub_array['unid']          = $row["unid"];
                $sub_array['paq']           = Strings::rdecimal($row["paq"] * $multiplicador, 1);
                $sub_array['bul']           = Strings::rdecimal($row["bul"] * $multiplicador, 1);
                $sub_array['kg']            = Strings::rdecimal($row["kg"] * $multiplicador, 1);
                $sub_array['instancia']     = $row["instancia"];
                $sub_array['montod']        =  Strings::rdecimal($montod  * $multiplicador, 2);
                $sub_array['descuento']     =  Strings::rdecimal($descuento  * $multiplicador, 2);
                $sub_array['factor']        =  Strings::rdecimal($row['factor'], 2);
                $sub_array['montobs']       =  Strings::rdecimal($row['montod'] * $multiplicador, 2);
                $sub_array['fechae']        = date(FORMAT_DATE, strtotime($row["fechae"]));
                $sub_array['mes']           =  utf8_encode($row['MES']);

                $paqt  += $row["paq"] * $multiplicador;
                $bult  += $row["bul"] * $multiplicador;
                $kilo  += $row["kg"]  * $multiplicador;
                $total += $row["montod"] * $multiplicador;

                $data[] = $sub_array;
            }
        }

        $totales_tabladinamica = array(
            "paqt"  => Strings::rdecimal($paqt, 2),
            "bult"  => Strings::rdecimal($bult, 2),
            "kilo"  => Strings::rdecimal($kilo, 2),
            "total" => Strings::rdecimal($total, 2),
        );

        /*if (is_array($retenciones_otros_periodos)==true and count($retenciones_otros_periodos)>0)
        {
            foreach ($retenciones_otros_periodos as $key => $row)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $ivare2 += Numbers::avoidNull($row['retencioniva']);

                $sub_array['num']  = $key+1;
                $sub_array['fechaemision']  = date(FORMAT_DATE, strtotime($row["fechaemision"]));
                $sub_array['rifcliente']    = $row["rifcliente"];
                $sub_array['nombre']        = utf8_encode($row["nombre"]);
                $sub_array['tipodoc']       = $row["tipodoc"];
                $sub_array['numerodoc']     = $row["numerodoc"];
                $sub_array['tiporeg']       = $row["tiporeg"];
                $sub_array['factafectada']  = Strings::avoidNull($row["factafectada"]);
                $sub_array['fecharetencion'] = !is_null($row["fecharetencion"]) ? date(FORMAT_DATE, strtotime($row["fecharetencion"])) : '';
                $sub_array['totalgravable_contribuye'] = Strings::rdecimal($row["totalgravable_contribuye"], 2);
                $sub_array['totalivacontribuye'] = Strings::rdecimal($row["totalivacontribuye"], 2);
                $sub_array['retencioniva']  = Strings::rdecimal($row["retencioniva"], 2);

                $otros_periodos[] = $sub_array;
            }
        }

        $totales_otros_periodos = array(
            "ivare" => Strings::rdecimal($ivare2, 2)
        );*/

        /*$resumen = array(
            array(
                "descripcion"    => 'Total ventas internas no gravadas',
                "base_imponible" => Strings::rdecimal($ve, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total ventas de Exportación',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total ventas internas Gravadas por Alicuota General (16%)',
                "base_imponible" => Strings::rdecimal($magbi16c, 2),
                "credito_fiscal" => Strings::rdecimal($mag16c, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total ventas internas Gravadas por Alicuota General',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total ventas Gravadas por Alicuota reducida',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(0, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(

                "descripcion"    => 'Total Ventas y Débitos Fiscales para efectos de determinación',
                "base_imponible" => Strings::rdecimal(($ve+$magbi16c), 2),
                "credito_fiscal" => Strings::rdecimal($mag16c, 2),
                "isBold" => true, "isColored" => true,
            ),
            array(
                "descripcion"    => 'Iva Retenidos periodos anteriores',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal($ivare2, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Iva Retenidos en este periodo',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal($ivare, 2),
                "isBold" => false, "isColored" => false,
            ),
            array(
                "descripcion"    => 'Total IVA Retenido',
                "base_imponible" => Strings::rdecimal(0, 2),
                "credito_fiscal" => Strings::rdecimal(($ivare2+$ivare), 2),
                "isBold" => true, "isColored" => true,
            ),
        );*/

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
            "tabla"   => $data,
            "totales_tabladinamica" => $totales_tabladinamica,
            /*"otros_periodos" => $otros_periodos,
            "totales_otros_periodos" => $totales_otros_periodos,
            "resumen" => $resumen*/
        );

        echo json_encode($results);
        break;

    case "listar_marcas":

        $output["lista_marcas"] = Marcas::todos();

        echo json_encode($output);
        break;

    case "listar_vendedores":

        $output['lista_vendedores'] = Vendedores::todos();

        echo json_encode($output);
        break;
}
