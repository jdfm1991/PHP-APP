<?php


class Choferes extends Conectar {

    public static function todos()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        //        $sql = "select * from choferes";
        $sql= "SELECT id_chofer, cedula as Cedula, descripcion as Nomper, estatus FROM appChofer";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByDni($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

        //    $sql="SELECT * FROM choferes WHERE cedula=?";
        $sql= "SELECT descripcion as Nomper,* FROM appChofer WHERE cedula=?";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}