<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO
require_once("DataVendedores_modelo.php");

//INSTANCIAMOS EL MODELO
$relacion = new DataVendedores();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {


    case "guardaryeditar":

        $CodVend = $_POST["CodVend"];
        $valor = $_POST["valorCampo"];
        $validdor=false;
        $Fecha = date('Y-m-d');

        $datos = $relacion->consultaSQL("SELECT count(CodVend) as contador FROM DataEntry_Vendedores WHERE CodVend ='$CodVend'");
        $contador=0;
        foreach ($datos as $row){
                $contador=$row["contador"];
        }

        if($contador==0){
             $SAPAGCXP= $relacion->InsertSQL("DataEntry_Vendedores", "CodVend,Valor,Fecha", "'$CodVend','$valor',$Fecha");

        }else{
             $validdor= $relacion->UpdateSQL("DataEntry_Vendedores", "Valor='$valor',Fecha=$Fecha", "CodVend='$CodVend'");

        }

            //mensaje
            if($validdor){
                $output["mensaje"] = "Vendedor $CodVend Actualizado con Exito";
                $output["icono"] = "success";
            } else {
                //en caso de error mostrara uno de los mensajes asignados
                $output["icono"] = "error";
            }
        

        echo json_encode($output);
        break;

    case "listar":

        $datos = $relacion->consultaSQL("SELECT * from savend where activo = '1' and CodVend != '00'");

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        $i=1;
        foreach ($datos as $row){

             $valor =0;

            $sub_array = array();

             $atrib = "btn btn-success btn-sm estado";
            $sub_array[] = $row["CodVend"];
            $codvend = $row["CodVend"];

            $datos2 = $relacion->consultaSQL("SELECT * FROM DataEntry_Vendedores WHERE CodVend = '$codvend' ");

            foreach ($datos2 as $row2){

                $valor = number_format($row2["Valor"],2);

            }

            $sub_array[] = '<div class="col text-center">
                            <input style="width : 160px;" class="form-control input-sm" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" type="text" value="'.$valor.'" id="input'.$i.'"> %
                            </div>';

            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="cambiar(\''.$row["CodVend"].'\',\''.$row["CodVend"].'\',\''.$i.'\');" name="cambiar" id="' . $row["CodVend"] . '" class="' . $atrib . '">  MODIFICAR </button>' . " " .'
                               </div>';

            $i +=1;
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