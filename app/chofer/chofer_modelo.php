<?php

//conexion a la base de datos
require_once("../../config/conexion.php");


class Chofer extends Conectar
{
    public function registrar_chofer($data)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();


        $sql = "INSERT INTO choferes(Cedula, Nomper, Fecha_Registro, Estado) VALUES(?,?,?,?);";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $data["cedula"]);
        $sql->bindValue(2, $data["nomper"]);
        $sql->bindValue(3, $data["fecha_ingreso"]);
        $sql->bindValue(4, $data["estado"]);

        return $sql->execute();
    }

    public function editar_chofer($data)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE choferes SET Nomper=?, Estado=?  WHERE Cedula=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $data["nomper"]);
        $sql->bindValue(2, $data["estado"]);
        $sql->bindValue(3, $data["cedula"]);

        return $sql->execute();
    }

    public function editar_estado($id, $estado)
    {
        //LLAMAMOS A LA CONEXION QUE CORRESPONDA CUANDO ES SAINT: CONEXION2
        //CUANDO ES APPWEB ES CONEXION.
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE choferes SET estado=? WHERE cedula=?";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $estado);
        $sql->bindValue(2, $id);
        return $sql->execute();
    }
}
