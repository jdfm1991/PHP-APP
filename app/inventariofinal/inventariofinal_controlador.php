<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("inventariofinal_modelo.php");

//INSTANCIAMOS EL MODELO
$invglobal = new InventarioFinal();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar_inventariofinal":

        //verificamos si existe al menos 1 deposito selecionado
        //y se crea el array.
        $depos = $_POST['depo'] ?? array();

        $fechaf = $_POST['fechaf'];
        $fechai = $_POST['fechai'];

        $coditem = $cantidad = $tipo = array();
        $t = 0;

        $dataGeneral = $invglobal->getproductos($fechai, $fechaf, $depos);
        
        $tbulto = $tpaq = $tbultoinv = $tpaqinv = $tbultsaint = $tpaqsaint = 0;
        $cant_paq = 0;
        $tmonto= $monto = $cant_bul = 0;
        $montoPaquetes=$montoUnidad=0;
        $i=0;
        //DECLARAMOS ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        $totales = Array();

        foreach ($dataGeneral as $row) {

            
            //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
            $sub_array = array();

            $relacion_inventarioglobal = $invglobal->getdata($fechai, $fechaf, $depos, $row["CodProd"]);

            foreach ($relacion_inventarioglobal as $row2) {

                $paquete=$row2["ExistAnt"];
                $unidades=$row2["ExistAntU"];

                if($row2["CantEmpaq"]==0){

                    $CantEmpaq=1;

                }else{
                    $CantEmpaq=$row2["CantEmpaq"];
                }


                if($row2["EsUnid"]==1){

                    $montoPaquetes=($row2["Costo"]*$CantEmpaq)*$paquete;
                    $montoUnidad=($row2["Costo"])*$unidades;

                }else{

                    $montoPaquetes=($row2["Costo"])*$paquete;
                    $montoUnidad=($row2["Costo"]/$CantEmpaq)*$unidades;

                }

                
            
            }


            //ASIGNAMOS EN EL SUB_ARRAY LOS DATOS PROCESADOS

            $sub_array['codprod']  = $row["CodProd"];
            $sub_array['descrip']  = $row["Descrip"];
            $sub_array['invbut']   = Strings::rdecimal($paquete,0);
            $sub_array['invpaq']   = Strings::rdecimal($unidades,0);
            $sub_array['monto']   = Strings::rdecimal( $montoPaquetes+$montoUnidad,2);

            //ACUMULAMOS LOS TOTALES
            $tbultsaint += $paquete;
            $tpaqsaint  += $unidades;
            $tmonto  += $montoPaquetes+$montoUnidad;

            //AGREGAMOS AL ARRAY DE CONTENIDO DE LA TABLA
            $data[] = $sub_array;
        }

        //CREAMOS UN SUB_ARRAY PARA ALMACENAR LOS DATOS ACUMULADOS
        $totales = array();
        $totales['tbultsaint'] = Strings::rdecimal($tbultsaint,0);
        $totales['tpaqsaint']  = Strings::rdecimal($tpaqsaint,0);
        $totales['tmonto']  = Strings::rdecimal($tmonto,2);


        //al terminar, se almacena en una variable de salida el array.
        $output['contenido_tabla'] = $data;

        //de igual forma, se almacena en una variable de salida el array de totales.
        $output['totales_tabla'] = $totales;

        echo json_encode($output);
        break;

    case "listar_depositos":

        $output['lista_depositos'] = Almacen::todos();

        echo json_encode($output);
        break;
}