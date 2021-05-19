<?php

//conexion a la base de datos
require_once("../../config/conexion.php");

class Usuarios extends Conectar
{
    //listar los usuarios
    public function get_usuarios()
    {

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT cedula, login, nomper, email, clave, id_rol, fecha_registro, fecha_ult_ingreso, estado 
                FROM Usuarios WHERE deleted_at IS NULL";
        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_usuario_byDni($dni)
    {

        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT cedula, login, nomper, email, clave, id_rol, fecha_registro, fecha_ult_ingreso, estado 
                FROM Usuarios WHERE deleted_at IS NULL AND cedula = ?";
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


    public function editar_estado($id, $estado)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "update usuarios set estado=? where cedula=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $estado);
        $sql->bindValue(2, $id);

        return $sql->execute();
    }

    public function get_cedula_correo_del_usuario($cedula, $email)
    {

        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT cedula, login, nomper, email, clave, id_rol, fecha_registro, fecha_ult_ingreso, estado 
                FROM usuarios WHERE cedula=? OR email=? AND deleted_at IS NULL";

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
//        $sql = "DELETE FROM usuarios WHERE cedula = ?";
        $sql = "update usuarios set deleted_at=? where cedula=?";

        //PREPARACION DE LA CONSULTA PARA EJECUTARLA.
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, date("Y/m/d h:i:s"));
        $sql->bindValue(2, $dni);

        return $sql->execute();
    }

}

?>
