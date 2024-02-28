<?php
//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO DE ACTIVACIONCLIENTES
require_once("NEcobros_modelo.php");

//INSTANCIAMOS EL MODELO
$NEcobros = new NEcobros();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {

    case "listar":

        //obtenemos la marca seleccionada enviada por post
       // $marca = $_POST['marca'];

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
        $datos = $NEcobros->getNotasPorCobrar($edv);

        //inicializamos los acumuladores
   
       

        //DECLARAMOS ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();
        $totales = Array();
 if (is_array($datos)==true and count($datos)>0)
{

                $SaldoPend = 0;
                $SaldoPendolar =0; 
                $total_SaldoPend_07=0;
                $total_SaldoPend_815=0;
                $total_SaldoPend_164=0;
                $total_SaldoPend_m40=0;

        foreach ($datos as $row) {

                 $SaldoPend_m40=0;
                $SaldoPend_164=0;
                $SaldoPend_815=0;
                $SaldoPend_07=0;
                

            //creamos un array para almacenar los datos procesados
            $sub_array = array();
            $sub_array['ruta'] = $row["Ruta"];
            $sub_array['NroDoc'] = $row["TipoOpe"].' '.$row["NroDoc"];
            $sub_array['CodClie'] = $row["CodClie"];
            $sub_array['Cliente'] = $row["Cliente"];
            $sub_array['FechaEmi'] = date('d/m/Y', strtotime($row["FechaEmi"]));



            if($row["DiasTransHoy"]>=0 AND $row["DiasTransHoy"]<=7){

                $sub_array['p0_7'] = number_format($row["SaldoPend"],2,',','.');
                $sub_array['p8_15'] = 0;
                $sub_array['p16_40'] = 0;
                $sub_array['mas_40'] = 0;
                 $SaldoPend_07 += $row['SaldoPend'];


            }else{

                if($row["DiasTransHoy"]>=8 AND $row["DiasTransHoy"]<=15){

                    $sub_array['p0_7'] = 0;
                    $sub_array['p8_15'] =  number_format($row["SaldoPend"],2,',','.');
                    $sub_array['p16_40'] = 0;
                    $sub_array['mas_40'] = 0;
                    $SaldoPend_815 += $row['SaldoPend'];


                }else{

                    if($row["DiasTransHoy"]>=16 AND $row["DiasTransHoy"]<=40){

                            $sub_array['p0_7'] = 0;
                            $sub_array['p8_15'] =  0;
                            $sub_array['p16_40'] = number_format($row["SaldoPend"],2,',','.');
                            $sub_array['mas_40'] = 0;
                            $SaldoPend_164 += $row['SaldoPend'];


                    }else{

                        if($row["DiasTransHoy"]>40){

                             $sub_array['p0_7'] = 0;
                            $sub_array['p8_15'] =  0;
                            $sub_array['p16_40'] = 0;
                            $sub_array['mas_40'] = number_format($row["SaldoPend"],2,',','.');
                            $SaldoPend_m40 += $row['SaldoPend'];


                        }else{


                        }


                    }


                }


            }

             $sub_array['SaldoPend'] = number_format($SaldoPend_07+$SaldoPend_815+$SaldoPend_164+$SaldoPend_m40,2,',','.');


             $sub_array['Supervisor'] = $row["Supervisor"];

            //ACUMULAMOS LOS TOTALES
            $SaldoPend += $row['SaldoPend'];


            $total_SaldoPend_07+=$SaldoPend_07;
            $total_SaldoPend_815+=$SaldoPend_815;
            $total_SaldoPend_164+=$SaldoPend_164;
            $total_SaldoPend_m40+=$SaldoPend_m40;


            //AGREGAMOS AL ARRAY DE CONTENIDO DE LA TABLA
            $data[] = $sub_array;
        }
        //creamos un array para almacenar los datos acumulados
        $sub_array1 = array();
        $sub_array1['SaldoPend'] = Strings::rdecimal($SaldoPend,2,',','.');
        $sub_array1['SaldoPend_m40'] = Strings::rdecimal($total_SaldoPend_m40,2,',','.');
        $sub_array1['SaldoPend_164'] = Strings::rdecimal($total_SaldoPend_164,2,',','.');
        $sub_array1['SaldoPend_815'] = Strings::rdecimal($total_SaldoPend_815,2,',','.');
        $sub_array1['SaldoPend_07'] = Strings::rdecimal($total_SaldoPend_07,2,',','.');
        $sub_array1['cantidad_registros'] = count($datos);

        //al terminar, se almacena en una variable de salida el array.
        $output['contenido_tabla'] = $data;
        //de igual forma, se almacena en una variable de salida el array de totales.
        $output['totales_tabla'] = $sub_array1;
}
        echo json_encode($output);


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

?>
