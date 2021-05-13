<?php

//conexion a la base de datos
require_once("../../config/conexion.php");

class Vehiculos extends Conectar {

  public function get_filas_vehiculos(){

    $conectar= parent::conexion();
    $sql="SELECT * FROM Vehiculos";
    $sql=$conectar->prepare($sql);
    $sql->execute();
    $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);

    return $sql->rowCount();
  }

 //listar los usuarios
  public function get_vehiculos(){

    $conectar=parent::conexion();
    parent::set_names();
    $sql="SELECT * FROM Vehiculos";
    $sql=$conectar->prepare($sql);
    $sql->execute();

    return $resultado=$sql->fetchAll();
  }


  public function registrar_vehiculo($placa,$modelo,$capacidad,$volumen,$estado,$id_vehiculo){

    $conectar=parent::conexion();
    parent::set_names();

    $placa = strtoupper(str_replace("-","",$_POST['placa']));
    $modelo = strtoupper($_POST['modelo']);
    $capacidad = str_replace(",","",$_POST['capacidad']);
    $capacidad1 = str_replace(".","",$capacidad);

    $sql="INSERT INTO vehiculos VALUES(?,?,?,?,getdate(),?);";

    $sql=$conectar->prepare($sql);

    $sql->bindValue(1, $placa);
    $sql->bindValue(2, $modelo);
    $sql->bindValue(3, $capacidad1);
    $sql->bindValue(4, $_POST["volumen"]);
    $sql->bindValue(5, $_POST["estado"]);
    $sql->execute();


  }

  public function editar_vehiculo($modelo,$capacidad,$volumen,$estado,$id_vehiculo){

    $conectar=parent::conexion();
    parent::set_names();

    $modelo = strtoupper($_POST['modelo']);
    $capacidad = str_replace(",","",$_POST['capacidad']);
    $capacidad1 = str_replace(".","",$capacidad);

    $sql="UPDATE vehiculos SET  Modelo=?,  Capacidad=?,  Volumen=?,  Estado=?  WHERE   ID=?";

//echo $sql; exit();

    $sql=$conectar->prepare($sql);

    $sql->bindValue(1, $modelo);
    $sql->bindValue(2, $capacidad1);
    $sql->bindValue(3, $_POST["volumen"]);
    $sql->bindValue(4, $_POST["estado"]);
    $sql->bindValue(5, $_POST["id_vehiculo"]);
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

    $sql="UPDATE vehiculos SET estado=? WHERE id=?";

    $sql=$conectar->prepare($sql);
    $sql->bindValue(1,$estado);
    $sql->bindValue(2,$id);
    $sql->execute();
  }

  public function get_placa_del_vehiculo($placa){

    $conectar=parent::conexion();
    parent::set_names();

    $sql="SELECT * FROM vehiculos WHERE placa=? ";

    $sql=$conectar->prepare($sql);
    $sql->bindValue(1, $placa);
    $sql->execute();
    return $resultado=$sql->fetchAll();

  }


}
?>
