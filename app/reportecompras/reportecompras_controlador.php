<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

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
            $compra = $reporte->get_ultimascompras_por_codprod($coditem["codprod"]);

            //creamos un array para almacenar los datos procesados
            $sub_array = array();
            $sub_array['num'] = $key+1;
            $sub_array['codproducto'] = $row[0]["codproducto"];
            $sub_array['descrip'] = $row[0]["descrip"];
            $sub_array['displaybultos'] = number_format($row[0]["displaybultos"], 0);
            $sub_array['costodisplay'] = Strings::rdecimal($row[0]["costodisplay"], 2);
            $sub_array['costobultos'] = Strings::rdecimal($row[0]["costobultos"], 2);
            $sub_array['rentabilidad'] = Strings::rdecimal($row[0]["rentabilidad"], 2);
            $sub_array['fechapenultimacompra'] = (count($compra) > 0) ? date("d/m/Y",strtotime($compra[0]["fechapenultimacompra"])) : '-';
            $sub_array['bultospenultimacompra'] = (count($compra) > 0) ? number_format($compra[0]["bultospenultimacompra"], 0) : 0;
            $sub_array['fechaultimacompra'] = (count($compra) > 0) ? date("d/m/Y",strtotime($compra[0]["fechaultimacompra"])) : '-';
            $sub_array['bultosultimacompra'] = (count($compra) > 0) ? number_format($compra[0]["bultosultimacompra"], 0) : 0;
            $sub_array['semana1'] = number_format($row[0]["semana1"], 0);
            $sub_array['semana2'] = number_format($row[0]["semana2"], 0);
            $sub_array['semana3'] = number_format($row[0]["semana3"], 0);
            $sub_array['semana4'] = number_format($row[0]["semana4"], 0);
            $sub_array['totalventasmesanterior'] = number_format($row[0]["totalventasmesanterior"], 0);
            $sub_array['bultosexistentes'] = Strings::rdecimal($row[0]["bultosexistentes"], 1);
            $sub_array['diasdeinventario'] = number_format($row[0]["diasdeinventario"], 0);
            $sub_array['sugerido'] = Strings::rdecimal($row[0]["sugerido"], 2);
            $sub_array['pedido'] = '<input type="text" name="n[]" style="text-align: right; width: 90%;">
                                    <input type="hidden" name="v[]" value="'. $row[0]["codproducto"] .'">';

            //AGREGAMOS AL ARRAY DE CONTENIDO DE LA TABLA
            $data[] = $sub_array;
        }
        //al terminar, se almacena en una variable de salida el array.
        $output['contenido_tabla'] = $data;

        //de igual forma, se almacena en una variable de salida el total de registros.
        $output['cantidad_registros'] = count($codidos_producto);

        echo json_encode($output);
        break;

    case "listar_marcas":

        $output["lista_marcas"] = Marcas::todos();

        echo json_encode($output);
        break;

}