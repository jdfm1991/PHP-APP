<?php

//llamar a la conexion de la base de datos
require_once("../acceso/conexion.php");
//llamar a el modelo Usuarios
require_once("vehiculos_modelo.php");
$vehiculo = new Vehiculos();

$id_vehiculo = isset($_POST["id_vehiculo"]);
$placa=isset($_POST["cedula"]);
$modelo=isset($_POST["login"]);
$capacidad=isset($_POST["nomper"]);
$volumen=isset($_POST["email"]);
$estado=isset($_POST["estado"]);
/*$fecha_registro = date("Y-m-d h:i:s");
$fecha_ult_ingreso = date("Y-m-d h:i:s");*/


switch($_GET["op"]){



  case "guardaryeditar":


/*si el id no existe entonces lo registra
importante: se debe poner el $_POST sino no funciona*/

if(empty($_POST["id_vehiculo"])){

  /*verificamos si existe la cedula y correo en la base de datos, si ya existe un registro con la cedula o correo entonces no se registra el usuario*/

  /*$datos = $usuarios->get_cedula_correo_del_usuario($_POST["cedula"],$_POST["email"]);*/

  /*if(is_array($datos)==true and count($datos)==0){*/

//no existe el usuario por lo tanto hacemos el registros

    $vehiculo->registrar_vehiculo($placa,$modelo,$capacidad,$volumen,$estado,$id_vehiculo);

    /*      $messages[]="El usuario se registró correctamente";*/

    /*si ya exista el correo y la cedula entonces aparece el mensaje*/

    /*} else {*/

      /*   $errors[]="La cédula o el correo ya existe";*/

      /*}*/

    } /*cierre de la validacion empty  */ else {

      /*si ya existe entonces editamos el usuario*/

      $vehiculo->editar_vehiculo($modelo,$capacidad,$volumen,$estado,$id_vehiculo);

      /*$messages[]="El usuario se editó correctamente";*/
    }



    break;

    case "mostrar":

//selecciona el id del usuario

//el parametro id_vehiculo se envia por AJAX cuando se edita el usuario

    $datos = $vehiculo->get_vehiculo_por_id($_POST["id_vehiculo"]);

//verifica si el id_vehiculo tiene registro asociado a compras
    /*$usuario_compras=$usuarios->get_usuario_por_id_compras($_POST["id_vehiculo"]);*/

//verifica si el id_vehiculo tiene registro asociado a ventas
    /*  $usuario_ventas=$usuarios->get_usuario_por_id_ventas($_POST["id_vehiculo"]);*/


//si el id_vehiculo NO tiene registros asociados en las tablas compras y ventas entonces se puede editar todos los campos de la tabla usuarios
    /*  if(is_array($usuario_compras)==true and count($usuario_compras)==0 and is_array($usuario_ventas)==true and count($usuario_ventas)==0){*/


      foreach($datos as $row){

        $output["id_vehiculo"] = $row["ID"];
        $output["placa"] = $row["Placa"];
        $output["modelo"] = $row["Modelo"];
        $output["capacidad"] = $row["Capacidad"];
        $output["volumen"] = $row["Volumen"];
        $output["estado"] = $row["Estado"];

      }
/*} else {
//si el id_vehiculo tiene relacion con la tabla compras y tabla ventas entonces se deshabilita el nombre, apellido y cedula
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
//los parametros id_vehiculo y est vienen por via ajax
$datos = $vehiculo->get_vehiculo_por_id($_POST["id"]);
//valida el id del usuario
if(is_array($datos)==true and count($datos)>0){
//edita el estado del usuario
  $vehiculo->editar_estado($_POST["id"],$_POST["est"]);
}
break;
case "listar":
$datos = $vehiculo->get_vehiculos();
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


  $Fecha_Registro = date('d/m/Y', strtotime($row['Fecha_Registro']));

  $sub_array[]= $row["Placa"];
  $sub_array[] = $row["Modelo"];
  $sub_array[] = $row["Capacidad"];
  $sub_array[]= $row["Volumen"];
  $sub_array[] = $Fecha_Registro;
  $sub_array[] = '<div class="col text-center"><button type="button" onClick="cambiarEstado('.$row["ID"].','.$row["Estado"].');" name="estado" id="'.$row["ID"].'" class="'.$atrib.'">'.$est.'</button>'." ".'<button type="button" onClick="mostrar('.$row["ID"].');"  id="'.$row["ID"].'" class="btn btn-info btn-sm update">Editar</button>'." ".'<button type="button" onClick="eliminar('.$row["ID"].');"  id="'.$row["ID"].'" class="btn btn-danger btn-sm eliminar">Eliminar</button></div>';

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
