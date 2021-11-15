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

        $datos = array(
            'fechai'    => $_POST['fechai'],
            'fechaf'    => $_POST['fechaf'],
            'vendedor'  => $_POST['vendedor'],
            'instancia' => $_POST['marca'],
        );

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = array();

        $total_monto = $total_peso = $total_cant = 0;
        $instancias_data = $ventaskg->getinstancias($datos);
        if (ArraysHelpers::validate($instancias_data)) {
            foreach ($instancias_data as $key => $instancia) {

                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $peso = $cant = $monto = 0;
                $notas_debitos = $ventaskg->getNotaDebitos($datos);
                if (ArraysHelpers::validate($notas_debitos)) {
                    foreach ($notas_debitos as $row)
                    {
                        $monto += $row["monto"];
                        if ($row['unidad'] == 0) {
                            $peso += $row["peso"];
                            $cant += $row["cantidad"];
                        } else {
                            $peso += (($row["peso"]/$row["paquetes"]) * $row["cantidad"]);
                            $cant += ($row["cantidad"] / $row["paquetes"]);
                        }
                    }
                }

                $descuento = Functions::find_discount($datos['fechai'], $datos['fechaf'], $instancia["codinst"]);
                $monto -= $descuento;

                $sub_array[] = $instancia["descrip"];
                $sub_array[] = Strings::rdecimal($cant, 2);
                $sub_array[] = Strings::rdecimal($peso, 2);
                $sub_array[] = Strings::rdecimal($monto, 2);

                $total_cant  += $cant;
                $total_peso  += $peso;
                $total_monto += $monto;

                $data[] = $sub_array;
            }
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $output = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            'totalCant'  => Strings::rdecimal($total_cant, 2),
            'totalPeso'  => Strings::rdecimal($total_peso, 2),
            'totalMonto' => Strings::rdecimal($total_monto, 2),
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
