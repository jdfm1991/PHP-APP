<?php

//conexion a la base de datos

//require_once("../config/conexion.php");


class Usuarios extends Conectar {

  public function get_filas_usuario(){

    $conectar= parent::conexion();
    $sql="select * from Usuarios";
    $sql=$conectar->prepare($sql);
    $sql->execute();
    $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);

    return $sql->rowCount();
  }

  public function login(){

    $conectar=parent::conexion();
    parent::set_names();
    if(isset($_POST["enviar"])){

//INICIO DE VALIDACIONES
      $clave = md5($_POST["clave"]);
      $login = $_POST["login"];

      if(empty($login) and empty($clave)){
        header("Location:".Conectar::ruta()."index.php?m=2");
        exit();
      } else {

        $sql= "SELECT * FROM Usuarios WHERE Login=? AND Clave =?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $login);
        $sql->bindValue(2, $clave);
        $sql->execute();

        $resultado = $sql->fetch();

//si existe el registro entonces se conecta en session
        if(is_array($resultado) and count($resultado)>0) {
          /*IMPORTANTE: la session guarda los valores de los campos de la tabla de la bd*/
          $_SESSION["cedula"] = $resultado['Cedula'];
          $_SESSION["login"] = $resultado['$usuario'];
          $_SESSION["nomper"] = $resultado['Nomper'];
          $_SESSION["email"] = $resultado['Email'];
          $_SESSION["rol"] = $resultado['ID_Rol'];

          header("Location:".Conectar::ruta()."principal.php");
          exit();
        } else {
//si no existe el registro entonces le aparece un mensaje
          header("Location:".Conectar::ruta()."index.php?m=1");
          exit();
        }
}//cierre del else
}//condicion enviar
}
//listar los usuarios
public function get_usuarios(){

  $conectar=parent::conexion();
  parent::set_names();
  $sql="select * from usuarios";
  $sql=$conectar->prepare($sql);
  $sql->execute();

  return $resultado=$sql->fetchAll();
}


public function registrar_usuario($cedula,$login,$nomper,$email,$clave,$rol,$estado){

  $conectar=parent::conexion();
  parent::set_names();
  $clave=md5($_POST["clave"]);

  $sql="INSERT INTO usuarios VALUES(?,?,?,?,?,?,getdate(),getdate(),?);";

  $sql=$conectar->prepare($sql);

  $sql->bindValue(1, $_POST["cedula"]);
  $sql->bindValue(2, $_POST["login"]);
  $sql->bindValue(3, $_POST["nomper"]);
  $sql->bindValue(4, $_POST["email"]);
  $sql->bindValue(5, $clave);
  $sql->bindValue(6, $_POST["rol"]);
  $sql->bindValue(7, $_POST["estado"]);
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

public function editar_usuario($login,$nomper,$email,$clave,$rol,$estado,$id_usuario){

  $conectar=parent::conexion();
  parent::set_names();

  $sql="UPDATE usuarios SET  Login=?,  Nomper=?,  Email=?,  Clave=?,  ID_Rol=?,  Estado=?  WHERE   Cedula=?";

//echo $sql; exit();

  $sql=$conectar->prepare($sql);

  $sql->bindValue(1,$_POST["login"]);
  $sql->bindValue(2,$_POST["nomper"]);
  $sql->bindValue(3,$_POST["email"]);
  $sql->bindValue(4,$_POST["clave"]);
  $sql->bindValue(5,$_POST["rol"]);
  $sql->bindValue(6,$_POST["estado"]);
  $sql->bindValue(7,$_POST["id_usuario"]);
  $sql->execute();

}

//fin editar usuario

//mostrar los datos del usuario por el id
public function get_usuario_por_id($id){

  $conectar=parent::conexion();
  parent::set_names();
  $sql="SELECT * FROM usuarios WHERE cedula=?";
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

  $sql="update usuarios set estado=? where cedula=?";
  $sql=$conectar->prepare($sql);
  $sql->bindValue(1,$estado);
  $sql->bindValue(2,$id);
  $sql->execute();
}

public function get_cedula_correo_del_usuario($cedula,$email){

  $conectar=parent::conexion();
  parent::set_names();

  $sql="select * from usuarios where cedula=? or email=?";

  $sql=$conectar->prepare($sql);

  $sql->bindValue(1, $cedula);
  $sql->bindValue(2, $email);
  $sql->execute();

  return $resultado=$sql->fetchAll();

}

public function get_roles(){

  $conectar=parent::conexion();
  parent::set_names();

  $sql="SELECT * FROM roles";

  $sql=$conectar->prepare($sql);
  $sql->execute();

  return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
}

}
?>
