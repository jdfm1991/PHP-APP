<?php

//conexion a la base de datos

//require_once("../config/conexion.php");


class Roles extends Conectar {

  public function get_filas_roles(){

    $conectar= parent::conexion();

    $sql="SELECT * FROM roles";

    $sql=$conectar->prepare($sql);
    $sql->execute();
    $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);

    return $sql->rowCount();
  }


//listar los usuarios
  public function get_roles(){

    $conectar=parent::conexion();
    parent::set_names();

    $sql="SELECT * FROM roles";

    $sql=$conectar->prepare($sql);
    $sql->execute();

    return $resultado=$sql->fetchAll();
  }


  public function registrar_rol($rol){

    $conectar=parent::conexion();
    parent::set_names();

    $rol =strtoupper($_POST["rol"]);

    $sql="INSERT INTO roles VALUES(?);";

    $sql=$conectar->prepare($sql);

    $sql->bindValue(1, $rol);
    $sql->execute();


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

public function get_nombre_rol($rol){

  $conectar=parent::conexion();
  parent::set_names();

  $sql="SELECT * FROM Roles WHERE Descripcion = ?";

  $sql=$conectar->prepare($sql);

  $sql->bindValue(1, $rol);
  $sql->execute();

  return $resultado=$sql->fetchAll();

}


}
?>
