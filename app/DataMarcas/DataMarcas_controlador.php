<?php

//LLAMAMOS A LA CONEXION BASE DE DATOS.
require_once("../../config/conexion.php");

//LLAMAMOS AL MODELO
require_once("DataMarcas_modelo.php");

//INSTANCIAMOS EL MODELO
$relacion = new DataMarcas();

//VALIDAMOS LOS CASOS QUE VIENEN POR GET DEL CONTROLADOR.
switch ($_GET["op"]) {


    case "guardaryeditar":

        $Marca = $_POST["Marca"];
        $valor = $_POST["valorCampo"];
        $validdor=false;
        $Fecha = date('Y-m-d');

        $datos = $relacion->consultaSQL("SELECT count(CodMarca) as contador FROM DataEntry_Marcas WHERE CodMarca ='$Marca'");
        $contador=0;
        foreach ($datos as $row){
                $contador=$row["contador"];
        }

        if($contador==0){
             $SAPAGCXP= $relacion->InsertSQL("DataEntry_Marcas", "CodMarca,Valor,Fecha", "'$Marca','$valor',$Fecha ");

        }else{
             $validdor= $relacion->UpdateSQL("DataEntry_Marcas", "Valor='$valor',Fecha=$Fecha", "CodMarca='$Marca'");

        }

            //mensaje
            if($validdor){
                $output["mensaje"] = "Marca $Marca Actualizado con Exito";
                $output["icono"] = "success";
            } else {
                //en caso de error mostrara uno de los mensajes asignados
                $output["icono"] = "error";
            }
        

        echo json_encode($output);
        break;

    case "listar":

        $datos = $relacion->consultaSQL("SELECT DISTINCT(marca) FROM SAPROD WHERE activo = '1' AND Marca IS NOT NULL AND Marca != '' ORDER BY marca ASC");

        //DECLARAMOS UN ARRAY PARA EL RESULTADO DEL MODELO.
        $data = Array();

        $i=1;
        foreach ($datos as $row){

             $valor =0;

            $sub_array = array();

             $atrib = "btn btn-success btn-sm estado";
            $sub_array[] = $row["marca"];
            $marca = $row["marca"];

            $datos2 = $relacion->consultaSQL("SELECT * FROM DataEntry_Marcas WHERE CodMarca = '$marca' ");

            foreach ($datos2 as $row2){

                $valor = number_format($row2["Valor"],2);

            }

            $sub_array[] = '<div class="col text-center">
                            <input style="width : 160px;" class="form-control input-sm" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" type="text" value="'.$valor.'" id="input'.$i.'"> $
                            </div>';

            $sub_array[] = '<div class="col text-center">
                                <button type="button" onClick="cambiar(\''.$row["marca"].'\',\''.$row["marca"].'\',\''.$i.'\');" name="cambiar" id="' . $row["marca"] . '" class="' . $atrib . '">  MODIFICAR </button>' . " " .'
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