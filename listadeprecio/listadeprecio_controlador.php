<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("listadeprecio_modelo.php");

//INSTANCIAMOS EL MODELO
$precios = new Listadeprecio();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar":

        $depos = $_POST['depo'];
        $marcas = $_POST['marca'];
        $orden = $_POST['orden'];
        $exis = $_POST['exis'];
        $iva = $_POST['iva'];
        $cubi = $_POST['cubi'];
        $p1 = str_replace("1","1",$_POST['p1']);
        $p2 = str_replace("1","2",$_POST['p2']);
        $p3 = str_replace("1","3",$_POST['p3']);
        $sumap = $_POST['p1'] + $_POST['p2'] + $_POST['p3'];
        $sumap2 = $p1 + $p2 + $p3;

        $datos = $precios->getListadeprecios($marcas, $depos, $exis, $orden);
        $num = count($datos);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        foreach ($datos as $row) {
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            if ($i['esexento']) {
                $precio1 = $row['precio1'] * $iva;
                $precio2 = $row['precio2'] * $iva;
                $precio3 = $row['precio3'] * $iva;
                $preciou1 = $row['preciou1'] * $iva;
                $preciou2 = $row['preciou2'] * $iva;
                $preciou3 = $row['preciou3'] * $iva;
            } else {
                $precio1 = $row['precio1'];
                $precio2 = $row['precio2'];
                $precio3 = $row['precio3'];
                $preciou1 = $row['preciou1'];
                $preciou2 = $row['preciou2'];
                $preciou3 = $row['preciou3'];
            }

            $sub_array[] = $row["codprod"];
            $sub_array[] = $row["descrip"];
            $sub_array[] = $row["marca"];
            //<!--BULTOS-->
            $sub_array[] = round($row['existen']);
            switch ($sumap) {
                case 1:
                    if ($i['esexento'] == 0)
                    {
                        $sub_array[] = number_format($row['precio'. $sumap2 ] * $iva, 2, ",", ".");
                    } else {
                        $sub_array[] = number_format($row['precio'. $sumap2 ], 2, ",", ".");
                    }
                    break;
                case 2:
                    if ($p1 == 1)
                    {
                        $sub_array[] = number_format($precio1, 2, ",", ".");
                    } else {
                        $sub_array[] = number_format($precio2, 2, ",", ".");
                    }
                    if ($p3 == 3)
                    {
                        $sub_array[] = number_format($precio3, 2, ",", ".");
                    } else {
                        $sub_array[] = number_format($precio2, 2, ",", ".");
                    }
                    break;
                default: /** 0 || 3**/
                    $sub_array[] = number_format($precio1, 2, ",", ".");
                    $sub_array[] = number_format($precio2, 2, ",", ".");
                    $sub_array[] = number_format($precio3, 2, ",", ".");
            }
            // <!--PAQUETES-->
            $sub_array[] = round($i['exunidad']);
            switch ($sumap) {
                case 1:
                    if ($i['esexento'] == 0)
                    {
                        $sub_array[] = number_format($i['preciou'. $sumap2 ]* $iva, 2, ",", ".");
                    } else {
                        $sub_array[] = number_format($i['preciou'. $sumap2 ], 2, ",", ".");
                    }
                    break;
                case 2:
                    if ($p1 == 1)
                    {
                        $sub_array[] = number_format($preciou1, 2, ",", ".");
                    } else {
                        $sub_array[] = number_format($preciou2, 2, ",", ".");
                    }
                    if ($p3 == 3)
                    {
                        $sub_array[] = number_format($preciou3, 2, ",", ".");
                    } else {
                        $sub_array[] = number_format($preciou2, 2, ",", ".");
                    }
                    break;
                default: /** 0 || 3**/
                    $sub_array[] = number_format($preciou1, 2, ",", ".");
                    $sub_array[] = number_format($preciou2, 2, ",", ".");
                    $sub_array[] = number_format($preciou3, 2, ",", ".");
                }
            if ($cubi == 1) {
                $sub_array[] = $i['cubicaje'];
            }

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
?>