<?php

//llamar a la conexion de la base de datos
require_once("../acceso/conexion.php");
//llamar a el modelo Usuarios
require_once("choferes_modelo.php");
$choferes = new Choferes();

$id_chofer = isset($_POST["id_chofer"]);
$cedula=isset($_POST["cedula"]);
$nomper=isset($_POST["nomper"]);
$estado=isset($_POST["estado"]);
/*$fecha_registro = date("Y-m-d h:i:s");
$fecha_ult_ingreso = date("Y-m-d h:i:s");*/


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

    case "mostrar":

//selecciona el id del usuario

//el parametro id_chofer se envia por AJAX cuando se edita el usuario

    $datos = $choferes->get_chofer_por_id($_POST["id_chofer"]);

//verifica si el id_chofer tiene registro asociado a compras
    /*$usuario_compras=$choferes->get_usuario_por_id_compras($_POST["id_chofer"]);*/

//verifica si el id_chofer tiene registro asociado a ventas
    /*  $usuario_ventas=$choferes->get_usuario_por_id_ventas($_POST["id_chofer"]);*/


//si el id_chofer NO tiene registros asociados en las tablas compras y ventas entonces se puede editar todos los campos de la tabla usuarios
    /*  if(is_array($usuario_compras)==true and count($usuario_compras)==0 and is_array($usuario_ventas)==true and count($usuario_ventas)==0){*/


      foreach($datos as $row){

        $output["cedula"] = $row["Cedula"];
        $output["nomper"] = $row["Nomper"];
        $output["estado"] = $row["Estado"];

      }
/*} else {
//si el id_chofer tiene relacion con la tabla compras y tabla ventas entonces se deshabilita el nombre, apellido y cedula
foreach($datos as $row){

$output["cedula_relacion"] = $row["cedula"];
$output["nombre"] = $row["nombres"];
$output["apellido"] = $row["apellidos"];
$output["cargo"] = $row["cargo"];
$output["usuario"] = $row["usuario"];
$output["password1"] = $row["password"];
$output["password2"] = $row["password2"];
$output["telefono"] = $row["telefono"];
$output["correo"] = $row["correo"];
$output["direccion"] = $row["direccion"];
$output["estado"] = $row["estado"];

}
}*///cierre del else

echo json_encode($output);
break;

case "activarydesactivar":
//los parametros id_chofer y est vienen por via ajax
$datos = $choferes->get_chofer_por_id($_POST["id"]);
//valida el id del usuario
if(is_array($datos)==true and count($datos)>0){
//edita el estado del usuario
  $choferes->editar_estado($_POST["id"],$_POST["est"]);
}
break;
case "listar":
$datos = $choferes->get_choferes();
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
