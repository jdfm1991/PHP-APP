<?php


class Choferes extends Conectar {

    public static function todos()
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

              //  $sql = "SELECT Cedula, Nomper, Fecha_Registro, Estado FROM choferes WHERE deleted_at IS NULL";
        //$sql= "SELECT id_chofer, cedula as Cedula, descripcion as Nomper, estatus as Estado FROM appChofer";
        $sql= "SELECT * from [AJ].[dbo].appchofer order by descripcion";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByDni($key)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.

            $sql="SELECT id_chofer, cedula, descripcion , estatus FROM [AJ].[dbo].appchofer WHERE cedula=?";
//        $sql= "SELECT descripcion as Nomper,* FROM appChofer WHERE cedula=?";

        $result = (new Conectar)->conexion2()->prepare($sql);
        $result->bindValue(1,$key);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}