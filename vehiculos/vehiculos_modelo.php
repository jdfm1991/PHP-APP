<?php

//conexion a la base de datos

//require_once("../config/conexion.php");


class Vehiculos extends Conectar {

  public function get_filas_vehiculos(){

    $conectar= parent::conexion();
    $sql="select * from Vehiculos";
    $sql=$conectar->prepare($sql);
    $sql->execute();
    $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);

    return $sql->rowCount();
  }

 //listar los usuarios
  public function get_vehiculos(){

    $conectar=parent::conexion();
    parent::set_names();
    $sql="select * from Vehiculos";
    $sql=$conectar->prepare($sql);
    $sql->execute();

    return $resultado=$sql->fetchAll();
  }


  public function registrar_vehiculo($placa,$modelo,$capacidad,$volumen,$estado,$id_vehiculo){

    $conectar=parent::conexion();
    parent::set_names();

    $sql="INSERT INTO vehiculos VALUES(?,?,?,?,getdate(),?);";

    $sql=$conectar->prepare($sql);

    $sql->bindValue(1, $_POST["placa"]);
    $sql->bindValue(2, $_POST["modelo"]);
    $sql->bindValue(3, $_POST["capacidad"]);
    $sql->bindValue(4, $_POST["volumen"]);
    $sql->bindValue(5, $_POST["estado"]);
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

public function editar_vehiculo($modelo,$capacidad,$volumen,$estado,$id_vehiculo){

  $conectar=parent::conexion();
  parent::set_names();

  $sql="UPDATE vehiculos SET  Modelo=?,  Capacidad=?,  Volumen=?,  Estado=?  WHERE   ID=?";

//echo $sql; exit();

  $sql=$conectar->prepare($sql);

  $sql->bindValue(1,$_POST["modelo"]);
  $sql->bindValue(2,$_POST["capacidad"]);
  $sql->bindValue(3,$_POST["volumen"]);
  $sql->bindValue(4,$_POST["estado"]);
  $sql->bindValue(5,$_POST["id_vehiculo"]);
  $sql->execute();

}

//fin editar usuario

//mostrar los datos del usuario por el id
public function get_vehiculo_por_id($id){

  $conectar=parent::conexion();
  parent::set_names();
  $sql="SELECT * FROM vehiculos WHERE id=?";
  $sql=$conectar->prepare($sql);
  $sql->bindValue(1, $id);
  $sql->execute();

  return $resultado=$sql->fetchAll();

}

public function editar_estado($id,$estado){

  $conectar=parent::conexion();
  parent::set_names();
//el parametro est se envia por via ajax
  if($_POST["est"]=="0"){
    $estado=1;
  } else {
    $estado=0;
  }

  $sql="update vehiculos set estado=? where id=?";
  $sql=$conectar->prepare($sql);
  $sql->bindValue(1,$estado);
  $sql->bindValue(2,$id);
  $sql->execute();
}

public function get_cedula_correo_del_vehiculo($placa){

  $conectar=parent::conexion();
  parent::set_names();

  $sql="select * from vehiculos where placa=? ";
  $sql=$conectar->prepare($sql);
  $sql->bindValue(1, $placa);
  $sql->execute();
  return $resultado=$sql->fetchAll();

}


}
?>
