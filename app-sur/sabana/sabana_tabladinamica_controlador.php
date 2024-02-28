<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("sabana_tabladinamica_modelo.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("../costodeinventario/costodeinventario_modelo.php");

//INSTANCIAMOS EL MODELO
$costo = new CostodeInventario();

//INSTANCIAMOS EL MODELO
$tabladinamica = new Tabladinamica_Sabana();

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
            case 'f': $datos = $tabladinamica->getTabladinamicaFactura($data); break;
            case 'n': $datos = $tabladinamica->getTabladinamicaNotaDeEntrega($data); break;
        }

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $arr_data = Array();

        $valor=$costo_total= $renta_bruta=$costod= $renta= $paqt = $bult = $kilo = $total = 0;

        if (is_array($datos)==true and count($datos)>0)
        {
            foreach ($datos as $key => $row)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $montod = $montobs = $descuento = 0;

                 /*  $multiplicador = in_array($row['tipo'], array('A','C'))
                    ? 1
                    : -1;*/

                    if($row['tipo']=='B' or $row['tipo']=='D'){
                            $multiplicador = -1;
                        }else{
                            $multiplicador = 1;
                        }

                switch ($_POST['tipo']) {
                    case 'f':
                        $montod = (is_numeric($row["factor"]) and $row["factor"]>0)
                            ? Numbers::avoidNull($row["montod"]) / $row["factor"]
                            : 0;

                        $montobs = $row['montod'];

                        $descuento = (is_numeric($row["factor"]) and $row["factor"]>0)
                            ? Numbers::avoidNull($row["descuento"]) / $row["factor"]
                            : 0;
                        break;
                    case 'n':
                        $montod = Numbers::avoidNull($row["montod"]);

                        $montobs = (is_numeric($row["factor"]) and $row["factor"]>0)
                            ? Numbers::avoidNull($row["montod"]) * $row["factor"]
                            : 0;

                        $descuento = Numbers::avoidNull($row["descuento"]);
                        break;
                }

                         /*  $numero = 1;
                        
                            //se contruye un string para listar los depositvos seleccionados
                            //en caso que no haya ninguno, sera vacio
                            $edv = "";
                            if(count($numero)>0) {
                                foreach ($numero AS $sf) {
                                    $edv .= "'" . $sd . "',";
                                }*
                            }*/

                             $fecha_E = date('Y-m-d', strtotime($row["fechae"]));

                $data_costo = $tabladinamica->getCostosdEinventario($row["coditem"],$fecha_E,$fecha_E);
                $costo= $factor=$bulto=$paquete=0;
                foreach ($data_costo as $pot){

                        if ($pot['display'] == 0) {
                             $cdisplay = 0;
                        } else {
                            $cdisplay = $pot['costo'] / $pot['display'];
                        }

                        $costo=$pot['costo'];
                        $factor=$pot['factor'];
                        $bulto=$pot['bultos'];
                        $paquete=$pot['paquetes'];
                        $EsExento=$pot["EsExento"];

                }

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
                $sub_array['bul']           = number_format($row["bul"] * $multiplicador, 2);
                $sub_array['kg']            = Strings::rdecimal($row["kg"] * $multiplicador, 1);
                $sub_array['instancia']     = $row["instancia"];
                $sub_array['montod']        =  Strings::rdecimal($montod  * $multiplicador, 2);


                if($row["unid"]=='BULT'){
                    if($factor == 0 ){
                            $sub_array['costod'] =0;
                            $sub_array['total_costod'] =0;
                            $sub_array['renta_bruta']  =  0;
                            $sub_array['rentabilidad']  =0;
                    }else{
                        $sub_array['costod'] = Strings::rdecimal(($costo),2); 
                        $sub_array['total_costod'] = Strings::rdecimal(($costo*$row["cantidad"])* $multiplicador,2);
                        $sub_array['renta_bruta']  =  Strings::rdecimal((($montod  * $multiplicador) - (($costo*$row["cantidad"])* $multiplicador)),2); 
                        
                 

                        if(((($montod  * $multiplicador)-($descuento  * $multiplicador)))>0){

                            $sub_array['rentabilidad']  =   Strings::rdecimal(  (( (((($montod  * $multiplicador)-($descuento  * $multiplicador)))-(($costo*$row["cantidad"])* $multiplicador)) / ((($montod  * $multiplicador)-($descuento  * $multiplicador))) )*100)* $multiplicador,2);
                        
                        }else{

                            $sub_array['rentabilidad']  = 0;

                        }
                        
                        
                    }
                        
                }else{
                    if($row["unid"]=='PAQ'){
                        if($factor == 0 ){
                            $sub_array['costod'] =0;
                            $sub_array['total_costod'] =0;
                             $sub_array['renta_bruta']  =  0;
                             $sub_array['rentabilidad']  =0;
                        }else{
                        $sub_array['costod'] =  Strings::rdecimal(($cdisplay),2);
                        $sub_array['total_costod'] =  Strings::rdecimal(($cdisplay *$row["cantidad"])* $multiplicador,2);
                        $sub_array['renta_bruta']  =  Strings::rdecimal((($montod  * $multiplicador) - (($cdisplay*$row["cantidad"])* $multiplicador)),2); 

                            if(((($montod  * $multiplicador)-($descuento  * $multiplicador)))>0){

                                $sub_array['rentabilidad']  =   Strings::rdecimal(  (( (((($montod  * $multiplicador)-($descuento  * $multiplicador)))-(($cdisplay*$row["cantidad"])* $multiplicador)) / ((($montod  * $multiplicador)-($descuento  * $multiplicador))) )*100)* $multiplicador,2);

                            }else{

                                $sub_array['rentabilidad']  = 0;

                            }


                        
                        }
                    }
                }

                $sub_array['descuento']     =  Strings::rdecimal($descuento  * $multiplicador, 2);
                $sub_array['factor']        =  Strings::rdecimal($row['factor'], 2);
                $sub_array['montobs']       =  Strings::rdecimal($montobs * $multiplicador, 2);
                $sub_array['fechae']        = date(FORMAT_DATE, strtotime($row["fechae"]));
                $sub_array['mes']           =  utf8_encode($row['MES']);

                $paqt  += $row["paq"] * $multiplicador;
                $bult  += $row["bul"] * $multiplicador;
                $kilo  += $row["kg"]  * $multiplicador;
                $total += $montod * $multiplicador;

                if($row["unid"]=='BULT' ){
                    if($factor == 0 ){
                            $costod +=0;
                            $costo_total+=0;
                            $renta_bruta +=0;
                            $renta +=   0;
                        }else{
                        $costod += (($costo )); 
                        $costo_total+=(($costo  )*$row["cantidad"])* $multiplicador; 
                        
                         $renta_bruta +=(($montod  * $multiplicador) - (($costo *$row["cantidad"])* $multiplicador));


                         if(((($montod  * $multiplicador)-($descuento  * $multiplicador)))>0){
                       

                            $renta+=( ( (((($montod  * $multiplicador)-($descuento  * $multiplicador)))-(($costo*$row["cantidad"])* $multiplicador)) / ((($montod  * $multiplicador)-($descuento  * $multiplicador))) )*100)* $multiplicador;
                        
                        }else{

                            $renta+= 0;

                        }

                            //$renta += (((($montod  * $multiplicador) - (($costo*$row["cantidad"])* $multiplicador))/($montod  * $multiplicador))*100);
                        }
                }else{
                    if($row["unid"]=='PAQ' ){
                        if($factor == 0 ){
                            $costod +=0;
                            $costo_total+=0;
                            $renta_bruta +=0;
                            $renta +=   0;
                        }else{
                          $costod += (($cdisplay));
                         $costo_total+=(($cdisplay  )*$row["cantidad"])* $multiplicador; 
                        
                         $renta_bruta +=(($montod  * $multiplicador) - (($cdisplay *$row["cantidad"])* $multiplicador));
                           // $renta+= (((($montod  * $multiplicador) - (($cdisplay )*$row["cantidad"]))/($montod  * $multiplicador))*100);

                           if(((($montod  * $multiplicador)-($descuento  * $multiplicador)))>0){
                            $renta+=( ( (((($montod  * $multiplicador)-($descuento  * $multiplicador)))-(($cdisplay*$row["cantidad"])* $multiplicador)) / ((($montod  * $multiplicador)-($descuento  * $multiplicador))) )*100)* $multiplicador;
                         
                            }else{

                                $renta+= 0;

                            }
                        }
                    }
                }

                
                $arr_data[] = $sub_array;
            }
        }

        $total = (hash_equals('n', $_POST['tipo']))
            ? Numbers::avoidNull($tabladinamica->getTotalNotaDeEntrega($data,'C')[0]['montod']) - Numbers::avoidNull($tabladinamica->getTotalNotaDeEntrega($data, 'D')[0]['montod'])
            : $total;

            if(count($datos)==0){
                $valor=0;
            }else{
                $valor=$renta/count($datos);
            }

        $totales_tabladinamica = array(
            "paqt"  => Strings::rdecimal($paqt, 2),
            "bult"  => Strings::rdecimal($bult, 2),
            "kilo"  => Strings::rdecimal($kilo, 2),
            "costod"  => Strings::rdecimal($costod, 2),
            "costo_total"  => Strings::rdecimal($costo_total, 2),
            "renta_bruta"  => Strings::rdecimal($renta_bruta, 2),
            "renta"  => Strings::rdecimal( $valor, 2),
            "total" => Strings::rdecimal($total, 2),
        );



        switch ($_POST['tipo']) {
            case 'f': $resumen = $tabladinamica->getResumenFactura($data); break;
            case 'n': $resumen = $tabladinamica->getResumenNotaDeEntrega($data); break;
        }

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $arr_data1 = Array();

        if (is_array($resumen)==true and count($resumen)>0)
        {
            foreach ($resumen as $key => $row)
            {
                //DECLARAMOS UN SUB ARRAY Y LO LLENAMOS POR CADA REGISTRO EXISTENTE.
                $sub_array = array();

                $descuentototal = $descuentototalbs = 0;

                switch ($_POST['tipo']) {
                    case 'f':
                        $descuentototal =  ($row["descto1"] > 0 & $row["descto2"] > 0)
                            ? ($row["descto1"] + $row["descto2"]) / $row["tasa"]
                            :  $row["descto1"] / $row["tasa"];

                        $descuentototalbs =  ($row["descto1"] > 0 & $row["descto2"] > 0)
                            ? $row["descto1"] + $row["descto2"]
                            : $row["descto1"];
                        break;
                    case 'n':
                        $descuentototal   = $row["descuento"];

                        $descuentototalbs = $row["descuento"] * $row["tasa"];
                        break;
                }

                $sub_array['num']              = $key+1;
                $sub_array['codvend']          = $row["codvend"];
                $sub_array['codclie']          = $row["codclie"];
                $sub_array['descrip']          = $row["descrip"];
                $sub_array['descuentototal']   = Strings::rdecimal($descuentototal, 2);
                $sub_array['tasa']             = Strings::rdecimal($row["tasa"], 2);
                $sub_array['descuentototalbs'] = Strings::rdecimal($descuentototalbs, 2);
                $sub_array['numerod']          = utf8_decode($row["numerod"]);
                $sub_array['tipofac']          = $row["tipofac"];
                $sub_array['fechae']           = date(FORMAT_DATE, strtotime($row["fechae"]));




                $i = explode("/", date(FORMAT_DATE, strtotime($row["fechae"])));


 if($i[1]==1){

            $string='ENERO';

         }else{

                if($i[1]==2){
                    $string='FEBRERO';
                }else{

                    if($i[1]==3){
                          $string='MARZO';      
                    }else{

                        if($i[1]==4){
                            $string='ABRIL';
                        }else{

                            if($i[1]==5){
                                $string='MAYO';
                            }else{

                                if($i[1]==6){
                                    $string='JUNIO';
                                }else{

                                    if($i[1]==7){
                                        $string='JULIO';
                                    }else{

                                        if($i[1]==8){
                                            $string='AGOSTO';
                                        }else{

                                            if($i[1]==9){
                                                $string='SEPTIEMBRE';
                                            }else{

                                                if($i[1]==10){
                                                    $string='OCTUBRE';
                                                }else{

                                                    if($i[1]==11){
                                                        $string='NOVIEMBRE';
                                                    }else{

                                                        if($i[1]==12){
                                                            $string='DICIEMBRE';
                                                        }else{
                                                                  $string='';
                                                        }   
                                                            
                                                    }
                                                    
                                                }
                                                
                                            }
                                            
                                        }
                                        
                                    }
                                    
                                }
                                    
                            }
                                                    
                        }
                        
                    }      
                    
                }

         }




                $sub_array['mes']           = $string;



                $arr_data1[] = $sub_array;
            }
        }

        //RETORNAMOS EL JSON CON EL RESULTADO DEL MODELO.
        $results = array(
            "tabla"   => $arr_data,
            "totales" => $totales_tabladinamica,
            "resumen" => $arr_data1
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
