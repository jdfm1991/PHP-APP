<?php

//llamar a la conexion de la base de datos
require_once("../acceso/conexion.php");
//llamar a el modelo Usuarios
require_once("../despacho/despacho_modelo.php");
$despachos = new Despachos();



switch($_GET["op"]){


    case "guardaryeditar":


        /*si el id no existe entonces lo registra
        importante: se debe poner el $_POST sino no funciona*/

        if(empty($_POST["id_chofer"])){

            /*verificamos si existe la cedula y correo en la base de datos, si ya existe un registro con la cedula o correo entonces no se registra el usuario*/

            $datos = $choferes->get_cedula_del_chofer($_POST["id_chofer"]);

            if(is_array($datos)==true and count($datos)==0){
                //no existe el usuario por lo tanto hacemos el registros

                $choferes->registrar_chofer($cedula,$nomper,$estado);



                /*si ya exista el correo y la cedula entonces aparece el mensaje*/

            } else {



            }

        } /*cierre de la validacion empty  */ else {

            /*si ya existe entonces editamos el usuario*/

            $choferes->editar_chofer($nomper,$estado,$id_chofer);


        }



        break;


    case "listar":

        $datos = $despachos->listar_despachos();
        //declaramos el array
        $data = Array();
        foreach($datos as $row){
            $sub_array= array();
            //ESTADO
            $est = '';
            $atrib = "btn btn-success btn-sm estado";
            if($row["Estado"] == 0){
                $est = 'INACTIVO';
                $atrib = "btn btn-warning btn-sm estado";
            }
            else{
                if($row["Estado"] == 1){
                    $est = 'ACTIVO';
                }
            }
            //nivel del rol asignado



            $Fecha_Registro = date('d/m/Y', strtotime($row['Fecha_Registro']));

            $sub_array[]= $row["Cedula"];
            $sub_array[] = $row["Nomper"];
            $sub_array[] = $Fecha_Registro;
            $sub_array[] = '<div class="col text-center"><button type="button" onClick="cambiarEstado('.$row["Cedula"].','.$row["Estado"].');" name="estado" id="'.$row["Cedula"].'" class="'.$atrib.'">'.$est.'</button>'." ".'<button type="button" onClick="mostrar('.$row["Cedula"].');"  id="'.$row["Cedula"].'" class="btn btn-info btn-sm update">Editar</button>'." ".'<button type="button" onClick="eliminar('.$row["Cedula"].');"  id="'.$row["Cedula"].'" class="btn btn-danger btn-sm eliminar">Eliminar</button></div>';

            $data[]=$sub_array;

        }

        $results= array(

            "sEcho"=>1, //Información para el datatables
            "iTotalRecords"=>count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
            "aaData"=>$data);
        echo json_encode($results);

        break;
}

?>
