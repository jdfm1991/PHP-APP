<?php

//llamar a la conexion de la base de datos
require_once("../acceso/conexion.php");
//llamar a el modelo Usuarios
require_once("roles_modelo.php");
$roles = new Roles();

$id_rol = isset($_POST["id_rol"]);
$rol=isset($_POST["rol"]);

/*$fecha_registro = date("Y-m-d h:i:s");
$fecha_ult_ingreso = date("Y-m-d h:i:s");*/


switch($_GET["op"]){



  case "guardaryeditar":


/*si el id no existe entonces lo registra
importante: se debe poner el $_POST sino no funciona*/

if(empty($_POST["id_rol"])){

  /*verificamos si existe la cedula y correo en la base de datos, si ya existe un registro con la cedula o correo entonces no se registra el usuario*/

  $datos = $roles->get_nombre_rol($_POST["rol"]);

  if(is_array($datos)==true and count($datos)==0){

//no existe el usuario por lo tanto hacemos el registros

    $roles->registrar_rol($rol);



    /*si ya exista el correo y la cedula entonces aparece el mensaje*/

  } else {



  }

} /*cierre de la validacion empty  */ else {

  /*si ya existe entonces editamos el usuario*/

  $roles->editar_rol($rol,$id_rol);


}



break;

case "mostrar":

//selecciona el id del usuario

//el parametro id_chofer se envia por AJAX cuando se edita el usuario

$datos = $roles->get_rol_por_id($_POST["id_rol"]);

//verifica si el id_chofer tiene registro asociado a compras
/*$usuario_compras=$roles->get_usuario_por_id_compras($_POST["id_chofer"]);*/

//verifica si el id_chofer tiene registro asociado a ventas
/*  $usuario_ventas=$roles->get_usuario_por_id_ventas($_POST["id_chofer"]);*/


//si el id_chofer NO tiene registros asociados en las tablas compras y ventas entonces se puede editar todos los campos de la tabla usuarios
/*  if(is_array($usuario_compras)==true and count($usuario_compras)==0 and is_array($usuario_ventas)==true and count($usuario_ventas)==0){*/


  foreach($datos as $row){


    $output["descripcion"] = $row["Descripcion"];

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


case "listar":
$datos = $roles->get_roles();
//declaramos el array
$data = Array();
foreach($datos as $row){
  $sub_array= array();





  $sub_array[]= $row["ID"];
  $sub_array[] = $row["Descripcion"];
  $sub_array[] = '<div class="col text-center"><button type="button" onClick="mostrar('.$row["ID"].');"  id="'.$row["ID"].'" class="btn btn-info btn-sm update">Editar</button>'." ".'<button type="button" onClick="eliminar('.$row["ID"].');"  id="'.$row["ID"].'" class="btn btn-danger btn-sm eliminar">Eliminar</button></div>';

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
