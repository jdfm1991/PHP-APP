<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("recepcion_compras_modelo.php");

//INSTANCIAMOS EL MODELO
$tabladinamica = new Tabladinamica();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_tabladinamica":

        $data = array(
            'fechai' => $_POST['fechai'],
            'fechaf' => $_POST['fechaf'],
        );

        $datos = array();
        switch ($_POST['tipo']) {
            case 'f': $datos = $tabladinamica->getTabladinamicaFactura($data); break;
            case 'n': $datos = $tabladinamica->getTabladinamicaNotaDeEntrega($data); break;
        }

        /*echo "<script>console.log('fechai: " . $_POST['fechai'] . "' );</script>";
        echo "<script>console.log('fechaf: " . $_POST['fechaf'] . "' );</script>";
        echo "<script>console.log('tipo: " . $_POST['tipo'] . "' );</script>";*/
        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $arr_data = Array();

        $Costo = $Cantidad = $TotalItem = 0;

        if (is_array($datos)==true and count($datos)>0)
        {
            foreach ($datos as $key => $row)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $montod = $montobs = $descuento = 0;

                    if($row['tipo']=='I' or $row['tipo']=='K'){
                            $multiplicador = -1;
                        }else{
                            $multiplicador = 1;
                        }


                $sub_array['num']  = $key+1;
                $sub_array['CodProv']       = $row["CodProv"];
                $sub_array['Descrip']      = utf8_encode($row["Descrip"]);
                $sub_array['NumeroD']     = $row["NumeroD"];
                $sub_array['CodItem']          = $row["CodItem"];
                $sub_array['Descrip1']       = utf8_encode($row["Descrip1"]);
                $sub_array['Costo']       = Strings::rdecimal($row["Costo"],2);
                $sub_array['Cantidad']       = Strings::rdecimal($row["Cantidad"]);
                $sub_array['TotalItem']     = Strings::rdecimal($row["TotalItem"],2);
                $sub_array['fechae']        = date(FORMAT_DATE, strtotime($row["FechaE"]));
                $sub_array['tasa']           =  Strings::rdecimal($row['tasa'],2);

                $Costo  += $row["Costo"] * $multiplicador;
                $Cantidad  += $row["Cantidad"] * $multiplicador;
                $TotalItem  += $row["TotalItem"]  * $multiplicador;

                $arr_data[] = $sub_array;
            }
        }

        /*$total = (hash_equals('n', $_POST['tipo']))
            ? Numbers::avoidNull($tabladinamica->getTotalNotaDeEntrega($data,'C')[0]['montod']) - Numbers::avoidNull($tabladinamica->getTotalNotaDeEntrega($data, 'D')[0]['montod'])
            : $total;*/

        $totales_tabladinamica = array(
            "Costo"  => Strings::rdecimal($Costo, 2),
            "Cantidad"  => Strings::rdecimal($Cantidad, 2),
            "TotalItem"  => Strings::rdecimal($TotalItem, 2),
        );



        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
            "tabla"   => $arr_data,
            "totales" => $totales_tabladinamica
        );

        echo json_encode($results);
        break;

    /*case "listar_marcas":

        $output["lista_marcas"] = Marcas::todos();

        echo json_encode($output);
        break;

    case "listar_vendedores":

        $output['lista_vendedores'] = Vendedores::todos();

        echo json_encode($output);
        break;*/
}
