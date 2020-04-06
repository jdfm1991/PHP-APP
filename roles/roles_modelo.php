<?php

//conexion a la base de datos

//require_once("../config/conexion.php");


class Roles extends Conectar {

  public function get_filas_roles(){

    $conectar= parent::conexion();
    $sql="select * from roles";
    $sql=$conectar->prepare($sql);
    $sql->execute();
    $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);

    return $sql->rowCount();
  }


//listar los usuarios
  public function get_roles(){

    $conectar=parent::conexion();
    parent::set_names();
    $sql="select * from roles";
    $sql=$conectar->prepare($sql);
    $sql->execute();

    return $resultado=$sql->fetchAll();
  }


  public function registrar_rol($rol){

    $conectar=parent::conexion();
    parent::set_names();

    $sql="INSERT INTO roles VALUES(?);";

    $sql=$conectar->prepare($sql);

    $sql->bindValue(1, $_POST["rol"]);
    $sql->execute();


//obtenemos el valor del id del usuario
    /*$id_usuario = $conectar->lastInsertId();*/


//insertamos los permisos

//almacena todos los checkbox que han sido marcados
//este es un array tiene un name=permiso[]
    /*$permisos= $_POST["permiso"];*/


// print_r($_POST);

/*
$num_elementos=0;

while($num_elementos<count($permisos)){

$sql_detalle= "insert into usuario_permiso
values(null,?,?)";

$sql_detalle=$conectar->prepare($sql_detalle);
$sql_detalle->bindValue(1, $id_usuario);
$sql_detalle->bindValue(2, $permisos[$num_elementos]);
$sql_detalle->execute();


//recorremos los permisos con este contador
$num_elementos=$num_elementos+1;
}*/


}

public function editar_rol($rol,$id_rol){

  $conectar=parent::conexion();
  parent::set_names();

  $sql="UPDATE roles SET Descripcion=? WHERE  ID=?";

//echo $sql; exit();

  $sql=$conectar->prepare($sql);

  $sql->bindValue(1,$_POST["rol"]);
  $sql->bindValue(2,$_POST["id_rol"]);
  $sql->execute();

}

//fin editar usuario

//mostrar los datos del usuario por el id
public function get_rol_por_id($id){

  $conectar=parent::conexion();
  parent::set_names();
  $sql="SELECT * FROM roles WHERE ID=?";
  $sql=$conectar->prepare($sql);
  $sql->bindValue(1, $id);
  $sql->execute();

  return $resultado=$sql->fetchAll();

}



}
?>
