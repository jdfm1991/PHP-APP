<?php


class CausasRechazos extends Conectar {

    public static function todos()
    {
        $sql= "SELECT id, descripcion, color FROM M_rechazos";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByNameOrId($key)
    {
        $sql= "SELECT id, descripcion, color FROM M_rechazos WHERE id=? OR descripcion=?";

        $result = (new Conectar)->conexion()->prepare($sql);
        $result->bindValue(1,$key);
        $result->bindValue(2,$key);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}