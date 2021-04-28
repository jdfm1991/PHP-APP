<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../acceso/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("costodeinventario_modelo.php");

//INSTANCIAMOS EL MODELO
$costo = new CostodeInventario();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_costoseinventario":

        //obtenemos la marca seleccionada enviada por post
        $marca = $_POST['marca'];

        //verificamos si existe al menos 1 deposito selecionado
        //y se crea el array.
        if(isset($_POST['depo'])){
            $numero = $_POST['depo'];
        } else {
            $numero = array();
        }

        //se contruye un string para listar los depositvos seleccionados
        //en caso que no haya ninguno, sera vacio
        $edv = "";
        if(count($numero)>0) {
            foreach ($numero AS $i) {
                $edv .= "'" . $i . "',";
            }
        }

        //realiza la consulta con marca y almacenes
        $datos = $costo->getCostosdEinventario($edv, $marca);

        //inicializamos los acumuladores
        $costos = 0;
        $costos_p = 0;
        $precios = 0;
        $bultos = 0;
        $paquetes = 0;
        $total_costo_bultos = 0;
        $total_costo_paquetes = 0;
        $total_tara = 0;

        //DECLARAMOS ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        $totales = Array();

        foreach ($datos as $row) {

            if ($row['display'] == 0) {
                $cdisplay = 0;
            } else {
                $cdisplay = $row['costo'] / $row['display'];
            }

            //creamos un array para almacenar los datos procesados
            $sub_array = array();
            $sub_array['codprod'] = $row["codprod"];
            $sub_array['descrip'] = $row["descrip"];
            $sub_array['marca'] = $row["marca"];
            $sub_array['costo'] = number_format($row['costo'],2, ",", ".");
            $sub_array['cdisplay'] = number_format($cdisplay,2, ",", ".");
            $sub_array['precio'] = number_format($row['precio'],2, ",", ".");
            $sub_array['bultos'] = number_format($row['bultos'],2, ",", ".");
            $sub_array['paquetes'] = number_format($row['paquetes'],2, ",", ".");
            $sub_array['costoxbulto'] = number_format($row['costo'] * $row['bultos'],2, ",", ".");
            $sub_array['cdisplayxpaquetes'] = number_format($cdisplay * $row['paquetes'],2, ",", ".");
            $sub_array['tara'] = number_format($row['tara'],2, ",", ".");

            //ACUMULAMOS LOS TOTALES
            $costos += $row['costo'];
            $costos_p += $cdisplay;
            $precios += $row['precio'];
            $bultos += $row['bultos'];
            $paquetes += $row['paquetes'];
            $total_costo_bultos += ($row['costo'] * $row['bultos']);
            $total_costo_paquetes += ($cdisplay * $row['paquetes']);
            $total_tara += $row['tara'];

            //AGREGAMOS AL ARRAY DE CONTENIDO DE LA TABLA
            $data[] = $sub_array;
        }
        //creamos un array para almacenar los datos acumulados
        $sub_array1 = array();
        $sub_array1['costos'] = number_format($costos,2, ",", ".");
        $sub_array1['costos_p'] = number_format($costos_p,2, ",", ".");
        $sub_array1['precios'] = number_format($precios,2, ",", ".");
        $sub_array1['bultos'] = number_format($bultos,2, ",", ".");
        $sub_array1['paquetes'] = number_format($paquetes,2, ",", ".");
        $sub_array1['total_costo_bultos'] = number_format($total_costo_bultos,2, ",", ".");
        $sub_array1['total_costo_paquetes'] = number_format($total_costo_paquetes,2, ",", ".");
        $sub_array1['total_tara'] = number_format($total_tara,2, ",", ".");
        $sub_array1['cantidad_registros'] = count($datos);

        //al terminar, se almacena en una variable de salida el array.
        $output['contenido_tabla'] = $data;
        //de igual forma, se almacena en una variable de salida el array de totales.
        $output['totales_tabla'] = $sub_array1;

        echo json_encode($output);
        break;


    case "listar_depositos":

        $output['lista_depositos'] = $costo->get_Almacenes();

        echo json_encode($output);
        break;
}

?>
