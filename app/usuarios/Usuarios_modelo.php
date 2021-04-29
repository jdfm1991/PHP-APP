<?php

//conexion a la base de datos
//require_once("../../config/conexion.php");

class Usuarios extends Conectar
{

    public function get_filas_usuario()
    {

        $conectar = parent::conexion();
        $sql = "select * from Usuarios";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);

        return $sql->rowCount();
    }

    //listar los usuarios
    public function get_usuarios()
    {

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "select * from usuarios";
        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }


    public function registrar_usuario($cedula, $login, $nomper, $email, $clave, $rol, $estado)
    {

        $conectar = parent::conexion();
        parent::set_names();

        $clave = md5($_POST["clave"]);
        $nomper = ucwords($_POST["nomper"]);
        $email = strtolower($_POST["email"]);

        $sql = "INSERT INTO usuarios VALUES(?,?,?,?,?,?,getdate(),getdate(),?);";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $_POST["cedula"]);
        $sql->bindValue(2, $_POST["login"]);
        $sql->bindValue(3, $nomper);
        $sql->bindValue(4, $email);
        $sql->bindValue(5, $clave);
        $sql->bindValue(6, $_POST["rol"]);
        $sql->bindValue(7, $_POST["estado"]);
        $sql->execute();


    }

    public function editar_usuario($login, $nomper, $email, $clave, $rol, $estado, $id_usuario)
    {

        $conectar = parent::conexion();
        parent::set_names();

        $clave = $_POST["clave"];
        $nomper = ucwords($_POST["nomper"]);
        $email = strtolower($_POST["email"]);

        $sql = "UPDATE usuarios SET  Login=?,  Nomper=?,  Email=?,  Clave=?,  ID_Rol=?,  Estado=?  WHERE   Cedula=?";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $_POST["login"]);
        $sql->bindValue(2, $nomper);
        $sql->bindValue(3, $email);
        $sql->bindValue(4, $clave);
        $sql->bindValue(5, $_POST["rol"]);
        $sql->bindValue(6, $_POST["estado"]);
        $sql->bindValue(7, $_POST["id_usuario"]);
        $sql->execute();

    }

    //fin editar usuario

    //mostrar los datos del usuario por el id
    public function get_usuario_por_id($id)
    {

        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT * FROM usuarios WHERE cedula=?";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $id);
        $sql->execute();

        return $resultado = $sql->fetchAll();

    }

    public function editar_estado($id, $estado)
    {

        $conectar = parent::conexion();
        parent::set_names();
        //el parametro est se envia por via ajax
        if ($_POST["est"] == "0") {
            $estado = 1;
        } else {
            $estado = 0;
        }

        $sql = "update usuarios set estado=? where cedula=?";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $estado);
        $sql->bindValue(2, $id);
        $sql->execute();
    }

    public function get_cedula_correo_del_usuario($cedula, $email)
    {

        $conectar = parent::conexion();
        parent::set_names();

        $sql = "select * from usuarios where cedula=? or email=?";

        $sql = $conectar->prepare($sql);

        $sql->bindValue(1, $cedula);
        $sql->bindValue(2, $email);
        $sql->execute();

        return $resultado = $sql->fetchAll();

    }

    public function get_roles()
    {

        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT * FROM roles";

        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>
