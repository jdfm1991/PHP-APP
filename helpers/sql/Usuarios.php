<?php


class Usuarios extends Conectar
{
    public static function todos()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT cedula, login, nomper, email, clave, id_rol, fecha_registro, fecha_ult_ingreso, estado 
               FROM Usuarios WHERE deleted_at IS NULL";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function byUserName($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT cedula, login, nomper, email, clave, id_rol, fecha_registro, fecha_ult_ingreso, estado
                FROM Usuarios WHERE deleted_at IS NULL AND login = ?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function byDni($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT cedula, login, nomper, email, clave, id_rol, fecha_registro, fecha_ult_ingreso, estado 
                FROM Usuarios WHERE deleted_at IS NULL AND cedula = ?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function byRol($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        $sql= "SELECT cedula, login, nomper, email, clave, id_rol, fecha_registro, fecha_ult_ingreso, estado 
               FROM Usuarios WHERE deleted_at IS NULL AND ID_Rol = ?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}