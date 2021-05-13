<?php

//conexion a la base de datos
require_once("../../config/conexion.php");

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

    public function get_usuario_byDni($dni)
    {

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "select * from usuarios where cedula = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $dni);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function registrar_usuario($data)
    {
        $i=0;
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO usuarios VALUES(?,?,?,?,?,?,?,?,?);";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $data["cedula"]);
        $sql->bindValue($i+=1, $data["login"]);
        $sql->bindValue($i+=1, ucwords($data["nomper"]));
        $sql->bindValue($i+=1, strtolower($data["email"]));
        $sql->bindValue($i+=1, md5($data["clave"]));
        $sql->bindValue($i+=1, $data["rol"]);
        $sql->bindValue($i+=1, date("d/m/Y h:i:s"));
        $sql->bindValue($i+=1, date("d/m/Y h:i:s"));
        $sql->bindValue($i+=1, $data["estado"]);

        return $sql->execute();
    }

    public function editar_usuario($data)
    {
        $i=0;
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE usuarios SET  Login=?,  Nomper=?,  Email=?,  Clave=?,  ID_Rol=?,  Estado=?  WHERE   Cedula=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue($i+=1, $data["login"]);
        $sql->bindValue($i+=1, ucwords($data["nomper"]));
        $sql->bindValue($i+=1, strtolower($data["email"]));
        $sql->bindValue($i+=1, md5($data["clave"]));
        $sql->bindValue($i+=1, $data["rol"]);
        $sql->bindValue($i+=1, $data["estado"]);
        $sql->bindValue($i+=1, $data["id_usuario"]);

        return $sql->execute();
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

    public function eliminar_usuario($dni) {

        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar= parent::conexion();
        parent::set_names();

        //QUERY
        $sql = "DELETE FROM usuarios WHERE cedula = ?";
        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$dni);

        return $sql->execute();;
    }

}

?>
