<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("reportecompras_modelo.php");

//INSTANCIAMOS EL MODELO
$reporte = new ReporteCompras();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar":
        $fechai = $_POST['fechai'];
        $marca = $_POST['marca'];

        $codidos_producto = $reporte->get_codprod_por_marca($marca);

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = array();
        foreach ($codidos_producto as $key=>$coditem){

            $row = $reporte->get_reportecompra_por_codprod($coditem["codprod"], $fechai);

            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            $sub_array[] = $key;
            $sub_array[] = $row[0]["codproducto"];
            $sub_array[] = $row[0]["descrip"];
            $sub_array[] = number_format($row[0]["displaybultos"], 0, ",", ".");
            $sub_array[] = number_format($row[0]["costodisplay"], 2, ",", ".");
            $sub_array[] = number_format($row[0]["costobultos"], 2, ",", ".");
            $sub_array[] = number_format($row[0]["rentabilidad"], 0, ",", ".") . "  %";
            $sub_array[] = date("d/m/Y",strtotime($row[0]["fechapenultimacompra"]));
            $sub_array[] = number_format($row[0]["bultospenultimacompra"], 0, ",", ".");
            $sub_array[] = date("d/m/Y",strtotime($row[0]["fechaultimacompra"]));
            $sub_array[] = number_format($row[0]["bultosultimacompra"], 0, ",", ".");
            $sub_array[] = number_format($row[0]["semana1"], 0, ",", ".");
            $sub_array[] = number_format($row[0]["semana2"], 0, ",", ".");
            $sub_array[] = number_format($row[0]["semana3"], 0, ",", ".");
            $sub_array[] = number_format($row[0]["semana4"], 0, ",", ".");
            $sub_array[] = number_format($row[0]["totalventasmesanterior"], 0, ",", ".");
            $sub_array[] = number_format($row[0]["bultosexistentes"], 1, ",", ".");
            $sub_array[] = number_format($row[0]["diasdeinventario"], 0, ",", ".");
            $sub_array[] = number_format($row[0]["sugerido"], 1, ",", ".");
            $sub_array[] = '<input type="text" name="n[]" class="n" style="text-align: right; width: 90%;">
                            <input type="hidden" name="v[]" value="'. $row[0]["codproducto"] .'">';

            $data[] = $sub_array;
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
            "sEcho" => 1, //INFORMACION PARA EL DATATABLE
            "iTotalRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS AL DATATABLE.
            "iTotalDisplayRecords" => count($data), //ENVIAMOS EL TOTAL DE REGISTROS A VISUALIZAR.
            "aaData" => $data
        );
        echo json_encode($results);

        break;

}